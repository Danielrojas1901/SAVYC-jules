<?php
#1) Requiero conexion 
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ImagenTrait;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

#2) Class + inicializador
class Productos extends Conexion{
    use ImagenTrait;
    use ValidadorTrait;
    #producto
    private $nombre;
    private $marca;
    #presentacion
    private $presentacion;
    private $cant_presentacion;
    private $costo;
    private $ganancia;
    private $excento;

    private $errores = [];

    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
        $this->setDirectorioBase('vista/dist/img');
        $this->setSubcarpeta('productos');
        $this->setImagenDefault('default.png');
    }

#ERROR
    public function check() {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    public function setDatos($datos) {
        foreach ($datos as $key => $value) {
            switch ($key) {
                case 'nombre':
                    $resultado = $this->validarTexto($value, 'nombre', 2, 50);
                    if($resultado === true) {
                        $this->nombre = $value;
                    } else {
                        $this->errores['nombre'] = $resultado;
                    }
                    break;
                case 'marca':
                    if(empty($value)) {
                        $this->marca = null;
                    } else {
                        $resultado = $this->validarNumerico($value, 'marca', 1, 9999);
                        if($resultado === true) {
                            $this->marca = $value;
                        } else {
                            $this->errores['marca'] = $resultado;
                        }
                    }
                    break;
                case 'presentacion':
                    if(empty($value)) {
                        $this->presentacion = null;
                    } else {
                        $resultado = $this->validarTexto($value, 'presentacion', 2, 50);
                        if($resultado === true) {
                            $this->presentacion = $value;
                        } else {
                            $this->errores['presentacion'] = $resultado;
                        }
                    }
                    break;
                case 'cant_presentacion':
                    if(empty($value)) {
                        $this->cant_presentacion = null;
                    } else {
                        $resultado = $this->validarAlfanumerico($value, 'cantidad presentacion', 1, 20);
                        if($resultado === true) {
                            $this->cant_presentacion = $value;
                        } else {
                            $this->errores['cant_presentacion'] = $resultado;
                        }
                    }
                    break;
                case 'costo':
                    if(empty($value)) {
                        $this->costo = 0;
                    } else {
                        $value = (float)$value;
                        $value = $value == (int)$value ? (int)$value : $value;
                        //$resultado = $this->validarDecimal($value, 'costo', 0, 20);
                        $resultado=true;
                        if($resultado === true) {
                            $this->costo = $value;
                        } else {
                            $this->errores['costo'] = $resultado;
                        }
                    }
                    break;
                case 'ganancia':
                    if(empty($value)) {
                        $this->ganancia = 0;
                    } else {
                        $value = (float)$value;
                        $value = $value == (int)$value ? (int)$value : $value;
                        $resultado = $this->validarNumerico($value, 'ganancia', 0, 20);
                        if($resultado === true) {
                            $this->ganancia = $value;
                        } else {
                            $this->errores['ganancia'] = $resultado;
                        }
                    }
                    break;
                case 'excento':
                    $this->excento = $value;
                    break;
                case 'imagen':
                    $this->imagen = $value;
                    break;
            }
        }
    }
#3) GETTER Y SETTER
#Producto
    public function getNombre(){
        return $this->nombre;
    }
    public function setNombre($nombre){
        $resultado = $this->validarTexto($nombre, 'nombre', 2, 50);
        if($resultado === true) {
            $this->nombre = $nombre;
        } else {
            $this->errores['nombre'] = $resultado;
        }
    }
    public function getMarca(){
        return $this->marca;
    }
    public function setMarca($marca){
        if(empty($marca)) {
            $this->marca = null;
        } else {
            $resultado = $this->validarNumerico($marca, 'marca', 1, 9999);
            if($resultado === true) {
                $this->marca = $marca;
            } else {
                $this->errores['marca'] = $resultado;
            }
        }
    }

#Presentacion
    public function getPresentacion(){
        return $this->presentacion;
    }
    public function setPresentacion($presentacion){
        if(empty($presentacion)) {
            $this->presentacion = null;
        } else {
            $resultado = $this->validarTexto($presentacion, 'presentacion', 2, 50);
            if($resultado === true) {
                $this->presentacion = $presentacion;
            } else {
                $this->errores['presentacion'] = $resultado;
            }
        }
    }
    public function getCantPresentacion(){
        return $this->cant_presentacion;
    }
    public function setCantPresentacion($cant_presentacion){
        if(empty($cant_presentacion)) {
            $this->cant_presentacion = null;
        } else {
            $resultado = $this->validarAlfanumerico($cant_presentacion, 'cantidad presentacion', 1, 20);
            if($resultado === true) {
                $this->cant_presentacion = $cant_presentacion;
            } else {
                $this->errores['cant_presentacion'] = $resultado;
            }
        }
    }
    public function getCosto(){
        return $this->costo;
    }
    public function setCosto($costo){
        if(empty($costo)) {
            $this->costo = 0;
            return;
        }
        $costo = (float)$costo;
        $costo = $costo == (int)$costo ? (int)$costo : $costo;
        $resultado = $this->validarDecimal($costo, 'costo', 0, 20);
        if($resultado === true) {
            $this->costo = $costo;
        } else {
            $this->errores['costo'] = $resultado;
        }
    }
    public function getGanancia(){
        return $this->ganancia;
    }
    public function setGanancia($ganancia){
        if(empty($ganancia)) {
            $this->ganancia = 0;
            return;
        }
        $ganancia = (float)$ganancia;
        $ganancia = $ganancia == (int)$ganancia ? (int)$ganancia : $ganancia;
        $resultado = $this->validarNumerico($ganancia, 'ganancia', 0, 20);
        if($resultado === true) {
            $this->ganancia = $ganancia;
        } else {
            $this->errores['ganancia'] = $resultado;
        }
    }
    public function getExcento(){
        return $this->excento;
    }
    public function setExcento($excento){
        $this->excento = $excento;
    }


#4) Metodos CRUD, etc
/*======================================================
TOTAL EN COSTO/VENTA DEL INVENTARIO
========================================================*/
private function inventario_costo(){
    $sql="SELECT
    COALESCE(ROUND(SUM(present.costo * COALESCE(dp.stock, 0)), 2), 0) AS total_costo,
    COALESCE(ROUND(SUM((present.costo * (1 + present.porcen_venta / 100)) * COALESCE(dp.stock, 0)), 2), 0) AS total_venta
    FROM productos AS p
    JOIN categorias AS c ON p.cod_categoria = c.cod_categoria
    JOIN presentacion_producto AS present ON p.cod_producto = present.cod_producto
    JOIN unidades_medida AS u ON present.cod_unidad = u.cod_unidad
    LEFT JOIN detalle_productos AS dp ON dp.cod_presentacion = present.cod_presentacion;";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    $resul = $strExec->execute();   
    $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resul){
        return $datos;
    }else{
        return $r = 0;
    }
}

