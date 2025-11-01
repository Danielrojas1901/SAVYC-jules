<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDOException;
use PDO;

class Compras extends Conexion{
   private $cod_compra;
   private $cod_prov;
   private $subtotal;
   private $total;
   private $impuesto_total;
   private $fecha;
   private $descuento;
   private $status;
   private $cantidad;
   private $monto;
   private $errores=[];
   private $fecha_v;
   use ValidadorTrait;
   private $condicion;


   public function __construct(){
      global $_ENV;
      parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
   }

   public function setdatac($data){
      $this->cod_prov = $data['cod_prov'];
      $this->condicion = $data['condicion'];
      if($this->validarDecimal($data['subtotal'], 'Subtotal')=== true){
         $this->subtotal = $data['subtotal'];
      }else{
         $this->errores['subtotal'] = $this->validarDecimal($data['subtotal'], 'Subtotal');
      }
      if($this->validarDecimal($data['total_general'], 'Total')=== true){
         $this->total = $data['total_general'];
      }else{
         $this->errores['total'] = $this->validarDecimal($data['total_general'], 'Total');
      }
      if($this->validarDecimal($data['impuesto_total'], 'Impuesto')=== true){
         $this->impuesto_total = $data['impuesto_total'];
      }else{
         $this->errores['impuesto'] = $this->validarDecimal($data['impuesto_total'], 'Impuesto');
      }
      if($this->validarFecha($data['fecha'], 'Fecha')=== true){
         $this->fecha = $data['fecha'];
      }else{
         $this->errores['fecha'] = $this->validarFecha($data['fecha'], 'Fecha');
      }
      if(!empty($data['fecha_v'])){
         if($this->validarFecha($data['fecha_v'], 'Fecha Vencimiento')=== true){
            $this->fecha_v = $data['fecha_v'];
         }else{
            $this->errores['fecha_v'] = $this->validarFecha($data['fecha_v'], 'Fecha Vencimiento');
         }
      }else{
         $this->fecha_v = null;
      }
   }

   // SETTER Y GETTER
   public function setCod1($cod_prov){
      $this->cod_prov = $cod_prov;
   }
   public function getCod1(){
      return $this->cod_prov;
   }
   public function getcod_compra(){
      return $this->cod_compra;
   }
   public function setcod_compra($cod_compra){
      $this->cod_compra = $cod_compra;
   }
   public function getsubtotal(){
      return $this->subtotal;
   }
   public function setsubtotal($subtotal){
      $this->subtotal = $subtotal;
   }
   public function gettotal(){
      return $this->total;
   }
   public function settotal($total){
      $this->total = $total;
   }
   public function getimpuesto_total(){
      return $this->impuesto_total;
   }

   public function setimpuesto_total($impuesto_total){
      $this->impuesto_total = $impuesto_total;
   }
   public function getfecha(){
      return $this->fecha;
   }
   public function setfecha($fecha){
      $this->fecha = $fecha;
   }
   public function getdescuento(){
      return $this->descuento;
   }
   public function setdescuento($descuento){
      $this->descuento = $descuento;
   }
   public function getStatus(){
      return $this->status;
   }
   public function geterrores(){
      return $this->errores;
   }
   public function getconex(){
      return $this->conex;
   }
   public function setStatus($status){
      $this->status = $status;
   }
   public function getmonto(){
      return $this->monto;
   }
   public function setmonto($monto){
      $this->monto = $monto;
   }
   public function getcantidad(){
      return $this->cantidad;
   }
   public function setcantidad($cantidad){
      $this->cantidad = $cantidad;
   }
   
