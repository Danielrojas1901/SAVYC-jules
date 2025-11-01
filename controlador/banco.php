<?php

use Modelo\Banco;
use Modelo\Bitacora;

$objBanco = new Banco();
$objbitacora = new Bitacora();

if (isset($_POST['guardar']) && !empty($_SESSION["permisos"]["config_finanza"]["registrar"])) {
    $errores = [];
    try {

        $objBanco->setDatos($_POST);
        $objBanco->check();
        $result = $objBanco->getRegistrar();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }


    if (!empty($errores)) {
        $registrar = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else if ($result == 1) {
        $registrar = [
            "title" => "Registrado con éxito",
            "message" => "El banco ha sido registrado",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de banco', $_POST["nombre"], 'Banco');
    }else if($result == 'error_nombre'){
        $registrar = [
            "title" => "Error",
            "message" => "El nombre del banco ya existe",
            "icon" => "error"
        ];

    } else {
        $registrar = [
            "title" => "Error",
            "message" => "Hubo un problema al registrar el banco",
            "icon" => "error"
        ];
    }
} else if (isset($_POST['actualizar']) && !empty($_SESSION["permisos"]["config_finanza"]["editar"])) {
    $errores = [];

    try {

        $objBanco->setDatos($_POST);
        $objBanco->check();
        $result = $objBanco->getactualizar();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }

    if (!empty($errores)) {
        $editar = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else if ($result == 1) {
        $editar = [
            "title" => "Editado con éxito",
            "message" => "Los datos del banco han sido actualizados",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar banco', $_POST["nombre"], 'Banco');
    }else if($result == 'error_nombre'){
        $editar = [
            "title" => "Error",
            "message" => "El nombre del banco no puede ser de otro banco",
            "icon" => "error"
        ];
    }else {
        $editar = [
            "title" => "Error",
            "message" => "Hubo un problema al editar los datos del banco",
            "icon" => "error"
        ];
    }
} else if (isset($_POST['borrar']) && !empty($_SESSION["permisos"]["config_finanza"]["eliminar"])) {
    $errores = [];
    try {
        
        $objBanco->setDatos($_POST);
        $objBanco->check();

        $result = $objBanco->getEliminar();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }

    if (!empty($errores)) {
        $registrar = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    }else if ($result == 'success') {
        $eliminar = [
            "title" => "Eliminado con éxito",
            "message" => "El banco ha sido eliminado",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar Banco', "Eliminado el banco con el código " . $_POST["cod_banco"], 'Banco');
    } else if ($result == 'error_cuenta') {
        $eliminar = [
            "title" => "Error",
            "message" => "El banco no se puede eliminar porque tiene cuentas bancarias asociadas",
            "icon" => "error"
        ];
    }else {
        $eliminar = [
            "title" => "Error",
            "message" => "Hubo un problema al eliminar el banco",
            "icon" => "error"
        ];
    }
}

$registro = $objBanco->consultar();

$_GET['ruta'] = 'banco';
require_once 'plantilla.php';