public function getinventario_costo(){
    return $this->inventario_costo();
}

/*======================================================================
REGISTRAR PRODUCTO con CATEGORIA + REGISTRAR PRESENTACION con UNIDAD
========================================================================*/
private function registrar($unidad, $categoria){ 
    $registro = "INSERT INTO productos(cod_categoria,nombre,cod_marca,imagen) VALUES(:cod_categoria,:nombre, :marca, :imagen)";
    parent::conectarBD();
    $strExec = $this->conex->prepare($registro);
    $strExec->bindParam(':cod_categoria',$categoria);
    $strExec->bindParam(':nombre', $this->nombre);
    $strExec->bindParam(':marca', $this->marca);
    $strExec->bindParam(':imagen', $this->imagen);
    $resul = $strExec->execute();
    if($resul){
        $nuevo_cod=$this->conex->lastInsertId(); 
        
            $sqlproducto = "INSERT INTO presentacion_producto(cod_unidad,cod_producto,presentacion,cantidad_presentacion,costo,porcen_venta,excento) VALUES(:cod_unidad,:cod_producto,:presentacion,:cantidad_presentacion,:costo,:porcen_venta,:excento)";  
            $strExec=$this->conex->prepare($sqlproducto);

                $strExec->bindParam(':cod_unidad',$unidad);
                $strExec->bindParam(':cod_producto',$nuevo_cod);
                $strExec->bindParam(':presentacion',$this->presentacion);
                $strExec->bindParam(':cantidad_presentacion',$this->cant_presentacion);
                $strExec->bindParam(':costo',$this->costo);
                $strExec->bindParam(':porcen_venta',$this->ganancia);
                $strExec->bindParam(':excento',$this->excento);
                $execute = $strExec->execute();
                if($execute){
                $r=1;
                }else{
                    $r = 0;
                }
                parent::desconectarBD();
                return $r;

            } else{
                parent::desconectarBD();
                return $resul=0;
            }
    }