   //metodos crud   registrar //
   private function registrar($dproducto){
      try{
         parent::conectarBD();
         $this->conex->beginTransaction();
      $sql = "INSERT INTO compras (cod_prov, condicion_pago, fecha_vencimiento, subtotal,total, impuesto_total, fecha, status) VALUES (:cod_prov, :condicion, :fecha_vencimiento, :subtotal,:total, :impuesto_total, :fecha, 1)";  
      $strExec = $this->conex->prepare($sql);  
      $strExec->bindParam(':cod_prov', $this->cod_prov); 
      $strExec->bindParam(':condicion', $this->condicion);
      $strExec->bindParam(':fecha_vencimiento', $this->fecha_v);
      $strExec->bindParam(':subtotal', $this->subtotal);  
      $strExec->bindParam(':impuesto_total', $this->impuesto_total);  
      $strExec->bindParam(':total', $this->total);  
      $strExec->bindParam(':fecha', $this->fecha);  
      $resul = $strExec->execute();  
      if ($resul) { 
         $cod_c=$this->conex->lastInsertId();
         foreach($dproducto as $producto){
         if(!empty($producto['cod-dp'])){
            $dcompra = "INSERT INTO detalle_compras (cod_compra, cod_detallep, cantidad, monto) VALUES (:cod_compra, :cod_detallep, :cantidad, :monto)";
            $strExec = $this->conex->prepare($dcompra);  
            $strExec->bindParam(':cod_compra', $cod_c);  
            $strExec->bindParam(':cod_detallep', $producto['cod-dp']);  
            $strExec->bindParam(':cantidad', $producto['cantidad']);  
            $strExec->bindParam(':monto', $producto['precio']);
            $dc=$strExec->execute();

            $incre="UPDATE detalle_productos SET stock = stock + :cantidad WHERE cod_detallep = :cod_detallep;";
            $str=$this->conex->prepare($incre);    
            $str->bindParam(':cod_detallep', $producto['cod-dp']);  
            $str->bindParam(':cantidad', $producto['cantidad']);
            $dp=$str->execute();

            $costo="UPDATE presentacion_producto SET costo= :costo, excento=:excento WHERE cod_presentacion=:cod_presentacion;";
            $sentencia=$this->conex->prepare($costo);
            $sentencia->bindParam(':costo', $producto['precio']);
            $sentencia->bindParam(':cod_presentacion', $producto['cod_presentacion']);
            $sentencia->bindParam(':excento', $producto['iva']);
            $sentencia->execute();
            
         }else{
               $dproducto = "INSERT INTO detalle_productos (cod_presentacion, stock, fecha_vencimiento, lote) VALUES (:cod_presentacion, :stock, :fecha_vencimiento, :lote)";
               $strExec = $this->conex->prepare($dproducto);  
               $strExec->bindParam(':cod_presentacion', $producto['cod_presentacion']);  
               $strExec->bindParam(':stock', $producto['cantidad']);  
               $strExec->bindParam(':fecha_vencimiento', $producto['fecha_v']);  
               $strExec->bindParam(':lote', $producto['lote']);
               $dp=$strExec->execute();

               $codp=$this->conex->lastInsertId();

               $dcompra = "INSERT INTO detalle_compras (cod_compra, cod_detallep, cantidad, monto) VALUES (:cod_compra, :cod_detallep, :cantidad, :monto)";
               $strExec = $this->conex->prepare($dcompra);  
               $strExec->bindParam(':cod_compra', $cod_c);  
               $strExec->bindParam(':cod_detallep', $codp);  
               $strExec->bindParam(':cantidad', $producto['cantidad']);  
               $strExec->bindParam(':monto', $producto['precio']);
               $dc=$strExec->execute();

               $costo="UPDATE presentacion_producto SET costo= :costo, excento=:excento WHERE cod_presentacion=:cod_presentacion;";
               $sentencia=$this->conex->prepare($costo);
               $sentencia->bindParam(':costo', $producto['precio']);
               $sentencia->bindParam(':cod_presentacion', $producto['cod_presentacion']);
               $sentencia->bindParam(':excento', $producto['iva']);
               $sentencia->execute();
         }
         }
         $res = $cod_c;
      } else {  
         $res = 0;  
      }  
         $this->conex->commit();
         parent::desconectarBD();
         return $res;
      }catch(Exception $e){
         $this->conex->rollBack();
         parent::desconectarBD();
      }
   }
   public function getRegistrarr($productos){
      return $this->registrar($productos);
   }

