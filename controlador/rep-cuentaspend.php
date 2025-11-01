<?php
use Modelo\CuentasPend;
use Modelo\Bitacora;


$obcuentas = new CuentasPend();

// Obtener datos
$cuentasPagar = $obcuentas->getmostrarCuentasPagar();
$cuentasCobrar = $obcuentas->getmostrar3();
//$tipopagar = $obcuentas->gettipopagar();

// Pasar nombre de ruta a la plantilla
$_GET['ruta'] = 'rep-cuentaspend';
require_once 'plantilla.php';
