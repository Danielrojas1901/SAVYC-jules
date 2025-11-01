<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;

class Descarga extends Conexion{
    use ValidadorTrait; // Usar el trait para validaciones
    private $errores = []; // Arreglo para almacenar errores de validación
    #Descarga
    private $fecha;
    private $descripcion;
    private $status;
    private $cantidad;
    private $costo;

    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function getfecha(){
        return $this->fecha;
    }
    public function setfecha($fecha){
        $this->fecha = $fecha;
    }
    public function getdescripcion(){
        return $this->descripcion;
    }
    public function setdescripcion($descripcion){
        $res = $this->validarDescripcion($descripcion, 'descripcion', 5, 45);
        if ($res === true) {
            $this->descripcion = $descripcion;
        } else {
            $this->errores['descripcion'] = $res;
        }
    }
    public function setcosto($costo){
        $res = $this->validarDecimal($costo, 'costo', 1, 20);
        if ($res === true) {
            $this->costo = $costo;
        } else {
            $this->errores['costo'] = $res;
        }
    }
    
    public function getcantidad(){
        return $this->cantidad;
    }
    public function setcantidad($cantidad){
        $res = $this->validarDecimal($cantidad, 'cantidad', 1, 20);
        if ($res === true) {
            $this->cantidad = $cantidad;
        } else {
            $this->errores['cantidad'] = $res;
        }
    }

        public function getErrores()
    {
        return $this->errores;
    }

    public function getConex() {
        return $this->conex;
    }

    // Chequear si hay errores
    public function check() {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }
    

/*================================
    REGISTRAR DESCARGA (TRANSACCION)
===========================*/
    public function registrar($cod_detalle) {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            $sql = 'INSERT INTO descarga(fecha, descripcion, costo, status) VALUES(:fecha, :descripcion, :costo, 1)';
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':fecha', $this->fecha);
            $strExec->bindParam(':descripcion', $this->descripcion);
            $strExec->bindParam(':costo', $this->costo);
            $resul = $strExec->execute();
            
            if (!$resul) {
                throw new Exception("Error al registrar la descarga");
            }
    
            $ultimocodigo = $this->conex->lastInsertId();
            
            // Insertar los detalles de descarga
            foreach ($cod_detalle as $det) {
                // Validar que los datos de cantidad y cod_detallep estén presentes
                if (empty($det['cantidad']) || empty($det['cod_detallep'])) {
                    throw new Exception("La cantidad o el código del detalle no son válidos.");
                }
                // Insertar en la tabla 'detalle_descarga'
                $sql2 = "INSERT INTO detalle_descarga(cod_detallep, cod_descarga, cantidad) VALUES(:cod_detallep, :cod_descarga, :cantidad)";
                $sentencia = $this->conex->prepare($sql2);
                $sentencia->bindParam(':cod_detallep', $det['cod_detallep']);
                $sentencia->bindParam(':cod_descarga', $ultimocodigo);
                $sentencia->bindParam(':cantidad', $det['cantidad']);
                $detalle = $sentencia->execute();

                if (!$detalle) {
                    throw new Exception("Error al registrar el detalle de descarga para el producto con código " . $det['cod_detallep']);
                }

                // Actualizar el stock en detalle_productos
                $sql3 = "UPDATE detalle_productos SET stock = stock - :cantidad WHERE cod_detallep = :cod_detallep";
                $updateStock = $this->conex->prepare($sql3);
                $updateStock->bindParam(':cantidad', $det['cantidad']);
                $updateStock->bindParam(':cod_detallep', $det['cod_detallep']);
                $stockactualizado = $updateStock->execute();

                if (!$stockactualizado) {
                    throw new Exception("Error al actualizar el stock para el producto con código " . $det['cod_detallep']);
                }
            }
            
            // Si todo fue exitoso, confirmar la transacción
            $this->conex->commit();
            parent::desconectarBD();
            return $ultimocodigo; 
        } catch (Exception $e) {
            // Si algo salió mal, revertir los cambios
            $this->conex->rollBack();
            parent::desconectarBD();
            error_log($e->getMessage()); // Registrar el error
            return 0;
        }
    }
    
