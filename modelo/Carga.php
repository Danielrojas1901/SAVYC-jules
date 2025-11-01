<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;

    class Carga extends Conexion{
    use ValidadorTrait; // Usar el trait para validaciones
    private $codigo;
    private $fecha;
    private $descripcion;
    private $status;
    private $lote;
    private $fecha_vencimiento;
    private $cod_presentacion;
    private $errores = [];

    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }
    // SETTER Y GETTER
    public function setCod($codigo){
        $this->codigo = $codigo;
    }
    public function setFecha($fecha){
        $this->fecha = $fecha;
    }
    public function setDes($descripcion){
        $resultado = $this->validarDescripcion($descripcion, 'descripcion', 2, 50);
        if ($resultado === true) {
            $this->descripcion = $descripcion;
        } else {
            $this->errores['descripcion'] = $resultado;
        }
    }
    public function setStatus($status){
        $this->status = $status;
    }
    public function getCod(){
        return $this->codigo;
    }
    public function getFecha(){
        return $this->fecha;
    }
    public function setCodp($cod_presentacion){
        $this->cod_presentacion = $cod_presentacion;
    }
    public function setFechaV($fecha_vencimiento){
        $this->fecha_vencimiento = $fecha_vencimiento;
    }
    public function setlote($lote){
        $this->lote = $lote;
    }
    
    public function getCodp(){
        return $this->cod_presentacion;
    }
    public function getFechaV(){
        return $this->fecha_vencimiento;
    }
    public function getlote(){
        return $this->lote;
    }
    public function getDes(){
        return $this->descripcion;
    }
    public function getStatus(){
        return $this->status;
    }

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
    
    //     Funciones
    /* #######      REGISTRAR CARGA        #######   */
    private function registrar(){
        $registro = "INSERT INTO carga(fecha, descripcion, status) VALUES(:fecha, :descripcion, 1)";
        parent::conectarBD();
        $strExec= $this->conex->prepare($registro);
        $strExec->bindParam(':fecha',$this->fecha);
        $strExec->bindParam(':descripcion', $this->descripcion);
        $result = $strExec->execute();
        $r = $this->conex->lastInsertId();
        echo '<script>console.log("el id'.$r.'")</script>';
        echo '<script>console.log("el resultado'.$this->conex->lastInsertId(). '")</script>';
        if($result){
           
        }else{
            $r = 0;
        }
        parent::desconectarBD();
        echo '<script>console.log("el id'.$r.'")</script>';
        return $r;
    }

    public function getcrear(){
        return $this->registrar();
    }

    private function buscar($valor){
        //$this->fecha=$valor;
        $registro = "select * from carga where fecha=:fecha";
        //$resutado= "";
        parent::conectarBD();
            $dato=$this->conex->prepare($registro);
            $dato->bindParam('fecha',$valor);
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

    /*######### CONSULTAR #########*/
    private function mostrarc(){
        $sql = "select * from carga";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul=$strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $datos;
        }else{
            return $res= 0;
        }
    }

    public function getmosc(){
        return $this->mostrarc();
    }

    public function producto($valor){
        //$this->fecha=$valor;
        $registro = "select * from productos where fecha=:fecha";
        //$resutado= "";
        parent::conectarBD();
            $dato=$this->conex->prepare($registro);
            $dato->bindParam('fecha',$valor);
            $resul=$dato->execute();
            $resultado=$dato->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
            if ($resul) {
                return $resultado;
            }else{
                return false;
            }
    }


        //METODO REGISTRAR
        private function registrarPro(){
            $registro = "INSERT INTO detalle_productos(cod_presentacion,stock, fecha_vencimiento, lote) VALUES(:cod_presentacion,0, :fecha_vencimiento, :lote)";
            parent::conectarBD();
            $strExec= $this->conex->prepare($registro);
            $strExec->bindParam('cod_presentacion', $this->cod_presentacion);
            $strExec->bindParam(':fecha_vencimiento',$this->fecha_vencimiento);
            $strExec->bindParam(':lote', $this->lote);
            $result = $strExec->execute();
            parent::desconectarBD();
            if($result){
                $r = 1;
            }else{
                $r = 0;
            }
            return $r;
        }
        public function getcrearPro(){
        return $this->registrarPro();
        }
}
?>