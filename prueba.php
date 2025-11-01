<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';

$_SESSION["iniciarsesion"] = "ok";
$_SESSION["user"] = "admin";
$_SESSION["nombre"] = "Administrador";
$_SESSION["cod_usuario"] = 1;
$_SESSION["rol"] = "Administrador";
$_SESSION['permisos']['venta']['consultar']=1;
$_SESSION['permisos']['venta']['registrar']=1;
$_SESSION['permisos']['venta']['editar']=1;
$_SESSION['permisos']['venta']['eliminar']=1;

$_SESSION['permisos']['producto']['consultar']=1;
$_SESSION['permisos']['producto']['registrar']=1;
$_SESSION['permisos']['producto']['editar']=1;
$_SESSION['permisos']['producto']['eliminar']=1;

$_SESSION['permisos']['inventario']['consultar']=1;
$_SESSION['permisos']['inventario']['registrar']=1;
$_SESSION['permisos']['inventario']['editar']=1;
$_SESSION['permisos']['inventario']['eliminar']=1;

$_SESSION['permisos']['reporte']['consultar']=1;
$_SESSION['permisos']['reporte']['registrar']=1;
$_SESSION['permisos']['reporte']['editar']=1;
$_SESSION['permisos']['reporte']['eliminar']=1;

$_SESSION['permisos']['compra']['consultar']=1;
$_SESSION['permisos']['compra']['registrar']=1;
$_SESSION['permisos']['compra']['editar']=1;
$_SESSION['permisos']['compra']['eliminar']=1;

$_SESSION['permisos']['config_productos']['consultar']=1;
$_SESSION['permisos']['config_productos']['registrar']=1;
$_SESSION['permisos']['config_productos']['editar']=1;
$_SESSION['permisos']['config_productos']['eliminar']=1;

$_SESSION['permisos']['seguridad']['consultar']=1;
$_SESSION['permisos']['seguridad']['registrar']=1;
$_SESSION['permisos']['seguridad']['editar']=1;
$_SESSION['permisos']['seguridad']['eliminar']=1;

$_SESSION['permisos']['config_finanza']['consultar']=1;
$_SESSION['permisos']['config_finanza']['registrar']=1;
$_SESSION['permisos']['config_finanza']['editar']=1;
$_SESSION['permisos']['config_finanza']['eliminar']=1;

$_SESSION['permisos']['proveedor']['consultar']=1;
$_SESSION['permisos']['proveedor']['registrar']=1;
$_SESSION['permisos']['proveedor']['editar']=1;
$_SESSION['permisos']['proveedor']['eliminar']=1;

$_SESSION['permisos']['cliente']['consultar']=1;
$_SESSION['permisos']['cliente']['registrar']=1;
$_SESSION['permisos']['cliente']['editar']=1;
$_SESSION['permisos']['cliente']['eliminar']=1;

$_SESSION['permisos']['gasto']['consultar']=1;
$_SESSION['permisos']['gasto']['registrar']=1;
$_SESSION['permisos']['gasto']['editar']=1;
$_SESSION['permisos']['gasto']['eliminar']=1;

$_SESSION['permisos']['cuentas_pendiente']['consultar']=1;
$_SESSION['permisos']['cuentas_pendiente']['registrar']=1;
$_SESSION['permisos']['cuentas_pendiente']['editar']=1;
$_SESSION['permisos']['cuentas_pendiente']['eliminar']=1;

$_SESSION['permisos']['finanza']['consultar']=1;
$_SESSION['permisos']['finanza']['registrar']=1;
$_SESSION['permisos']['finanza']['editar']=1;
$_SESSION['permisos']['finanza']['eliminar']=1;

$_SESSION['permisos']['tesoreria']['consultar']=1;
$_SESSION['permisos']['tesoreria']['registrar']=1;
$_SESSION['permisos']['tesoreria']['editar']=1;
$_SESSION['permisos']['tesoreria']['eliminar']=1;

$_SESSION['permisos']['contabilidad']['consultar']=1;
$_SESSION['permisos']['contabilidad']['registrar']=1;
$_SESSION['permisos']['contabilidad']['editar']=1;
$_SESSION['permisos']['contabilidad']['eliminar']=1;

/*para vistas
$ruta=$_GET["ruta"];
$_GET['ruta']=$ruta;
require_once "vista/plantilla.php";*/

//para controladores
$pagina=$_GET['pagina'];
require_once 'controlador/'.$pagina.'.php';

?>
