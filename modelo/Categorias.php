<?php 
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;

class Categorias extends Conexion{

    use ValidadorTrait;
    private $nombre;
    private $status;

    private $errores = [];

    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

#GETTER Y SETTER
public function setDatos($datos){

        if(isset($datos['nombre'])){
        $resultado = $this->validarTexto($datos['nombre'], 'nombre', 2, 50);
            if ($resultado === true) {
                $this->nombre = $datos['nombre'];
            } else {
                $this->errores['nombre'] = $resultado;
            }
        }

        if(isset($datos['statusDelete'])){
            $res = $this->validarStatusInactivo($datos['statusDelete']);
            if ($res === true) {
                $this->status = $datos['statusDelete'];
            } else {
                $this->errores['statusDelete'] = $res;
            }
        }
    
        if(isset($datos['status'])){
            $r = $this->validarStatus($datos['status']);
                if ($r === true) {
                    $this->status = $datos['status'];
                } else {
                    $this->errores['status'] = $r;
                }
            }
}


public function getNombre(){
    return $this->nombre;
}

public function getStatus(){
    return $this->status;
}


#VALIDACIONES

public function check(){
    if (!empty($this->errores)) {
        $mensajes = implode(" | ", $this->errores);
        throw new Exception("Errores de validación: $mensajes");
    }
}

#Acceder a los errores individualmente
public function getErrores() {
    return $this->errores;
}

/*==============================
REGISTRAR CATEGORIA
================================*/
    private function registrar(){
        $sql = "INSERT INTO categorias(nombre,status) VALUES(:nombre,1)";
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
VALIDAR CATEGORIAS
================================*/
private function buscar($valor){
    $this->nombre=$valor;
    $registro = "SELECT * FROM categorias WHERE nombre='".$this->nombre."'";
    $resultado= "";
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

public function getbuscar($valor){
    return $this->buscar($valor);
}

/*==============================
MOSTRAR CATEGORIAS
================================*/
    private function mostrar(){
        $registro = "select * from categorias";
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
EDITAR CATEGORIAS
================================*/
    private function editar($valor){
        $sql="UPDATE categorias SET nombre=:nombre, status=:status WHERE cod_categoria=$valor";
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
ELIMINAR CATEGORIAS
================================*/
    private function eliminar($valor){
        $sql="SELECT COUNT(*) AS count FROM categorias c JOIN productos p ON c.cod_categoria = p.cod_categoria WHERE c.cod_categoria=$valor";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        $resultado=$strExec->fetch(PDO::FETCH_ASSOC);
        if($resultado){
            if($resultado["count"]==0){
                $fisico = "DELETE FROM categorias WHERE cod_categoria=$valor";
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
