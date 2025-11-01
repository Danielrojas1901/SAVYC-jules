<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
class Clientes extends Conexion{
    use ValidadorTrait; // Usar el trait para validaciones
    private $nombre;
    private $apellido;
    private $cedula;
    private $telefono;
    private $email;
    private $direccion;
    private $status;
    private $errores = [];

    public function __construct() {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function setData($datos) {
    // Limpiar errores anteriores
    $this->errores = [];

    // Validar y asignar nombre (obligatorio)
    if (isset($datos['nombre'])) {
        $resultado = $this->validarTexto($datos['nombre'], 'nombre', 2, 50);
        if ($resultado === true) {
            $this->nombre = $datos['nombre'];
        } else {
            $this->errores['nombre'] = $resultado;
        }
    }

    // Validar y asignar apellido (obligatorio)
    if (isset($datos['apellido'])) {
        $resultado = $this->validarTexto($datos['apellido'], 'apellido', 2, 50);
        if ($resultado === true) {
            $this->apellido = $datos['apellido'];
        } else {
            $this->errores['apellido'] = $resultado;
        }
    }

    // Validar y asignar cedula (obligatorio)
    if (isset($datos['cedula'])) {
        $resultado = $this->validarNumerico($datos['cedula'], 'cedula', 6, 12);
        if ($resultado === true) {
            $this->cedula = $datos['cedula'];
        } else {
            $this->errores['cedula'] = $resultado;
        }
    }

    // Validar y asignar telefono (opcional)
    if (isset($datos['telefono']) && !empty(trim($datos['telefono']))) {
        $resultado = $this->validarTelefono($datos['telefono']);
        if ($resultado === true) {
            $this->telefono = $datos['telefono'];
        } else {
            $this->errores['telefono'] = $resultado;
        }
    } else {
        $this->telefono = null; 
    }

    // Validar y asignar email (opcional)
    if (isset($datos['email']) && !empty(trim($datos['email']))) {
        $resultado = $this->validarEmail($datos['email']);
        if ($resultado === true) {
            $this->email = $datos['email'];
        } else {
            $this->errores['email'] = $resultado;
        }
    } else {
        $this->email = null; 
    }

    // Validar y asignar direccion (opcional)
    if (isset($datos['direccion']) && !empty(trim($datos['direccion']))) {
        $resultado = $this->validarAlfanumerico($datos['direccion'], 'direccion', 5, 100);
        if ($resultado === true) {
            $this->direccion = $datos['direccion'];
        } else {
            $this->errores['direccion'] = $resultado;
        }
    } else {
        $this->direccion = null; 
    }

    if (isset($datos['status'])) {
        $resultado = $this->validarNumerico($datos['status'], 'status', 1, 10);
        if ($resultado === true) {
            $this->status = $datos['status'];
        } else {
            $this->errores['status'] = $resultado;
        }
    }
}
    // Chequear si hay errores
    public function check() {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }


    // Si quieres acceder a los errores individualmente
    public function getErrores() {
        return $this->errores;
    }

    public function getNombre(){
        return $this->nombre;
    }

    public function getApellido(){
        return $this->apellido;
    }

    public function getCedula(){
        return $this->cedula;
    }

    public function getTelefono(){
        return $this->telefono;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getDireccion(){
        return $this->direccion;
    }

    public function getStatus(){
        return $this->status;
    }


    /*==============================
    REGISTRAR CLIENTE
    ================================*/
private function registrar(){ 
    $registro = "INSERT INTO clientes(nombre,apellido,cedula_rif,telefono,email,direccion,status) 
                VALUES(:nombre, :apellido, :cedula_rif, :telefono, :email, :direccion, 1)";
    
    parent::conectarBD();
    $strExec = $this->conex->prepare($registro);
    
    $strExec->bindParam(':nombre', $this->nombre);
    $strExec->bindParam(':apellido', $this->apellido);
    $strExec->bindParam(':cedula_rif', $this->cedula);
    
    // Para campos opcionales, usar bindValue con tipo PDO::PARAM_NULL si son nulos
    if ($this->telefono === null) {
        $strExec->bindValue(':telefono', null, PDO::PARAM_NULL);
    } else {
        $strExec->bindParam(':telefono', $this->telefono);
    }
    
    if ($this->email === null) {
        $strExec->bindValue(':email', null, PDO::PARAM_NULL);
    } else {
        $strExec->bindParam(':email', $this->email);
    }
    
    if ($this->direccion === null) {
        $strExec->bindValue(':direccion', null, PDO::PARAM_NULL);
    } else {
        $strExec->bindParam(':direccion', $this->direccion);
    }
    
    $resul = $strExec->execute();
    parent::desconectarBD();
    
    return $resul ? 1 : 0;
}
    public function getRegistrar(){
        return $this->registrar();
    }

    public function consultar(){
        $registro = "select * from clientes";
        parent::conectarBD();
        $consulta = $this->conex->prepare($registro);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $datos;

        }else{
            return [];
        }

    }

    public function buscar($valor){
        $this->cedula=$valor;
        $registro = "select * from clientes where cedula_rif='".$this->cedula."'";
        $resutado= "";
        parent::conectarBD();
            $dato=$this->conex->prepare($registro);
            $resul=$dato->execute();
            $resultado=$dato->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
            if ($resul) {
                return $resultado;
            }else{
                return [];
            }
        
    }

    public function getactualizar($valor){
        return $this->actualizar($valor);
    }

   private function actualizar($valor){
    $cod = $valor;
    $registro = "UPDATE clientes SET 
                nombre = :nombre, 
                apellido = :apellido, 
                cedula_rif = :cedula_rif, 
                telefono = :telefono, 
                email = :email, 
                direccion = :direccion, 
                status = :status 
                WHERE cod_cliente = $cod";
    
    parent::conectarBD();
    $strExec = $this->conex->prepare($registro);
    
    $strExec->bindParam(':nombre', $this->nombre);
    $strExec->bindParam(':apellido', $this->apellido);
    $strExec->bindParam(':cedula_rif', $this->cedula);
    
    // Para campos opcionales
    if ($this->telefono === null) {
        $strExec->bindValue(':telefono', null, PDO::PARAM_NULL);
    } else {
        $strExec->bindParam(':telefono', $this->telefono);
    }
    
    if ($this->email === null) {
        $strExec->bindValue(':email', null, PDO::PARAM_NULL);
    } else {
        $strExec->bindParam(':email', $this->email);
    }
    
    if ($this->direccion === null) {
        $strExec->bindValue(':direccion', null, PDO::PARAM_NULL);
    } else {
        $strExec->bindParam(':direccion', $this->direccion);
    }
    
    $strExec->bindParam(':status', $this->status);
    
    $resul = $strExec->execute();
    parent::desconectarBD();
    
    return $resul ? 1 : 'success';
}

    public function geteliminar($valor){
        return $this->eliminar($valor);
    }

    private function eliminar($valor){
        $this->conectarBD();
        $registro="SELECT COUNT(*) AS n_ventas FROM ventas WHERE cod_cliente =$valor ";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $resul = $strExec->execute();
        $this->desconectarBD();
        if($resul){
            $resultado=$strExec->fetch(PDO::FETCH_ASSOC); 
            if ($resultado['n_ventas']>0){
                $r='venta';
            }else{
                $this->conectarBD();
                $fisico="DELETE FROM clientes WHERE cod_cliente=$valor";
                $strExec=$this->conex->prepare($fisico);
                $strExec->execute();
                $this->desconectarBD();
                $r='success';
            }
            parent::desconectarBD();
        }else {
            $r='error_delete';
            parent::desconectarBD();
        }
        return $r;
    }

    //Widget de inicio
    public function widgetConteo(){
        $sql='SELECT COUNT(*) AS total_clientes
            FROM clientes
            WHERE status = 1;';
        parent::conectarBD();
        $str=$this->conex->prepare($sql);
        $resultado=$str->execute();
        $r=$str->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resultado){
            return $r;
        }else{
            return [];
        }

    }
}