<?php

use Modelo\Bitacora;
use Modelo\CategoriaGasto;

$objbitacora = new Bitacora();
$objgasto = new CategoriaGasto();

if (isset($_POST['buscar'])) {
    $nombre = $_POST['buscar'];
    $objgasto->setDatos(['nombre' => $nombre]);
    $result = $objgasto->buscarCategoria();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else if (isset($_POST['buscarF'])) {
    $objgasto->setDatos(['frecuencia' => $_POST['buscarF']]);
    $result = $objgasto->buscarFrecuencia();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
} else if (isset($_POST['guardar_frecuencia']) && !empty($_SESSION["permisos"]["config_finanza"]["registrar"])) {
    $errores = [];
    try {
        $objgasto->setDatos($_POST);
        $objgasto->check();
        $resul = $objgasto->publicregistrarf();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }
    if (!empty($errores)) {
        $guardarF = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else {
        if ($resul == 1) {
            $guardarF = [
                "title" => "Registrado con éxito",
                "message" => "Frecuencia de pagos de gastos registrada con éxito". implode(" | ", $errores),
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de frecuencia de gasto', $_POST["frecuencia"], 'plazo de pago');
        }
    }
} else if (isset($_POST['guardarC']) && !empty($_SESSION["permisos"]["config_finanza"]["registrar"])) {
    $errores = [];
    try {
        $objgasto->setDatos($_POST);
        $objgasto->check();
        $resul = $objgasto->publicregistrarc();
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
        } else if ($resul == 'error_frecuencia') {
            $guardarC = [
                "title" => "Advertencia",
                "message" => "Si se registra una categoría de gastos con naturaleza fijo, debe registrarse una frecuencia de pago",
                "icon" => "warning"
            ];
        } else {
            $guardarC = [
                "title" => "Error",
                "message" => "Error al registrar la categoría de gastos". implode(" | ", $errores),
                "icon" => "error"
            ];
        }
    }
} else if (isset($_POST['editarG']) && !empty($_SESSION["permisos"]["config_finanza"]["editar"])) {
    $errores = [];
    try {
        $objgasto->setDatos($_POST);
        $objgasto->check();
        $res = $objgasto->editarC();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }
    if (!empty($errores)) {
        $editar = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else if ($res == 1) {
        $editar = [
            "title" => "Editado con éxito",
            "message" => "La categoría de gastos ha sido editada",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar categoría de gastos', $_POST["cod_cat_gasto"], 'Categoría de gastos');
    } else if ($res == 'error_associated') {
        $editar = [
            "title" => "Advertencia",
            "message" => "La categoría de gastos ya se encuentra registrada",
            "icon" => "warning"
        ];
    } else if ($res == 'error_query') {
        $editar = [
            "title" => "Error",
            "message" => "Hubo un problema de consulta al editar la categoría de gastos",
            "icon" => "error"
        ];
    } else {
        $editar = [
            "title" => "Error",
            "message" => "Error al editar la categoría de gastos". implode(" | ", $errores),
            "icon" => "error"
        ];
    }
} else if (isset($_POST['cod_cat_gasto']) && !empty($_SESSION["permisos"]["config_finanza"]["eliminar"])) {
    $objgasto->setDatos($_POST);
    $objgasto->check();
    $res = $objgasto->eliminarCat();
    if ($res == 'success') {
        $eliminar = [
            "title" => "Eliminado con éxito",
            "message" => "La categoría de gastos ha sido eliminada",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminación de categoría de gastos', $_POST["cod_cat_gasto"], 'Categoría de gastos');
    } else if ($res == 'error_associated') {
        $eliminar = [
            "title" => "Advertencia",
            "message" => "La categoría de gastos tiene gastos asociados",
            "icon" => "warning"
        ];
    } else if ($res == 'error_delete') {
        $eliminar = [
            "title" => "Error",
            "message" => "Error al eliminar la categoría de gastos",
            "icon" => "error"
        ];
    } else if ($res == 'error_query') {
        $eliminar = [
            "title" => "Error",
            "message" => "Hubo un problema de consulta al eliminar la categoría de gastos",
            "icon" => "error"
        ];
    } else if ($res == 'error_status') {
        $eliminar = [
            "title" => "Advertencia",
            "message" => "No se puede eliminar una categoría con estatus activo",
            "icon" => "warning"
        ];
    }
}

$frecuencia = $objgasto->consultarFrecuencia();
$tipo = $objgasto->consultarTipo();
$categorias = $objgasto->consultarCategoria();
$naturaleza = $objgasto->consulNaturaleza();

$_GET['ruta'] = 'categoriag';

require_once 'plantilla.php';
