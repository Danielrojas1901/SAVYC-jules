<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class Venta extends Conexion
{

    use ValidadorTrait;
    private $total;
    private $fecha;
    private $descuento;
    private $condicion;
    private $fecha_v;
    private $subtotal;
    private $impuesto;
    private $errores = [];

    public function __construct()
    {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function setdatav($data)
    {
        if ($this->validarDecimal($data['total_general'], 'total_general')=== true) {
            $this->total = $data['total_general'];
        } else {
            $this->errores['total'] = $this->validarDecimal($data['total_general'], 'total_general');
        }
        if ($this->validardatetime($data['fecha_hora'], 'fecha_hora')=== true) {
            $this->fecha = $data['fecha_hora'];
        } else {
            $this->errores['fecha'] = $this->validardatetime($data['fecha_hora'], 'fecha_hora');
        }
        if (isset($data['fecha_v'])) {
            if ($this->validarFecha($data['fecha_v'], 'fecha_v')=== true) {
                $this->fecha_v = $data['fecha_v'];
            } else {
                $this->errores['fecha_v'] = $this->validarFecha($data['fecha_v'], 'fecha_v');
            }
        } else {
            $this->fecha_v = null;
        }
        if ($this->validarDecimal($data['subtotal'], 'subtotal')=== true) {
            $this->subtotal = $data['subtotal'];
        }else{
            $this->errores['subtotal'] = $this->validarDecimal($data['subtotal'], 'subtotal');
        }
        if ($this->validarDecimal($data['impuesto'], 'impuesto total')=== true) {
            $this->impuesto = $data['impuesto'];
        }else{
            $this->errores['impuesto'] = $this->validarDecimal($data['impuesto'], 'impuesto total');
        }
        $this->condicion = $data['condicion'];
    }

    public function check()
    {
        if (!empty($this->errores)) {
            return $this->errores;
        } else {
            return true;
        }
    }

    public function get_total()
    {
        return $this->total;
    }
    public function set_total($valor)
    {
        $this->total = $valor;
    }

    public function getErrores(){
        return $this->errores;
    }

    public function getfecha()
    {
        return $this->fecha;
    }
    public function setfecha($valor)
    {
        $this->fecha = $valor;
    }

    public function getdescuento()
    {
        return $this->descuento;
    }
    public function getconex(){
        return $this->conex;
    }
    public function setdescuento($valor)
    {
        $this->descuento = $valor;
    }

    public function consultar()
    {
        $registro = "SELECT 
                        v.*,
                        c.nombre, 
                        c.apellido, 
                        c.cedula_rif,
                        c.telefono, 
                        c.email, 
                        c.direccion, 
                        IFNULL(SUM(p.monto_total), 0) AS total_pagado,
                        (v.total - IFNULL(SUM(p.monto_total), 0)) AS saldo_restante
                    FROM ventas v 
                    INNER JOIN clientes c ON v.cod_cliente = c.cod_cliente 
                    LEFT JOIN pago_recibido p ON v.cod_venta = p.cod_venta 
                    GROUP BY v.cod_venta
                    ORDER BY v.cod_venta;";
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
    public function getb_productos($valor){
        return $this->b_productos($valor);
    }

    private function b_productos($valor)
    {
        $sql = "SELECT
        present.cod_presentacion,
        p.cod_producto,
        p.nombre AS producto_nombre,
        present.costo,
        m.nombre AS marca,
        present.excento,
        present.porcen_venta,
        u.cod_unidad,
        u.tipo_medida, 
        c.nombre AS cat_nombre,
        CONCAT(present.presentacion, ' x ', present.cantidad_presentacion, ' x ', u.tipo_medida) AS presentacion,
        COALESCE(ROUND(SUM(dp.stock), 2), 0) AS total_stock 
        FROM presentacion_producto AS present
        JOIN productos AS p ON present.cod_producto = p.cod_producto
        LEFT JOIN marcas AS m ON p.cod_marca = m.cod_marca
        JOIN categorias AS c ON p.cod_categoria = c.cod_categoria
        JOIN unidades_medida AS u ON present.cod_unidad = u.cod_unidad
        LEFT JOIN detalle_productos AS dp ON dp.cod_presentacion = present.cod_presentacion
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

    public function getregistrar($cliente, $productos){
        return $this->registrar($cliente, $productos);
    }

    private function registrar($cliente, $productos)
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            foreach ($productos as $producto) {
                if ($producto['cantidad'] == 0) {
                    throw new Exception("la cantidad del producto es invalida");
                }
            }
            $registro = "INSERT INTO ventas(cod_cliente, condicion_pago, fecha_vencimiento, subtotal_v, total, impuesto_v, fecha, status) VALUES(:cod_cliente, :condicion_pago, :fecha_vencimiento, :subtotal_v, :total, :impuesto_v, :fecha, 1)";
            $strExec = $this->conex->prepare($registro);
            $strExec->bindParam(':cod_cliente', $cliente);
            $strExec->bindParam(':condicion_pago', $this->condicion);
            $strExec->bindParam(':fecha_vencimiento', $this->fecha_v);
            $strExec->bindParam(':subtotal_v', $this->subtotal);
            $strExec->bindParam(':total', $this->total);
            $strExec->bindParam(':impuesto_v', $this->impuesto);
            $strExec->bindParam(':fecha', $this->fecha);
            $resul = $strExec->execute();
            if (!$resul) {
                throw new Exception("Error al registrar la venta");
            }
            $nuevo_cod = $this->conex->lastInsertId();
            foreach ($productos as $producto) {
                $cod_presentacion = $producto['codigo'];
                $cantidad_a_vender = $producto['cantidad'];
                $precio = $producto['precio'];
                $costo = $producto['costo'];

                $loteQuery = "SELECT cod_detallep, stock FROM detalle_productos 
                            WHERE cod_presentacion = :cod_presentacion AND stock > 0 
                            ORDER BY cod_detallep ASC";
                $loteStmt = $this->conex->prepare($loteQuery);
                $loteStmt->bindParam(':cod_presentacion', $cod_presentacion);
                $loteStmt->execute();
                $lotes = $loteStmt->fetchAll(PDO::FETCH_ASSOC);

                $cantidad_restante = $cantidad_a_vender;
                foreach ($lotes as $lote) {
                    $cod_detallep = $lote['cod_detallep'];
                    $stock_disponible = $lote['stock'];

                    if ($cantidad_restante <= 0) {
                        break;
                    }
                    if ($stock_disponible >= $cantidad_restante) {
                        $nuevo_stock = $stock_disponible - $cantidad_restante;
                        $detalleQuery = "INSERT INTO detalle_ventas(cod_venta, cod_detallep, cantidad, importe, costo_unitario) 
                                        VALUES(:cod_venta, :cod_detallep, :cantidad, :importe, :costo_unitario)";
                        $detalleStmt = $this->conex->prepare($detalleQuery);
                        $detalleStmt->bindParam(':cod_venta', $nuevo_cod);
                        $detalleStmt->bindParam(':cod_detallep', $cod_detallep);
                        $detalleStmt->bindParam(':costo_unitario', $costo);
                        $detalleStmt->bindParam(':cantidad', $cantidad_restante);
                        $importe = $precio * $cantidad_restante;
                        $detalleStmt->bindParam(':importe', $importe);

                        if (!$detalleStmt->execute()) {
                            throw new Exception("Error al registrar el detalle de venta para el producto con lote $cod_detallep");
                        }
                        $actualizarStockQuery = "UPDATE detalle_productos SET stock = :nuevo_stock WHERE cod_detallep = :cod_detallep";
                        $actualizarStockStmt = $this->conex->prepare($actualizarStockQuery);
                        $actualizarStockStmt->bindParam(':nuevo_stock', $nuevo_stock);
                        $actualizarStockStmt->bindParam(':cod_detallep', $cod_detallep);
                        if (!$actualizarStockStmt->execute()) {
                            throw new Exception("Error al actualizar el stock para el detalle de producto $cod_detallep");
                        }
                        $cantidad_restante = 0;
                    } else {
                        $cantidad_usada = $stock_disponible;
                        $detalleQuery = "INSERT INTO detalle_ventas(cod_venta, cod_detallep, cantidad, importe, costo_unitario) 
                                        VALUES(:cod_venta, :cod_detallep, :cantidad, :importe, :costo_unitario)";
                        $detalleStmt = $this->conex->prepare($detalleQuery);
                        $detalleStmt->bindParam(':cod_venta', $nuevo_cod);
                        $detalleStmt->bindParam(':cod_detallep', $cod_detallep);
                        $detalleStmt->bindParam(':costo_unitario', $costo);
                        $detalleStmt->bindParam(':cantidad', $cantidad_usada);
                        $importe = $precio * $cantidad_usada;
                        $detalleStmt->bindParam(':importe', $importe);

                        if (!$detalleStmt->execute()) {
                            throw new Exception("Error al registrar el detalle de venta para el producto con lote $cod_detallep");
                        }
                        $actualizarStockQuery = "UPDATE detalle_productos SET stock = 0 WHERE cod_detallep = :cod_detallep";
                        $actualizarStockStmt = $this->conex->prepare($actualizarStockQuery);
                        $actualizarStockStmt->bindParam(':cod_detallep', $cod_detallep);

                        if (!$actualizarStockStmt->execute()) {
                            throw new Exception("Error al actualizar el stock para el detalle de producto $cod_detallep");
                        }
                        $cantidad_restante -= $stock_disponible;
                    }
                }
                if ($cantidad_restante > 0) {
                    throw new Exception("No hay suficiente stock disponible para el producto con código $cod_presentacion");
                }
            }
            $this->conex->commit();
            parent::desconectarBD();
            return $nuevo_cod;
        } catch (Exception $e) {
            $this->conex->rollBack();
            parent::desconectarBD();
            error_log($e->getMessage());
            return $e->getMessage();
        }
    }

    public function anular($cod_v)
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            $sql = "UPDATE ventas SET status=0 WHERE cod_venta=:cod_venta;";
            $anu = $this->conex->prepare($sql);
            $anu->bindParam(':cod_venta', $cod_v);
            $resul = $anu->execute();
            if ($resul) {
                $revertir = "UPDATE detalle_productos AS dp
                JOIN detalle_ventas AS dv ON dp.cod_detallep = dv.cod_detallep
                SET dp.stock = dp.stock + dv.cantidad
                WHERE dv.cod_venta = :cod_venta;";
                $stock = $this->conex->prepare($revertir);
                $stock->bindParam(':cod_venta', $cod_v);
                $r = $stock->execute();
                if ($r) {
                    $res = 1;
                }
            }
            $this->conex->commit();
            parent::desconectarBD();
            return $res;
        } catch (Exception $e) {
            $this->conex->rollBack();
            parent::desconectarBD();
        }
    }

    public function factura($valor)
    {
        $sql = "SELECT 
        dv.cod_detallev,
        dv.cod_venta,
        dv.cantidad,
        dv.importe,
        p.cod_producto,
        p.nombre AS producto_nombre,
        m.nombre AS marca,
        present.excento,
        present.cod_presentacion,
        present.presentacion,
        present.cantidad_presentacion,
        present.costo,
        present.porcen_venta,
        u.tipo_medida,
        CONCAT(present.presentacion, ' x ', present.cantidad_presentacion, ' ', u.tipo_medida) AS presentacion 
    FROM detalle_ventas AS dv
    JOIN detalle_productos AS dp ON dv.cod_detallep = dp.cod_detallep
    JOIN presentacion_producto AS present ON dp.cod_presentacion = present.cod_presentacion
    JOIN productos AS p ON present.cod_producto = p.cod_producto
    LEFT JOIN marcas AS m ON p.cod_marca = m.cod_marca
    JOIN unidades_medida AS u ON present.cod_unidad = u.cod_unidad 
    WHERE dv.cod_venta =:cod_venta;";
        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $consulta->bindParam(':cod_venta', $valor);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }

    public function v_cliente()
    {
        $sql = "SELECT 
        c.cod_cliente,
        c.nombre,
        c.apellido,
        c.cedula_rif,
        c.telefono,
        c.email,
        c.direccion,
        COUNT(v.cod_venta) AS cantidad_ventas,
        COALESCE(SUM(v.total), 0) AS monto_total
    FROM clientes c
    LEFT JOIN ventas v ON c.cod_cliente = v.cod_cliente
    GROUP BY c.cod_cliente,c.nombre,c.apellido,c.cedula_rif,c.telefono,c.email,c.direccion
    ORDER BY cantidad_ventas DESC;";
        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }

    public function venta_f($fi, $ff)
    {
        $sql = "SELECT c.nombre, c.apellido, v.*
    FROM clientes c
    INNER JOIN ventas v ON c.cod_cliente = v.cod_cliente
    WHERE v.fecha BETWEEN :fechainicio AND :fechafin
    ORDER BY v.cod_venta ASC;";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':fechainicio', $fi);
        $stmt->bindParam(':fechafin', $ff);
        $resul = $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }

    /*public function rmovimiento($resul){
        $sql="CALL R_movimiento_operacion(:cod_venta, 1);";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':cod_venta', $resul);
        $resul=$stmt->execute();
        parent::desconectarBD();
    }*/


    public function notificacionesCobrar($dias_alerta = 3)
    {
        $hoy = date('Y-m-d');
        $alerta = date('Y-m-d', strtotime("+$dias_alerta days"));

        //Me traigo todo lo pendiente x cobrar
        $sql = "SELECT 
    v.cod_venta,
    v.fecha,
    v.fecha_vencimiento,
    v.total,
    c.nombre,
    COALESCE(SUM(pr.monto_total), 0) AS monto_pagado
    FROM ventas v
    INNER JOIN clientes c ON v.cod_cliente = c.cod_cliente
    LEFT JOIN pago_recibido pr ON pr.cod_venta = v.cod_venta
    WHERE v.status IN (1, 2)
    GROUP BY v.cod_venta, v.fecha, v.fecha_vencimiento, v.total, c.nombre";

        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->execute();
        $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();

        $notificaciones = [];

        foreach ($ventas as $venta) {
            $monto_pagado = floatval($venta['monto_pagado'] ?? 0);
            $saldo_pendiente = floatval($venta['total']) - $monto_pagado;

            if ($saldo_pendiente == 0) {
                continue; // Ya pagada
            }

            // Fecha de referencia: vencimiento o fecha de venta
            $fecha_base = null;
            if (!empty($venta['fecha_vencimiento']) && $venta['fecha_vencimiento'] != '0000-00-00') {
                $fecha_base = $venta['fecha_vencimiento'];
            } else {
                $fecha_base = $venta['fecha'];
            }

            $dias_restantes = intval((strtotime($fecha_base) - strtotime($hoy)) / 86400);

            // Vencidas
            if ($fecha_base < $hoy) {
                $notificaciones[] = [
                    'descripcion' => 'Cobro vencido a ' . $venta['nombre'],
                    'fecha_vencimiento' => $fecha_base,
                    'dias_restantes' => $dias_restantes,
                    'tipo' => 'cobrar',
                    'estado' => 'vencida'
                ];
            }
            // Próximas a vencer
            elseif ($fecha_base >= $hoy && $fecha_base <= $alerta) {
                $notificaciones[] = [
                    'descripcion' => 'Cobro a ' . $venta['nombre'],
                    'fecha_vencimiento' => $fecha_base,
                    'dias_restantes' => $dias_restantes,
                    'tipo' => 'cobrar',
                    'estado' => 'proxima'
                ];
            }
        }

        return $notificaciones;
    }

    public function datos()
    {
        $registro = "SELECT 
    c.nombre, 
    c.apellido, 
    c.cedula_rif, 
    c.telefono, 
    c.email, 
    c.direccion, 
    COUNT(v.cod_venta) AS cantidad_ventas, 
    SUM(p.monto_total) AS monto_total
FROM 
    clientes c 
LEFT JOIN 
    ventas v ON c.cod_cliente = v.cod_cliente 
LEFT JOIN 
    pago_recibido p ON v.cod_venta = p.cod_venta
GROUP BY 
    c.cod_cliente
ORDER BY 
    c.apellido, c.nombre";
    parent::conectarBD();
        $consulta = $this->conex->prepare($registro);
        $resul = $consulta->execute();
        $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
        if ($resul) {
            parent::desconectarBD();
            return $datos;
        } else {
            parent::desconectarBD();
            return $res = 0;
        }
    }

    public function total_v(){
        $sql="SELECT SUM(total) AS total_ventas FROM ventas WHERE DATE(fecha) = CURDATE();";
        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $resul = $consulta->execute();
        $datos = $consulta->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return $res = 0;
        }
    }

    public function total_s(){
        $sql="SELECT SUM(total) AS total_semana FROM ventas WHERE YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1);";
        parent::conectarBD();
        $consulta = $this->conex->prepare($sql);
        $resul = $consulta->execute();
        $datos = $consulta->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return $res = 0;
        }
    }
}
