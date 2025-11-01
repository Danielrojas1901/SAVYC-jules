<?php

namespace Modelo;

use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class CategoriaGasto extends Conexion
{
    use ValidadorTrait;
    private $errores = [];
    private $datos = [];
    
    private $cod_frecuencia;
    private $dias;
    private $cod_naturaleza;
    private $frecuencia;
    private $cod_condicion;
    private $cod_cat_gasto;
    private $cod_tipo_gasto;
    private $nombre;
    private $origin;
    private $fecha;
    private $status_cat_gasto;

    public function __construct()
    {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function check()
    {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }


    public function getErrores()
    {
        return $this->errores;
    }


    public function setDatos(array $datos)
    {
        foreach ($datos as $key => $value) {
            switch ($key) {

                case 'status_cat_gasto':
                    if (is_numeric($value)) {
                        $this->status_cat_gasto = $value;
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;

                case 'frecuenciaC':
                    if (is_numeric($value)) {
                        $this->cod_frecuencia = $value;
                    } else if ($value == null) {
                        $this->cod_frecuencia = null;
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;
                case 'naturaleza':
                    if (is_numeric($value)) {
                        $this->cod_naturaleza = $value;
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;
                case 'cod_condicion':
                    if (is_numeric($value)) {
                        $this->cod_condicion = $value;
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;
                case 'cod_cat_gasto':
                    if (is_numeric($value)) {
                        $this->cod_cat_gasto = $value;
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;
                case 'tipogasto':
                    if (is_numeric($value)) {
                        $this->cod_tipo_gasto = $value;
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;
                case 'dias':
                    $res = $this->validarNumerico($value, $key, 1, 1);
                    if ($res == true) {
                        if ($value > 0 && $value <= 365) {
                            $this->dias = $value;
                        } else {
                            $this->errores[] = "El campo $key debe estar entre 1 y 365.";
                        }
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;
                case 'nombre':

                    $res = $this->validarDescripcion($value, $key, 2, 50);
                    if ($res === true) {
                        $this->nombre = $value;
                    } else {
                        $this->errores[] = $res;
                    }

                    break;
                case 'origin':
                    $res = $this->validarDescripcion($value, $key, 2, 50);
                    if ($res === true) {
                        $this->origin = $value;
                    } else {
                        $this->errores[] = $res;
                    }

                    break;
                case 'frecuencia':
                    $res = $this->validarDescripcion($value, $key, 2, 50);
                    if ($res === true) {
                        $this->frecuencia = $value;
                    } else {
                        $this->errores[] = $res;
                    }
                    break;
                case 'fecha':
                    $res = $this->validarFecha($value, $key);
                    if ($res === true) {
                        $this->fecha = $value;
                    } else if (!empty($value)) {
                        $this->fecha = $value;
                    } else {
                        $this->errores[] = $res;
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


    #SECCIÓN DE AJUSTES DE GASTOS
    private function registrarF()
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            if (!empty($this->frecuencia) && !empty($this->dias)) {
                $consulta = "SELECT * FROM frecuencia_gasto WHERE nombre = :nombre OR dias = :dias";
                $strExec = $this->conex->prepare($consulta);
                $strExec->bindParam(':nombre', $this->frecuencia);
                $strExec->bindParam(':dias', $this->dias);
                $resul = $strExec->execute();
                $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
                if ($datos != null) {
                    throw new Exception("La frecuencia ya existe.");
                } else {
                    $registro = "INSERT INTO frecuencia_gasto(nombre, dias) VALUES(:nombre, :dias)";
                    $strExec = $this->conex->prepare($registro);
                    $strExec->bindParam(':nombre', $this->frecuencia);
                    $strExec->bindParam(':dias', $this->dias);
                    $resul = $strExec->execute();
                }

                if ($resul == 1) {
                    $r = 1;
                } else {
                    throw new Exception("Error al registrar la frecuencia de gasto.");
                }
            } else {
                throw new Exception("Error al registrar la frecuencia datos vacios.");
            }
            $this->conex->commit();
            return $r;
        } catch (Exception $e) {
            $this->conex->rollBack();
            $this->errores[] = $e->getMessage();
            return false;
        }finally{
            parent::desconectarBD();
        }
    }

    public function publicregistrarf()
    {
        return $this->registrarF();
    }

    private function consultarF()
    {
        $registro = "SELECT * FROM frecuencia_gasto";
        parent::conectarBD();
        $consulta = $this->conex->prepare($registro);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function consultarFrecuencia()
    {
        return $this->consultarF();
    }

    private function consultarT()
    {
        $sql = "SELECT * FROM tipo_gasto";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function consultarTipo()
    {
        return $this->consultarT();
    }

    private function registrarC()
    {

        if (!empty($this->cod_tipo_gasto) && !empty($this->nombre) && !empty($this->fecha)) {
            if ($this->buscarCategoria() == null) {
                if ($this->cod_naturaleza == 1) {
                    if ($this->cod_frecuencia == null) {
                        return 'error_frecuencia';
                    }
                }

                $registro = "INSERT INTO categoria_gasto(cod_tipo_gasto, cod_frecuencia, cod_naturaleza, nombre, fecha, status_cat_gasto) VALUES(:cod_tipo_gasto, :cod_frecuencia,:cod_naturaleza, :nombre, :fecha, 1)";
                parent::conectarBD();
                $strExec = $this->conex->prepare($registro);
                $strExec->bindParam(':cod_frecuencia', $this->cod_frecuencia);
                $strExec->bindParam(':cod_tipo_gasto', $this->cod_tipo_gasto);
                $strExec->bindParam(':nombre', $this->nombre);
                $strExec->bindParam(':cod_naturaleza', $this->cod_naturaleza);
                $strExec->bindParam(':fecha', $this->fecha);
                $resul = $strExec->execute();
                parent::desconectarBD();
                if ($resul == 1) {
                    return $r = 1;
                } else {
                    return $r = 0;
                }
            } else {
                return $r = 2;
            }
        }
    }
    public function publicregistrarc()
    {
        return $this->registrarC();
    }

    private function consultarC()
    {
        $sql = "SELECT c.cod_cat_gasto, c.cod_tipo_gasto, c.fecha, c.status_cat_gasto, c.cod_frecuencia, c.nombre AS categoria, f.cod_frecuencia, f.nombre, t.cod_tipo_gasto, t.nombre AS nombret, n.cod_naturaleza, n.nombre_naturaleza,
        f.nombre AS nombref FROM categoria_gasto c
        LEFT JOIN tipo_gasto t ON c.cod_tipo_gasto = t.cod_tipo_gasto
        LEFT JOIN naturaleza_gasto n ON c.cod_naturaleza = n.cod_naturaleza
        LEFT JOIN frecuencia_gasto f ON c.cod_frecuencia = f.cod_frecuencia";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }

    public function consultarCategoria()
    {
        return $this->consultarC();
    }

    private function consultarCondicion()
    {
        $sql = "SELECT * FROM condicion_pagoe";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function consultarCondi()
    {
        return $this->consultarCondicion();
    }

    private function consulN()
    {
        $sql = "SELECT cod_naturaleza, nombre_naturaleza FROM naturaleza_gasto";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function consulNaturaleza()
    {
        return $this->consulN();
    }

    private function buscarTporC()
    {
        $sql = "SELECT c.cod_cat_gasto, c.cod_tipo_gasto, c.nombre, t.cod_tipo_gasto, t.nombre AS nombret
                FROM categoria_gasto c
                JOIN tipo_gasto t ON c.cod_tipo_gasto = t.cod_tipo_gasto
                WHERE c.cod_cat_gasto = :cod_cat_gasto";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':cod_cat_gasto', $this->cod_cat_gasto);
        $resul = $strExec->execute();
        $datos = $strExec->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }

    public function buscarTporCategoria()
    {
        return $this->buscarTporC();
    }

    private function mostrarporN()
    {
        $sql = "SELECT c.cod_cat_gasto, c.cod_tipo_gasto, c.nombre, n.nombre_naturaleza AS nombrenatu
                FROM categoria_gasto c
                JOIN naturaleza_gasto n ON c.cod_naturaleza = n.cod_naturaleza
                WHERE c.cod_cat_gasto = :cod_cat_gasto";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':cod_cat_gasto', $this->cod_cat_gasto);
        $resul = $strExec->execute();
        $datos = $strExec->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function mostrarFVporN()
    {
        return $this->mostrarporN();
    }

    private function bC()
    {
        $sql = "SELECT nombre FROM categoria_gasto WHERE nombre = :nombre";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':nombre', $this->nombre);
        $resul = $strExec->execute();
        $datos = $strExec->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }


    public function buscarCategoria()
    {
        return $this->bc();
    }

    private function bF()
    {
        $sql = "SELECT nombre FROM frecuencia_gasto WHERE nombre = :nombre";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':nombre', $this->frecuencia);
        $resul = $strExec->execute();
        $datos = $strExec->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function buscarFrecuencia()
    {
        return $this->bF();
    }

    private function EditC()
    {
        try {
            parent::conectarBD();
            if ($this->status_cat_gasto !== null && $this->nombre === $this->origin) {

                $registro = "UPDATE categoria_gasto SET status_cat_gasto = :status_cat_gasto WHERE cod_cat_gasto = :cod_cat_gasto";

                $strExec = $this->conex->prepare($registro);
                $strExec->bindParam(':cod_cat_gasto', $this->cod_cat_gasto);
                $strExec->bindParam(':status_cat_gasto', $this->status_cat_gasto);
                $res = $strExec->execute();
                parent::desconectarBD();
                if ($res) {
                    return $res = 1;
                } else {

                    return $res = 'error_query';
                }
            }

            $comparar = "SELECT nombre FROM categoria_gasto WHERE nombre = :nombre";

            $strExec = $this->conex->prepare($comparar);
            $strExec->bindParam(':nombre', $this->nombre);
            $res = $strExec->execute();
            $datos = $strExec->fetch(PDO::FETCH_ASSOC);

            if ($datos != null) {
                if ($datos['nombre'] == $this->nombre && $this->nombre != $this->origin) {
                    parent::desconectarBD();
                    return 'error_associated';
                }
            } else {
                $registro = "UPDATE categoria_gasto SET status_cat_gasto = :status_cat_gasto, nombre = :nombre WHERE cod_cat_gasto = :cod_cat_gasto";
                $strExec = $this->conex->prepare($registro);
                $strExec->bindParam(':cod_cat_gasto', $this->cod_cat_gasto);
                $strExec->bindParam(':status_cat_gasto', $this->status_cat_gasto);
                $strExec->bindParam(':nombre', $this->nombre);
                $res = $strExec->execute();
                parent::desconectarBD();
                if ($res) {
                    return 1;
                } else {
                    return 'error_query';
                }
            }
        } catch (Exception $e) {
            error_log($e->getMessage());

            parent::desconectarBD();
            $errores[] = throw new Exception("Error en la transacción: " . $e->getMessage());
            return false;
        }
    }

    public function editarC()
    {
        return $this->EditC();
    }

    private function eliminarC()
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();

            $gasto = "SELECT COUNT(*) AS n_gasto FROM gasto WHERE cod_cat_gasto = :cod_cat_gasto";
            $strExec = $this->conex->prepare($gasto);
            $strExec->bindParam(':cod_cat_gasto', $this->cod_cat_gasto, PDO::PARAM_INT);
            $strExec->execute();
            $resultado = $strExec->fetch(PDO::FETCH_ASSOC);

            if ($resultado['n_gasto'] > 0) {
                $this->conex->rollBack();
                parent::desconectarBD();
                return "error_associated";
            }
            $status = "SELECT status_cat_gasto FROM categoria_gasto  WHERE cod_cat_gasto = :cod_cat_gasto";
            $strExec = $this->conex->prepare($status);
            $strExec->bindParam(':cod_cat_gasto', $this->cod_cat_gasto, PDO::PARAM_INT);
            $re = $strExec->execute();
            $estado = $strExec->fetch(PDO::FETCH_ASSOC);
            if ($estado['status_cat_gasto'] == 1) {
                $this->conex->rollBack();
                parent::desconectarBD();
                return "error_status";
            }
            $fisico = "DELETE FROM categoria_gasto  WHERE cod_cat_gasto = :cod_cat_gasto";
            $strExec = $this->conex->prepare($fisico);
            $strExec->bindParam(':cod_cat_gasto', $this->cod_cat_gasto, PDO::PARAM_INT);
            $re = $strExec->execute();

            if ($re) {
                $this->conex->commit();
                parent::desconectarBD();
                return "success";
            } else {
                $this->conex->rollBack();
                parent::desconectarBD();
                return "error_delete";
            }
        } catch (Exception $e) {
            $this->conex->rollBack();
            parent::desconectarBD();
            return "error_query: " . $e->getMessage();
        }
    }

    public function eliminarCat()
    {
        return $this->eliminarC();
    }
}
