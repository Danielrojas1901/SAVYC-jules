<?php

use Modelo\CuentaBancaria;
use Modelo\Bitacora;
use Modelo\Banco;
use Modelo\Divisa;

$objBanco = new Banco;
$objDivisa = new Divisa;
$objCuenta = new CuentaBancaria;

$objbitacora = new Bitacora();

$banco = $objBanco->consultar();
$tipo = $objCuenta->consultarTipo();
$Cuenta = $objCuenta->consultarCuenta();
$divisas = $objDivisa->consultarDivisas();

if (isset($_POST["guardar"]) || isset($_POST["guardaru"]) && !empty($_SESSION["permisos"]["tesoreria"]["registrar"])) {
    if (!empty($_POST["numerocuenta"])) {
        $errores = [];
        try {


            $data = [
                'numero_cuenta' => $_POST["numerocuenta"],
                'divisa' => $_POST["divisa"],
                'cod_banco' => $_POST["banco"],
                'cod_tipo_cuenta' => $_POST["tipo_cuenta"],
                'saldo' => $_POST["saldo"],
                'status' => 1,


            ];

            $objCuenta->setData($data);

          
            $objCuenta->check();

         
            $resul = $objCuenta->getcrearCuenta();

            if ($resul == 1) {
                $registrar = [
                    "title" => "Exito",
                    "message" => "¡Registro exitoso!",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de Cuenta', $_POST["numerocuenta"], 'Cuenta Bancaria');
            } else {
                $registrar = [
                    "title" => "Error",
                    "message" => "Hubo un problema al intentar registrar la cuenta bancaria.",
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
}else if (isset($_POST['guardarB']) && !empty($_SESSION["permisos"]["config_finanza"]["registrar"])) {
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
} else if (isset($_POST['editar']) && !empty($_SESSION["permisos"]["tesoreria"]["editar"])) {
    $errores = [];
    try {

        $data = [
            'numero_cuenta' => $_POST["numero_cuenta1"],
            'divisa' => $_POST["divisa1"],
            'cod_banco' => $_POST["banco1"],
            'cod_tipo_cuenta' => $_POST["tipodecuenta1"],
            'saldo' => $_POST["saldo1"],
            'status' => $_POST["status"],
            'cod_cuenta_bancaria' => $_POST['cod_cuenta_bancaria1'],
            'origin' => $_POST['origin'],

        ];

        $objCuenta->setData($data);
        $objCuenta->check();

       
        $resul = $objCuenta->geteditar();
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
        
    }
    if(!empty($errores)){
        $editar = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    }else if ($resul == 1) {
        
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar Cuenta', $_POST["numero_cuenta1"], 'Cuenta Bancaria');
        $editar = [
            "title" => "Editado con éxito",
            "message" => "La cuenta bancaria ha sido actualizada",
            "icon" => "success"
        ];
    } else {
        $editar = [
            "title" => "Error",
            "message" => "Hubo un problema al editar la cuenta bancaria.",
            "icon" => "error"
        ];
    }
} else if (isset($_POST['eliminar']) && !empty($_SESSION["permisos"]["tesoreria"]["eliminar"])) {

    $cod_cuenta_bancaria = $_POST['eliminar'];
    $resul = $objCuenta->geteliminar($cod_cuenta_bancaria);

    if ($resul == 'success') {
        $eliminar = [
            "title" => "Eliminado con éxito",
            "message" => "La Cuenta ha sido eliminada",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar Cuenta Bancaria', "Eliminada la cuenta bancaria con el numero " . $_POST["eliminar"], 'Cuenta Bancaria');
    } else if ($resul == 'error_status') {
        $eliminar = [
            "title" => "Error",
            "message" => "La cuenta bancaria no se puede eliminar porque tiene status: activo",
            "icon" => "error"
        ];
    } else if ($resul == 'error_delete') {
        $eliminar = [
            "title" => "Error",
            "message" => "Hubo un problema al eliminar la cuenta bancaria error delete",
            "icon" => "error"
        ];
    } else if ($resul == 'error_query') {
        $eliminar = [
            "title" => "Error",
            "message" => "Hubo un problema al eliminar la cuenta bancaria error",
            "icon" => "error"
        ];
    } else if ($resul == 'error_tipo_pago') {

        $eliminar = [
            "title" => "Error",
            "message" => "No se puede eliminar la cuenta bancaria porque tiene un tipo de pago asociado",
            "icon" => "error"
        ];
    } else  if ($resul == 'error_saldo') {
        $eliminar = [
            "title" => "Error",
            "message" => "No se puede eliminar la cuenta bancaria porque tiene un saldo positivo",
            "icon" => "error"
        ];
    }
}else if (isset($_POST['detalle'])) {
    $detalle = $objCuenta->obtenerMovimientosCuentaBancaria($_POST['detalle']);
    header('Content-type: application/json');
    echo json_encode($detalle);
    exit;
}
$datos = $objCuenta->consultarCuenta();
$_GET['ruta'] = 'cuentabancaria';
require_once 'plantilla.php';
