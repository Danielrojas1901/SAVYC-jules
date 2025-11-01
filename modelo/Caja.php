<?php

namespace Modelo;

use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;

class Caja extends Conexion
{

    use ValidadorTrait;

    private $nombre;
    private $saldo;
    private $divisa;
    private $status;

    private $cod_caja;
    private $cod_divisa;

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
                case 'nombre':
                    $resultado = $this->validarTexto($value, 'nombre', 1, 50);
                    if ($resultado === true) {
                        $this->nombre = $value;
                    } else {
                        $this->errores['nombre'] = $resultado;
                    }
                    break;

                case 'cod_divisa':
                    $resultado = $this->validarNumerico($value, 'cod_divisa', 1, 50);
                    if ($resultado === true) {
                        $this->cod_divisa = $value;
                    } else {
                        $this->errores['cod_divisa'] = $resultado;
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
                case 'status':
                    $resultado = $this->validarNumerico($value, 'status', 1, 2);
                    if ($resultado === true) {
                        $this->status = $value;
                    } else {
                        $this->errores['status'] = $resultado;
                    }
                    break;
                case 'cod_caja':
                    $resultado = $this->validarNumerico($value, 'cod_caja', 1, 100);
                    if ($resultado === true) {
                        $this->cod_caja = $value;
                    } else {
                        $this->errores['cod_caja'] = $resultado;
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
            'nombre' => $this->nombre,
            'cod_divisa' => $this->cod_divisa,
            'saldo' => $this->saldo,
            'status' => $this->status,
            'cod_caja' => $this->cod_caja,
        ];
    }

    public function check()
    {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    /*==============================
    REGISTRAR CAJA
    ================================*/
    private function crearCaja()
    {
        $sql = "INSERT INTO caja(nombre, saldo, cod_divisas, status) VALUES(:nombre, :saldo, :divisa, :status)";
        //$this->status = 1;
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(":nombre", $this->nombre);
        $strExec->bindParam(":saldo", $this->saldo);
        $strExec->bindParam(":divisa", $this->cod_divisa);
        $strExec->bindParam(":status", $this->status);
        $resul = $strExec->execute();
        parent::desconectarBD();
        return $resul ? 1 : 0;
    }
    public function getcrearCaja()
    {
        return $this->crearCaja();
    }

    /*==============================
    MOSTRAR CAJAS  
    ================================*/
    public function consultarCaja()
    {
        $sql = "SELECT
        c.cod_caja,
        c.nombre,
        c.saldo,
        c.status AS caja_status,
        d.nombre AS divisa,
        d.cod_divisa,
        ctl.status AS status_control 
        FROM caja c
        JOIN divisas d ON c.cod_divisas = d.cod_divisa 
        LEFT JOIN (SELECT cod_caja, status, ROW_NUMBER() OVER 
        (PARTITION BY cod_caja ORDER BY fecha_apertura DESC) AS rn FROM control ) 
        ctl ON c.cod_caja = ctl.cod_caja AND ctl.rn = 1;";
        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();

        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    /*==============================
    BUSCAR                                  
    ================================*/
    private function buscar($dato)
    {
        $this->nombre = $dato;
        $registro = "SELECT * FROM caja WHERE nombre='" . $this->nombre . "'";
        $resultado = "";
        parent::conectarBD();
        $dato = $this->conex->prepare($registro);
        $resul = $dato->execute();
        $resultado = $dato->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function getbuscar($dato)
    {
        return $this->buscar($dato);
    }

    /*==============================
    EDITAR CAJA
===============================*/
    private function editar()
    {
        try {
            parent::conectarBD();
            // Validar que la caja tenga al menos un cierre
            $sql = "SELECT COUNT(*) as count
                FROM control 
                WHERE cod_caja = :cod_caja
                AND fecha_cierre IS NULL";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod_caja', $this->cod_caja);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                throw new Exception('No se puede editar la caja mientras esté abierta.');
            }

            // Proceder con la edición
            $sql = "UPDATE caja 
                    SET nombre = :nombre, saldo = :saldo, status = :status 
                    WHERE cod_caja = :cod_caja";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod_caja', $this->cod_caja);
            $stmt->bindParam(':nombre', $this->nombre);
            $stmt->bindParam(':saldo', $this->saldo);
            $stmt->bindParam(':status', $this->status);

            $success = $stmt->execute();
            if (!$success) {
                throw new Exception('Error al intentar editar la caja.');
            }
            return $success ;
        } catch (Exception $e) {
            return "Error al editar caja: " . $e->getMessage();
        } finally {
            parent::desconectarBD();
        }
    }

    public function geteditar()
    {
        return $this->editar();
    }


    /*==============================
        ELIMINAR CAJA
    ===============================*/
    private function eliminar($valor)
    {
        try {
            parent::conectarBD();

            // Verificar si existe la caja
            $sql = "SELECT * FROM caja WHERE cod_caja = :cod";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod', $valor);
            $stmt->execute();
            $caja = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$caja) {
                throw new Exception('La caja no existe.');
            }

            // Verificar si la caja está abierta
            $sql = "SELECT COUNT(*) as count
                FROM control 
                WHERE cod_caja = :cod_caja
                AND fecha_cierre IS NULL";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod_caja', $this->cod_caja);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                throw new Exception('No se puede eliminar la caja mientras esté abierta.');
            }
            
            // Verificar si la caja está activa
            if ($caja['status'] != 0) {
                throw new Exception('No se puede eliminar una caja activa. Debe inactivarla primero.');
            }

            // Verificar si tiene tipos de pago asociados
            $sql = "SELECT COUNT(*) as count FROM detalle_tipo_pago WHERE cod_caja = :cod";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod', $valor);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                throw new Exception('La caja tiene tipos de pago asociados.');
            }

            // Verificar si tiene saldo positivo
            $sql = "SELECT COUNT(*) as count FROM caja WHERE cod_caja = :cod AND saldo > 0";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod', $valor);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                throw new Exception('No se puede eliminar una caja con saldo positivo.');
            }

