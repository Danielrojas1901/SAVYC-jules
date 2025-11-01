<?php

include 'app/config.php';


$tipo = $_POST['tipo'] ?? null;
$fechaInicio = $_POST['fechaInicio1'] ?? null;
$fechaFin = $_POST['fechaFin1'] ?? null;

if (! $tipo) {
    die('Debe especificar un tipo de reporte.');
}

$map = [
    'productos'      => 'productos.php',
    'productoscat'   => 'productoscat.php',
    'productosexcel' => 'productosexcel.php',
    'proveedores'    => 'proveedores.php',
    'clientes'       => 'clientes.php',
    'venta'          => 'venta.php',
    'compra'         => 'compra.php',
    'carga'          => 'carga.php',
    'descarga'       => 'descarga.php',
    'compra'         => 'compra.php',
    'ventaxlsx'      => 'ventaxlsx.php',
    'vclientes'      => 'vclientes.php',
    'gastos'         => 'gastos.php',
    'gastosexcel'    => 'gastosexcel.php',
    'fijos'          => 'fijos.php',
    'variables'      => 'variables.php',
    'pagar'          => 'pagar.php',
    'cobrar'         => 'cobrar.php',
    'mayor'         => 'repcont.php',
    'caja'          => 'caja.php',

];

if (! isset($map[$tipo])) {
    die('Tipo de reporte no válido.');
}

$pdfScriptBase = 'reportes/' . $map[$tipo];

if (!file_exists($pdfScriptBase)) {
    die("No existe el archivo de reporte: {$pdfScriptBase}");
}

// Armamos parámetros para carga con fechas
$params = '';
if ($tipo === 'carga' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}

if ($tipo === 'descarga' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}

if ($tipo === 'compra' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}
if ($tipo === 'venta' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}
if ($tipo === 'ventaxlsx' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}
if ($tipo === 'vclientes' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}
if ($tipo === 'fijos' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}
if ($tipo === 'variables' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}
if ($tipo === 'pagar' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}
if ($tipo === 'cobrar' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}
if ($tipo === 'productoscat' && isset($_POST['codigocategoria'])) {
    $params = '?codigocategoria=' . urlencode($_POST['codigocategoria']);
}
if ($tipo === 'caja' && isset($_POST['cod_control']) && isset($_POST['fechaa']) && isset($_POST['fechac'])) {
    $params = '?cod_control=' . urlencode($_POST['cod_control']) . '&cod_caja=' . urlencode($_POST['cod_caja']) . '&fechaa=' . urlencode($_POST['fechaa']) . '&fechac=' . urlencode($_POST['fechac']);
}
if ($tipo === 'mayor' && !empty($fechaInicio) && !empty($fechaFin)) {
    $params = '?fechaInicio1=' . urlencode($fechaInicio) . '&fechaFin1=' . urlencode($fechaFin) . '&fechas=true';
}


// Finalmente, redirigimos al script con parámetros
header("Location: {$pdfScriptBase}{$params}");
exit;
