<?php

namespace Modelo;

use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use DateTime;

class ControlCaja extends Conexion
{
    use ValidadorTrait;


    private $fecha_apertura;
    private $fecha_cierre;
    private $monto_apertura;
    private $monto_cierre;
    private $status;

    private $cod_control;
    private $cod_caja;
    private $cod_divisas;
    private $observacion;

    private $data = [];
    private $errores = [];

    public function __construct()
    {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function setData(array $data)
    {
        $this->data = $data;

        foreach ($data as $key => $value) {
            switch ($key) {
                case 'cod_control':
                    if ($value !== '' && $value !== null) {
                        if (($r = $this->validarNumerico($value, 'cod_control', 1, 100)) === true) {
                            $this->cod_control = $value;
                        } else {
                            $this->errores['cod_control'] = $r;
                        }
                    }
                    break;
                case 'fecha_apertura':
                    if ($value !== '' && $value !== null) {
                        if (($r = $this->validardatetime($value, 'fecha_apertura')) === true) {
                            $this->fecha_apertura = $value;
                        } else {
                            $this->errores['fecha_apertura'] = $r;
                        }
                    }
                    break;
                case 'fecha_cierre':
                    if ($value !== '' && $value !== null) {
                        if (($r = $this->validardatetime($value, 'fecha_cierre')) === true) {
                            $this->fecha_cierre = $value;
                        } else {
                            $this->errores['fecha_cierre'] = $r;
                        }
                    }
                    break;
                case 'monto_apertura':
                    if (isset($value)) {
                        if (($r = $this->validarDecimal2($value, 'monto_apertura')) === true) {
                            $this->monto_apertura = $value;
                        } else {
                            $this->errores['monto_apertura'] = $r;
                        }
                    }
                    break;
                case 'monto_cierre':
                    if (($r = $this->validarDecimal2($value, 'monto_cierre')) === true) {
                        $this->monto_cierre = $value;
                    } else {
                        $this->errores['monto_cierre'] = $r;
                    }

                    break;
                case 'cod_caja':
                    if (($r = $this->validarNumerico($value, 'cod_caja', 1, 100)) === true) {
                        $this->cod_caja = $value;
                    } else {
                        $this->errores['cod_caja'] = $r;
                    }
                    break;
                case 'cod_divisas':
                    if (($r = $this->validarNumerico($value, 'cod_divisas', 1, 100)) === true) {
                        $this->cod_divisas = $value;
                    } else {
                        $this->errores['cod_divisas'] = $r;
                    }
                    break;
                case 'status':
                    if (($r = $this->validarNumerico($value, 'status', 1, 2)) === true) {
                        $this->status = $value;
                    } else {
                        $this->errores['status'] = $r;
                    }
                    break;
                case 'observacion':
                    if ($value !== '' && $value !== null) {
                        if (($r = $this->validarDescripcion($value, 'observacion', 1, 100)) === true) {
                            $this->observacion = $value;
                        } else {
                            $this->errores['observacion'] = $r;
                        }
                    }
                    break;
                default:
                    break;
            }
        }
    }

    public function getData()
    {
        return [
            'codigo_control' => $this->cod_control,
            'fecha_apertura' => $this->fecha_apertura,
            'fecha_cierre' => $this->fecha_cierre,
            'monto_apertura' => $this->monto_apertura,
            'monto_cierre' => $this->monto_cierre,
            'cod_caja' => $this->cod_caja,
            'cod_divisas' => $this->cod_divisas,
            'status' => $this->status,
            'observacion' => $this->observacion
        ];
    }

    public function check()
    {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    // CONSULTAR CAJAS ACTIVAS
    private function consultarCajasActivas()
    {
        $sql = "SELECT
        c.cod_caja,
        c.nombre,
        c.saldo,
        c.status AS caja_status,
        d.nombre AS divisa,
        d.cod_divisa,
        ctl.fecha_apertura,
        COALESCE(ctl.status, 0) AS status_control,
        ctl.cod_control,
        CASE 
            WHEN COUNT(dtp.cod_tipo_pago) > 0 THEN 1
            ELSE 0
        END AS tiene_tipo_pago
        FROM caja c
        JOIN divisas d ON c.cod_divisas = d.cod_divisa
        LEFT JOIN (
            SELECT cod_control, cod_caja, fecha_apertura, status, 
                ROW_NUMBER() OVER (PARTITION BY cod_caja ORDER BY fecha_apertura DESC) AS rn
            FROM control
        ) ctl ON c.cod_caja = ctl.cod_caja AND ctl.rn = 1
        LEFT JOIN detalle_tipo_pago dtp ON dtp.cod_caja = c.cod_caja
        WHERE c.status = 1
        GROUP BY 
        c.cod_caja, c.nombre, c.saldo, c.status, d.nombre, d.cod_divisa,
        ctl.fecha_apertura, ctl.status, ctl.cod_control;";

        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();

        return $resul ? $datos : [];
    }

    public function getConsultarActivas()
    {
        return $this->consultarCajasActivas();
    }

    // ABRIR CAJA
    private function abrirCaja($monto_apertura)
    {
        $monto_apertura = $monto_apertura ?? ($this->data['saldoa'] ?? null);
        $fechaApertura = new DateTime($this->fecha_apertura);
        $hoy = new DateTime();

        if ($fechaApertura->format('Y-m-d') !== $hoy->format('Y-m-d')) {
            throw new Exception("Solo puede registrar aperturas con la fecha de hoy");
        }

        // Validar si ya hay una caja abierta
        if ($this->existeCajaAbierta($this->cod_caja)) {
            throw new Exception("Ya existe una sesión abierta para esta caja");
        }

        $sql = "INSERT INTO control(fecha_apertura, monto_apertura, status, cod_caja) 
                VALUES(:fecha_apertura, :monto_apertura, :status, :cod_caja)";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(":fecha_apertura", $this->fecha_apertura);
        $stmt->bindParam(":monto_apertura", $monto_apertura);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":cod_caja", $this->cod_caja);
        $resul = $stmt->execute();
        parent::desconectarBD();

        return $resul ? 1 : 0;
    }

    public function getAbrirCaja($monto_apertura)
    {
        return $this->abrirCaja($monto_apertura);
    }

    // FUNCIÓN PARA VERIFICAR SI EXISTE UNA CAJA ABIERTA
    private function existeCajaAbierta($cod_caja)
    {
        $sql = "SELECT 1 FROM control 
                WHERE cod_caja = :cod_caja AND status = 1 AND fecha_cierre IS NULL 
                LIMIT 1";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod_caja', $cod_caja);
        $stmt->execute();
        $existe = $stmt->fetchColumn();
        parent::desconectarBD();
        return (bool)$existe;
    }

    //Movimientos por cada control
    private function obtenerMovControl($cod_control)
    {
        $sql = "CALL getMovimientosControl(:cod_control)";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':cod_control', $cod_control, PDO::PARAM_INT);

        $strExec->execute();
        $resul = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $resul;
    }

