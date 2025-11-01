<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;

class Backup extends Conexion {

    use ValidadorTrait;
    private $nombreArchivo;
    private $ruta;
    private $tamanio;
    private $descripcion;


    private $frecuencia;
    private $dia;
    private $hora;
    private $retencion;
    private $habilitado;
    private $modo;
    private $tipo;

    private $errores = [];

    public function __construct() {
        global $_ENV;
        parent::__construct($_ENV['_SEC_DB_HOST_'], $_ENV['_SEC_DB_NAME_'], $_ENV['_SEC_DB_USER_'], $_ENV['_SEC_DB_PASS_']);
    }


    /* ==================
    GETTERS Y SETTERS 
    ===================*/

    public function getNombreArchivo() {
        return $this->nombreArchivo;
    }

    public function setDatos($datos) {

        if(isset($datos['nombre_backup'])){
            $r = $this->nombreArchivo($datos['nombre_backup']);
            if ($r === true) {
                $this->nombreArchivo = $datos['nombre_backup'];
            } else {
                $this->errores['nombre_backup'] = $r;
            }
        }

        if(isset($datos['desc_backup'])){
            $res= $this->validarDescripcion($datos['desc_backup'], 'descripcion', 2, 50);
            if ($res === true) {
                $this->descripcion = $datos['desc_backup'];
            } else {
                $this->errores['descripcion'] = $res;
            }
        }

        if(isset($datos['retencion'])){
            $valor = intval($datos['retencion']);
            if ($valor < 5) {
                $this->errores['retencion'] = "El valor de retención debe ser al menos 5.";
            } else {
                $this->retencion = $valor;
            }
        }

    }
    
    public function getDescripcion() {
        return $this->descripcion;
    }

    public function getFrecuencia() {
        return $this->frecuencia;
    }

    public function getDia(){
        return $this->dia;
    }

    public function getHora(){
        return $this->hora;
    }

    public function getRetencion(){
        return $this->retencion;
    }

    public function getHabilitado(){
        return $this->habilitado;
    }


    public function getModo(){
        return $this->modo;
    }

    public function getErrores() {
        return $this->errores;
    }

    public function check() {
        if (!empty($this->errores)) {
        $mensajes = implode(" | ", $this->errores);
        throw new Exception("Errores de validación: $mensajes");
        }
    }
    

