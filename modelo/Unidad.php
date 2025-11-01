<?php 
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class Unidad extends Conexion{
    private $tipo_medida;
    private $status;
    private $cod_unidad;
    use ValidadorTrait;


    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    private $errores = [];

#GETTER Y SETTER
    public function getTipo(){
        return $this->tipo_medida;
    }
    public function setTipo($tipo_medida){
        $resultado = $this->validarTexto($tipo_medida, 'tipo_medida', 2, 50);
        if ($resultado === true) {
            $this->tipo_medida = $tipo_medida;
        } else {
            $this->errores['tipo_medida'] = $resultado;
        }
        $this->tipo_medida = $tipo_medida;
    }
    public function getStatus(){
        return $this->status;
    }
    public function setStatus($status){
        $this->status = $status;
    }

    public function setCod($cod_unidad){
        $this->cod_unidad = $cod_unidad;
    }

    public function getCod(){
        return $this->cod_unidad;
    }

     // Chequear si hay errores
     public function check() {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }


/*==============================
REGISTRAR UNIDAD DE MEDIDA
================================*/
    private function crearUnidad(){

        $sql = "INSERT INTO unidades_medida(tipo_medida,status) VALUES(:tipo_medida, 1)";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(":tipo_medida", $this->tipo_medida);
        $resul = $strExec->execute();
        parent::desconectarBD();
        if($resul){
            $r = 1;
        }else{
            $r = 0;
        }
        return $r;

    }
    public function getcrearUnidad(){
        return $this->crearUnidad();
    }

    /*==============================
    MOSTRAR UNIDADES DE MEDIDAS
================================*/

    public function consultarUnidad(){
        $sql = "select * from unidades_medida";
        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $datos;
        }return $r = 0;
    }

    private function buscar($dato){
        $this->tipo_medida = $dato;
        $registro="select * from unidades_medida where tipo_medida='".$this->tipo_medida."'";
        $resultado= "";
        parent::conectarBD();
        $dato = $this->conex->prepare($registro);
        $resul = $dato->execute();
        $resultado=$dato->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $resultado;
        }else{
            return false;
        }
    }

    public function getbuscar($dato){
        return $this->buscar($dato);
    }

    private function buscarcod($valor){
        $this->tipo_medida=$valor;
        $registro = "select * from tipo_usuario where rol='".$this->tipo_medida."'";
        $resutado= "";
        parent::conectarBD();
            $dato=$this->conex->prepare($registro);
            $resul=$dato->execute();
            $resultado=$dato->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
            if ($resul) {
                return $resultado;
            }else{
                return false;
            }
    
    }

    public function getcodU($valor){
        return $this->buscarcod($valor);
    }
    private function editar(){
        $registro = "UPDATE unidades_medida SET tipo_medida = :tipo_medida, status = :status WHERE cod_unidad = :cod_unidad";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $strExec->bindParam(':cod_unidad',$this->cod_unidad);
        $strExec->bindParam(':tipo_medida',$this->tipo_medida);
        $strExec->bindParam(':status', $this->status);
        $resul = $strExec->execute();
        parent::desconectarBD();
        if($resul == 1){
            $r = 1;
        }else{
            $r = 0;
        }
        return $r;
    }

    public function geteditar(){
        return $this->editar();
    }

    

    private function eliminar($valor){ 
        $registro = "SELECT COUNT(*) AS n_p FROM presentacion_producto WHERE cod_unidad = $valor";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $strExec->execute();
        $resul = $strExec->fetch(PDO::FETCH_ASSOC);
        if($resul){
            $registro = "SELECT status FROM unidades_medida WHERE cod_unidad = $valor";
            $strExec = $this->conex->prepare($registro);
            $strExec->execute();
            $resu = $strExec->fetch(PDO::FETCH_ASSOC);

            if($resu['status'] != 0){ // Si esta inactivo
                $r = 'error_status';
                parent::desconectarBD();
                return $r;
            }

            if($resul['n_p'] != 0){ //Si tiene productos
                $r='error_associated';
                parent::desconectarBD();
                return $r;
            }

            $f = "DELETE FROM unidades_medida WHERE cod_unidad = $valor";
            $strExec = $this->conex->prepare($f);
            $ress=$strExec->execute();
            if($ress){
                $r='success';
                parent::desconectarBD();
                return $r;
            }else{
                $r='error_delete';
                parent::desconectarBD();
                return $r;
            }
        } else{
            $r='error_query';
            parent::desconectarBD();
            return $r;
        }
    }

    public function geteliminar($valor){
        return $this->eliminar($valor);
    }
}