public function getRegistrar($unidad,$categoria){
    return $this->registrar($unidad,$categoria);
}

/*==============================
Consultar solo las UNIDADES activas
================================*/
public function consultarUnidad(){
    $sql = "SELECT * FROM unidades_medida WHERE status=1";
    parent::conectarBD();
    $consulta = $this->conex->prepare($sql);
    $resul = $consulta->execute();
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resul){
        return $datos;
    }return $r = 0;
}

/*==============================
Consultar solo las CATEGORIAS activas
================================*/
public function consultarCategoria(){
    $registro = "SELECT * FROM categorias WHERE status=1";
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


public function consultarMarca() {
    $sql = "SELECT * FROM marcas WHERE status=1";
    parent::conectarBD();
    $consulta = $this->conex->prepare($sql);
    $resultado = $consulta->execute();
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resultado) {
        return $datos;
    } else {
        return $r=0;
    }
}

/*==============================
REGISTRAR PRESENTACION A UN PRODUCTO EXISTENTE
================================*/

public function registrar2($unidad, $cod_producto){
        $sql2='INSERT INTO presentacion_producto(cod_unidad,cod_producto,presentacion,cantidad_presentacion,costo,porcen_venta,excento) VALUES(:cod_unidad,:cod_producto,:presentacion,:cantidad_presentacion,:costo,:porcen_venta,:excento)';
        parent::conectarBD();
        $strExec=$this->conex->prepare($sql2);
        $strExec->bindParam(':cod_unidad',$unidad);
        $strExec->bindParam(':cod_producto',$cod_producto);
        $strExec->bindParam(':presentacion',$this->presentacion);
        $strExec->bindParam(':cantidad_presentacion',$this->cant_presentacion);
        $strExec->bindParam(':costo',$this->costo);
        $strExec->bindParam(':porcen_venta',$this->ganancia);
        $strExec->bindParam(':excento',$this->excento);
        $r = $strExec->execute();
        parent::desconectarBD();
        if($r){
            $res=1;
            }else{
                $res = 0;
            }
            return $res;
    }

/*==============================
MOSTRAR PRODUCTO categoria, unidad y su presentación (tabla)
================================*/

private function mostrar(){
    $sql = "SELECT
    p.imagen,
    p.cod_producto,
    p.nombre,
    m.nombre AS marca,
    m.cod_marca AS cod_marca,
    c.nombre AS cat_nombre,
    c.cod_categoria AS cat_codigo,
    present.cod_presentacion,
    present.presentacion,
    present.cantidad_presentacion,
    present.costo,
    present.porcen_venta,
    present.excento,
    u.tipo_medida,
    u.cod_unidad,
    (CONCAT(present.presentacion,'  ',present.cantidad_presentacion, ' x ', u.tipo_medida)) AS presentacion_concat, #Concatena
    COALESCE(ROUND(SUM(dp.stock), 2), 0) AS stock_total
    FROM productos AS p
    LEFT JOIN marcas as m ON p.cod_marca = m.cod_marca
    JOIN categorias AS c ON p.cod_categoria = c.cod_categoria
    JOIN presentacion_producto AS present ON p.cod_producto = present.cod_producto
    JOIN unidades_medida AS u ON present.cod_unidad = u.cod_unidad
    LEFT JOIN detalle_productos AS dp ON dp.cod_presentacion = present.cod_presentacion
    GROUP BY present.cod_presentacion"; #Se agrupa por el código de presentacion para separar las distintas presentaciones q puede haber
    parent::conectarBD();
    $consulta = $this->conex->prepare($sql);
    $resul = $consulta->execute();
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resul){
        return $datos;
    }else{
        return [];
    }
}

public function getmostrar(){
    return $this->mostrar();
}

