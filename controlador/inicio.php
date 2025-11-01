<?php 
use Modelo\Clientes;
use Modelo\Productos;
use Modelo\Gasto;
use Modelo\Finanzas;
use Modelo\Venta;

$objgasto = new Gasto();
$objFinanzas = new Finanzas();

if(isset($_POST["accion"])){
    $accion=$_POST["accion"];
    switch($accion){
        case 'obtener_balance_semanal':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar balance semanal'
                ];
                break;
            }
            try {
                $resultado = $objFinanzas->obtenerBalanceSemanal();
                if (!$resultado) {
                    $respuesta = [
                        'success' => false,
                        'message' => 'No se pudieron obtener los datos del balance semanal'
                    ];
                } else {
                    $respuesta = [
                        'success' => true,
                        'datos' => $resultado
                    ];
                }
            } catch (Exception $e) {
                $respuesta = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
            break;
    }
    if (!empty($respuesta)) {
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }
}

if (isset($_POST['obtenerDatosGrafico']) && !empty($_SESSION["permisos"]["gasto"]["consultar"])) {
    $datosGrafico = $objgasto->grafico_inicio();
    header('Content-Type: application/json');
    echo json_encode($datosGrafico);
    exit;
} 


$objCliente = new Clientes();
$objProductos = new Productos();
$objv=new Venta();
$t_v=$objv->total_v();
$t_s=$objv->total_s();

$clientes=$objCliente->widgetConteo();
$bestseller=$objProductos->bestseller();
$totalP = $objgasto->consultarTotalP();
$menorStock=$objProductos->menorStock();


