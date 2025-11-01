<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class Pago extends Conexion{
    private $monto_total;
    private $monto_dpago;
    private $cod_venta;
    private $cod_pago;
    private $fecha_pago;
    private $cod_vuelto;
    private $vuelto;
    use ValidadorTrait;
    private $errores=[];


    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }
    public function get_montototal(){
        return $this->monto_total;
    }
    public function set_montototal($valor){
        $this->monto_total=$valor;
    }
    public function get_montodpago(){
        return $this->monto_dpago;
    }
    public function set_montodpago($valor){
        $this->monto_dpago=$valor;
    }
    public function get_cod_venta(){
        return $this->cod_venta;
    }
    public function set_cod_venta($valor){
        $this->cod_venta=$valor;
    }
    public function set_cod_pago($valor){
        $this->cod_pago=$valor;
    }
    public function get_cod_pago(){
        return $this->cod_pago;
    }

    public function setdatap($datos){
        if($this->validarDecimal($datos['monto_pagado'], 'Monto pagado', 1, 20)){
            $this->monto_total = $datos['monto_pagado'];
        }else{
            $this->errores['monto_pagado'] = $this->validarDecimal($datos['monto_pagado'], 'Monto pagado', 1, 20);
        }
        if($this->validarNumerico($datos['nro_venta'], 'Número de venta', 1, 20)){
            $this->cod_venta = $datos['nro_venta'];
        }else{
            $this->errores['nro_venta'] = $this->validarNumerico($datos['nro_venta'], 'Número de venta', 1, 20);
        }
        if($this->validardatetime($datos['fecha_pago'], 'Fecha de pago')){
            $this->fecha_pago = $datos['fecha_pago'];
        }else{
            $this->errores['fecha_pago'] = $this->validardatetime($datos['fecha_pago'], 'Fecha de pago');
        }
        if(!empty($datos['vuelto_data'])){
            parse_str($datos['vuelto_data'], $this->vuelto);
            foreach($this->vuelto['vuelto'] as $vuel){
                $sql="SELECT 
                        dtp.cod_tipo_pago,
                        CASE 
                            WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN 'banco'
                            WHEN dtp.cod_caja IS NOT NULL THEN 'caja'
                            ELSE 'indefinido'
                        END AS origen,

                        CASE 
                            WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN 
                                ROUND(cb.saldo * IFNULL(
                                    (SELECT cd.tasa
                                    FROM cambio_divisa cd 
                                    WHERE cd.cod_divisa = cb.cod_divisa 
                                    ORDER BY cd.fecha DESC 
                                    LIMIT 1), 1), 2)
                            WHEN dtp.cod_caja IS NOT NULL THEN 
                                ROUND(cj.saldo * IFNULL(
                                    (SELECT cd.tasa
                                    FROM cambio_divisa cd 
                                    WHERE cd.cod_divisa = cj.cod_divisas 
                                    ORDER BY cd.fecha DESC 
                                    LIMIT 1), 1), 2)
                            ELSE NULL
                        END AS saldo_disponible

                    FROM detalle_tipo_pago dtp
                    LEFT JOIN cuenta_bancaria cb ON dtp.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
                    LEFT JOIN caja cj ON dtp.cod_caja = cj.cod_caja
                    WHERE dtp.cod_tipo_pago = :cod_tipo_pago;";
                parent::conectarBD();
                $sentencia=$this->conex->prepare($sql);
                $sentencia->bindParam(':cod_tipo_pago', $vuel['cod_tipo_pago']);
                $sentencia->execute();
                $result=$sentencia->fetch(PDO::FETCH_ASSOC);
                parent::desconectarBD();
                if($result){
                    if($vuel['monto'] > $result['saldo_disponible']){
                        $this->errores['vuelto'] = "El monto del vuelto excede el saldo disponible en {$result['origen']} para el tipo de pago {$vuel['cod_tipo_pago']}.";
                }
            
            }else{
            $this->cod_vuelto = null;
                }
            }
        }
    }

    public function check() {
        if(!empty($this->errores)) {
            $mensajes = implode(" | ",  $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    public function get_registrar($pago, $monto_venta){
        return $this->registrar($pago, $monto_venta);
    }

    private function registrar($pago, $monto_venta){
        try{
        parent::conectarBD();
        $this->conex->beginTransaction();
        if(!empty($this->vuelto)){
            $sql="INSERT INTO vuelto_emitido(vuelto_total) VALUES(:vuelto_total)";
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':vuelto_total', $this->vuelto['vuelto_pagado']);
            $resultado=$strExec->execute();
            if($resultado){
                $this->cod_vuelto = $this->conex->lastInsertId();
                foreach($this->vuelto['vuelto'] as $dvuelto){
                    if(!empty($dvuelto['monto']) && $dvuelto['monto']>0){
                        $registro="INSERT INTO detalle_vueltoe(cod_vuelto, cod_tipo_pago, monto) VALUES(:cod_vuelto, :cod_tipo_pago, :monto)";
                        $sentencia=$this->conex->prepare($registro);
                        $sentencia->bindParam(':cod_vuelto', $this->cod_vuelto);
                        $sentencia->bindParam(':cod_tipo_pago', $dvuelto['cod_tipo_pago']);
                        $sentencia->bindParam(':monto', $dvuelto['monto']);
                        $r=$sentencia->execute();
                        if($r){
                            $tp="SELECT 
                                    dtp.cod_tipo_pago,
                                    dtp.cod_cuenta_bancaria,
                                    dtp.cod_caja,

                                    -- Origen del dinero
                                    CASE 
                                        WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN 'banco'
                                        WHEN dtp.cod_caja IS NOT NULL THEN 'caja'
                                        ELSE 'indefinido'
                                    END AS origen,

                                    -- Datos de divisa
                                    CASE 
                                        WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN dcb.nombre
                                        WHEN dtp.cod_caja IS NOT NULL THEN dcj.nombre
                                        ELSE 'Sin divisa'
                                    END AS nombre_divisa,

                                    CASE 
                                        WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN dcb.abreviatura
                                        WHEN dtp.cod_caja IS NOT NULL THEN dcj.abreviatura
                                        ELSE '-'
                                    END AS abreviatura_divisa,

                                    CASE 
                                        WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN dcb.cod_divisa
                                        WHEN dtp.cod_caja IS NOT NULL THEN dcj.cod_divisa
                                        ELSE NULL
                                    END AS cod_divisa,

                                    -- Última tasa de cambio
                                    CASE 
                                        WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN (
                                            SELECT cd.tasa
                                            FROM cambio_divisa cd
                                            WHERE cd.cod_divisa = cb.cod_divisa
                                            ORDER BY cd.fecha DESC
                                            LIMIT 1
                                        )
                                        WHEN dtp.cod_caja IS NOT NULL THEN (
                                            SELECT cd.tasa
                                            FROM cambio_divisa cd
                                            WHERE cd.cod_divisa = cj.cod_divisas
                                            ORDER BY cd.fecha DESC
                                            LIMIT 1
                                        )
                                        ELSE NULL
                                    END AS ultima_tasa

                                FROM detalle_tipo_pago dtp
                                LEFT JOIN cuenta_bancaria cb ON dtp.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
                                LEFT JOIN divisas dcb ON cb.cod_divisa = dcb.cod_divisa

                                LEFT JOIN caja cj ON dtp.cod_caja = cj.cod_caja
                                LEFT JOIN divisas dcj ON cj.cod_divisas = dcj.cod_divisa

                                WHERE dtp.cod_tipo_pago = :cod_tipo_pago;";
                            $sentencia=$this->conex->prepare($tp); 
                            $sentencia->bindParam(':cod_tipo_pago', $dvuelto['cod_tipo_pago']);
                            $sentencia->execute();
                            $result=$sentencia->fetch(PDO::FETCH_ASSOC);
                            if(!empty($result['cod_cuenta_bancaria'])){
                                $monto= $result['cod_divisa'] !=1 ? $dvuelto['monto']/$result['ultima_tasa'] : $dvuelto['monto'];
                                $sql="UPDATE cuenta_bancaria SET saldo=saldo-:monto WHERE cod_cuenta_bancaria= :cod_cuenta_bancaria;";
                                $sen=$this->conex->prepare($sql);
                                $sen->bindParam(':monto', $monto);
                                $sen->bindParam(':cod_cuenta_bancaria', $result['cod_cuenta_bancaria']);
                                $r=$sen->execute();
                                if(!$r){
                                    throw new Exception("Error al actualizar el saldo de la cuenta bancaria");
                                }
                            } else if(!empty($result['cod_caja'])){
                                $monto= $result['cod_divisa'] !=1 ? $dvuelto['monto']/$result['ultima_tasa'] : $dvuelto['monto'];
                                $sql="UPDATE caja SET saldo=saldo-:monto WHERE cod_caja = :cod_caja;";
                                $sen=$this->conex->prepare($sql);
                                $sen->bindParam(':monto', $monto);
                                $sen->bindParam(':cod_caja', $result['cod_caja']);
                                $r=$sen->execute();
                                if(!$r){
                                    throw new Exception("Error al actualizar el saldo de la caja");
                                }
                            }else{
                                throw new Exception("Error al obtener la cuenta bancaria o caja");
                            }
                        }else{
                            throw new Exception("Error al registrar el detalle del vuelto");
                        }

                    }
                }
            }else{
                throw new Exception("Error al registrar el vuelto");
            }
        }

        $sql="INSERT INTO pago_recibido(cod_venta, cod_vuelto, fecha, monto_total) VALUES(:cod_venta, :cod_vuelto, :fecha, :monto_total)";
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':cod_venta', $this->cod_venta);
        $strExec->bindParam(':cod_vuelto', $this->cod_vuelto);
        $strExec->bindParam(':fecha', $this->fecha_pago);
        $strExec->bindParam(':monto_total', $this->monto_total);
        $resul = $strExec->execute();
        if($resul){
            $nuevo_cod = $this->conex->lastInsertId();
            $this->cod_pago = $nuevo_cod;
            foreach ($pago as $pagos){
                if(!empty($pagos['monto']) && $pagos['monto']>0){
                    $registro="INSERT INTO detalle_pago_recibido(cod_pago, cod_tipo_pago, monto) VALUES($nuevo_cod, :cod_tipo_pago, :monto)";
                    $sentencia=$this->conex->prepare($registro);
                    $sentencia->bindParam(':cod_tipo_pago', $pagos['cod_tipo_pago']);
                    $sentencia->bindParam(':monto', $pagos['monto']);
                    $r=$sentencia->execute();
                    if($r){
                        $tp="SELECT 
                                dtp.cod_tipo_pago,
                                dtp.cod_cuenta_bancaria,
                                dtp.cod_caja,

                                -- Origen del dinero
                                CASE 
                                    WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN 'banco'
                                    WHEN dtp.cod_caja IS NOT NULL THEN 'caja'
                                    ELSE 'indefinido'
                                END AS origen,

                                -- Datos de divisa
                                CASE 
                                    WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN dcb.nombre
                                    WHEN dtp.cod_caja IS NOT NULL THEN dcj.nombre
                                    ELSE 'Sin divisa'
                                END AS nombre_divisa,

                                CASE 
                                    WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN dcb.abreviatura
                                    WHEN dtp.cod_caja IS NOT NULL THEN dcj.abreviatura
                                    ELSE '-'
                                END AS abreviatura_divisa,

                                CASE 
                                    WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN dcb.cod_divisa
                                    WHEN dtp.cod_caja IS NOT NULL THEN dcj.cod_divisa
                                    ELSE NULL
                                END AS cod_divisa,

                                -- Última tasa de cambio
                                CASE 
                                    WHEN dtp.cod_cuenta_bancaria IS NOT NULL THEN (
                                        SELECT cd.tasa
                                        FROM cambio_divisa cd
                                        WHERE cd.cod_divisa = cb.cod_divisa
                                        ORDER BY cd.fecha DESC
                                        LIMIT 1
                                    )
                                    WHEN dtp.cod_caja IS NOT NULL THEN (
                                        SELECT cd.tasa
                                        FROM cambio_divisa cd
                                        WHERE cd.cod_divisa = cj.cod_divisas
                                        ORDER BY cd.fecha DESC
                                        LIMIT 1
                                    )
                                    ELSE NULL
                                END AS ultima_tasa

                            FROM detalle_tipo_pago dtp
                            LEFT JOIN cuenta_bancaria cb ON dtp.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
                            LEFT JOIN divisas dcb ON cb.cod_divisa = dcb.cod_divisa

                            LEFT JOIN caja cj ON dtp.cod_caja = cj.cod_caja
                            LEFT JOIN divisas dcj ON cj.cod_divisas = dcj.cod_divisa

                            WHERE dtp.cod_tipo_pago = :cod_tipo_pago;";
                        $sentencia=$this->conex->prepare($tp); 
                        $sentencia->bindParam(':cod_tipo_pago', $pagos['cod_tipo_pago']);
                        $sentencia->execute();
                        $result=$sentencia->fetch(PDO::FETCH_ASSOC);
                        if(!empty($result['cod_cuenta_bancaria'])){
                            $monto= $result['cod_divisa'] !=1 ? $pagos['monto']/$result['ultima_tasa'] : $pagos['monto'];
                            $sql="UPDATE cuenta_bancaria SET saldo=saldo+:monto WHERE cod_cuenta_bancaria= :cod_cuenta_bancaria;";
                            $sen=$this->conex->prepare($sql);
                            $sen->bindParam(':monto', $monto);
                            $sen->bindParam(':cod_cuenta_bancaria', $result['cod_cuenta_bancaria']);
                            $r=$sen->execute();
                            if(!$r){
                                throw new Exception("Error al actualizar el saldo de la cuenta bancaria");
                            }
                        } else if(!empty($result['cod_caja'])){
                            $monto= $result['cod_divisa'] !=1 ? $pagos['monto']/$result['ultima_tasa'] : $pagos['monto'];
                            $sql="UPDATE caja SET saldo=saldo+:monto WHERE cod_caja = :cod_caja;";
                            $sen=$this->conex->prepare($sql);
                            $sen->bindParam(':monto', $monto);
                            $sen->bindParam(':cod_caja', $result['cod_caja']);
                            $r=$sen->execute();
                            if(!$r){
                                throw new Exception("Error al actualizar el saldo de la caja");
                            }
                        }else{
                            throw new Exception("Error al obtener la cuenta bancaria o caja");
                        }
                    }else{
                        throw new Exception("Error al registrar el detalle del vuelto");
                    }
                }
            }
            if($monto_venta > $this->monto_total){
                $estado="UPDATE ventas SET status= 2 WHERE cod_venta=:cod_venta";
                $strExec=$this->conex->prepare($estado);
                $strExec->bindParam(':cod_venta', $this->cod_venta);
                $strExec->execute();
                $r=$monto_venta-$this->monto_total;
            } else if($monto_venta <= $this->monto_total){
                $estado="UPDATE ventas SET status= 3 WHERE cod_venta=:cod_venta";
                $strExec=$this->conex->prepare($estado);
                $strExec->bindParam(':cod_venta', $this->cod_venta);
                $strExec->execute();
                $r=0;
            }
        }else{
            throw new Exception("Error al registrar el pago");
        }
        $this->conex->commit();
        return $r;
        }catch(PDOException $e){
            $this->conex->rollBack();
            error_log("Error en la consulta: " . $e->getMessage());
            echo '<script>console.log("Error en la consulta: ' . $e->getMessage() . '");</script>';
            return false;
        }finally{
            parent::desconectarBD();
        }
    }

    public function parcialp($pago){
        parent::conectarBD();
        foreach ($pago as $pagos){
            if(!empty($pagos['monto']) && $pagos['monto']>0){
                $registro="INSERT INTO detalle_pagos(cod_pago, cod_tipo_pago, monto) VALUES(:cod_pago, :cod_tipo_pago, :monto)";
                $sentencia=$this->conex->prepare($registro);
                $sentencia->bindParam(':cod_pago', $this->cod_pago);
                $sentencia->bindParam(':cod_tipo_pago', $pagos['cod_tipo_pago']);
                $sentencia->bindParam(':monto', $pagos['monto']);
                $resul=$sentencia->execute();
                if($resul){
                    $sql="UPDATE pagos SET monto_total=monto_total+:abono WHERE cod_pago=:cod_pago;";
                    $sen=$this->conex->prepare($sql);
                    $sen->bindParam(':abono', $this->monto_dpago);
                    $sen->bindParam(':cod_pago', $this->cod_pago);
                    $sen->execute();
                    if($this->monto_total>$this->monto_dpago){
                        $estado="UPDATE ventas SET status= 2 WHERE cod_venta=:cod_venta";
                        $strExec=$this->conex->prepare($estado);
                        $strExec->bindParam(':cod_venta', $this->cod_venta);
                        $strExec->execute();
                        $r=$this->monto_total-$this->monto_dpago;
                    } else if($this->monto_total <= $this->monto_dpago){
                        $estado="UPDATE ventas SET status= 3 WHERE cod_venta=:cod_venta";
                        $strExec=$this->conex->prepare($estado);
                        $strExec->bindParam(':cod_venta', $this->cod_venta);
                        $strExec->execute();
                        $r=0;
                    }
                    
                }
                
            }
        }
        parent::desconectarBD();
        return $r;
    }

}
