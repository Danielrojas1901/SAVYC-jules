<?php
    namespace Modelo;
    use Modelo\Conexion;
    use Modelo\Traits\ValidadorTrait;
    use Exception;
    use PDO;
    //NECESITO TRABAJAR CON ESTO, POR ESO LO DEJE ASI, CUANDO ESTE LISTO PRODUCTO SOLO SE AJUSTA
    
    class Detallep extends Conexion{

        private $lote;
        private $fecha_vencimiento;
        private $cod_presentacion;


        public function __construct(){
            global $_ENV;
            parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
        }

        //SET Y GET
        public function setCodp($cod_presentacion){
            $this->cod_presentacion = $cod_presentacion;
        }
        public function setFecha($fecha_vencimiento){
            $this->fecha_vencimiento = $fecha_vencimiento;
        }
        public function setlote($lote){
            $this->lote = $lote;
        }
        
        public function getCodp(){
            return $this->cod_presentacion;
        }
        public function getFecha(){
            return $this->fecha_vencimiento;
        }
        public function getlote(){
            return $this->lote;
        }

        //METODO REGISTRAR
        private function registrar(){
            $registro = "INSERT INTO detalle_productos(cod_presentacion, fecha_vencimiento, lote, status) VALUES(:cod_presentacion, :fecha_vencimiento, :lote, 1)";
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
    
        public function getcrear(){
            return $this->registrar();
        }
        
    }
?>