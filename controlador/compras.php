<?php

use Modelo\Compras;
use Modelo\Productos;
use Modelo\Bitacora;
use Modelo\Pago_Emitido;
use Modelo\Tpago;
use Modelo\Movimientos;

$objbitacora = new Bitacora();
$objCompras = new Compras();
$objProducto = new Productos();
$objpago = new Pago_Emitido();
$objtp = new Tpago();
$objmov = new Movimientos();
$categoria = $objProducto->consultarCategoria();
$unidad = $objProducto->consultarUnidad();


if (isset($_POST['buscar'])) {
    $resul = $objCompras->getbuscar_p($_POST['buscar']);
    header('Content-Type: application/json');
    echo json_encode($resul);
    exit;
} else if (isset($_POST['b_lotes']) && isset($_POST['cod'])) {
    $re = $objCompras->buscar_l($_POST['b_lotes'], $_POST['cod']);
    header('Content-Type: application/json');
    echo json_encode($re);
    exit;
} else if (isset($_POST['detallep'])) {
    $detalle = $objCompras->b_detalle($_POST['detallep']);
    header('Content-Type: application/json');
    echo json_encode($detalle);
    exit;
} else if (isset($_POST['cod_tipo_pago'])) {
    $cod_tipo_pago = $_POST['cod_tipo_pago'];
    $response = $objpago->saldo($cod_tipo_pago);
    header('Content-Type: application/json');
    echo json_encode(['n' => $response]);
    exit;
} else if (isset($_POST["registrar"]) && !empty($_SESSION["permisos"]["compra"]["registrar"])) {
    if (!empty($_POST["subtotal"]) && !empty($_POST["total_general"]) && !empty($_POST["cod_prov"]) && !empty($_POST["fecha"])) {
        if (isset($_POST['productos'])) {
            $objCompras->setdatac($_POST);
            $resul = $objCompras->getRegistrarr($_POST['productos']);
            echo "<script>console.log(" . $resul . ")</script>";
            if ($resul >= 1) {
                $objmov->rmovimiento($resul, 2);
                $registrar = [
                    "title" => "La compra ha sido registrada exitosamente.",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de compra', $_POST["subtotal"], 'Compras');
            } else {
                $registrar = [
                    "title" => "Error al registrar la compra.",
                    "icon" => "error"
                ];
            }
        } else {
            $registrar = [
                "title" => "No se encontraron productos en tu solicitud.",
                "icon" => "error"
            ];
        }
    } else {
        $registrar = [
            "title" => "Faltan campos obligatorios.",
            "icon" => "error"
        ];
    }
} else if (isset($_POST['anular']) && !empty($_SESSION["permisos"]["compra"]["eliminar"])) {
    if (!empty($_POST['codcom'])) {
        $resul = $objCompras->anular($_POST["codcom"]);

        if ($resul == 1) {
            $eliminar = [
                "title" => "Eliminado con éxito",
                "message" => "la  compra ha sido eliminada",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Anulación de compra', $_POST["codcom"], 'Compras');
        } elseif ($resul == 0) {
            $eliminar = [
                "title" => "Error",
                "message" => "Hubo un problema al eliminar la compra",
                "icon" => "error"
            ];
        }
    }
} else if (isset($_POST['pagar_compra'])  && !empty($_SESSION["permisos"]["compra"]["registrar"]) || isset($_POST['pagocompracuenta']) && !empty($_SESSION["permisos"]["cuentas_pendiente"]["registrar"])) {
    $errores = [];

    try {
        $objpago->setDatos($_POST);
        $objpago->check();
        $res = $objpago->registrarPgasto();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }
    if (!empty($errores)) {
        $registrarPC = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else if ($res == 0) {
        $objmov->mpagos($objpago->getcod_pago(), 4, 4);
        //echo '<script>console.log('.$objpago->getcod_pago().');</script>';
        $registrarPC = [
            "title" => "El pago de la compra ha sido registrado exitosamente.",
            "message" => "La compra se ha completado.",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de pago completo', 'Compra #' . $_POST['cod_compra'] . ' - Monto: ' . $_POST['montopagado'], (isset($_POST['pagocompracuenta']) ? 'Cuentas por Pagar - Pago emitido' : 'Compras - Pago emitido'));
    } else if ($res > 0) {
        $objmov->mpagos($objpago->getcod_pago(), 4, 4);
        //echo '<script>console.log('.$objpago->getcod_pago().');</script>';
        $registrarPC = [
            "title" => "Se ha registrado un pago parcial.",
            "message" => "El monto pendiente es de " . $res . "Bs.",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de pago parcial', 'Compra #' . $_POST['cod_compra'] . ' - Monto: ' . $_POST['montopagado'], (isset($_POST['pagocompracuenta']) ? 'Cuentas por Pagar - Pago emitido' : 'Compras - Pago emitido'));
    } else if (!is_numeric($res)) {
        $registrarPC = [
            "title" => "Advertencia",
            "message" => $res,
            "icon" => "warning"
        ];
    } else {
        $registrarPC = [
            "title" => "Error",
            "message" => "Ocurrio un error inesperado al registrar el pago",
            "icon" => "error"
        ];
    }
}
if (!empty($_SESSION["permisos"]["compra"]["consultar"]) || !empty($_SESSION["permisos"]["cuentas_pendiente"]["consultar"])) {
    $d = $objCompras->divisas();
    $compra = $objCompras->getconsultar();
    $formaspago = $objtp->consultar();
}

if (isset($_POST["pagocompracuenta"])) {
    $_GET['ruta'] = 'cuentaspend';
} else {
    $_GET['ruta'] = 'compras';
}
require_once 'plantilla.php';
