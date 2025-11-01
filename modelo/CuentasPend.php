<?php 
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;

class CuentasPend extends Conexion{

    public function __construct(){
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

//BOX CUENTAS X COBRAR
private function boxcobrar(){
    $sql = "SELECT 
    SUM(saldo_pendiente) AS total_cobrar
    FROM (
        SELECT 
            v.cod_venta,
            (v.total - COALESCE(SUM(pr.monto_total),0)) AS saldo_pendiente
        FROM ventas v
        LEFT JOIN pago_recibido pr ON pr.cod_venta = v.cod_venta
        WHERE v.status IN (1, 2)
        GROUP BY v.cod_venta
    ) AS subconsulta
    WHERE saldo_pendiente > 0;";
    parent::conectarBD();
    $consulta = $this->conex->prepare($sql);
    $resul = $consulta->execute();
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resul){
        return $datos;
    }else{
        return $r=0;
    }
}

public function getboxcobrar(){
    return $this->boxcobrar();
}

private function boxpagar(){
    $sql = "SELECT 
    SUM(saldo_pendiente) AS total_pagar
    FROM (
        SELECT 
            c.cod_compra,
            (c.total - COALESCE(SUM(pe.monto_total),0)) AS saldo_pendiente
        FROM compras c
        LEFT JOIN pago_emitido pe ON pe.cod_compra = c.cod_compra
        WHERE c.status IN (1, 2)
        GROUP BY c.cod_compra
    ) AS subconsulta
    WHERE saldo_pendiente > 0;";
    
    parent::conectarBD();
    $consulta = $this->conex->prepare($sql);
    $resul = $consulta->execute();
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resul){
        return $datos;
    }else{
        return 0;
    }
}

public function getboxpagar(){
    return $this->boxpagar();
}

//METODO PARA EL LISTADO DE CUENTAS POR COBRAR: POR CLIENTE
private function mostrar(){
    $sql = "SELECT
    c.cod_cliente,
    c.nombre AS cliente,
    c.cedula_rif,
    COUNT(DISTINCT v.cod_venta) AS total_ventas,
    SUM(v.total) AS total,
    SUM(COALESCE(pr.monto_total, 0)) AS total_cobrado,
    SUM(v.total - COALESCE(pr.monto_total, 0)) AS total_pendiente
    FROM ventas v
    INNER JOIN clientes c ON v.cod_cliente = c.cod_cliente
    LEFT JOIN pago_recibido pr ON pr.cod_venta = v.cod_venta
    WHERE v.status IN (1, 2)
    GROUP BY c.cod_cliente, c.nombre
    ORDER BY cliente;";
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
    
public function getmostrarcliente(){
    return $this->mostrar();
}

//METODO PARA EL LISTADO DE CUENTAS POR COBRAR: VENTAS DE UN CLIENTE
private function mostrar2($cod_cliente){
    $sql="SELECT
    v.cod_venta,
    v.total,
    v.fecha,
    v.fecha_vencimiento,
    COALESCE(SUM(pr.monto_total),0) AS monto_pagado,
    (v.total - COALESCE(SUM(pr.monto_total),0)) AS saldo_pendiente,
    COALESCE(DATEDIFF(v.fecha_vencimiento, CURDATE()),0) AS dias_restantes,
        CASE	
            WHEN v.status = 3 THEN 'Pagado'
            WHEN v.status = 2 THEN 'Pago parcial'
            WHEN v.fecha_vencimiento < CURDATE() THEN 'Vencido'
            ELSE 'Pendiente'
        END AS estado,
        c.nombre,
        c.apellido,
        c.cedula_rif,
        c.direccion,
        c.telefono
    FROM ventas v
    LEFT JOIN pago_recibido pr ON pr.cod_venta = v.cod_venta
    INNER JOIN clientes c ON v.cod_cliente = c.cod_cliente
    WHERE v.cod_cliente = :cod_cliente AND v.status IN (1, 2)
    GROUP BY v.cod_venta, v.total, v.fecha, v.fecha_vencimiento, v.status,
    c.nombre, c.apellido, c.cedula_rif, c.direccion, c.telefono
    ORDER BY v.fecha_vencimiento ASC;";
    parent::conectarBD();
    $consulta = $this->conex->prepare($sql);
    $consulta->bindParam(':cod_cliente', $cod_cliente, PDO::PARAM_INT);
    $resul = $consulta->execute();  
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resul){
        return $datos;
    }else{
            return [];
        }
}

public function getmostrar2($cod_cliente){
    return $this->mostrar2($cod_cliente);
}


private function mostrar3($fechaInicio = null, $fechaFin = null) {
    $sql = "SELECT
        v.cod_venta,
        v.total,
        v.fecha,
        v.fecha_vencimiento,
        COALESCE(SUM(pr.monto_total), 0) AS monto_pagado,
        (v.total - COALESCE(SUM(pr.monto_total), 0)) AS saldo_pendiente,
        CASE    
            WHEN v.status = 3 THEN 'Pagado'
            WHEN v.status = 2 THEN 'Pago parcial'
            WHEN v.fecha_vencimiento < CURDATE() THEN 'Vencido'
            ELSE 'Pendiente'
        END AS estado,
        c.nombre,
        c.apellido,
        c.cedula_rif,
        c.direccion,
        c.telefono
    FROM ventas v
    LEFT JOIN pago_recibido pr ON pr.cod_venta = v.cod_venta
    INNER JOIN clientes c ON v.cod_cliente = c.cod_cliente
    WHERE v.status IN (1, 2)";
    
    // Agregar filtro de fecha si se pasan los parámetros
    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $sql .= " AND v.fecha BETWEEN :fechaInicio AND :fechaFin";
    }

    $sql .= "
    GROUP BY v.cod_venta, v.total, v.fecha, v.fecha_vencimiento, v.status,
            c.nombre, c.apellido, c.cedula_rif, c.direccion, c.telefono
    ORDER BY v.fecha_vencimiento ASC";

    parent::conectarBD();
    $consulta = $this->conex->prepare($sql);

    // Bind de fechas solo si se pasaron
    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $consulta->bindParam(':fechaInicio', $fechaInicio);
        $consulta->bindParam(':fechaFin', $fechaFin);
    }

    $resul = $consulta->execute();  
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();

    return $resul ? $datos : [];
}

public function getmostrar3(){
    return $this->mostrar3();
}

/*CUENTAS POR PAGAR: PROBANDO VISTAS*/
private function mostrarCuentasPagar(){
    $sql = "SELECT * FROM vista_pendientes_compras_gastos";
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

public function getmostrarCuentasPagar(){
    return $this->mostrarCuentasPagar();
}

//REPORTE CUENTAS POR PAGAR
public function cuentaspagarporfecha($fechaInicio,$fechaFin) {
    $sql = "SELECT * FROM vista_pendientes_compras_gastos WHERE fecha BETWEEN :fechaInicio AND :fechaFin";
    parent::conectarBD();
    $consulta = $this->conex->prepare($sql);
    $consulta->bindParam(':fechaInicio', $fechaInicio);
    $consulta->bindParam(':fechaFin', $fechaFin);
    $resul = $consulta->execute();
    $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
    parent::desconectarBD();
    if($resul){
        return $datos;
    }else{
        return [];
    }
}


}