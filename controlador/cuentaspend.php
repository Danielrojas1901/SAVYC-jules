<?php
use Modelo\CuentasPend;
use Modelo\Tpago;

$objtp = new Tpago();
$objCuentasPendientes = new CuentasPend();

//CONSULTAR DETALLE DEPENDIENDO DEL CLIENTE
if(isset($_POST['detallecuenta'])){
    $cobrarf = $objCuentasPendientes->getmostrar2($_POST['detallecuenta']);
    header('Content-Type: application/json');
    echo json_encode($cobrarf);
    exit;
} 

if(!empty($_SESSION["permisos"]["cuentas_pendiente"]["consultar"])){
$cobrar = $objCuentasPendientes->getmostrarcliente();
$pagar = $objCuentasPendientes->getmostrarCuentasPagar();
$totalcobrar = $objCuentasPendientes->getboxcobrar();
$totalpagar = $objCuentasPendientes->getboxpagar();
$formaspago = $objtp->consultar();
$opciones= $objtp->consultar();
}

require_once 'plantilla.php';