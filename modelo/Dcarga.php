<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;

class Dcarga extends Conexion
{
    use ValidadorTrait; // Usar el trait para validaciones  
    private $errores = [];
    private $codigo;
    private $cod_carga;
    private $cod_detallep;
    private $cantidad;
    private $codpro;
    private $status;
    private $fecha;
    private $descripcion;
    private $lote;
    private $fecha_vencimiento;
    private $cod_presentacion;
    private $costo;

    public function __construct()
    {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    private function actualizarStock()
    {
        $cod_detallep = $this->getproductod();
        $aumentar = "UPDATE detalle_productos SET stock = stock + :cantidad WHERE cod_detallep = :cod_detallep";
        parent::conectarBD();
        $strExec = $this->conex->prepare($aumentar);
        $strExec->bindParam(':cod_detallep', $this->cod_detallep);
        $strExec->bindParam(':cantidad', $this->cantidad);
        $strExec->execute();
        parent::desconectarBD();
    }

    //OBTENER EL CODIGO RECIEN DE CARGA
    private function carga()
    {
        $sql = "SELECT MAX(cod_carga) as ultimo FROM carga";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        if ($resul == 1) {
            $r = $strExec->fetch(PDO::FETCH_ASSOC);
            parent::desconectarBD();
            return $r['ultimo'];
        } else {
            parent::desconectarBD();
            return $r = 0;
        }
    }

    //OBTENER EL CODIGO ULTIMO DE PRODUCTO
    private function getproductod()
    {

        $sql = "SELECT MAX(cod_detallep) as ultimo FROM detalle_productos WHERE cod_presentacion = :cod_presentacion";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':cod_presentacion', $this->codpro);
        $resul = $strExec->execute();
        if ($resul == 1) {
            //var_dump($this->codpro);
            $r = $strExec->fetch(PDO::FETCH_ASSOC);
            parent::desconectarBD();
            return $r['ultimo'];
        } else {
            //var_dump($this->codpro);
            parent::desconectarBD();
            return $r = 0;
        }
    }