/*======================================
EDITAR PRODUCTO y categoria, unidad y su presentación
========================================*/
public  function editar($present,$product,$categoria,$unidad){
    $sql="UPDATE productos SET 
    cod_categoria=:cod_categoria,
    nombre=:nombre,
    cod_marca=:marca,
    imagen=:imagen
    WHERE cod_producto=:cod_producto";
    parent::conectarBD();
    $strExec=$this->conex->prepare($sql);
    $strExec->bindParam(':cod_categoria', $categoria);
    $strExec->bindParam(':nombre', $this->nombre);
    $strExec->bindParam(':marca',$this->marca);
    $strExec->bindParam(':imagen',$this->imagen);
    $strExec->bindParam(':cod_producto',$product);
    $result=$strExec->execute();
    if($result){
        $sqlPresent = "UPDATE presentacion_producto SET
        presentacion=:presentacion,
        cantidad_presentacion=:cant_presentacion,
        costo=:costo,
        excento=:excento,
        porcen_venta=:porcen_venta,
        cod_unidad=:cod_unidad
        WHERE cod_presentacion=:cod_presentacion";
            $strExec = $this->conex->prepare($sqlPresent);
            $strExec->bindParam(':presentacion', $this->presentacion);
            $strExec->bindParam(':cant_presentacion', $this->cant_presentacion);
            $strExec->bindParam(':costo',$this->costo);
            $strExec->bindParam(':excento',$this->excento);
            $strExec->bindParam(':porcen_venta',$this->ganancia);
            $strExec->bindParam(':cod_unidad', $unidad);
            $strExec->bindParam(':cod_presentacion',$present);
            $resultado=$strExec->execute() ? 1 : 0;
            parent::desconectarBD();
            return $resultado;
    }
    parent::desconectarBD();
    return 0;
}

/*======================================
ELIMINAR PRODUCTO
========================================*/

public function eliminar($p, $pp) {
    // Verificar si la presentación tiene algún detalle con stock > 0
    $sqls = "SELECT * FROM detalle_productos WHERE cod_presentacion = :cod_presentacion AND stock > 0";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sqls);
    $strExec->bindParam(':cod_presentacion', $pp, PDO::PARAM_INT);
    $strExec->execute();
    $stock = $strExec->fetchAll(PDO::FETCH_ASSOC);

    if ($stock) {
        parent::desconectarBD();
        return 'error_stock';  // No se puede eliminar porque hay stock
        exit;
    }

    // Si no tiene stock en los detalles, eliminar la presentación
    $sqld = "DELETE FROM presentacion_producto WHERE cod_presentacion = :cod_presentacion";
    $strExec = $this->conex->prepare($sqld);
    $strExec->bindParam(':cod_presentacion', $pp, PDO::PARAM_INT);
    $delete = $strExec->execute();

    if ($delete) {
        // Ver si la presentación eliminada era la última asociada al producto
        $sqlcheck= "SELECT * FROM presentacion_producto WHERE cod_producto = :cod_producto";
        $strExec = $this->conex->prepare($sqlcheck);
        $strExec->bindParam(':cod_producto', $p, PDO::PARAM_INT);
        $strExec->execute();
        $check = $strExec->fetch(PDO::FETCH_ASSOC);

        if (!$check) {
            // Si no hay más presentaciones, eliminar el producto
            $sqlp = "DELETE FROM productos WHERE cod_producto = :cod_producto";
            $strExec = $this->conex->prepare($sqlp);
            $strExec->bindParam(':cod_producto', $p, PDO::PARAM_INT);
            $deleteproducto = $strExec->execute();
            if ($deleteproducto) {
                // Eliminar la imagen del producto si existe y no es la imagen por defecto
                $sqlImagen = "SELECT imagen FROM productos WHERE cod_producto = :cod_producto";
                $strExec = $this->conex->prepare($sqlImagen);
                $strExec->bindParam(':cod_producto', $p, PDO::PARAM_INT);
                $strExec->execute();
                $imagen = $strExec->fetch(PDO::FETCH_ASSOC);
                
                if ($imagen && $imagen['imagen'] !== $this->directorioBase . $this->subcarpeta . $this->imagenDefault) {
                    $this->eliminar($imagen['imagen'], true);
                }
                parent::desconectarBD();
                return 'producto';
            }
            parent::desconectarBD();
            return 'error_delete';
        }
        parent::desconectarBD();
        return 'success';  // Se eliminó la presentación, pero quedan otras asociadas al producto
    }
    parent::desconectarBD();
    return 'error_delete'; // No se pudo eliminar la presentación
}