// DETALLE DESCARGA (Modal)
    public function consultardetalledescarga($cod_descarga){
        $sql = 'SELECT
		de.cod_descarga,
        dd.cod_det_descarga,
        dd.cod_detallep,
        dd.cantidad,
        detp.cod_detallep,
        detp.cod_presentacion,
        detp.stock,
        detp.lote,
        present.cod_producto,
        (CONCAT(present.presentacion, " x ",present.cantidad_presentacion, " x ", u.tipo_medida)) AS presentacion_concat,
        pro.nombre,
        pro.imagen
        FROM descarga AS de 
        JOIN detalle_descarga AS dd ON de.cod_descarga = dd.cod_descarga
        JOIN detalle_productos AS detp ON detp.cod_detallep = dd.cod_detallep
        JOIN presentacion_producto AS present ON present.cod_presentacion=detp.cod_presentacion
        JOIN unidades_medida AS u ON u.cod_unidad = present.cod_unidad
        JOIN productos AS pro ON pro.cod_producto = present.cod_producto
        WHERE de.cod_descarga = :cod_descarga';
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':cod_descarga', $cod_descarga, PDO::PARAM_INT);
        $resul=$strExec->execute();
        $array=$strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $array;
        }else{
            return [];
        }
    }

    //Mostrar productos en el datatable
    public function consultardescarga(){
        $sql = 'SELECT * FROM descarga';
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        $array = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $array;
        }else{
            return [];
        }
    }

    //Buscar productos para seleccionar
    public function buscar($nombrep){
        $sql="SELECT                
        det.cod_detallep,
        det.cod_presentacion,
        det.stock,
        det.lote,
        u.tipo_medida,
        present.cod_unidad,
        present.costo,                             
        pro.nombre AS producto_nombre, 
        m.nombre AS producto_marca,
        (CONCAT(present.presentacion,' x ',present.cantidad_presentacion, ' ', u.tipo_medida)) AS presentacion_concat, #Concatena                                                                                                         
        present.cod_producto               
        FROM detalle_productos AS det JOIN presentacion_producto AS present ON det.cod_presentacion = present.cod_presentacion      
        JOIN productos AS pro ON pro.cod_producto=present.cod_producto 
        JOIN unidades_medida AS u ON present.cod_unidad = u.cod_unidad
        LEFT JOIN marcas AS m ON pro.cod_marca = m.cod_marca
        WHERE pro.nombre LIKE ? AND det.stock != 0 GROUP BY det.cod_detallep LIMIT 7;";
        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $buscar = '%' . $nombrep. '%';
        $consulta->bindParam(1, $buscar, PDO::PARAM_STR);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $datos;
        }else{
            return [];
        }
    }

    //Para el reporte todos
    public function consultardescargapdf(){
        $sql="SELECT
        de.descripcion,
        de.fecha,
        m.nombre AS marca,
        detd.cantidad,
        detp.lote,
        present.cod_presentacion,
        pro.nombre, present.presentacion, present.cantidad_presentacion 
        FROM descarga AS de 
        JOIN detalle_descarga AS detd ON de.cod_descarga=detd.cod_descarga
        JOIN detalle_productos AS detp ON detp.cod_detallep = detd.cod_detallep
        JOIN presentacion_producto AS present ON present.cod_presentacion = detp.cod_presentacion
        JOIN productos AS pro ON pro.cod_producto = present.cod_producto
        LEFT JOIN marcas m ON pro.cod_marca = m.cod_marca
        GROUP BY detd.cod_det_descarga;";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $r = $strExec->execute();
        $array = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($r){
            return $array;
        }else{
            return [];
        }
    }

    //Datatable para reporte
    public function consultardescargar(){
        $sql="SELECT
        de.cod_descarga,
        de.descripcion,
        de.fecha,
        detp.lote,
        detd.cantidad,
        (CONCAT(pro.nombre, ' x ', present.presentacion, ' ', present.cantidad_presentacion)) AS producto_concat
        FROM descarga AS de JOIN detalle_descarga AS detd ON de.cod_descarga=detd.cod_descarga
        JOIN detalle_productos AS detp ON detp.cod_detallep = detd.cod_detallep
        JOIN presentacion_producto AS present ON present.cod_presentacion = detp.cod_presentacion
        JOIN productos AS pro ON pro.cod_producto = present.cod_producto";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $r = $strExec->execute();
        $array = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($r){
            return $array;
        }else{
            return [];
        }
    }

    public function descargafecha($fi, $ff){
        $sql=" SELECT 
        de.descripcion,
        de.fecha,
        m.nombre AS marca,
        detd.cantidad,
        detp.lote,
        present.cod_presentacion,
        pro.nombre, present.presentacion, present.cantidad_presentacion 
        FROM descarga AS de 
        JOIN detalle_descarga AS detd ON de.cod_descarga=detd.cod_descarga
        JOIN detalle_productos AS detp ON detp.cod_detallep = detd.cod_detallep
        JOIN presentacion_producto AS present ON present.cod_presentacion = detp.cod_presentacion
        JOIN productos AS pro ON pro.cod_producto = present.cod_producto
        LEFT JOIN marcas m ON pro.cod_marca = m.cod_marca
        WHERE de.fecha BETWEEN :fechainicio AND :fechafin
        ORDER BY de.cod_descarga ASC;";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':fechainicio', $fi);
        $stmt->bindParam(':fechafin', $ff);
        $resul=$stmt->execute();
        $datos=$stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if($resul){
            return $datos;
        }else{
            return [];
        }
    }
}