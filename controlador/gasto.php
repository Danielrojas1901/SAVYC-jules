<?php

use Modelo\Gasto;
use Modelo\Pago_Emitido;
use Modelo\Bitacora;
use Modelo\Tpago;
use Modelo\Movimientos;
use Modelo\CategoriaGasto;

$objmov = new Movimientos();
$objgasto = new Gasto();
$objpago = new Pago_Emitido();
$objtp = new Tpago();
$objbitacora = new Bitacora();
$catGasto = new CategoriaGasto();
if (isset($_POST['buscar'])) {
    $descripcion = $_POST['buscar'];
    $objgasto->setDatos(['descripcion' => $descripcion]);
    $result = $objgasto->buscar_gasto();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else if (isset($_POST['cod_tipo_pago'])) {
    $cod_tipo_pago = $_POST['cod_tipo_pago'];
    $response = $objpago->saldo($cod_tipo_pago); 
    header('Content-Type: application/json');
    echo json_encode(['n' => $response]);
    exit;
} else if (isset($_POST['mostrarTporC'])) {
    $catGasto->setDatos(['cod_cat_gasto' => $_POST['mostrarTporC']]);
    $resul = $catGasto->buscarTporCategoria();
    $tipoGasto = $resul['nombret'];
    header('Content-Type: application/json');
    echo json_encode(['tipo_gasto' => $tipoGasto]);
    exit;
} else if (isset($_POST['mostrarFVporN'])) {
    $catGasto->setDatos(['cod_cat_gasto' => $_POST['mostrarFVporN']]);
    $resul = $catGasto->mostrarFVporN();
    $natu = $resul['nombrenatu'];
    header('Content-Type: application/json');
    echo json_encode(['natu' => $natu]);
    exit;
} else if (isset($_POST['guardarC']) && !empty($_SESSION["permisos"]["config_finanza"]["registrar"])) {
    $errores = [];
    try {
        $catGasto->setDatos($_POST);
        $catGasto->check();
        $resul = $catGasto->publicregistrarc();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }

    if (!empty($errores)) {
        $guardarC = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else {
        if ($resul == 1) {
            $guardarC = [
                "title" => "Registrado con éxito",
                "message" => "La información de la categoría ha sido registrada",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de categoría de gastos', $_POST["nombre"], 'Categoría de gastos');
        } else if ($resul == 2) {
            $guardarC = [
                "title" => "Advertencia",
                "message" => "La categoría ya se encuentra registrada",
                "icon" => "warning"
            ];
        } else {
            $guardarC = [
                "title" => "Error",
                "message" => "Error al registrar la categoría de gastos".implode(" | ", $errores),
                "icon" => "error"
            ];
        }
    }
} else if (isset($_POST['guardarG']) && !empty($_SESSION["permisos"]["gasto"]["registrar"])) {
    $errores = [];
    try {
        $objgasto->setDatos($_POST);
        $objgasto->check();

        $resul = $objgasto->publicregistrarg();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }
    if (!empty($errores)) {
        $guardarG = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else if ($resul == 1) {
        $objmov->rmovimiento($objgasto->get_codgasto(), 3);
        $guardarG = [
            "title" => "Registrado con éxito",
            "message" => "El gasto ha sido registrado",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de gasto', $_POST["descripcion"], 'Gasto');
    } else if ($resul == 2) {
        $guardarG = [
            "title" => "Gasto ya registrado",
            "message" => "No se puede registrar el gasto; ya existe un gasto con la misma descripción",
            "icon" => "warning"
        ];
    } else {
        $guardarG = [
            "title" => "Error",
            "message" => "Error al registrar el gasto. Error inesperado".implode(" | ", $errores),
            "icon" => "error"
        ];
    }
} else if (isset($_POST['pagar_gasto']) || isset($_POST['pagogastocuenta']) && !empty($_SESSION["permisos"]["cuentas_pendiente"]["registrar"])) {
    $errores = [];
    try {
        $objpago->setDatos($_POST);
        $objpago->check();
        $res = $objpago->registrarPgasto();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }
    if (!empty($errores)) {
        $registrarPG = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"

        ];
    } else if ($res == 0) {
        $objmov->mpagos($objpago->getcod_pago(), 4, 5);
        $registrarPG = [
            "title" => "El pago del gasto ha sido registrado exitosamente.",
            "message" => "El gasto se ha completado.",
            "icon" => "success"

        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de pago completo', 'Gasto #' . $_POST['cod_gasto'] . ' - Monto: ' . $_POST['montopagado'], (isset($_POST['pagogastocuenta']) ? 'Cuentas por Pagar - Pago emitido' : 'Gastos - Pago emitido'));
    } else if ($res > 0) {
        $objmov->mpagos($objpago->getcod_pago(), 4, 5);
        $registrarPG = [
            "title" => "Se ha registrado un pago parcial.",
            "message" => "El monto pendiente es de " . $res . "Bs.",
            "icon" => "success"

        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de pago parcial', 'Gasto #' . $_POST['cod_gasto'] . ' - Monto: ' . $_POST["montopagado"], (isset($_POST['pagogastocuenta']) ? 'Cuentas por Pagar - Pago emitido' : 'Gastos - Pago emitido'));
    }  else {
        $registrarPG = [

            "title" => "Error",
            "message" => "Ocurrio un error inesperado al registrar el pago".implode(" | ", $errores),
            "icon" => "error"

        ];
    }
} else if (isset($_POST['eliminarG']) && !empty($_SESSION["permisos"]["gasto"]["eliminar"])) {
    $errores = [];
    try {

        $objgasto->setDatos($_POST);
        $objgasto->check();
        $res = $objgasto->eliminarGasto();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }
    if (!empty($errores)) {
        $eliminar = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else if ($res == 'success') {

        $eliminar = [
            "title" => "Eliminado con éxito",
            "message" => "El gasto ha sido eliminado",
            "icon" => "success"
        ];

        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminación de gasto', $_POST["cod_gasto"], 'Gasto');
    } else if ($res == 'error_associated') {
        $eliminar = [
            "title" => "Error",
            "message" => "Error al eliminar el gasto tiene pagos asociados",
            "icon" => "error"
        ];
    } else if ($res == 'error_delete') {
        $eliminar = [
            "title" => "Error",
            "message" => "Error al eliminar el gasto",
            "icon" => "error"
        ];
    } else if ($res == 'error_query') {
        $eliminar = [
            "title" => "Error",
            "message" => "Hubo un problema de consulta al eliminar el gasto",
            "icon" => "error"
        ];
    } else {
        $eliminar = [
            "title" => "Error",
            "message" => "Ocurrio un error inesperado al eliminar el gasto".implode(" | ", $errores),
            "icon" => "error"
        ];
    }
} else if (isset($_POST['editarG']) && !empty($_SESSION["permisos"]["gasto"]["editar"])) {
    $errores = [];

    try {
        $objgasto->setDatos($_POST);
        $objgasto->check();
        $resul = $objgasto->editarGasto();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }
    if (!empty($errores)) {
        $editarG = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else if ($resul == 1) {
        $editarG = [
            "title" => "Editado con éxito",
            "message" => "El gasto ha sido editado",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Edición de gasto', $_POST["descripcion"], 'Gasto');
    }else {
        $editarG = [
            "title" => "Error",
            "message" => "Error al editar el gasto. Error inesperado".implode(" | ", $errores),
            "icon" => "error"
        ];
    }
}else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cod_gasto'])) {
    $objpago->setDatos(['cod_gasto' => $_POST['cod_gasto']]);
    $resultado = $objpago->getGastos();
    if (!empty($resultado)) {
        echo json_encode(['success' => true, 'monto_total' => $resultado['monto_total']]);
        exit;
    } else {
        echo json_encode(['success' => false, 'monto_total' => 0]);
        exit;
    }
}

$gasto = $objpago->getGastos();
$naturaleza = $catGasto->consulNaturaleza();
$frecuencia = $catGasto->consultarFrecuencia();
$tipo = $catGasto->consultarTipo();
$categorias = $catGasto->consultarCategoria();
$condicion = $catGasto->consultarCondi();
$gastosF = $objgasto->consultarGastoF();
$gastosV = $objgasto->consultarGastoV();
$totalV = $objgasto->consultarTotalV();
$totalF = $objgasto->consultarTotalF();
$totalG = $objgasto->consultarTotalG();
$totalP = $objgasto->consultarTotalP();
$formaspago = $objtp->consultar();

if (isset($_POST["pagogastocuenta"])) {
    $_GET['ruta'] = 'cuentaspend';
} else {
    $_GET['ruta'] = 'gasto';
}
require_once 'plantilla.php';
