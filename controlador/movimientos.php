<?php
use Modelo\Movimientos;
use Modelo\Bitacora;

$obj=new Movimientos();
$objbitacora=new Bitacora();

if (isset($_POST['cod_asiento'])) {
    $asientos = $obj->get_c_asientos2($_POST['cod_asiento']);
    header('Content-Type: application/json');
    echo json_encode($asientos);
    exit;
} else if (isset($_POST['codmov'])) {
    $asientos = $obj->get_c_asientos($_POST['codmov']);
    header('Content-Type: application/json');
    echo json_encode($asientos);
    exit;
} else if(isset($_POST['movimientos'])) {
    $result=$obj->get_sincronizar($_POST['movimientos']);
    if($result===true){
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Sincronización de Movimientos', COUNT($_POST['movimientos'], 0).' Movimientos sincronizados correctamente', 'Administración');
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

else if (isset($_POST['confirmarApertura']) || isset($_POST['confirmarManual'])) {

    echo "<script>console.log('POST:', " . json_encode($_POST) . ");</script>";

    $errores = [];
    $result = null;

    try {
        $obj->setDatos($_POST);
        $obj->check();
        $result = $obj->getregistrarapertura();

        if ($result == 1) {
            $detalles = $_POST['detalles'];
            $cuentas = [];

            foreach ($detalles as $fila) {
                if (!empty($fila['codigo_contable'])) {
                    $cuentas[] = $fila['codigo_contable'];
                }
            }

            $resumenCuentas = implode(', ', $cuentas);
            $registrar = [
                "title" => "Registrado con éxito",
                "message" => "El asiento de apertura ha sido registrado",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de Asiento de Apertura', 'Códigos: ' . $resumenCuentas, 'Administración');

        } else {
            $registrar = [
                "title" => "Error",
                "message" => $result,
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




$rep=$obj->con_rep();
$movi=$obj->consultar();
$movi_a=$obj->consulta_asientos();
$mayor=$obj->reporte1();
$cuentas_apertura=$obj->consultarapertura();
$cuentas_manual=$obj->consultar_cuentasM();
//$asientos=$obj->c_asientos();

if(isset($_POST['codmov']) || isset($_POST['movimientos'])){
    $_GET['ruta'] = 'movimientos';
}else{
    $_GET['ruta'] = 'rep-contabilidad';
}

require_once "plantilla.php";
