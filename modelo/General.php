<?php 
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Modelo\Traits\ImagenTrait;
use Exception;
use PDO;
use PDOException;

class General extends Conexion{
    use ValidadorTrait;
    use ImagenTrait;

    private $cod;
    private $rif;
    private $nombre;
    private $direccion;
    private $telefono;
    private $email;
    private $descripcion;
    private $logo;
    private $errores = [];

    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
        $this->setDirectorioBase('vista/dist/img/');
        $this->setSubcarpeta('logos');
        $this->setImagenDefault('logo_generico.png');
    }

    public function check() {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

#GETTER Y SETTER
    public function getRif(){
        return $this->rif;
    }
    public function setRif($rif){
        $resultado = $this->validarAlfanumerico($rif, 'rif', 7, 15);
        if ($resultado === true) {
            $this->rif = $rif;
        } else {
            $this->errores['rif'] = $resultado;
        }
    }
    public function getNom(){
        return $this->nombre;
    }
    public function setNom($nombre){
        $resultado = $this->validarAlfanumerico($nombre, 'nombre', 2, 50);
        if ($resultado === true) {
            $this->nombre = $nombre;
        } else {
            $this->errores['nombre'] = $resultado;
        }
    }
    public function setcod($valor){
        $resultado=$this->validarNumerico($valor, 'codigo', 1, 5);
        if ($resultado === true) {
            $this->cod = $valor;
        } else {
            $this->errores['codigo'] = $resultado;
        }
    }
    public function getDir(){
        return $this->direccion;
    }
    public function setDir($direccion){
        if(empty($direccion)) {
            $this->direccion = null;
        } else {
            $resultado = $this->validarAlfanumerico($direccion, 'direccion', 5, 100);
            if ($resultado === true) {
                $this->direccion = $direccion;
            } else {
                $this->errores['direccion'] = $resultado;
            }
        }
    }
    public function gettlf(){
        return $this->telefono;
    }
    public function settlf($telefono){
        if(empty($telefono)) {
            $this->telefono = null;
        } else {
            $resultado = $this->validarTelefono($telefono);
            if ($resultado === true) {
                $this->telefono = $telefono;
            } else {
                $this->errores['telefono'] = $resultado;
            }
        }
    }
    public function getemail(){
        return $this->email;
    }
    public function setemail($email){
        if(empty($email)) {
            $this->email = null;
        } else {
            $resultado = $this->validarEmail($email);
            if ($resultado === true) {
                $this->email = $email;
            } else {
                $this->errores['email'] = $resultado;
            }
        }
    }
    public function getDescri(){
        return $this->descripcion;
    }
    public function setDescri($descripcion){
        if(empty($descripcion)) {
            $this->descripcion = null;
        } else {
            $resultado = $this->validarAlfanumerico($descripcion, 'descripcion', 5, 100);
            if ($resultado === true) {
                $this->descripcion = $descripcion;
            } else {
                $this->errores['descripcion'] = $resultado;
            }
        }
    }
    public function getlogo(){
        return $this->logo;
    }
    public function setlogo($logo){
        $this->logo = $logo;
    }
/*==============================
REGISTRAR INFO DE EMPRESA
================================*/
    private function registrar($horarios){
        try {
            $sql = "INSERT INTO empresa(rif,nombre,direccion,telefono,email,descripcion,logo) VALUES(:rif,:nombre,:direccion,:telefono,:email,:descripcion,:logo)";
            parent::conectarBD();
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(":rif", $this->rif);
            $strExec->bindParam(":nombre", $this->nombre);
            $strExec->bindParam(":direccion", $this->direccion);
            $strExec->bindParam(":telefono", $this->telefono);
            $strExec->bindParam(":email", $this->email);
            $strExec->bindParam(":descripcion", $this->descripcion);
            $strExec->bindParam(":logo", $this->logo);
            $resul = $strExec->execute();
            
            if($resul){
                $cod=$this->conex->lastInsertId();
                foreach($horarios as $dia => $horario){
                    if(empty($horario['cerrado'])){
                        $horario['cerrado'] = 0;
                    }
                    $sql = "UPDATE horarios SET cod=:cod, desde=:desde, hasta=:hasta, cerrado=:cerrado WHERE cod_dia=:cod_dia";
                    $strExec = $this->conex->prepare($sql);
                    $strExec->bindParam(":cod", $cod);
                    $strExec->bindParam(":cod_dia", $horario['cod']);
                    $strExec->bindParam(":desde", $horario['desde']);
                    $strExec->bindParam(":hasta", $horario['hasta']);
                    $strExec->bindParam(":cerrado", $horario['cerrado']);
                    $resul = $strExec->execute();
                    if(!$resul){
                        throw new Exception("Error al actualizar los horarios");
                    }
                }
                $r = 1;
            } else {
                throw new Exception("Error al registrar la información de la empresa");
            }
            parent::desconectarBD();
            return $r;
        } catch (PDOException $e) {
            parent::desconectarBD();
            throw new Exception($e->getMessage());
        }
    }
    public function getregistrar($horarios){
        return $this->registrar($horarios);
    }

/*==============================
MOSTRAR INFO DE EMPRESA
================================*/
    public function mostrar(){
        $registro="select * from empresa";
        parent::conectarBD();
        $consulta=$this->conex->prepare($registro);
        $resul=$consulta->execute();
        $datos=$consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $datos;
        }else{
            return [];
        }
    }

    public function horarios(){
        $registro="select * from horarios";
        parent::conectarBD();
        $consulta=$this->conex->prepare($registro);
        $resul=$consulta->execute();
        $datos=$consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $datos;
        }else{
            return [];
        }
    }

    //VALIDAR REGISTRO
    public function buscar(){
        $registro="select count(*) as total from empresa";
        $resultado= "";
        parent::conectarBD();
            $dato=$this->conex->prepare($registro);
            $resul=$dato->execute();
            $resultado=$dato->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
            if($resul){
                if($resultado['total']>0){
                return $resultado;
            }else{
                return false;
            }
        }
    }


