<?php
use Modelo\Descarga;
use Modelo\Bitacora;
use Modelo\Movimientos;

$objDescarga = new Descarga();
$objbitacora = new Bitacora();
$objmov = new Movimientos();

//BUSCAR DETALLE PRODUCTOS (LISTADO)
if (isset($_POST['buscar'])) {
    $resul = $objDescarga->buscar($_POST['buscar']);
    header('Content-type: application/json');
    echo json_encode($resul);
    exit;
    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Buscar producto', $_POST['buscar'], 'Productos');

    //CONSULTAR DETALLE DESCARGA
} else if (isset($_POST['detalled'])) {
    $result = $objDescarga->consultardetalledescarga($_POST['detalled']);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;


    //REGISTRAR
} else if (isset($_POST['guardar']) && !empty($_SESSION["permisos"]["inventario"]["registrar"])) {
    $errores = [];
    try {
        if (!empty($_POST['fecha']) && !empty($_POST['descripcion'])) {
            /*Validar fecha strtotime(), convierte una fecha y hora a un timestamp (segundos desde la "época Unix").
        $fechaingresada = $_POST['fecha'];
        $timestamp = strtotime($fechaingresada);
        // Obtener el timestamp de la fecha y hora actual
        $fechaactual = time();
        if ($timestamp < $fechaactual) {*/
            $objDescarga->setfecha($_POST['fecha']);
            $objDescarga->setdescripcion($_POST['descripcion']);
            $objDescarga->setcosto($_POST['costo_descarga']);
            echo '<script>console.log("Costo de descarga: ' . json_encode($_POST['productos']) . '");</script>';

            $errorCantidad = false;

            foreach ($_POST['productos'] as $producto) {
                if (empty($producto['cantidad'])) {
                    $r = [
                        "title" => "Error",
                        "message" => "La cantidad a descargar no puede estar vacía.",
                        "icon" => "error"
                    ];
                    $errores[]= $r['message'];
                }
                if ($producto['cantidad'] > $producto['stock']) {
                    $r = [
                        "title" => "Error",
                        "message" => "La cantidad del producto no puede ser mayor al stock.",
                        "icon" => "error"
                    ];
                    $errores[]= $r['message'];
                }
                $objDescarga->setcantidad($producto['cantidad']);
            }
            $objDescarga->check();
            $cod=$objDescarga->registrar($_POST['productos']);
        }
    } catch (Exception $e) {
        $errores[] = $e->getMessage();
    }
    if (!empty($errores)) {
        $registrar = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else {
        $objmov->r_ajuste($cod, 7);
        $registrar = [
            "title" => "Registrado con éxito",
            "message" => "La descarga ha sido registrada",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de descarga', $_POST["descripcion"], 'Descarga');
    }
}

$descarga = $objDescarga->consultardescarga();
$_GET['ruta'] = 'descarga';
require_once 'plantilla.php';