    public function getobtenerMovControl($cod_control)
    {
        return $this->obtenerMovControl($cod_control);
    }


    public function consultarControlHoy($cod_caja)
    {
        parent::conectarBD();
        $sql = "SELECT *
        FROM control
        WHERE cod_caja = :cod_caja
        AND status = 1
        ORDER BY fecha_apertura DESC
        LIMIT 1";
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod_caja', $cod_caja, PDO::PARAM_INT);
        $resultado = $stmt->execute();
        $mov = $stmt->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resultado) {
            return $mov;
        } else {
            return [];
        }
    }

    // CERRAR CAJA
    private function cerrarCaja()
    {
        if ($this->monto_cierre === null || !$this->fecha_cierre) {
            throw new Exception("Faltan datos para cerrar caja");
        }

        $username = $_SESSION['user'];
        $sql = "UPDATE control 
        SET observacion = :observacion, fecha_cierre = :fecha_cierre, monto_cierre = :monto_cierre, username = :username, status = 0 WHERE cod_control = :cod_control";

        try {
            parent::conectarBD();
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':observacion', $this->observacion, PDO::PARAM_STR);
            $stmt->bindParam(':fecha_cierre', $this->fecha_cierre);
            $stmt->bindParam(':monto_cierre', $this->monto_cierre,);
            $stmt->bindParam(':cod_control', $this->cod_control);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $resultado = $stmt->execute();
            return $resultado ? 1 : 0;
        } catch (Exception $e) {
            echo "<script>console.error('Error PDO: " . addslashes($e->getMessage()) . "');</script>";
            return 0;
        } finally {
            parent::desconectarBD();
        }
    }

    public function getCerrarCaja()
    {
        return $this->cerrarCaja();
    }

    // RESUMEN DE CAJA AL CERRAR
    private function resumenCerrar($cod_control)
    {
        parent::conectarBD();
        $sql = "CALL resumenMovimientosCaja(:cod_control)";
        $stmt = $this->conex->prepare($sql);
        //$stmt->bindParam(':cod_caja', $cod_caja, PDO::PARAM_INT);
        $stmt->bindParam(':cod_control', $cod_control, PDO::PARAM_INT);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $resultados;
    }

    public function getResumenCerrar($cod_control)
    {
        return $this->resumenCerrar($cod_control);
    }


    //NOTIFICACIONES DE CAJA
    private function controlAbierto($cod_caja){
    parent::conectarBD();
    $sql = "SELECT *
            FROM control
            WHERE cod_caja = :cod_caja
            AND status = 1
            AND fecha_cierre IS NULL
            ORDER BY fecha_apertura DESC
            LIMIT 1";
    $stmt = $this->conex->prepare($sql);
    $stmt->bindParam(':cod_caja', $cod_caja, PDO::PARAM_INT);
    $stmt->execute();
    $mov = $stmt->fetch(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    return $mov ?: null;
}

public function getControlAbierto($cod_caja)
{
    return $this->controlAbierto($cod_caja);
}


}