   public function anular($cod){
      try{
         parent::conectarBD();
         $this->conex->beginTransaction();
         $sql="UPDATE compras SET status=0 WHERE cod_compra=:cod_compra;";
         $anu=$this->conex->prepare($sql);
         $anu->bindParam(':cod_compra', $cod);
         $resul=$anu->execute();
         if($resul){
            $revertir="UPDATE detalle_productos AS dp
            JOIN detalle_compras AS dc ON dp.cod_detallep = dc.cod_detallep
            SET dp.stock = dp.stock - dc.cantidad
            WHERE dc.cod_compra = :cod_compra;";
            $stock=$this->conex->prepare($revertir);
            $stock->bindParam(':cod_compra', $cod);
            $r=$stock->execute();
         }
         if($r){
            $res=1;
         }else{
            $res=0;
         }
         $this->conex->commit();
         parent::desconectarBD();
         return $res;
      } catch(Exception $e){
         $this->conex->rollBack();
         parent::desconectarBD();
      }
   }

   // -------------------------fin de registtrar


   // --------------------------A  eliminar esta funcional
   /*private function eliminar($valor){
      // Usar una declaración preparada para evitar inyecciones SQL  
      $registro = "SELECT COUNT(*) AS n_dcompra FROM detalle_compras WHERE cod_compra = :valor";
      $strExec = $this->conex->prepare($registro);
      $strExec->bindParam(':valor', $valor, PDO::PARAM_INT); // Vincular el parámetro
      
      $resul = $strExec->execute();
      
      if ($resul) {
         $resul = $strExec->fetch(PDO::FETCH_ASSOC);
      
         // Verificar si hay registros en detalle_compras
         if ($resul['n_dcompra'] > 0) {
               // Verificar si hay un proveedor asociado
               $proveedorCheck = "SELECT COUNT(*) AS n_proveedor FROM compras WHERE cod_compra = :valor AND cod_prov IS NOT NULL";
               $strExecProveedor = $this->conex->prepare($proveedorCheck);
               $strExecProveedor->bindParam(':valor', $valor, PDO::PARAM_INT);
               $strExecProveedor->execute();
               $r = 'success';
               $proveedorResult = $strExecProveedor->fetch(PDO::FETCH_ASSOC);
      
               if ($proveedorResult['n_proveedor'] > 0) {
                  // Si hay un proveedor asociado, actualiza el estado a 2
                  $log = "UPDATE compras SET status = 2 WHERE cod_compra = :valor";
                  $strExecUpdate = $this->conex->prepare($log);
                  $strExecUpdate->bindParam(':valor', $valor, PDO::PARAM_INT);
                  $strExecUpdate->execute();
                  $r = 'success';
               } 
         } else {
               // Si no hay registros en detalle_compras, cambiar el estado a 0 (eliminado lógicamente)
               $log = "UPDATE compras SET status = 0 WHERE cod_compra = :valor";
               $strExecUpdate = $this->conex->prepare($log);
               $strExecUpdate->bindParam(':valor', $valor, PDO::PARAM_INT);
               $strExecUpdate->execute();
            }
         } else {
               $r = 'error_delete';
         }
         return $r;
      }


   public function geteliminar($valor)
   {
      return $this->eliminar($valor);
   }*/

   // --------------------------eliminar