/*======================================================================
BUSCAR PRODUCTO (para que si ya existe asignarle una nueva presentacion)
========================================================================*/

public function buscar($nombrep){
    $sql="SELECT                
    p.cod_producto,
    c.cod_categoria,                                 
    p.nombre AS producto_nombre,                                   
    m.nombre AS marca,
    m.cod_marca AS cod_marca,                                                                        
    c.nombre AS cat_nombre                          
    FROM productos AS p
    JOIN categorias AS c ON p.cod_categoria = c.cod_categoria
    JOIN marcas AS m ON p.cod_marca = m.cod_marca
    WHERE p.nombre LIKE ? GROUP BY p.nombre, m.nombre LIMIT 5;";
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
/*======================================================================
BUSCAR DETALLE DE PRODUCTO 
========================================================================*/
public function consultardetalleproducto($cod_presentacion){
    $sql = 'SELECT
    detp.lote,
    detp.cod_detallep,
    detp.fecha_vencimiento,
    detp.stock
    FROM detalle_productos AS detp JOIN presentacion_producto AS present ON detp.cod_presentacion=:cod_presentacion
    GROUP BY detp.cod_detallep';
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    $strExec->bindParam(':cod_presentacion',$cod_presentacion);
    $resul=$strExec->execute();
    $array=$strExec->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resul){
        return $array;
    }else{
        return [];
    }
}

