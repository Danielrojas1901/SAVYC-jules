<?php

namespace Modelo;

use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class CuentaBancaria extends Conexion
{

    use ValidadorTrait;

    private $numero_cuenta;
    private $saldo;
    private $divisa;
    private $status;
    private $tipo_cuenta;
    private $origin;

    private $cod_cuenta_bancaria;

    private $cod_tipo_cuenta;
    private $cod_banco;

    public function __construct()
    {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }
    private $errores = [];

    
    public function setData(array $data)
    {
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'numero_cuenta':
                    $resultado = $this->validarNumerico($value, 'numero_cuenta', 16, 20);
                    if ($resultado === true) {
                        $this->numero_cuenta = $value;
                    } else {
                        $this->errores['numero_cuenta'] = $resultado;
                    }
                    break;
                case 'origin':
                    $resultado = $this->validarNumerico($value, 'numero_cuenta1', 16, 20);
                    if ($resultado === true) {
                        $this->origin = $value;
                    } else {
                        $this->errores['origin'] = $resultado;
                    }
                    break;

                case 'cod_banco':
                    $resultado = $this->validarNumerico($value, 'cod_banco', 1, 50);
                    if ($resultado === true) {
                        $this->cod_banco = $value;
                    } else {
                        $this->errores['cod_banco'] = $resultado;
                    }
                    break;

                case 'cod_tipo_cuenta':
                    $resultado = $this->validarNumerico($value, 'cod_tipo_cuenta', 1, 50);
                    if ($resultado === true) {
                        $this->cod_tipo_cuenta = $value;
                    } else {
                        $this->errores['cod_tipo_cuenta'] = $resultado;
                    }
                    break;

                case 'saldo':
                    $resultado = $this->validarDecimal($value, 'saldo', 1, 100);
                    if ($resultado === true) {
                        $this->saldo = $value;
                    } else {
                        $this->errores['saldo'] = $resultado;
                    }
                    break;

                case 'divisa':
                    $resultado = $this->validarNumerico($value, 'divisa', 1, 10);
                    if ($resultado === true) {
                        $this->divisa = $value;
                    } else {
                        $this->errores['divisa'] = $resultado;
                    }
                    break;

                case 'status':
                    $this->status = $value;
                    break;

                case 'cod_cuenta_bancaria':
                    $this->cod_cuenta_bancaria = $value;
                    break;

                default:
                    $this->errores[$key] = "Campo no reconocido: $key";
            }
        }
    }

    public function getData($key = null)
    {
        $data = [
            'cod_cuenta_bancaria' => $this->cod_cuenta_bancaria ?? null,
            'numero_cuenta' => $this->numero_cuenta ?? null,
            'cod_banco' => $this->cod_banco ?? null,
            'cod_tipo_cuenta' => $this->cod_tipo_cuenta ?? null,
            'saldo' => $this->saldo ?? null,
            'divisa' => $this->divisa ?? null,
            'status' => $this->status ?? null,
            'origin' => $this->origin ?? null,
        ];

        if ($key !== null) {
            return $data[$key] ?? null;
        }

        return $data;
    }


    public function check()
    {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }


    /*==============================
REGISTRAR CUENTA BANCARIA
================================*/
    private function crearCuenta()
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            $buscar = $this->getbuscar($this->numero_cuenta);
            if ($buscar) {
                throw new Exception("La cuenta ya existe.");
            }


            $sql = "INSERT INTO cuenta_bancaria (numero_cuenta, saldo, cod_divisa, status, cod_tipo_cuenta, cod_banco) 
            VALUES(:numero_cuenta, :saldo, :cod_divisa, 1, :cod_tipo_cuenta, :cod_banco)";

            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(":numero_cuenta", $this->numero_cuenta);
            $strExec->bindParam(":saldo", $this->saldo);
            $strExec->bindParam(":cod_divisa", $this->divisa);
            $strExec->bindParam(":cod_tipo_cuenta", $this->cod_tipo_cuenta);
            $strExec->bindParam(":cod_banco", $this->cod_banco);
            $result = $strExec->execute();
            if (!$result) {
                throw new Exception("Error al insertar la cuenta bancaria.");
            }
            $this->conex->commit();
            return $result;
        } catch (Exception $e) {
            $this->conex->rollBack();
            error_log("Error de validación: " . $e->getMessage());
            throw $e;
        } finally {
            parent::desconectarBD();
        }
    }
    public function getcrearCuenta()
    {
        return $this->crearCuenta();
    }

    /*==============================
    MOSTRAR CUENTAS BANCARIAS
================================*/

    public function consultarCuenta()
    {
        $sql = "SELECT c.cod_cuenta_bancaria, c.numero_cuenta, c.saldo, c.status, c.cod_banco,
         b.nombre_banco, t.nombre AS tipo_cuenta, d.nombre AS divisa, d.cod_divisa, c.cod_tipo_cuenta 
         FROM cuenta_bancaria c INNER JOIN divisas d ON c.cod_divisa = d.cod_divisa INNER JOIN tipo_cuenta t 
        ON c.cod_tipo_cuenta = t.cod_tipo_cuenta INNER JOIN banco b ON c.cod_banco = b.cod_banco;";
        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        }
        return $r = 0;
    }

    public function consultarTipo()
    {
        $sql = "SELECT * FROM tipo_cuenta";
        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        }
        return $r = 0;
    }

    public function getbuscar($numero_cuenta)
    {
        $sql = "SELECT numero_cuenta FROM cuenta_bancaria WHERE numero_cuenta = :numero_cuenta";
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':numero_cuenta', $numero_cuenta);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado;
    }



    private function editar()
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            if ($this->numero_cuenta != $this->origin) {
                $buscar = $this->getbuscar($this->numero_cuenta);
                if ($buscar) {
                    throw new Exception("La cuenta bancaria ya se encuentra en otro registro.");
                }
            }

            if ($this->status == 0) {

                $str  = "SELECT cod_tipo_pago FROM detalle_tipo_pago WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria";
                $strExec = $this->conex->prepare($str);
                $strExec->bindParam(':cod_cuenta_bancaria', $this->cod_cuenta_bancaria);
                $res = $strExec->execute();
                $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
                if($resultado) {

                    $reg = "UPDATE detalle_tipo_pago SET status=0 WHERE cod_tipo_pago = :cod_tipo_pago";
                    $strExec = $this->conex->prepare($reg);
                    $strExec->bindParam(':cod_tipo_pago', $resultado['cod_tipo_pago']);
                    $res = $strExec->execute();

                    if (!$res) {
                        throw new Exception("No se pudo actualizar el estado de los tipos de pago asociados.");
                    }
                }
            } else if ($this->status == 1) {
                $str  = "SELECT cod_tipo_pago FROM detalle_tipo_pago WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria";
                $strExec = $this->conex->prepare($str);
                $strExec->bindParam(':cod_cuenta_bancaria', $this->cod_cuenta_bancaria);
                $res = $strExec->execute();
                $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
                $valor = $resultado;
               
                if ($valor) {
                    $reg = "UPDATE detalle_tipo_pago SET status = 1 WHERE cod_tipo_pago = :cod_tipo_pago";
                    $strExec = $this->conex->prepare($reg);
                    $strExec->bindParam(':cod_tipo_pago', $valor);
                    $res = $strExec->execute();

                    if (!$res) {
                        throw new Exception("No se pudo actualizar el estado de los tipos de pago asociados.");
                    }
                }
            }

            $editar = "UPDATE cuenta_bancaria 
                     SET numero_cuenta = :numero_cuenta, 
                         saldo = :saldo, 
                         cod_divisa = :divisa, 
                         status = :status, 
                         cod_tipo_cuenta = :cod_tipo_cuenta, 
                         cod_banco = :cod_banco 
                     WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria";
            parent::conectarBD();
            $strExec = $this->conex->prepare($editar);
            $strExec->bindParam(':cod_cuenta_bancaria', $this->cod_cuenta_bancaria);
            $strExec->bindParam(':numero_cuenta', $this->numero_cuenta);
            $strExec->bindParam(':saldo', $this->saldo);
            $strExec->bindParam(':divisa', $this->divisa);
            $strExec->bindParam(':status', $this->status);
            $strExec->bindParam(':cod_tipo_cuenta', $this->cod_tipo_cuenta);
            $strExec->bindParam(':cod_banco', $this->cod_banco);
            $result = $strExec->execute();
            if (!$result) {
                throw new Exception("Error al editar la cuenta bancaria.");
            }
            $this->conex->commit();
            return $result;
        } catch (PDOException $e) {
            $this->conex->rollBack();
            error_log("Error al editar la cuenta bancaria: " . $e->getMessage());
            throw $e;
        } finally {
            parent::desconectarBD();
        }
    }


    public function geteditar()
    {
        return $this->editar();
    }



    private function eliminar($valor)
    {
       
        $sql = "SELECT * FROM cuenta_bancaria WHERE cod_cuenta_bancaria = :cod";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod', $valor);
        $stmt->execute();
        $caja = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$caja) {
            parent::desconectarBD();
            return 'error_query';
        }

      
        if ($caja['status'] != 0) {
            parent::desconectarBD();
            return 'error_status';
        }

        
        $sql = "SELECT COUNT(*) as count FROM detalle_tipo_pago WHERE cod_cuenta_bancaria = :cod";
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod', $valor);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            parent::desconectarBD();
            return 'error_tipo_pago';
        }
     
        $sql = "SELECT COUNT(*) as count FROM cuenta_bancaria WHERE cod_cuenta_bancaria = :cod AND saldo > 0";
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod', $valor);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['count'] > 0) {
            parent::desconectarBD();
            return 'error_saldo';
        }


        
        $sql_delete = "DELETE FROM cuenta_bancaria WHERE cod_cuenta_bancaria = :cod";
        $stmt_delete = $this->conex->prepare($sql_delete);
        $stmt_delete->bindParam(':cod', $valor);
        $resultado = $stmt_delete->execute();
        parent::desconectarBD();

        return $resultado ? 'success' : 'error_delete';
    }

    public function geteliminar($valor)
    {
        return $this->eliminar($valor);
    }

    public function obtenerMovimientosCuentaBancaria($cod_cuenta_bancaria, $fecha_inicio = null, $fecha_fin = null)
    {
        try {
            parent::conectarBD();

            $sql = "
                SELECT 
    pr.fecha AS fecha_movimiento,
    v.cod_venta AS cod_transaccion,
    'venta' AS modulo,
    dpr.monto AS monto,
    'entrada' AS tipo,
    cb.numero_cuenta,
    cb.cod_cuenta_bancaria, 
    cb.saldo, 
    cb.status, 
    cb.cod_banco,
    b.nombre_banco, 
    t.nombre AS tipo_cuenta, 
    d.nombre AS divisa, 
    d.cod_divisa, 
    cb.cod_tipo_cuenta 
FROM 
    detalle_pago_recibido dpr
JOIN 
    detalle_tipo_pago dtp ON dpr.cod_tipo_pago = dtp.cod_tipo_pago
JOIN 
    cuenta_bancaria cb ON dtp.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
JOIN 
    pago_recibido pr ON dpr.cod_pago = pr.cod_pago
JOIN 
    ventas v ON pr.cod_venta = v.cod_venta
JOIN 
    divisas d ON cb.cod_divisa = d.cod_divisa 
JOIN 
    tipo_cuenta t ON cb.cod_tipo_cuenta = t.cod_tipo_cuenta 
JOIN 
    banco b ON cb.cod_banco = b.cod_banco 
WHERE 
    cb.cod_cuenta_bancaria = :cod_cuenta_bancaria

UNION ALL

SELECT 
    pe.fecha AS fecha_movimiento,
    c.cod_compra AS cod_transaccion,
    'compra' AS modulo,
    -dpe.monto AS monto,
    'salida' AS tipo,
    cb.numero_cuenta,
    cb.cod_cuenta_bancaria, 
    cb.saldo, 
    cb.status, 
    cb.cod_banco,
    b.nombre_banco, 
    t.nombre AS tipo_cuenta, 
    d.nombre AS divisa, 
    d.cod_divisa, 
    cb.cod_tipo_cuenta 
FROM 
    detalle_pago_emitido dpe
JOIN 
    detalle_tipo_pago dtp ON dpe.cod_tipo_pagoe = dtp.cod_tipo_pago
JOIN 
    cuenta_bancaria cb ON dtp.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
JOIN 
    pago_emitido pe ON dpe.cod_pago_emitido = pe.cod_pago_emitido
JOIN 
    compras c ON pe.cod_compra = c.cod_compra
JOIN 
    divisas d ON cb.cod_divisa = d.cod_divisa 
JOIN 
    tipo_cuenta t ON cb.cod_tipo_cuenta = t.cod_tipo_cuenta 
JOIN 
    banco b ON cb.cod_banco = b.cod_banco 
WHERE 
    cb.cod_cuenta_bancaria = :cod_cuenta_bancaria

UNION ALL

SELECT 
    pe.fecha AS fecha_movimiento,
    g.cod_gasto AS cod_transaccion,
    'gasto' AS modulo,
    -dpe.monto AS monto,
    'salida' AS tipo,
    cb.numero_cuenta,
    cb.cod_cuenta_bancaria, 
    cb.saldo, 
    cb.status, 
    cb.cod_banco,
    b.nombre_banco, 
    t.nombre AS tipo_cuenta, 
    d.nombre AS divisa, 
    d.cod_divisa, 
    cb.cod_tipo_cuenta 
FROM 
    detalle_pago_emitido dpe
JOIN 
    detalle_tipo_pago dtp ON dpe.cod_tipo_pagoe = dtp.cod_tipo_pago
JOIN 
    cuenta_bancaria cb ON dtp.cod_cuenta_bancaria = cb.cod_cuenta_bancaria
JOIN 
    pago_emitido pe ON dpe.cod_pago_emitido = pe.cod_pago_emitido
JOIN 
    gasto g ON pe.cod_gasto = g.cod_gasto
JOIN 
    divisas d ON cb.cod_divisa = d.cod_divisa 
JOIN 
    tipo_cuenta t ON cb.cod_tipo_cuenta = t.cod_tipo_cuenta 
JOIN 
    banco b ON cb.cod_banco = b.cod_banco 
WHERE 
    cb.cod_cuenta_bancaria = :cod_cuenta_bancaria

ORDER BY 
    fecha_movimiento;";

           
            $params = [':cod_cuenta_bancaria' => $cod_cuenta_bancaria];

            if ($fecha_inicio) {
                $params[':fecha_inicio'] = $fecha_inicio;
            }

            if ($fecha_fin) {
                $params[':fecha_fin'] = $fecha_fin;
            }

            $stmt = $this->conex->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error al obtener movimientos de cuenta bancaria: " . $e->getMessage());
            return [];
        } finally {
            parent::desconectarBD();
        }
    }
}