    /* ######  SETTER Y GETTER      ###### */
    public function getcod()
    {
        return $this->codigo;
    }
    public function setcod($codigo)
    {
        $this->codigo = $codigo;
    }
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }
    public function setDes($descripcion)
    {
        $resultado = $this->validarDescripcion($descripcion, 'descripcion', 2, 50);
        if ($resultado === true) {
            $this->descripcion = $descripcion;
        } else {
            $this->errores['descripcion'] = $resultado;
        }
    }

    public function setFechaV($fecha_vencimiento)
    {
        $this->fecha_vencimiento = $fecha_vencimiento;
    }
    public function setlote($lote)
    {
        $this->lote = $lote;
    }
    public function getFechaV()
    {
        return $this->fecha_vencimiento;
    }
    public function getlote()
    {
        return $this->lote;
    }
    public function getDes()
    {
        return $this->descripcion;
    }
    public function getcodcarga()
    {
        return $this->cod_carga;
    }
    public function setcodcarga($cod_carga)
    {
        $this->cod_carga = $cod_carga;
    }
    public function getcodp()
    {
        return $this->cod_detallep;
    }
    public function setcodp($cod_detallep)
    {
        $this->cod_detallep = $cod_detallep;
    }

    public function getcantidad()
    {
        return $this->cantidad;
    }
    public function setcantidad($cantidad)
    {
        $resultado = $this->validarDecimal($cantidad, 'cantidad', 1, 10);
        if ($resultado === true) {
            $this->cantidad = $cantidad;
        } else {
            $this->errores['cantidad'] = $resultado;
        }
    }
    public function setcosto($costo)
    {
        $resultado = $this->validarDecimal($costo, 'costo', 1, 10);
        if ($resultado === true) {
            $this->costo = $costo;
        } else {
            $this->errores['costo'] = $resultado;
        }
    }
    public function getstatus()
    {
        return $this->status;
    }
    public function setstatus($status)
    {
        $this->status = $status;
    }

    public function getcodpro()
    {
        return $this->codpro;
    }
    public function setcodpro($codpro)
    {
        $this->codpro = $codpro;
    }

    public function check()
    {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    // Si quieres acceder a los errores individualmente
    public function getErrores()
    {
        return $this->errores;
    }

    private function crear($productos)
    {
        $r = 0;
        $registro = "INSERT INTO carga(fecha, descripcion, costo, status) VALUES(:fecha, :descripcion, :costo, 1);";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $strExec->bindParam(':fecha', $this->fecha);
        $strExec->bindParam(':descripcion', $this->descripcion);
        $strExec->bindParam(':costo', $this->costo);
        $result = $strExec->execute();
        $carga = $this->conex->lastInsertId(); // Obtener el último ID insertado
        $this->setcodcarga($carga); // Obtener el último código de carga insertado

        if ($result) {

            foreach ($productos as $producto) {
                $codigo1 = $producto['codigo1'];
                $cantidad = $producto['cantidad'];
                // Verifica que el código y la cantidad no estén vacíos
                if (!empty($codigo1) && !empty($cantidad)) {
                    $producto = $this->verificarDetalleProducto($codigo1);
                    $this->setcodpro($codigo1);
                    if ($producto && isset($producto['cod_detallep'])) {
                        // Si el detalle existe, registrar el producto
                        $detallep = $producto['cod_detallep'];
                        $this->setcodp($detallep);
                    } else {
                        parent::desconectarBD();
                        return 0; // Producto no encontrado
                    }
                    $this->setcantidad($cantidad);
                    $r =$carga;

                    if ($r > 0 && $detallep) {
                        $cod_carga = $this->carga(); // Obtener el último código de carga insertado
                        $this->setcodcarga($cod_carga); // Asignar el código de carga a la propiedad
                        $sql = "INSERT INTO detalle_carga(cod_detallep,cod_carga, cantidad) VALUES(:cod_detallep, :cod_carga, :cantidad)";
                        parent::conectarBD();
                        $strExec = $this->conex->prepare($sql);
                        $strExec->bindParam(':cod_detallep', $detallep);
                        $strExec->bindParam(':cod_carga', $this->cod_carga);
                        $strExec->bindParam(':cantidad', $this->cantidad);
                        $resul = $strExec->execute();
                        if ($resul) {
                            // Actualizar stock
                            $this->actualizarStock();
                        } else {
                            $eliminarerror = "DELETE from carga where cod_carga = :cod_carga";
                            parent::conectarBD();
                            $strExec = $this->conex->prepare($eliminarerror);
                            $strExec->bindParam(':cod_carga', $this->cod_carga);
                            $strExec->execute();
                            parent::desconectarBD();
                            return 0; // Fallo en el registro
                        }
                    }
                } else {
                    $eliminar = "DELETE from carga where cod_carga = :cod_carga";
                    parent::conectarBD();
                    $strExec = $this->conex->prepare($eliminar);
                    $strExec->bindParam(':cod_carga', $this->cod_carga);
                    $strExec->execute();
                    parent::desconectarBD();
                    return -1; 
                }
            }
        } else {
            parent::desconectarBD();
            return 0;
        }
        parent::desconectarBD();
        return $r; // Registro exitoso
    }

    public function getcrear($productos)
    {
        return $this->crear($productos);
    }

    public function productod()
    {
        return $this->getproductod();
    }

    private function mostrartodoo($valor)
    {
        // Construir la consulta SQL
        $sql = "SELECT c.cod_carga, c.status, c.descripcion, 
                    pre.cod_presentacion, pre.cod_producto, pre.presentacion, 
                    pre.cantidad_presentacion, p.cod_producto, p.imagen, p.nombre, 
                    dp.cod_detallep,dp.lote,dp.fecha_vencimiento,dc.cod_det_carga, dc.cantidad, m.nombre AS marca
                    FROM detalle_carga dc
                    JOIN carga c ON dc.cod_carga = c.cod_carga
                    JOIN detalle_productos dp ON dc.cod_detallep = dp.cod_detallep
                    JOIN presentacion_producto pre ON dp.cod_presentacion = pre.cod_presentacion
                    JOIN productos p ON pre.cod_producto = p.cod_producto
                    LEFT JOIN marcas m ON p.cod_marca = m.cod_marca
                    WHERE dc.cod_carga = :cod_carga";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':cod_carga', $valor, PDO::PARAM_STR);
        $resul = $strExec->execute();
        $result = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $result;
        } else {
            return []; // Retornar un array vacío si no hay resultados
        }
    }

    public function gettodoo($valor)
    {
        return $this->mostrartodoo($valor);
    }

    //MOSTRAR EN REPORTE TODO
    private function mostrartodo()
    {
        // Construir la consulta SQL
        $sql = "SELECT c.fecha, c.cod_carga, c.status, c.descripcion, 
                    pre.cod_presentacion, pre.cod_producto, pre.presentacion, 
                    pre.cantidad_presentacion, p.cod_producto, p.nombre, 
                    dp.cod_detallep, dp.stock, dc.cod_det_carga, dc.cantidad
                    FROM detalle_carga dc
                    JOIN carga c ON dc.cod_carga = c.cod_carga
                    JOIN detalle_productos dp ON dc.cod_detallep = dp.cod_detallep
                    JOIN presentacion_producto pre ON dp.cod_presentacion = pre.cod_presentacion
                    JOIN productos p ON pre.cod_producto = p.cod_producto";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        $result = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $result;
        } else {
            return []; // Retornar un array vacío si no hay resultados
        }
    }

    public function getodoo()
    {
        return $this->mostrartodo();
    }

    private function obtenerP()
    {
        $sql = "SELECT pre.cod_presentacion, pre.cod_producto, pre.presentacion, p.cod_producto, p.nombre, m.nombre
            FROM presentacion_producto pre
            JOIN productos p ON pre.cod_producto = p.cod_producto
            JOIN marcas m ON p.cod_marca = m.cod_marca";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        $result = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $result;;
        } else {
            return $r = 0;
        }

        if ($resul) {
            return $result;;
        } else {
            return $r = 0;
        }
    }

    public function b_productos($valor)
    {

        $sql = "SELECT
        present.cod_presentacion,
        present.costo,                        
        p.cod_producto,                                  
        p.nombre AS producto_nombre,                                                       
        m.nombre AS marca,                                                                                                  
        CONCAT(present.presentacion, ' x ', present.cantidad_presentacion, ' ', u.tipo_medida) AS presentacion  
        FROM presentacion_producto AS present                 
        JOIN productos AS p ON present.cod_producto = p.cod_producto      
        JOIN unidades_medida AS u ON present.cod_unidad = u.cod_unidad 
        LEFT JOIN marcas AS m ON p.cod_marca = m.cod_marca
        WHERE p.nombre LIKE ? GROUP BY present.cod_presentacion LIMIT 5;";
        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $buscar = '%' . $valor . '%';
        $consulta->bindParam(1, $buscar, PDO::PARAM_STR);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }

    public function verificarDetalleProducto($valor)
    {
        $sql = "SELECT cod_detallep FROM detalle_productos WHERE cod_presentacion = :cod_presentacion";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(':cod_presentacion', $valor);
        $rr = $strExec->execute();
        $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($rr) {
            return $resultado;
        } else {
            return $r = 0;
        }
    }

    public function getP()
    {
        return $this->obtenerP();
    }

    private function mostrar()
    {
        $sql = "select * from detalle_carga";
        parent::conectarBD();
        $strExec = $this->conex->prepare($sql);
        $resul = $strExec->execute();
        $result = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $result;;
        } else {
            return $r = 0;
        }
    }

    public function getmos()
    {
        return $this->mostrar();
    }

    public function getmostrarPorFechas($fechaInicio, $fechaFin)
    {
        $sql = "SELECT c.fecha, c.cod_carga, c.status, c.descripcion, pre.cod_presentacion, pre.cod_producto, pre.presentacion, pre.cantidad_presentacion , p.cod_producto, p.nombre, dp.cod_detallep, dp.stock, dc.cod_det_carga, dc.cantidad
            FROM detalle_carga dc
            JOIN carga c ON dc.cod_carga = c.cod_carga
            JOIN detalle_productos dp ON dc.cod_detallep = dp.cod_detallep
            JOIN presentacion_producto pre ON dp.cod_presentacion = pre.cod_presentacion
            JOIN productos p ON pre.cod_producto = p.cod_producto
            WHERE c.fecha BETWEEN :fechaInicio AND :fechaFin
            GROUP BY pre.cod_presentacion";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':fechaInicio', $fechaInicio);
        $stmt->bindParam(':fechaFin', $fechaFin);
        $stmt->execute();
        $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        return $resultado;
    }
}
