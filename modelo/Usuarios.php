<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class Usuarios extends Conexion{

    use ValidadorTrait;

    private $nombre;
    private $user;
    private $password;
    private $status;

    private $errores = [];

    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_SEC_DB_HOST_'], $_ENV['_SEC_DB_NAME_'], $_ENV['_SEC_DB_USER_'], $_ENV['_SEC_DB_PASS_']);
    }

/*GETTER Y SETTER*/

    public function setDatos($datos){

        //Registro de Usuario
        if(isset($datos['nombre'])){
            $r = $this->validarTexto($datos['nombre'], 'nombre', 2, 50);
            if ($r === true) {
                $this->nombre = $datos['nombre'];
            } else {
                $this->errores['nombre'] = $r;
            }
        }

        if (isset($datos['user'])){
            $datos['user'] = strtolower($datos['user']);
            $res = $this->validarTextoNumero($datos['user'], 'user', 2, 20);
            if ($res === true) {
                $this->user = $datos['user'];
            } else {
                $this->errores['user'] = $res;
            }
        }
        
        if(isset($datos['pass']) && !empty($datos['pass']) && isset($datos['user'])){
            $resp = $this->password($datos['pass'], $datos['user']);
            if ($resp === true) {
                $this->password = password_hash($datos['pass'], PASSWORD_DEFAULT);
            } else {
                $this->errores['pass'] = $resp;
            }
        }

        if(isset($datos['statusDelete'])){
            $rp = $this->validarStatusInactivo($datos['statusDelete']);
            if ($rp === true) {
                $this->status = $datos['statusDelete'];
            } else {
                $this->errores['statusDelete'] = $rp;
            }
        }
        
        if(isset($datos['status'])){
            $rp = $this->validarStatus($datos['status']);
            if ($rp === true) {
                $this->status = $datos['status'];
            } else {
                $this->errores['status'] = $rp;
            }
        }

        //Login
        if(isset($datos['ingUsuario'])){
            $datos['ingUsuario'] = strtolower($datos['ingUsuario']);
            $r = $this->validarTextoNumero($datos['ingUsuario'], 'user', 2, 20);
            if ($r === true) {
                $this->user = $datos['ingUsuario'];
            } else {
                $this->errores['user'] = $r;
            }
        }
    }

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

    public function getNombre(){
        return $this->nombre;
    }

    public function getUser(){
        return $this->user;
    }

    public function getPassword(){
        return $this->password;
    }

    public function getStatus(){
        return $this->status;
    }

//LOGIN
    public function mostrar($valor){
            $resultado = [];
            $sql = "SELECT * FROM usuarios WHERE user = :user";
            parent::conectarBD();
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(":user", $valor, PDO::PARAM_STR);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            parent::desconectarBD();
            if ($resultado === false) {
            return [];
            }else{
                return $resultado;
            }
    }

public function accesos($valor){
        $sql="SELECT p.nombre AS modulos, c.nombre AS accion FROM usuarios u 
        INNER JOIN tipo_usuario tu ON u.cod_tipo_usuario = tu.cod_tipo_usuario 
        INNER JOIN tpu_permisos tp ON tu.cod_tipo_usuario = tp.cod_tipo_usuario 
        INNER JOIN modulos p ON tp.cod_modulo = p.cod_modulo 
        INNER JOIN permisos c ON tp.cod_crud=c.cod_crud 
        WHERE u.cod_usuario = :valor ORDER BY p.nombre;";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    $strExec->bindParam(':valor', $valor, PDO::PARAM_INT); 
    $resul=$strExec->execute();
    $datos=$strExec->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resul){
        return $datos;
    }else{
        return $res=[];
    }
}
//FIN LOGIN

/*==============================
REGISTRAR USUARIO
================================*/
private function registrar($rol){ 
    $sql = "INSERT INTO usuarios(nombre,user,password,cod_tipo_usuario,status) VALUES(:nombre,:user,:password,:cod_tipo_usuario,1)";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    $strExec->bindParam(":nombre", $this->nombre);
    $strExec->bindParam(":user", $this->user);
    $strExec->bindParam(":password", $this->password);
    $strExec->bindParam(":cod_tipo_usuario", $rol);
    $resul = $strExec->execute();
    parent::desconectarBD();
    if($resul){
        $r = 1;
    }else{
        $r = 0;
    }
    return $r;

}