/*==============================
EDITAR INFO DE EMPRESA
================================*/
private function editar($horarios){
    try {
        $sql = "UPDATE empresa SET rif=:rif,nombre=:nombre,direccion=:direccion,telefono=:telefono,email=:email,descripcion=:descripcion,logo=:logo WHERE cod=:cod";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(":rif", $this->rif);
        $strExec->bindParam(":nombre", $this->nombre);
        $strExec->bindParam(":direccion", $this->direccion);
        $strExec->bindParam(":telefono", $this->telefono);
        $strExec->bindParam(":email", $this->email);
        $strExec->bindParam(":descripcion", $this->descripcion);
        $strExec->bindParam(":logo", $this->logo);
        $strExec->bindParam(":cod", $this->cod);
        $resul = $strExec->execute();
        
        if($resul){
            foreach($horarios as $dia => $horario){
                if(empty($horario['cerrado'])){
                    $horario['cerrado'] = 0;
                }
                $sql = "UPDATE horarios SET desde=:desde, hasta=:hasta, cerrado=:cerrado WHERE cod_dia=:cod_dia AND cod=:cod";
                $strExec = $this->conex->prepare($sql);
                $strExec->bindParam(":cod", $this->cod);
                $strExec->bindParam(":cod_dia", $horario['cod']);
                $strExec->bindParam(":desde", $horario['desde']);
                $strExec->bindParam(":hasta", $horario['hasta']);
                $strExec->bindParam(":cerrado", $horario['cerrado']);
                $resul = $strExec->execute();
                if(!$resul){
                    throw new Exception("Error al actualizar los horarios");
                }
            }
            $r = 1;
        } else {
            throw new Exception("Error al actualizar la información de la empresa");
        }
        parent::desconectarBD();
        return $r;
    } catch (PDOException $e) {
        parent::desconectarBD();
        throw new Exception($e->getMessage());
    }
}

public function geteditar($horarios){
    return $this->editar($horarios);
}


}

