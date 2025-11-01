<?php

use Modelo\Caja;
use Modelo\Bitacora;
use Modelo\ControlCaja;
use Modelo\Divisa;

$objCaja = new Caja();
$objDivisa = new Divisa();
$objControl = new ControlCaja();
$objbitacora = new Bitacora();

if(!empty($_SESSION["permisos"]["config_finanza"]["consultar"])) {
    $divisas = $objDivisa->consultarDivisas();
    $datos = $objCaja->consultarCaja();
}


if (isset($_POST['buscar'])) {
    $nombre = $_POST['buscar'];
    $result = $objCaja->getbuscar($nombre);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else if (isset($_POST["guardar"]) && !empty($_SESSION["permisos"]["config_finanza"]["registrar"])) {
    if (!empty($_POST["nombre"])) {
        $errores = [];
        try {
            $data = [
                'nombre' => $_POST["nombre"],
                'cod_divisa' => $_POST["divisa"],
                'saldo' => $_POST["saldo"],
                'status' => 1,
            ];

            $objCaja->setData($data);
            $objCaja->check();

            if (!$objCaja->getbuscar($_POST['nombre'])) {

                $resul = $objCaja->getcrearCaja();

                if ($resul == 1) {
                    $registrar = [
                        "title" => "Exito",
                        "message" => "¡Registro exitoso!",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de caja', 'Nombre:'. $_POST["nombre"], 'Caja');
                } else {
                    $registrar = [
                        "title" => "Error",
                        "message" => "Hubo un problema al intentar registrar la caja..",
                        "icon" => "error"
                    ];
                }
            } else {
                $registrar = [
                    "title" => "Error",
                    "message" => "Ya existe una caja registrada con este nombre. Intenta de nuevo",
                    "icon" => "error"
                ];
            }
        } catch (Exception $e) {
            $errores[] = $e->getMessage();
            $registrar = [
                "title" => "Error",
                "message" => implode(" ", $errores),
                "icon" => "error"
            ];
        }
    }
} else if (isset($_POST['editar']) && !empty($_SESSION["permisos"]["config_finanza"]["editar"])) {
    $errores = [];

    try {
        
        $data = [
            'nombre' => $_POST["nombre1"],
            'saldo' => $_POST["saldo1"],
            'status' => $_POST["status"],
            'cod_caja' => $_POST['cod_caja'],
        ];

        $objCaja->setData($data);
        $objCaja->check();

        $cajaExistente = $objCaja->getbuscar($_POST["nombre1"]);
        if ($cajaExistente && $cajaExistente['cod_caja'] != $_POST['cod_caja']) {
            throw new Exception("El nombre de caja ya está en uso por otra caja.");
        }

        $resul = $objCaja->geteditar($_POST['cod_caja']);

        if ($resul == 1) {
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Edición de Caja', 'Nombre: '.$_POST["nombre1"], 'Caja');
            $editar = [
                "title" => "Editado con éxito",
                "message" => "La caja ha sido actualizada",
                "icon" => "success"
            ];
        } else {
            $editar = [
                "title" => "Error",
                "message" => $resul,
                "icon" => "error"
            ];
        }
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
        $editar = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    }
} else if (isset($_POST['eliminar']) && !empty($_SESSION["permisos"]["config_finanza"]["eliminar"])) {
    try {
        $cod_caja = $_POST['eliminar'];
        $resul = $objCaja->geteliminar($cod_caja);

        if ($resul == 'success') {
            $eliminar = [
                "title" => "Eliminado con éxito",
                "message" => "La caja ha sido eliminada",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar Caja', "Se eliminó la caja con el código " . $_POST["eliminar"], 'Caja');
        }
    } catch (Exception $e) {
        $eliminar = [
            "title" => "Error",
            "message" => $e->getMessage(),
            "icon" => "error"
        ];
    }
}else if (isset($_POST['cod_caja_historial'])) {
    $cod = $_POST['cod_caja_historial'];
    try {
        $result = $objCaja->getHistorialCaja($cod);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener historial: ' . $e->getMessage()
        ]);
    }
    exit;
} 

if(isset($_POST['movimientosHistorial']) && !empty($_SESSION["permisos"]["config_finanza"]["consultar"])) {

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





$_GET['ruta'] = 'caja';
require_once 'plantilla.php';