public function getregistrar($rol){
    return $this->registrar($rol);
    
}

/*==============================
VALIDAR USUARIO (USER)
================================*/
public function buscar($valor){
    $this->user=$valor;
    $registro = "select * from usuarios where user=:user"; 
    $resultado= "";
    parent::conectarBD();
        $dato=$this->conex->prepare($registro);
        $dato->bindParam(':user',$this->user); 
        $resul=$dato->execute();
        $resultado=$dato->fetch(PDO::FETCH_ASSOC);
    parent::desconectarBD();
        if ($resul) {
            return $resultado;
        }else{
            return false;
        }
    }

/*==============================
MOSTRAR USUARIOS
================================*/
public function listar(){
    $registro = "SELECT
    u.cod_usuario,
    u.nombre,
    u.user,
    u.password,
    u.cod_tipo_usuario,
    u.status,
    tp.rol
    FROM usuarios AS u JOIN tipo_usuario AS tp ON u.cod_tipo_usuario = tp.cod_tipo_usuario
    GROUP BY u.cod_usuario";
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

/*==============================
EDITAR USUARIO
================================*/
    public function editar($codigo, $rol){
    $sql = "UPDATE usuarios 
        SET nombre=:nombre, user=:user, cod_tipo_usuario=:cod_tipo_usuario, status=:status 
        WHERE cod_usuario=:codigo";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':nombre', $this->nombre);
        $strExec->bindParam(':user', $this->user);
        $strExec->bindParam(':cod_tipo_usuario', $rol);
        $strExec->bindParam(':status', $this->status);
        $strExec->bindParam(':codigo', $codigo, PDO::PARAM_INT);
        $resul = $strExec->execute();
        parent::desconectarBD();
        return $resul ? 1 : 0;
    }
    
    public function editar2($codigo, $rol){
    $sql = "UPDATE usuarios 
            SET nombre=:nombre, user=:user, password=:password, cod_tipo_usuario=:cod_tipo_usuario, status=:status 
            WHERE cod_usuario=:codigo";
            parent::conectarBD();
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':nombre', $this->nombre);
            $strExec->bindParam(':user', $this->user);
            $strExec->bindParam(':password', $this->password);
            $strExec->bindParam(':cod_tipo_usuario', $rol);
            $strExec->bindParam(':status', $this->status);
            $strExec->bindParam(':codigo', $codigo, PDO::PARAM_INT);
            $resul = $strExec->execute();
            parent::desconectarBD();
            return $resul ? 1 : 0;
    }

/*==============================
ELIMINAR USUARIO
================================*/
public function eliminar($valor) {
    $sql = "SELECT cod_tipo_usuario, status FROM usuarios WHERE cod_usuario = :valor";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    $strExec->bindParam(':valor', $valor, PDO::PARAM_INT);
    $strExec->execute();
    $usuario = $strExec->fetch(PDO::FETCH_ASSOC);
    if ($usuario) {
        // Si el usuario es administrador, verificar si es el último
        if ($usuario['cod_tipo_usuario'] == 1) {
            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE cod_tipo_usuario = 1";
            $strExec = $this->conex->prepare($sql);
            $strExec->execute();
            $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
            if ($resultado['total'] == 2) { // 2 porque por defecto, el admin de programadores estará en la bd
                parent::desconectarBD();
                return 'error_ultimo';
            }
        }
        $sqlDelete = "DELETE FROM usuarios WHERE cod_usuario = :valor";
        $strExecDelete = $this->conex->prepare($sqlDelete);
        $strExecDelete->bindParam(':valor', $valor, PDO::PARAM_INT);
        $delete = $strExecDelete->execute();
        parent::desconectarBD();
        return $delete ? 'success' : 'error_delete'; 
        }
        parent::desconectarBD();
    }

}