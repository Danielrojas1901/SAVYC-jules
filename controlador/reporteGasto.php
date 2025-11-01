<?php
use Modelo\Gasto;

// Activar reporte de errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

 

try {
    $objgasto = new Gasto();
    
    // Obtener datos para los reportes
    $data = [
        'gastosFijos' => $objgasto->consultarGastoF(),
        'gastosVariables' => $objgasto->consultarGastoV(),
        'gastos' => $objgasto->repSet(),
        'totalF' => $objgasto->consultarTotalF(),
        'totalV' => $objgasto->consultarTotalV(),
        'totalG' => $objgasto->consultarTotalG(),
        'totalP' => $objgasto->consultarTotalP(),
        'categorias' => $objgasto->consultarCategoria(),
     
    ];

    // Depuración (descomentar si es necesario)
    // echo '<pre>'.print_r($data, true).'</pre>'; exit;

    // Hacer disponibles las variables en la vista
    extract($data);
    
    $_GET['ruta'] = 'rep-gastos';
    require_once 'plantilla.php';
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}