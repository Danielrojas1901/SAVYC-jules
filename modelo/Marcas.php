<?php 
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;

class Marcas extends Conexion{
    use ValidadorTrait;
    private $nombre;
    private $status;

    private $errores = [];

    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

#ERRORCHECK
    public function check() {
        if(!empty($this->errores)) {
            $mensajes = implode(" | ",  $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

#GETTER Y SETTER
    public function getNombre(){
        return $this->nombre;
    }
    public function setNombre($nombre){
        $resultado = $this->validarTexto($nombre, 'nombre', 2, 50);
        if($resultado === true) {
            $this->nombre = $nombre;
        }
        else {
            $this->errores['nombre'] = $resultado;
        }
    }

    public function getStatus(){
        return $this->status;
    }
    public function setStatus($status){
        $resultado = $this->validarStatus($status);
        if($resultado === true) {
            $this->status = $status;
        } else {
            $this->errores['status'] = $resultado;
        }
    }
    public function getErrores() {
        return $this->errores;
    }

/*==============================
REGISTRAR MARCA
================================*/
    private function registrar(){
        $sql = "INSERT INTO marcas(nombre,status) VALUES(:nombre,1)";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(":nombre", $this->nombre);
        $resul = $strExec->execute();
        parent::desconectarBD();
        if($resul){
            $r = 1;
        }else{
            $r = 0;
        }
        return $r;
    }

    public function getregistrar(){
        return $this->registrar();
    }
    
/*==============================
VALIDAR MARCAS
================================*/
private function buscar($valor){
    $this->nombre=$valor;
    $registro = "SELECT * FROM marcas WHERE nombre=:nombre";
    $resultado= "";
    parent::conectarBD();
        $dato=$this->conex->prepare($registro);
        $dato->bindParam(":nombre", $this->nombre);
        $resul=$dato->execute();
        $resultado=$dato->fetch(PDO::FETCH_ASSOC);
    parent::desconectarBD();
        if ($resul) {
            return $resultado;
        }else{
            return false;
        }
}

public function getbuscar($valor){
    return $this->buscar($valor);
}

/*==============================
MOSTRAR MARCAS
================================*/
    private function mostrar(){
        $registro = "select * from marcas";
        parent::conectarBD();
        $consulta = $this->conex->prepare($registro);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $datos;
        }else{
            return $r=0;
        }

    }
    public function getmostrar(){
        return $this->mostrar();
    }

/*==============================
EDITAR MARCAS
================================*/
    private function editar($valor){
        $sql="UPDATE marcas SET nombre=:nombre, status=:status WHERE cod_marca=$valor";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        #Instanciar metodo BINDPARAM
        $strExec->bindParam(':nombre', $this->nombre);
        $strExec->bindParam(':status', $this->status);
        $resul = $strExec->execute();
        parent::desconectarBD();
        if($resul){
            $r = 1;
        }else{
            $r = 0;
        }
        return $r;
    }

    public function geteditar($valor){
        return $this->editar($valor);
    }    

/*==============================
ELIMINAR MARCAS
================================*/
    private function eliminar($valor){
        $sql="SELECT COUNT(*) AS count FROM marcas m JOIN productos p ON m.cod_marca = p.cod_marca WHERE m.cod_marca=$valor";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();

        $resultado=$strExec->fetch(PDO::FETCH_ASSOC);

        if($resultado){
            if($resultado["count"]==0){
                $fisico = "DELETE FROM marcas WHERE cod_marca=$valor";
                $strExec=$this->conex->prepare($fisico);
                $delete = $strExec->execute();
                if($delete){
                    $r='success';
                    parent::desconectarBD();
                    return $r;
                }else{
                    $r='error_delete';
                    parent::desconectarBD();
                    return $r;
                }
            }else{
                $r='error_associated';
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
