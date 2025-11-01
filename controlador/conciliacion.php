<?php

use Modelo\CuentaBancaria;
use Modelo\Bitacora;
use Modelo\Conciliacion; // Modelo para leer el PDF

$objCuenta = new CuentaBancaria();
$objbitacora = new Bitacora();
$objPdf = new Conciliacion();

// ==================== PROCESO AJAX: Lectura de PDF ====================
if (isset($_POST['bancario_pdf']) && !empty($_SESSION["permisos"]["tesoreria"]["registrar"])) {
    ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

    header('Content-Type: application/json');

    // Verificamos que exista el archivo
    if (!isset($_FILES['archivo_pdf']) || $_FILES['archivo_pdf']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo cargar el archivo PDF. Código: ' . ($_FILES['archivo_pdf']['error'] ?? 'desconocido')
        ]);
        exit;
    }

    $tmpName = $_FILES['archivo_pdf']['tmp_name'];

    // Verificamos que el archivo temporal exista y sea válido
    if (!is_uploaded_file($tmpName) || !file_exists($tmpName)) {
        echo json_encode([
            'success' => false,
            'message' => 'El archivo temporal no es válido o no se encuentra.'
        ]);
        exit;
    }

  try {
    $registros = $objPdf->leerPDF($_FILES['archivo_pdf']['tmp_name']);

  

    echo json_encode(['success' => true, 'registros' => $registros]);
    exit;

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error al procesar PDF: ' . $e->getMessage()]);
    exit;
}
}

// ==================== OPERACIONES POST CLÁSICAS ====================
if (isset($_POST['guardar'])) {
    // lógica guardar...
} elseif (isset($_POST['actualizar'])) {
    // lógica actualizar...
} elseif (isset($_POST['borrar'])) {
    // lógica borrar...
}

// ==================== CONSULTA GENERAL PARA VISTA ====================

$datos = [];
if(!empty($_SESSION["permisos"]["tesoreria"]["consultar"])){
    $datos = $objCuenta->consultarCuenta();
}

$_GET['ruta'] = 'conciliacion';
require_once 'plantilla.php';