   //inicio de consultar  //
   private function consultar(){
      $registro = "SELECT DISTINCT
    c.cod_compra,
    pr.razon_social,
    c.subtotal,
    c.fecha,
    c.total,
    c.status,
    COALESCE(pe.fecha, 'Sin fecha') AS fecha_pago,
    COALESCE(pe.monto_total, 0) AS monto_ultimo_pago,
    COALESCE(tp.total_pagos_emitidos, 0) AS total_pagos_emitidos
FROM 
    compras AS c
LEFT JOIN 
    proveedores AS pr ON c.cod_prov = pr.cod_prov
LEFT JOIN 
    (
        SELECT 
            pe.cod_compra, 
            MAX(pe.fecha) AS fecha,
            SUM(pe.monto_total) AS monto_total
        FROM 
            pago_emitido AS pe
        GROUP BY 
            pe.cod_compra
    ) AS pe ON c.cod_compra = pe.cod_compra
LEFT JOIN 
    (
        SELECT 
            cod_compra, 
            SUM(monto_total) AS total_pagos_emitidos
        FROM 
            pago_emitido
        GROUP BY 
            cod_compra
    ) tp ON c.cod_compra = tp.cod_compra;";
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
   public function getconsultar()
   {
      return $this->consultar();
   }
   //fin de consultar//

   public function divisas(){
      $sql="SELECT 
               d.cod_divisa,
               d.nombre,
               d.abreviatura,
               cd.tasa,
               cd.fecha AS fecha_tasa
            FROM divisas d
            LEFT JOIN cambio_divisa cd
            ON cd.cod_cambio = (
                  SELECT cd2.cod_cambio
                  FROM cambio_divisa cd2
                  WHERE cd2.cod_divisa = d.cod_divisa
                  ORDER BY cd2.fecha DESC
                  LIMIT 1
            )
            ORDER BY d.nombre;";
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
   
   //metodo buscar
   private function buscar_p($valor){
      $sql="SELECT
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
      CONCAT(present.presentacion, ' x ', present.cantidad_presentacion, ' ', u.tipo_medida) AS presentacion  
      FROM presentacion_producto AS present                 
      JOIN productos AS p ON present.cod_producto = p.cod_producto  
      JOIN categorias AS c ON p.cod_categoria = c.cod_categoria      
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
         if($resul){
               return $datos;
         }else{
               return [];
         }
      }
   public function getbuscar_p($valor){
      return $this->buscar_p($valor);
   }
   public function buscar_l($lot, $cod){
      $busqueda="SELECT dp.*, pp.*
      FROM detalle_productos AS dp
      JOIN presentacion_producto AS pp ON dp.cod_presentacion = pp.cod_presentacion WHERE pp.cod_presentacion = :cod_presentacion AND dp.lote LIKE :lote;";
      parent::conectarBD();
      $consulta = $this->conex->prepare($busqueda);
      $buscar = '%' . $lot . '%';
      $consulta->bindParam(':lote', $buscar, PDO::PARAM_STR);
      $consulta->bindParam(':cod_presentacion', $cod);
      $resul = $consulta->execute();
      $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
      parent::desconectarBD();
      if($resul){
         return $datos;
      }else{
         return [];
      }
   }

   public function b_detalle($cod){
      $busqueda="SELECT dc.*, dp.*, m.nombre AS marca, prod.nombre AS nproducto,
      CONCAT(' - ', p.presentacion, ' x ', p.cantidad_presentacion) AS presentacion 
      FROM detalle_compras dc 
      JOIN compras c ON dc.cod_compra=c.cod_compra 
      JOIN detalle_productos dp ON dc.cod_detallep=dp.cod_detallep
      JOIN presentacion_producto p ON dp.cod_presentacion=p.cod_presentacion
      JOIN productos AS prod ON p.cod_producto = prod.cod_producto
      LEFT JOIN marcas AS m ON prod.cod_marca = m.cod_marca
      WHERE dc.cod_compra=:cod_compra;";
      parent::conectarBD();
      $consulta = $this->conex->prepare($busqueda);
      $consulta->bindParam(':cod_compra', $cod);
      $resul = $consulta->execute();
      $datos = $consulta->fetchAll(PDO::FETCH_ASSOC);
      parent::desconectarBD();
      if($resul){
         return $datos;
      }else{
         return [];
      }
   }

   public function compra_f($fi, $ff){
      $sql="SELECT p.razon_social, c.*
   FROM compras c
   INNER JOIN proveedores p ON c.cod_prov = p.cod_prov
   WHERE c.fecha BETWEEN :fechainicio AND :fechafin
   ORDER BY c.cod_compra ASC;";
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



//pagooooooo

private $vuelto;
private $pago;
private $monto_pagar;
private $monto_total;
private $fecha_pago;
private $cod_vuelto;
private $pagos;
private $tipo_pago;
private $cod_pago;


public function setdatap($datos){
   $this->tipo_pago = $datos['tipo_pago'];
   if($this->validarDecimal($datos['montopagado'], 'Monto pagado', 1, 20)){
         $this->monto_total = $datos['montopagado'];
   }else{
         $this->errores['montopagado'] = $this->validarDecimal($datos['montopagado'], 'Monto pagado', 1, 20);
   }
   if($this->validarNumerico($datos['cod_compra'], 'Número de compra', 1, 20)){
         $this->cod_compra = $datos['cod_compra'];
   }else{
         $this->errores['cod_compra'] = $this->validarNumerico($datos['cod_compra'], 'Número de venta', 1, 20);
   }
   if($this->validardatetime($datos['fecha'], 'Fecha de pago')){
         $this->fecha_pago = $datos['fecha'];
   }else{
         $this->errores['fecha'] = $this->validardatetime($datos['fecha'], 'Fecha de pago');
   }
   if(!empty($datos['vuelto_data'])){
         parse_str($datos['vuelto_data'], $this->vuelto);
   }else{
         $this->cod_vuelto = null;
   }
   if(!empty($datos['pago'])){
         $this->pagos= $datos['pago'];
   } else{
      $this->errores['pago'] = 'El campo pago es obligatorio.';
   }
   if($this->validarDecimal($datos['monto_pagar'], 'Monto pagado', 1, 20)){
         $this->monto_pagar = $datos['monto_pagar'];
   }
}

public function getcod_pago(){
   return $this->cod_pago;
}

public function registrarpcompra(){
   try {
         parent::conectarBD();
         $this->conex->beginTransaction();

         if (!empty($this->vuelto)) {
            $sql = "INSERT INTO vuelto_recibido(vuelto_total) VALUES(:vuelto_total)";
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':vuelto_total', $this->vuelto['vuelto_pagado']);
            $resultado = $strExec->execute();
            if ($resultado) {
               $this->cod_vuelto = $this->conex->lastInsertId();

               foreach ($this->vuelto['vuelto'] as $dvuelto) {
                     if (!empty($dvuelto['monto']) && $dvuelto['monto'] > 0) {
                        $registro = "INSERT INTO detalle_vueltor(cod_vuelto_r, cod_tipo_pago, monto) 
                                    VALUES(:cod_vuelto, :cod_tipo_pago, :monto)";
                        $sentencia = $this->conex->prepare($registro);
                        $sentencia->bindParam(':cod_vuelto', $this->cod_vuelto);
                        $sentencia->bindParam(':cod_tipo_pago', $dvuelto['cod_tipo_pago']);
                        $sentencia->bindParam(':monto', $dvuelto['monto']);
                        $r = $sentencia->execute();

                        if ($r) {
                           $tp = "SELECT cod_cuenta_bancaria, cod_caja 
                                    FROM detalle_tipo_pago 
                                    WHERE cod_tipo_pago = :cod_tipo_pago";
                           $sentencia = $this->conex->prepare($tp);
                           $sentencia->bindParam(':cod_tipo_pago', $dvuelto['cod_tipo_pago']);
                           $sentencia->execute();
                           $result = $sentencia->fetch(PDO::FETCH_ASSOC);

                           if (!empty($result['cod_cuenta_bancaria'])) {
                                 $sql = "UPDATE cuenta_bancaria SET saldo = saldo + :monto 
                                       WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria";
                                 $sen = $this->conex->prepare($sql);
                                 $sen->bindParam(':monto', $dvuelto['monto']);
                                 $sen->bindParam(':cod_cuenta_bancaria', $result['cod_cuenta_bancaria']);
                                 $sen->execute();
                           } elseif (!empty($result['cod_caja'])) {
                                 $sql = "UPDATE caja SET saldo = saldo + :monto 
                                       WHERE cod_caja = :cod_caja";
                                 $sen = $this->conex->prepare($sql);
                                 $sen->bindParam(':monto', $dvuelto['monto']);
                                 $sen->bindParam(':cod_caja', $result['cod_caja']);
                                 $sen->execute();
                           } else {
                                 throw new Exception("Error al obtener la cuenta bancaria o caja para vuelto recibido.");
                           }
                        } else {
                           throw new Exception("Error al registrar el detalle del vuelto recibido.");
                        }
                     }
               }
            } else {
               throw new Exception("Error al registrar el vuelto recibido.");
            }
         }

         $sql = "INSERT INTO pago_emitido(tipo_pago, cod_vuelto_r, fecha, cod_compra, monto_total) 
               VALUES(:tipo_pago, :cod_vuelto, :fecha, :cod_compra, :monto_total)";
         $strExec = $this->conex->prepare($sql);
         $strExec->bindParam(':cod_compra', $this->cod_compra);
         $strExec->bindParam(':cod_vuelto', $this->cod_vuelto);
         $strExec->bindParam(':fecha', $this->fecha_pago);
         $strExec->bindParam(':monto_total', $this->monto_total);
         $strExec->bindParam(':tipo_pago', $this->tipo_pago);
         $resul = $strExec->execute();

         if ($resul) {
            $nuevo_cod = $this->conex->lastInsertId();
            $this->cod_pago = $nuevo_cod;
            foreach ($this->pagos as $pagos) {
               if (!empty($pagos['monto']) && $pagos['monto'] > 0) {
                  $tp = "SELECT cod_cuenta_bancaria, cod_caja 
                              FROM detalle_tipo_pago 
                              WHERE cod_tipo_pago = :cod_tipo_pago";
                        $sentencia = $this->conex->prepare($tp);
                        $sentencia->bindParam(':cod_tipo_pago', $pagos['cod_tipo_pago']);
                        $sentencia->execute();
                        $result = $sentencia->fetch(PDO::FETCH_ASSOC);
                     if (!empty($result['cod_cuenta_bancaria'])) {
                        $sql = "SELECT saldo FROM cuenta_bancaria WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria";
                        $strExec = $this->conex->prepare($sql);
                        $strExec->bindParam(':cod_cuenta_bancaria', $result['cod_cuenta_bancaria']);
                        $res = $strExec->execute();
                        $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
                     }else if(!empty($result['cod_caja'])) {
                        $sql = "SELECT saldo FROM caja WHERE cod_caja = :cod_caja";
                        $strExec = $this->conex->prepare($sql);
                        $strExec->bindParam(':cod_caja', $result['cod_caja']);
                        $res = $strExec->execute();
                        $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
                     }
                     if ($res && $resultado['saldo'] < $pagos['monto']) {
                        throw new Exception("Error al registrar el pago emitido. El monto pagado es mayor al saldo disponible.");
                     }

                     $registro = "INSERT INTO detalle_pago_emitido(cod_pago_emitido, cod_tipo_pagoe, monto) 
                                 VALUES(:cod_pago_emitido, :cod_tipo_pago, :monto)";
                     $sentencia = $this->conex->prepare($registro);
                     $sentencia->bindParam(':cod_pago_emitido', $nuevo_cod);
                     $sentencia->bindParam(':cod_tipo_pago', $pagos['cod_tipo_pago']);
                     $sentencia->bindParam(':monto', $pagos['monto']);
                     $r = $sentencia->execute();

                     if ($r) {
                        if (!empty($result['cod_cuenta_bancaria'])) {
                           $sql = "UPDATE cuenta_bancaria SET saldo = saldo - :monto 
                                    WHERE cod_cuenta_bancaria = :cod_cuenta_bancaria";
                           $sen = $this->conex->prepare($sql);
                           $sen->bindParam(':monto', $pagos['monto']);
                           $sen->bindParam(':cod_cuenta_bancaria', $result['cod_cuenta_bancaria']);
                           $sen->execute();
                        } elseif (!empty($result['cod_caja'])) {
                           $sql = "UPDATE caja SET saldo = saldo - :monto 
                                    WHERE cod_caja = :cod_caja";
                           $sen = $this->conex->prepare($sql);
                           $sen->bindParam(':monto', $pagos['monto']);
                           $sen->bindParam(':cod_caja', $result['cod_caja']);
                           $sen->execute();
                        } else {
                           throw new Exception("Error al obtener la cuenta bancaria o caja para pago emitido.");
                        }
                     } else {
                        throw new Exception("Error al registrar el detalle del pago emitido.");
                     }
               }
            }

            if ($this->monto_pagar > $this->monto_total) {
               $estado = "UPDATE compras SET status = 2 WHERE cod_compra = :cod_compra";
               $strExec = $this->conex->prepare($estado);
               $strExec->bindParam(':cod_compra', $this->cod_compra);
               $strExec->execute();
               $r = $this->monto_pagar - $this->monto_total;
            } else {
               $estado = "UPDATE compras SET status = 3 WHERE cod_compra = :cod_compra";
               $strExec = $this->conex->prepare($estado);
               $strExec->bindParam(':cod_compra', $this->cod_compra);
               $strExec->execute();
               $r = 0;
            }

         } else {
            throw new Exception("Error al registrar el pago emitido.");
         }

         $this->conex->commit();
         return $r;

   } catch (PDOException $e) {
         $this->conex->rollBack();
         error_log("Error en la consulta: " . $e->getMessage());
         echo '<script>console.log("Error en la consulta: ' . $e->getMessage() . '");</script>';
         return $e->getMessage();
      } finally {
         parent::desconectarBD();
      }
}
//pagoooooooooo


   

   public function notificacionesPagar($dias_alerta = 3) {
      $hoy = date('Y-m-d');
      $alerta = date('Y-m-d', strtotime("+$dias_alerta days"));

      try {
         $sql = "SELECT 
                     c.cod_compra,
                     p.razon_social,
                     c.total,
                     c.fecha,
                     c.fecha_vencimiento,
                     COALESCE(SUM(pe.monto_total), 0) AS monto_pagado,
                     (c.total - COALESCE(SUM(pe.monto_total), 0)) AS saldo_pendiente
                  FROM compras c
                  JOIN proveedores p ON p.cod_prov = c.cod_prov
                  LEFT JOIN pago_emitido pe ON pe.cod_compra = c.cod_compra
                  WHERE c.status IN (1, 2)
                  GROUP BY c.cod_compra, p.razon_social, c.total, c.fecha, c.fecha_vencimiento, c.status";

         parent::conectarBD();
         $stmt = $this->conex->prepare($sql);
         $stmt->execute();
         $compras = $stmt->fetchAll(PDO::FETCH_ASSOC);
         parent::desconectarBD();
      } catch (PDOException $e) {
         error_log("Error en notificacionesComprasPorPagar: " . $e->getMessage());
         return [];
      }

      $notificaciones = [];
      foreach ($compras as $compra) {
         $saldo_pendiente = floatval($compra['total']) - floatval($compra['monto_pagado']);
         if ($saldo_pendiente <= 0) continue;

         $fecha_base = (!empty($compra['fecha_vencimiento']) && $compra['fecha_vencimiento'] != '0000-00-00')
                        ? $compra['fecha_vencimiento']
                        : $compra['fecha'];

         $dias_restantes = intval((strtotime($fecha_base) - strtotime($hoy)) / 86400);

         if ($fecha_base < $hoy) {
            $notificaciones[] = [
                  'descripcion' => 'Pago vencido a ' . $compra['razon_social'],
                  'fecha_vencimiento' => $fecha_base,
                  'dias_restantes' => $dias_restantes,
                  'tipo' => 'compra',
                  'estado' => 'vencida'
            ];
         } elseif ($fecha_base >= $hoy && $fecha_base <= $alerta) {
            $notificaciones[] = [
                  'descripcion' => 'Pago a ' . $compra['razon_social'],
                  'fecha_vencimiento' => $fecha_base,
                  'dias_restantes' => $dias_restantes,
                  'tipo' => 'compra',
                  'estado' => 'proxima'
            ];
         }
      }

      return $notificaciones;
   }

}
