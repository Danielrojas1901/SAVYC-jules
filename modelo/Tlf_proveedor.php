<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class Tlf_proveedor extends Conexion
{
    use ValidadorTrait;
    private $cod_tlf;
    private $cod_prov;
    private $telefono;
    private $errores = [];

    public function __construct()
    {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function check() {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    public function setcod_tlf($cod_tlf)
    {
        $resultado = $this->validarNumerico($cod_tlf, 'código de teléfono', 1, 20);
        if ($resultado === true) {
            $this->cod_tlf = $cod_tlf;
        } else {
            $this->errores['cod_tlf'] = $resultado;
        }
    }

    public function getcod_tlf()
    {
        return $this->cod_tlf;
    }

    public function setCod1($cod_prov)
    {
        $resultado = $this->validarNumerico($cod_prov, 'código de proveedor', 1, 20);
        if ($resultado === true) {
            $this->cod_prov = $cod_prov;
        } else {
            $this->errores['cod_prov'] = $resultado;
        }
    }

    public function getCod1()
    {
        return $this->cod_prov;
    }

    public function gettelefono()
    {
        return $this->telefono;
    }

    public function settelefono($telefono)
    {
        $resultado = $this->validarTelefono($telefono);
        if ($resultado === true) {
            $this->telefono = $telefono;
        } else {
            $this->errores['telefono'] = $resultado;
        }
    }

    // registrar
    private function registra() {
        $sql = "INSERT INTO tlf_proveedores (cod_prov, cod_tlf, telefono) VALUES (:cod_prov, :cod_tlf, :telefono)";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        // Vincula los parámetros
        $strExec->bindParam(':cod_prov', $this->cod_prov);
        $strExec->bindParam(':cod_tlf', $this->cod_tlf); 
        $strExec->bindParam(':telefono', $this->telefono);
        // Ejecuta la consulta  
        $resul = $strExec->execute();
        parent::desconectarBD();
        if ($resul == 1) {
            return 1; // Éxito  
        } else {
            return 0; // Fallo  
        }
    }
    
    public function getregistra() {
        return $this->registra();
    }
    

  


    //inicio de actualizar//
    private function editar()
    {
        $sql = "UPDATE tlf_proveedores SET cod_tlf = :cod_tlf,telefono = :telefono, status = :status   
            WHERE cod_tlf = :cod_tlf";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':cod_tlf', $this->cod_tlf);
        $strExec->bindParam(':telefono', $this->telefono);
        // Ejecuta la consulta  
        $resul = $strExec->execute();
        parent::desconectarBD();
        if ($resul == 1) {
            return 1; // Éxito  
        } else {
            return 0; // Fallo  
        }
    }

    public function geteditar()
    {
        return $this->editar();
    }
    //actualizar//


 // eliminar
    private function eliminar($valor)
    {
        $sql = "DELETE FROM tlf_proveedores WHERE cod_tlf = $valor";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        parent::desconectarBD();
        if ($resul) {
            $res = 1;
        } else {
            $res = 0;
        }
        return $res;
    }

    public function geteliminar($valor)
    {
        return $this->eliminar($valor);
    }

    //inicio de consultar  //
    private function consultar()
    {
        $registro = "select * from tlf_proveedores";
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
    public function getconsulta()
    {
        return $this->consultar();
    }
    //fin de consultar//


    

  //metodo buscar
    private function busca($dato)
    {
    $this->telefono = $dato;
    $registro = "select * from tlf_proveedores where telefono='" . $this->telefono . "'";
    $resulado = "";
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

    public function getbusca($valor)
    {
    return $this->busca($valor);
    }
}