    private function generarRespaldoManual($cod_usuario) {
        try {
            $this->check();
    
            $n = $this->nombreArchivo . '_' . date("Y-m-d_H-i-s") . ".sql";
            $directorio = "respaldos/";
            $this->ruta = $directorio . $n;
    
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }        
            
            $comando = "C:/xampp/mysql/bin/mysqldump.exe --user=" . $_ENV['_SEC_DB_USER_'] . " --databases " . $_ENV['_SEC_DB_NAME_'] . " " . $_ENV['_DB_NAME_'] . " > " . $this->ruta;
            exec($comando, $output, $return_var);
            if ($return_var !== 0) {
                throw new Exception("mysqldump falló con código: $return_var");
            }

            if (!file_exists($this->ruta)) {
                throw new Exception("No se generó el archivo.");
            }
    
            $this->tamanio = filesize($this->ruta) / 1024 / 1024;
    
            $sql = "INSERT INTO backup (cod_usuario, cod_config_backup, nombre, descripcion, ruta, fecha, tipo, tamanio)
                    VALUES (:cod_usuario, :cod_config_backup, :nombre, :descripcion, :ruta, :fecha, :tipo, :tamanio)";
    
            parent::conectarBD();
            $stmt = $this->conex->prepare($sql);
    
            $fecha = date("Y-m-d H:i:s");
            $tipo = "manual";
            $stmt->bindParam(':cod_usuario', $cod_usuario);
            $stmt->bindValue(':cod_config_backup', 1);
            $stmt->bindParam(':ruta', $this->ruta);
            $stmt->bindParam(':nombre', $this->nombreArchivo);
            $stmt->bindParam(':descripcion', $this->descripcion);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->bindParam(':tamanio', $this->tamanio);
    
            $res = $stmt->execute();
    
            if (!$res) {
                throw new Exception("No se pudo registrar el respaldo.");
            }
    
            return true;
    
        } catch (Exception $e) {
            return "Error al generar respaldo manual: " . $e->getMessage();
        } finally {
            parent::desconectarBD();
        }
    }
        
    public function getGenerarRespaldoManual($cod_usuario) {
        return $this->generarRespaldoManual($cod_usuario);
    }
        


    /*==============================
    MOSTRAR RESPALDOS
    ==============================*/
    private function mostrar() {
        $sql = "SELECT b.*,
        u.nombre AS nombre_usuario
        FROM backup b 
        INNER JOIN usuarios u ON b.cod_usuario = u.cod_usuario
        ORDER BY fecha DESC";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $datos;
    }

    public function getMostrar() {
        return $this->mostrar();
    }

    /*==============================
    ELIMINAR RESPALDO + POLITICA DE RETENCION
    ==============================*/
    private function eliminarRetencion($retencion, $tipo) {
        try {
            $sql = "SELECT cod_backup, ruta FROM backup ";
    
            // revisar para ambos
            if ($tipo == 'manual') {
                $sql .= "WHERE tipo = 'manual' ";
            } elseif ($tipo == 'automatico') {
                $sql .= "WHERE tipo = 'automatico' ";
            }
    
            $sql .= "ORDER BY fecha DESC LIMIT 18446744073709551615 OFFSET $retencion";

    
            parent::conectarBD();
            $stmt = $this->conex->prepare($sql);
            $resul = $stmt->execute();
    
            if ($resul) {
                $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);
                foreach ($registros as $r) {
                    if (!empty($r['ruta']) && file_exists($r['ruta'])) {
                        unlink($r['ruta']); // elimina archivo físico
                    }
    
                    $del = $this->conex->prepare("DELETE FROM backup WHERE cod_backup = :cod_backup");
                    $del->bindParam(':cod_backup', $r['cod_backup']);
                    $del->execute();
                }
                $r = 1;
            } else {
                $r = 0;
            }
            return $r;
    
        } catch (Exception $e) {
            throw new Exception("Error al aplicar política de retención: " . $e->getMessage());
        } finally {
            parent::desconectarBD(); 
        }
    }

    public function geteliminarRetencion($retencion, $tipo) {
        return $this->eliminarRetencion($retencion, $tipo);
    }


    /*==============================
    ELIMINAR MANUAL
    ==============================*/
    private function eliminar($cod_backup) {
        try {
            $sql = "SELECT ruta FROM backup WHERE cod_backup = :cod_backup";
            parent::conectarBD();
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':cod_backup', $cod_backup);
            $stmt->execute();
            $ruta = $stmt->fetchColumn();
    
            if (!empty($ruta) && file_exists($ruta)) {
                unlink($ruta); // elimina archivo físico
            }
    
            $del = $this->conex->prepare("DELETE FROM backup WHERE cod_backup = :cod_backup");
            $del->bindParam(':cod_backup', $cod_backup);
            return $del->execute() ? 1 : 0;
    
        } catch (Exception $e) {
            throw new Exception("Error al eliminar respaldo: " . $e->getMessage());
        } finally {
            parent::desconectarBD(); 
        }
    }   

    public function getEliminar($cod_backup) {
        return $this->eliminar($cod_backup);
    }


    /*==============================
    CONFIGURACIÓN DE BACKUP
    ==============================*/
    private function obtenerConfig() {
        $sql = "SELECT * FROM config_backup LIMIT 1";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->execute();
        $datos = $stmt->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $datos;
    }

    public function getConfig() {
        return $this->obtenerConfig();
    }

    /*==============================
    ACTUALIZAR CONFIGURACIÓN DE RESPALDO
    ==============================*/
    private function guardarConfig() {
        try {
            $sql = "UPDATE config_backup SET
            frecuencia = :frecuencia,
            modo = :modo,
            retencion = :retencion,
            hora = :hora,
            dia = :dia,
            habilitado = :habilitado
            WHERE cod_config_backup = 1";

            parent::conectarBD();
            $stmt = $this->conex->prepare($sql);

            $stmt->bindParam(':frecuencia', $this->frecuencia, PDO::PARAM_INT);
            $stmt->bindParam(':dia', $this->dia, PDO::PARAM_INT);
            $stmt->bindParam(':hora', $this->hora);
            $stmt->bindParam(':retencion', $this->retencion, PDO::PARAM_INT);
            $stmt->bindParam(':habilitado', $this->habilitado, PDO::PARAM_INT);
            $stmt->bindParam(':modo', $this->modo, PDO::PARAM_INT);

            $res = $stmt->execute();
            return $res ? 1 : 0;

        } catch (Exception $e) {
            throw new Exception("Error al actualizar configuración: " . $e->getMessage());
        } finally {
            parent::desconectarBD();
        }
    }

    public function getGuardarConfig() {
        return $this->guardarConfig();
    }


}