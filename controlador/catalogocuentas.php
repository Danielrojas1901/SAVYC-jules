<?php

use Modelo\CatalogoCuentas;
use Modelo\Bitacora;

$objCuenta = new CatalogoCuentas();
$objBitacora = new Bitacora();

//VALIDAR QUE NO SEA EL MISMO NOMBRE
if(isset($_POST['buscar'])){
    $nombre = $_POST['buscar'];
    $result = $objCuenta->getbuscar($nombre); 
    header('Content-Type: application/json'); 
    echo json_encode($result); 
    exit;
}


// QUE ME TRAIGA LAS CUENTAS PADRES
if(isset($_POST['padre'])){
    $nivel = $_POST['padre'];
    $result=$objCuenta->get_listarcuentaspadrespornivel($nivel);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

// GENERAR CODIGO DE CUNETA PADRE
else if (isset($_POST['generarRaiz'])) {
    $nivel = $_POST['nivel'];
    $codPadre = $_POST['cod_padre'];
    $result=$objCuenta->get_generarCodigo($nivel,$codPadre);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}


// SI ES HIJA, GEENRAR EL CODIGO
else if (isset($_POST['codigohija'])) {
    $nivel = $_POST['nivel'];
    $codPadre = $_POST['cod_padre'];
    $result= $objCuenta->get_generarCodigo($nivel, $codPadre);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;

}else if (isset($_POST['guardar']) && !empty($_SESSION["permisos"]["contabilidad"]["registrar"])) {
    $errores = [];
    if (!$objCuenta->getbuscar($_POST["nombreCuenta"])){
        try {
            $objCuenta->setDatos($_POST);
            $objCuenta->check();
            $resultado = $objCuenta->getregistrar();

            if ($resultado == 1) {
            $respuesta = [
                "title" => "Registrado",
                "message" => "La cuenta fue registrada exitosamente.",
                "icon" => "success"
                ];
            $objBitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de cuenta contable', $_POST['nombreCuenta'], 'Contabilidad');
            
            } else {
            $respuesta = [
                "title" => "Error",
                "message" => "Ocurrió un problema al registrar la cuenta.",
                "icon" => "error"
                ];
            }
        } catch (Exception $e) {
            $errores[] = $e->getMessage();

            if (!empty($errores)) {
            $respuesta = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
            ];
            }
        }
    }else {
        $respuesta = [
            "title" => "Error",
            "message" => "No se puede registrar la cuenta con un nombre existente.",
            "icon" => "error"
        ];
    }



//EDITAR CUENTA
}else if(isset($_POST['editar']) && !empty($_SESSION["permisos"]["contabilidad"]["editar"])){
    
    $errores=[];
    if (!$objCuenta->getbuscar($_POST["nombreCuenta"])){
        try{
            //echo '<script>console.log("Entrando a editar cuenta contable")</script>';
            //echo '<script>console.log("Datos recibidos: ' . json_encode($_POST) . '")</script>';
            $objCuenta->setDatos($_POST);
            $objCuenta->check();
            $r = $objCuenta->geteditar($_POST);
            if($r == 1){
                $respuesta = [
                    "title" => "Editado",
                    "message" => "La cuenta fue editada exitosamente.",
                    "icon" => "success"
                ];
                $objBitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Edición de cuenta contable con el código', $_POST['codigocuenta'], 'Contabilidad');
            }else {
                $respuesta = [
                    "title" => "Error",
                    "message" => "Ocurrió un problema al editar la cuenta.",
                    "icon" => "error"
                ];
            }

        }catch(Exception $e){
            $errores[]= $e->getMessage();
            if(!empty($errores)){
                $respuesta = [
                    "title" => "Error",
                    "message" => implode(" ", $errores),
                    "icon" => "error"
                ];
            }
        }
    }else {
        $respuesta = [
            "title" => "Error",
            "message" => "No se puede editar la cuenta con un nombre existente.",
            "icon" => "error"
        ];
    }

//ELIMINAR CUENTA
} else if (isset($_POST['borrar']) && !empty($_SESSION["permisos"]["contabilidad"]["eliminar"])) {
    try {
        $objCuenta->setDatos($_POST);
        $objCuenta->check();

        $resultado = $objCuenta->geteliminar($_POST['codigocuenta']);

        if ($resultado === 1) {
            $respuesta = [
                "title" => "Eliminado",
                "message" => "La cuenta fue eliminada exitosamente.",
                "icon" => "success"
            ];
            $objBitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar cuenta contable con el código', $_POST['codigocuenta'], 'Contabilidad');
        } else {
            $respuesta = [
                "title" => "Error",
                "message" => $resultado,
                "icon" => "error"
            ];
        }

    } catch (Exception $e) {
        $respuesta = [
            "title" => "Error",
            "message" => $e->getMessage(), 
            "icon" => "error"
        ];
    }
}


if(!empty($_SESSION["permisos"]["contabilidad"]["consultar"])){
$registro = $objCuenta->getconsultar_cuentas();
}

$_GET['ruta'] = 'catalogocuentas';
require_once 'plantilla.php';