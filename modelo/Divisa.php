<?php

namespace Modelo;

use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class Divisa extends Conexion
{
    use ValidadorTrait; // Usar el trait para validaciones
    private $errores = [];
    private $nombre;
    private $simbolo;
    private $status;
    private $tasa;
    private $fecha;

    public function __construct()
    {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function setnombre($valor)
    {
        $res = $this->validarTexto($valor, 'nombre', 2, 50);
        if ($res === true) {
            $this->nombre = $valor;
        } else {
            $this->errores['nombre'] = $res;
        }
    }
    public function setsimbolo($valor)
    {
        $res = $this->validarAlfanumerico($valor, 'simbolo', 2, 10);
        if ($res === true) {
            $this->simbolo = $valor;
        } else {
            $this->errores[] = $res;
        }
    }
    public function setstatus($valor)
    {
        $res = $this->validarNumerico($valor, 'status', 1, 1);
        if ($res === true) {
            $this->status = $valor;
        } else {
            $this->errores['status'] = $res;
        }
    }
    public function set_tasa($valor)
    {
        $res = $this->validarNumerico($valor, 'tasa', 0, 1000000);
        if ($res === true) {
            $this->tasa = $valor;
        } else {
            $this->errores['tasa'] = $res;
        }
    }

    public function check()
    {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    // Si quieres acceder a los errores individualmente
    public function getErrores()
    {
        return $this->errores;
    }


    public function setfecha($valor)
    {
        $this->fecha = $valor;
    }

    public function getnombre()
    {
        return $this->nombre;
    }
    public function getsimbolo()
    {
        return $this->simbolo;
    }
    public function getStatus()
    {
        return $this->status;
    }
    public function get_tasa()
    {
        return $this->tasa;
    }
    public function getfecha()
    {
        return $this->fecha;
    }

    public function incluir()
    {
        $registro = "INSERT INTO divisas(nombre, abreviatura, status) VALUES(:nombre, :abreviatura, 1)";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $strExec->bindParam(':nombre', $this->nombre);
        $strExec->bindParam(':abreviatura', $this->simbolo);
        $resul = $strExec->execute();
        if ($resul) {
            $ultimo_cod = $this->conex->lastInsertId();
            $sqlCambio = "INSERT INTO cambio_divisa (cod_divisa, tasa, fecha) VALUES (:cod_divisa, :tasa, :fecha)";
            $strExec = $this->conex->prepare($sqlCambio);
            $strExec->bindParam(':cod_divisa', $ultimo_cod);
            $strExec->bindParam(':tasa', $this->tasa);
            $strExec->bindParam(':fecha', $this->fecha);
            $strExec->execute();
            $res = 1;
        } else {
            $res = 0;
        }
        parent::desconectarBD();
        return $res;
    }


    public function consultarDivisas()
    {
        parent::conectarBD();
        $sql = "SELECT d.*, 
            (SELECT cd.tasa FROM cambio_divisa cd WHERE cd.cod_divisa = d.cod_divisa ORDER BY cd.fecha DESC, cd.cod_cambio DESC LIMIT 1) AS tasa,
            (SELECT cd.fecha FROM cambio_divisa cd WHERE cd.cod_divisa = d.cod_divisa ORDER BY cd.fecha DESC, cd.cod_cambio DESC LIMIT 1) AS fecha
            FROM divisas d";
        $stmt = $this->conex->prepare($sql);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $datos;
    }

    public function consultar()
    {
        $registro = "SELECT d.cod_divisa, d.nombre, d.abreviatura, d.status AS divisa_status,c.cod_cambio, c.tasa, c.fecha
        FROM divisas AS d
        JOIN cambio_divisa AS c 
            ON d.cod_divisa = c.cod_divisa
        JOIN ( SELECT cod_divisa, MAX(fecha) AS ultima_fecha
            FROM cambio_divisa
            GROUP BY cod_divisa ) AS ultimos_cambios
            ON c.cod_divisa = ultimos_cambios.cod_divisa 
            AND c.fecha = ultimos_cambios.ultima_fecha
        ORDER BY d.cod_divisa;";
        parent::conectarBD();
        $consulta = $this->conex->prepare($registro);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return $res = 0;
        }
    }

    public function buscar($valor)
    {
        $this->nombre = $valor;
        $registro = "select * from divisas where nombre='" . $this->nombre . "'";
        $resutado = "";
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

    public function editar($valor)
    {
        $registro = "UPDATE divisas SET nombre=:nombre, abreviatura=:abreviatura, status=:status WHERE cod_divisa=$valor";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        #instanciar metodo bindparam
        $strExec->bindParam(':nombre', $this->nombre);
        $strExec->bindParam(':abreviatura', $this->simbolo);
        $strExec->bindParam(':status', $this->status);
        $resul = $strExec->execute();
        parent::desconectarBD();
        if ($resul) {
            $r = 1;
        } else {
            $r = 0;
        }
        return $r;
    }


    public function eliminar($valor)
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            $sql = "SELECT COUNT(*) as count FROM caja WHERE cod_divisas = :cod_divisa";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod_divisa', $valor);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result['count'] > 0) {
                throw new Exception("No se puede eliminar la divisa porque está asociada a una caja.");
            }

            $sqlb = "SELECT COUNT(*) as count FROM cuenta_bancaria WHERE cod_divisa = :cod_divisa";
            $stmtb = $this->conex->prepare($sql);
            $stmtb->bindParam(':cod_divisa', $valor);
            $stmtb->execute();
            $resultb = $stmtb->fetch(PDO::FETCH_ASSOC);
            if ($resultb['count'] > 0) {
                throw new Exception("No se puede eliminar la divisa porque está asociada a una cuenta bancaria.");
            }

            $str = "SELECT status FROM divisas WHERE cod_divisa = :cod_divisa";
            $stmt = $this->conex->prepare($str);
            $stmt->bindParam(':cod_divisa', $valor);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($resultado['status'] == 1) {
                throw new Exception("No se puede eliminar la divisa porque está activa.");
            }

            if ($result['count'] == 0) {
                $fisico = "DELETE FROM divisas WHERE cod_divisa = :cod_divisa";
                $strExec = $this->conex->prepare($fisico);
                $strExec->bindParam(':cod_divisa', $valor);
                $strExec->execute();
                $r = 1;
                $this->conex->commit();
                return $r;
            }
        } catch (PDOException $e) {
            $this->conex->rollBack();
            // violacion de clave foranea
            if ($e->getCode() == '23000') {
                $r = 0;
            } else {
                throw $e; // volver a lanzar si es un error diferente
            }
        } finally {
            parent::desconectarBD();
        }
    }

    public function tasa($valor)
    {
        foreach ($valor as $divisa) {
            $sql = "INSERT INTO cambio_divisa (cod_divisa, tasa, fecha) VALUES (:cod_divisa, :tasa, :fecha)";
            parent::conectarBD();
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':tasa', $divisa['tasa']);
            $strExec->bindParam(':fecha', $divisa['fecha']);
            $strExec->bindParam(':cod_divisa', $divisa['cod_divisa']);
            $resul = $strExec->execute();
            parent::desconectarBD();
            if (!$resul) {
                return false;
            }
        }
        return true;
    }

    public function historial()
    {
        $registro = "SELECT * FROM cambio_divisa ORDER BY fecha DESC;";
        parent::conectarBD();
        $consulta = $this->conex->prepare($registro);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return $res = 0;
        }
    }
}
