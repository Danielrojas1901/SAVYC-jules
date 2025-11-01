<?php
//session_start(); lo comente para evitar conflictos con el inicio de sesion

use Modelo\Bitacora;

$objbitacora = new Bitacora();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['modulo'])) {
    $modulo = $_POST['modulo'];
    $descripcion = "Acceso a " . $modulo;
    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], $descripcion, '', $modulo);
    echo json_encode(["status" => "ok"]);
    exit;
}
if(isset($_POST['registrar'])){
    $cod_usuario = $_POST['cod_usuario'];
    $accion = $_POST['accion'];
    $detalles = $_POST['detalles'];
    $objbitacora->registrarEnBitacora($cod_usuario, $accion, $detalles, $modulo);
    header('Location: bitacora');
}

if (isset($_POST['eliminar_por_fecha'])) {
    $fechaInicio = $_POST['fecha_desde'];
    $fechaFin = $_POST['fecha_hasta'];

    if (!empty($fechaInicio) && !empty($fechaFin)) {
        $resultado = $objbitacora->eliminarPorFechas($fechaInicio, $fechaFin);
        if ($resultado) {
            $resultado = [
                "title" => "¡Eliminado!",
                "message" => "Los registros de bitácora fueron eliminados correctamente.",
                "icon" => "success"
            ];
        } else {
            $resultado = [
                "title" => "Error",
                "message" => "No se pudieron eliminar los registros.",
                "icon" => "error"
            ];
        }
    } else {
        $resultado = [
            "title" => "Advertencia",
            "message" => "Debe seleccionar ambas fechas.",
            "icon" => "warning"
        ];
    }
}





$bitacora = $objbitacora->obtenerRegistros();

$_GET['ruta'] = 'bitacora';
require_once 'plantilla.php';
?>