            // Eliminar la caja
            $sql_delete = "DELETE FROM caja WHERE cod_caja = :cod";
            $stmt_delete = $this->conex->prepare($sql_delete);
            $stmt_delete->bindParam(':cod', $valor);
            $resultado = $stmt_delete->execute();

            if (!$resultado) {
                throw new Exception('Error al intentar eliminar la caja.');
            }

            return 'success';
        } catch (Exception $e) {
            throw new Exception("Error al eliminar caja: " . $e->getMessage());
        } finally {
            parent::desconectarBD();
        }
    }

    public function geteliminar($valor)
    {
        return $this->eliminar($valor);
    }

    /*==============================
    HISTORIAL DE CAJA
    ===============================*/
    private function historialCaja($cod_caja)
    {
        try {
            parent::conectarBD();
            $sql = "SELECT 
                    c.cod_control,
                    c.cod_caja,
                    ca.nombre AS nombre_caja,
                    ca.cod_divisas,
                    d.nombre AS nombre_divisa,
                    DATE(c.fecha_apertura) AS fecha, 
                    TIME(c.fecha_apertura) AS hora_apertura, 
                    TIME(c.fecha_cierre) AS hora_cierre,
                    c.fecha_apertura,
                    c.fecha_cierre,
                    c.username,
                    c.monto_apertura AS saldo_inicial, 
                    c.monto_cierre AS saldo_final
                FROM control c
                JOIN caja ca ON c.cod_caja = ca.cod_caja
                JOIN divisas d ON ca.cod_divisas = d.cod_divisa
                WHERE c.cod_caja = :cod_caja
                ORDER BY c.fecha_apertura DESC;";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod_caja', $cod_caja);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $result;
        } catch (Exception $e) {
            throw new Exception("No se pudo obtener el historial de la caja.");
        } finally {
            parent::desconectarBD();
        }
    }

    public function getHistorialCaja($cod_caja)
    {
        return $this->historialCaja($cod_caja);
    }


    // APERTURA Y CAJA

    public function consultarControlHoy(int $cod_caja): array
    {
        try {
            parent::conectarBD();
            $sql = "
                SELECT fecha_apertura, fecha_cierre
                FROM control
                WHERE cod_caja = :cod_caja
                AND DATE(fecha_apertura) = CURDATE()
                LIMIT 1";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod_caja', $cod_caja, PDO::PARAM_INT);
            $stmt->execute();
            $fila = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            return [
                'fecha_apertura' => $fila['fecha_apertura'] ?? null,
                'fecha_cierre'   => $fila['fecha_cierre']   ?? null,
                'nombre'       => $fila['nombre']       ?? null,
            ];
        } catch (\Exception $e) {
            return ['fecha_apertura' => null, 'fecha_cierre' => null];
        } finally {
            parent::desconectarBD();
        }
    }

    public function CajasConControl()
    {

        try {
            parent::conectarBD();
            $sql = "SELECT c.cod_caja, c.nombre, c.saldo, c.status, d.nombre AS divisa, d.cod_divisa
            FROM caja c INNER JOIN control co ON co.cod_caja = c.cod_caja
            INNER JOIN divisas d ON c.cod_divisa = d.cod_divisa
            WHERE c.status = 1";
            $stmt = $this->conex->prepare($sql);
            $stmt->execute();
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $datos;
        } catch (\Exception $e) {
            return [];
        } finally {
            parent::desconectarBD();
        }
    }
}