/*======================================================================
PRODUCTOS MAS VENDIDOS
========================================================================*/
public function bestseller(){
    $sql="SELECT
    present.cod_presentacion,
    p.nombre,
    (CONCAT(present.presentacion,'  ',present.cantidad_presentacion, ' x ', u.tipo_medida)) AS presentacion_concat, #Concatena    p.nombre,
    present.costo,
    present.porcen_venta,
    m.nombre AS nombre_marca,
    present.excento,
    (CONCAT(SUM(detv.cantidad), ' ', u.tipo_medida)) AS vendido
    FROM productos AS p
    JOIN presentacion_producto AS present ON p.cod_producto = present.cod_producto
    JOIN unidades_medida AS u ON present.cod_unidad = u.cod_unidad
    LEFT JOIN detalle_productos AS dp ON dp.cod_presentacion = present.cod_presentacion
    LEFT JOIN marcas AS m ON m.cod_marca = p.cod_marca
    JOIN detalle_ventas AS detv ON detv.cod_detallep = dp.cod_detallep
    GROUP BY present.cod_presentacion DESC LIMIT 5;";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    $resul=$strExec->execute();
    $array=$strExec->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resul){
        return $array;
    }else{
        return [];
    }
}
/*======================================================================
    FILTRADO POR CATEGORIA
========================================================================*/
public function productocategoria($cod_categoria){
    $sql= "SELECT
	present.cod_presentacion,
    p.nombre,
    m.nombre AS marca,
    c.cod_categoria,
    c.nombre AS cat_nombre,
    (CONCAT(present.presentacion,' ',present.cantidad_presentacion, ' x ', u.tipo_medida)) AS presentacion_concat
    FROM productos AS p
    JOIN marcas AS m ON p.cod_marca = m.cod_marca
    JOIN categorias AS c ON p.cod_categoria = c.cod_categoria
    JOIN presentacion_producto AS present ON p.cod_producto = present.cod_producto
    JOIN unidades_medida AS u ON present.cod_unidad = u.cod_unidad
    WHERE c.cod_categoria = :cod_categoria";
    parent::conectarBD();
    $strExec = $this->conex->prepare($sql);
    $strExec->bindParam('cod_categoria',$cod_categoria);
    $result = $strExec->execute();
    $array=$strExec->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($result){
        return $array;
    }else{
        return [];
    }
    }

    /*======================================================================
    PRODUCTOS CON MENOR STOCK
    =========================================================================*/
    public function menorStock(){
        $sql = "SELECT
        present.cod_presentacion,
        p.nombre,
        m.nombre AS marca,
        COALESCE(SUM(dp.stock), 0) as stock,
        (CONCAT(present.presentacion,' ',present.cantidad_presentacion, ' x ', u.tipo_medida)) AS presentacion_concat
        FROM productos AS p
        JOIN presentacion_producto AS present ON p.cod_producto = present.cod_producto
        JOIN unidades_medida AS u ON present.cod_unidad = u.cod_unidad
        LEFT JOIN marcas AS m ON p.cod_marca = m.cod_marca
        LEFT JOIN detalle_productos dp ON dp.cod_presentacion = present.cod_presentacion
        WHERE dp.stock < 5
        GROUP BY present.cod_presentacion, p.nombre, present.presentacion, present.cantidad_presentacion, u.tipo_medida
        ORDER BY stock ASC
        LIMIT 5";
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

    /*======================================================================
    PRODUCTOS PROXIMOS A VENCER - NOTIFICACIONES
    =========================================================================*/
    public function consultarProductosProximosVencer($dias_alerta = 30) {
        $hoy = date('Y-m-d');
        $alerta = date('Y-m-d', strtotime("+$dias_alerta days"));

        $sql = "SELECT 
            dp.cod_detallep,
            dp.lote,
            dp.fecha_vencimiento,
            dp.stock,
            p.nombre AS producto_nombre,
            present.presentacion,
            present.cantidad_presentacion,
            u.tipo_medida,
            m.nombre AS marca,
            (CONCAT(present.presentacion, ' ', present.cantidad_presentacion, ' x ', u.tipo_medida)) AS presentacion_concat
        FROM detalle_productos dp
        JOIN presentacion_producto present ON dp.cod_presentacion = present.cod_presentacion
        JOIN productos p ON present.cod_producto = p.cod_producto
        JOIN unidades_medida u ON present.cod_unidad = u.cod_unidad
        LEFT JOIN marcas m ON p.cod_marca = m.cod_marca
        WHERE dp.fecha_vencimiento IS NOT NULL 
        AND dp.fecha_vencimiento != '0000-00-00'
        AND dp.stock > 0
        AND dp.fecha_vencimiento <= :fecha_alerta
        ORDER BY dp.fecha_vencimiento ASC";

        try {
            parent::conectarBD();
            $stmt = $this->conex->prepare($sql);
            $stmt->bindParam(':fecha_alerta', $alerta);
            $stmt->execute();
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            parent::desconectarBD();
        } catch (PDOException $e) {
            error_log("Error en consultarProductosProximosVencer: " . $e->getMessage());
            return [];
        }

        $notificaciones = [];
        foreach ($productos as $producto) {
            $dias_restantes = intval((strtotime($producto['fecha_vencimiento']) - strtotime($hoy)) / 86400);
            
            $descripcion_producto = $producto['producto_nombre'];
            if (!empty($producto['marca'])) {
                $descripcion_producto .= ' (' . $producto['marca'] . ')';
            }
            $descripcion_producto .= ' - ' . $producto['presentacion_concat'];
            if (!empty($producto['lote'])) {
                $descripcion_producto .= ' - Lote: ' . $producto['lote'];
            }

            if ($producto['fecha_vencimiento'] < $hoy) {
                $notificaciones[] = [
                    'descripcion' => 'Producto vencido: ' . $descripcion_producto,
                    'fecha_vencimiento' => $producto['fecha_vencimiento'],
                    'dias_restantes' => $dias_restantes,
                    'stock' => $producto['stock'],
                    'lote' => $producto['lote'],
                    'tipo' => 'inventario',
                    'estado' => 'vencida'
                ];
            } else {
                $notificaciones[] = [
                    'descripcion' => 'Producto próximo a vencer: ' . $descripcion_producto,
                    'fecha_vencimiento' => $producto['fecha_vencimiento'],
                    'dias_restantes' => $dias_restantes,
                    'stock' => $producto['stock'],
                    'lote' => $producto['lote'],
                    'tipo' => 'inventario',
                    'estado' => 'proxima'
                ];
            }
        }

        return $notificaciones;
    }
}