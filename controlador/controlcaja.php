<?php

use Modelo\Bitacora;
use Modelo\ControlCaja;

$objControl = new ControlCaja();
$objbitacora = new Bitacora();

if(!empty($_SESSION["permisos"]["tesoreria"]["consultar"])){
$listado = $objControl->getConsultarActivas();
}

//VALIDAR SI LA CAJA ESTA ABIERTA
if (isset($_POST['buscar_control_abierto'])) {
    $cod_caja = (int) $_POST['cod_caja'];

    try {
        $control = $objControl->consultarControlHoy($cod_caja);
        echo json_encode([
            'cod_control' => $control['cod_control'] ?? null,
            'fecha_apertura' => $control['fecha_apertura'] ?? null,
        ]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al consultar control: ' . $e->getMessage()]);
    }
    exit;
}

//VER MOVIMIENTOS
if (isset($_POST['movimientosActuales']) && !empty($_SESSION["permisos"]["tesoreria"]["registrar"])) {
    
    try {
        $movimientos = $objControl->getobtenerMovControl($_POST['cod_control']);

        header('Content-Type: application/json');
    echo json_encode([
    
            'movimientos' => $movimientos
    ]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'Error al obtener movimientos: ' . $e->getMessage()]);
    }
    exit;
}

// ABRIR CAJA
if (isset($_POST["abrir_caja"]) && !empty($_SESSION["permisos"]["tesoreria"]["registrar"])) {

    $errores = [];
    try {
        $data = [
            'cod_control' => null,
            'fecha_apertura' => $_POST["fecha_apertura"],
            'fecha_cierre' => null,
            'monto_apertura' => $_POST["saldoa"] ?? 0,
            'monto_cierre' => 0,
            'cod_caja' => $_POST["cod_caja"],
            'status' => 1
        ];

        $objControl->setData($data);
        $objControl->check();
        $resultado = $objControl->getAbrirCaja($_POST["saldoa"]);

        if ($resultado == 1) {
            $respuesta = [
                "title" => "Éxito",
                "message" => "Caja abierta correctamente.",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Apertura de Caja', "Caja cod #{$_POST['cod_caja']}", 'Caja');
        } else {
            $errores[] = "No se pudo abrir la caja.";
            $respuesta = [
                "title" => "Error",
                "message" => implode(" ", $errores),
                "icon" => "error"
            ];
        }
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
        $respuesta = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    }
} else if (isset($_POST['cerrar_caja'])) {
    try {
        $datos = [
            'cod_caja' => $_POST['cod_caja'] ?? null,
            'cod_control' => $_POST['cod_control'] ?? null,
            'monto_cierre' => $_POST['monto_contado'] ?? 0,
            'observacion' => trim($_POST['observacion'] ?? ''),
            'fecha_cierre' => date('Y-m-d H:i:s'),
        ];
        $objControl->setData($datos);
        $objControl->check();
        $resultado = $objControl->getCerrarCaja(); 

        if ($resultado == 1) {
            $respuesta = [
                'title' => 'Éxito',
                'message' => 'La caja fue cerrada y los datos fueron registrados.',
                'icon' => 'success'
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Cierre de Caja', "Caja cod #{$_POST['cod_caja']}", 'Caja');
        } else {
            $respuesta = [
                'title' => 'Error',
                'message' => 'No se pudo cerrar la caja. Verifica los datos.',
                'icon' => 'error'
            ];
        }
    } catch (Exception $e) {
        $respuesta = [
            'title' => 'Error',
            'message' => $e->getMessage(),
            'icon' => 'error'
        ];
    }
}

if (isset($_POST['resumen_pagos_caja'])) {
    $cod_control = $_POST['cod_control'] ?? null;
    //$cod_caja = $_POST['cod_caja'] ?? null;

    $resumen = $objControl->getResumenCerrar($cod_control);

    if ($resumen && is_array($resumen)) {
        echo json_encode([
            'success' => true,
            'resumen' => $resumen
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No se encontraron movimientos para esta caja.'
        ]);
    }
    exit;
}
