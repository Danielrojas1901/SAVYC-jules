<?php

namespace Modelo;

use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;


class Banco extends Conexion
{
    use ValidadorTrait;
    private $nombre_banco;
    private $cod_banco;
    private $origin;
    private $errores = [];
    private $datos = [];

    public function __construct()
    {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function setDatos(array $datos)
    {
        foreach ($datos as $key => $value) {
            switch ($key) {
                case 'nombre':
                    $res = $this->validarDescripcion($value, $key, 2, 50);
                    if ($res === true) {
                        $this->nombre_banco = $value;
                    } else {
                        $this->errores[] = "El campo $value debe ser solo letras y espacios y debe tener entre 2 y 50 caracteres.";
                    }
                    break;
                case 'origin':
                    $res = $this->validarDescripcion($value, $key, 2, 50);
                    if ($res === true) {
                        $this->origin = $value;
                    } else {
                        $this->errores[] = "El campo $value debe ser solo letras y espacios y debe tener entre 2 y 50 caracteres.";
                    }
                    break;
                case 'cod_banco':
                    if (is_numeric($value)) {
                        $this->cod_banco = $value;
                    } else {
                        $this->errores[] = "El campo $value debe ser numerico.";
                    }
                    break;
            }
            $this->datos[$key] = $value;
        }
    }
    public function getDatos()
    {
        return $this->datos;
    }

 
    public function check()
    {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }


    // REGISTRAR
    private function registrar()
    {
        $dup = $this->buscarPorNombre();
        if($dup){
            return 'error_nombre';
        }
        $sql = "INSERT INTO banco (nombre_banco) VALUES (:nombre_banco)";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':nombre_banco', $this->nombre_banco);
        $resul = $stmt->execute() ? 1 : 0;
        parent::desconectarBD();
        return $resul;
    }

    public function getRegistrar()
    {
        return $this->registrar();
    }

    // CONSULTAR TODOS
    public function consultar()
    {
        $sql = "SELECT * FROM banco";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        if ($stmt->execute()) {
            $resul = $stmt->fetchAll(PDO::FETCH_ASSOC);
            parent::desconectarBD();
            return $resul;
        }
        parent::desconectarBD();
        return 0;
    }

    // ACTUALIZAR
    private function actualizar()
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            $resp = "SELECT * FROM banco WHERE nombre_banco = :nombre_banco";
            $str = $this->conex->prepare($resp);
            $str->bindParam(':nombre_banco', $this->nombre_banco);
            $str->execute();
            $resultado = $str->fetch(PDO::FETCH_ASSOC);
     
            if($resultado == true || $this->nombre_banco == $this->origin) {
                return 'error_nombre';
            }
            $sql = "UPDATE banco SET nombre_banco = :nombre_banco WHERE cod_banco = :cod_banco";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':nombre_banco', $this->nombre_banco);
            $stmt->bindParam(':cod_banco', $this->cod_banco, PDO::PARAM_INT);
            $resultado = $stmt->execute() ? 1 : 0;
            $this->conex->commit();
            return $resultado;
        } catch (Exception $e) {
            $this->conex->rollBack();
            return "error_query: " . $e->getMessage();
        } finally {
            parent::desconectarBD();
        }
    }

    public function getactualizar()
    {
        return $this->actualizar();
    }

    // ELIMINAR 
    private function eliminar()
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();


       
            $sql = "SELECT COUNT(*) as count FROM cuenta_bancaria WHERE cod_banco = :cod";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod', $this->cod_banco);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                return 'error_cuenta';
            }

     
            $sql = "DELETE FROM banco WHERE cod_banco = :cod_banco";
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod_banco', $this->cod_banco, PDO::PARAM_INT);
            $resultado = $stmt->execute();
            $this->conex->commit();
            return $resultado;
        } catch (Exception $e) {
            $this->conex->rollBack();
            parent::desconectarBD();
            return "error_query: " . $e->getMessage();
        } finally {
            parent::desconectarBD();
        }
    }


    public function getEliminar()
    {
        return $this->eliminar();
    }

    public function buscarPorNombre()
    {
        $sql = "SELECT * FROM banco WHERE nombre_banco = :nombre_banco";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':nombre_banco', $this->nombre_banco);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $resultado;
    }
}
