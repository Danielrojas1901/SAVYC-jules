<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class  Representantes extends Conexion{
  use ValidadorTrait;
  private $cod_representante;
  private $cod_prov;
  private $cedula;
  private $nombre;
  private $apellido;
  private $telefono;
  private $status;
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
  public function setCod($cod_representante)
  {
    $resultado = $this->validarNumerico($cod_representante, 'código de representante', 1, 20);
    if ($resultado === true) {
      $this->cod_representante = $cod_representante;
    } else {
      $this->errores['cod_representante'] = $resultado;
    }
  }
  public function getCod()
  {
    return $this->cod_representante;
  }

  public function getcedula()
  {
    return $this->cedula;
  }

  public function setcedula($cedula)
  {
    $resultado = $this->validarNumerico($cedula, 'cédula', 7, 8);
    if ($resultado === true) {
      $this->cedula = $cedula;
    } else {
      $this->errores['cedula'] = $resultado;
    }
  }

  public function getnombre()
  {
    return $this->nombre;
  }

  public function setnombre($nombre)
  {
    $resultado = $this->validarTexto($nombre, 'nombre', 4, 20);
    if ($resultado === true) {
      $this->nombre = $nombre;
    } else {
      $this->errores['nombre'] = $resultado;
    }
  }

  public function getapellido()
  {
    return $this->apellido;
  }

  public function setapellido($apellido)
  {
    if (empty($apellido)) {
      $this->apellido = null;
    } else {
      $resultado = $this->validarTexto($apellido, 'apellido', 4, 20);
      if ($resultado === true) {
        $this->apellido = $apellido;
      } else {
        $this->errores['apellido'] = $resultado;
      }
    }
  }

  public function gettelefono()
  {
    return $this->telefono;
  }

  public function settelefono($telefono)
  {
    if (empty($telefono)) {
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

  public function getStatus()
  {
    return $this->status;
  }

  public function setStatus($status)
  {
    $resultado = $this->validarStatus($status);
    if ($resultado === true) {
      $this->status = $status;
    } else {
      $this->errores['status'] = $resultado;
    }
  }

  //metodos crud  registrar  //
  private function registrar()
  {
    $sql = "INSERT INTO prov_representantes (cod_prov, cedula, nombre, apellido, telefono, status) VALUES (:cod_prov, :cedula, :nombre, :apellido, :telefono, 1)";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    // Vincula los parámetros  
    $strExec->bindParam(':cod_prov', $this->cod_prov);
    $strExec->bindParam(':cedula', $this->cedula);
    $strExec->bindParam(':nombre', $this->nombre);
    $strExec->bindParam(':apellido', $this->apellido);
    $strExec->bindParam(':telefono', $this->telefono);
    // Ejecutar el INSERT  
    $resul = $strExec->execute();
    parent::desconectarBD();
    if ($resul) {
      $res = 1;
    } else {
      $res = 0;
    }
    return $res;
  } //fin de registrar//

  public function getregistra()
  {
    return $this->registrar();
  }




  //inicio de actualizar   //
  private function editar()
  {
    $sql = "UPDATE prov_representantes 
              SET cedula=:cedula, nombre=:nombre, apellido=:apellido, telefono=:telefono, status=:status 
              WHERE cod_representante = :cod_representante";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    $strExec->bindParam(':cod_representante', $this->cod_representante);
    $strExec->bindParam(':cedula', $this->cedula);
    $strExec->bindParam(':nombre', $this->nombre);
    $strExec->bindParam(':apellido', $this->apellido);
    $strExec->bindParam(':telefono', $this->telefono);
    $strExec->bindParam(':status', $this->status);
    // Ejecuta la consulta  
    $resul = $strExec->execute();
    parent::desconectarBD();
    if ($resul == 1) {
      return 1; // Éxito  
    } else {
      return 0; // Fallo  
    }
  }


  public function getedita()
  {
    return $this->editar();
  }


  //actualizar//
  private function eliminar($valor)
  {
      // Eliminar de forma física sin buscar nada
      $eliminar = "DELETE FROM prov_representantes WHERE cod_representante = :cod_representante";
      parent::conectarBD();
      $strExecDelete = $this->conex->prepare($eliminar);
      $strExecDelete->bindParam(':cod_representante', $valor);
      if ($strExecDelete->execute()) {
        parent::desconectarBD();
          return 'success_physical_delete';
      } else {
        parent::desconectarBD();
          return 'error_physical_delete';
      }
  }
  
  // Método para obtener el resultado de la eliminación
  public function geteliminar($valor)
  {
      return $this->eliminar($valor);
  }

  //inicio de consultar//
  private function consultar()
  {
    $registro = "select * from prov_representantes";
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
  private function buscar($dato)
  {
    $this->cedula = $dato;
    $registro = "select * from prov_representantes where cedula='" . $this->cedula . "'";
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
  public function getbuscar($valor)
  {
    return $this->buscar($valor);
  }
}
