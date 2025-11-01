<?php

use Modelo\Venta;
use Modelo\Gasto;
use Modelo\Compras;
use Modelo\ControlCaja;
use Modelo\Productos;
//use Modelo\Bitacora;

$venta = new Venta();
$gasto = new Gasto();
$compra = new Compras();
$objControl = new ControlCaja();
$productos = new Productos();

if (isset($_POST['accion']) && $_POST['accion'] === 'todas_las_alertas') {
    $dias_alerta = isset($_POST['dias_alerta']) ? (int) $_POST['dias_alerta'] : 3;
    $dias_alerta_productos = isset($_POST['dias_alerta_productos']) ? (int) $_POST['dias_alerta_productos'] : 30;
    try {
        $alertasVentas = $venta->notificacionesCobrar($dias_alerta);
    } catch (Exception $e) {
        $alertasVentas = [];
    }

    try {
        $alertasGastos = $gasto->consultarProximosPagos($dias_alerta);
    } catch (Exception $e) {
        $alertasGastos = [];
    }

    try {
        $alertasCompras = $compra->notificacionesPagar($dias_alerta);
    } catch (Exception $e) {
        $alertasCompras = [];
    }

    try {
        $alertasProductos = $productos->consultarProductosProximosVencer($dias_alerta_productos);
    } catch (Exception $e) {
        $alertasProductos = [];
    }

    // Notificaciones de caja
    $alertasCaja = [];

    try {
        $horaActual = date('H:i');
        $diaSemana = date('w'); // 3
    
        $dias = [
            0 => 'domingo',
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles', //$dias = miercoles
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
        ];
        $nombreDia = $dias[$diaSemana]; 
        $horario = $_SESSION["horario"][$nombreDia];

        date_default_timezone_set('America/Caracas');

        $cajas = $objControl->getConsultarActivas();

        if (is_array($horario) && isset($horario['cerrado']) && $horario['cerrado'] != 1) {
            foreach ($cajas as $caja) {
                $control = $objControl->getControlAbierto($caja['cod_caja']);

                //ALERTA: No está abierta y estamos dentro del horario
                if($caja['tiene_tipo_pago']>0){
                    if ($caja['status_control'] == 0 && $horaActual >= $horario['desde'] && $horaActual <= $horario['hasta']) {
                        $alertasCaja[] = [
                            'tipo_alerta' => 'caja',
                            'tipo' => 'apertura',
                            'nombre_caja' => $caja['nombre'],
                            'mensaje' => "La caja <b>{$caja['nombre']}</b> aún no ha sido abierta.",
                            'desde' => $horario['desde'],
                            'hasta' => $horario['hasta']
                        ];
                    }
                }
                //ALERTA: Está abierta y ya pasó la hora de cierre
                if ($caja['status_control'] == 1 && $control['fecha_apertura'] && !$control['fecha_cierre'] && $horaActual > $horario['hasta']) {
                    $alertasCaja[] = [
                        'tipo_alerta' => 'caja',
                        'tipo' => 'cierre',
                        'nombre_caja' => $caja['nombre'],
                        'mensaje' => "La caja <b>{$caja['nombre']}</b> esta abierta y ya pasó el horario de cierre.",
                        'hasta' => $horario['hasta']

                    ];
                }
            }
        }
    } catch (Exception $e) {
        $alertasCaja = [];
    }


    // Añade campo identificador tipo_alerta a las otras notificaciones
    $convertirTipo = function ($arr, $tipo) {
        return array_map(function ($item) use ($tipo) {
            $item['tipo_alerta'] = $tipo;
            return $item;
        }, $arr);
    };

    $alertas = array_merge(
        $convertirTipo($alertasVentas, 'venta'),
        $convertirTipo($alertasGastos, 'gasto'),
        $convertirTipo($alertasCompras, 'compra'),
        $convertirTipo($alertasProductos, 'producto'),
        $alertasCaja
    );


    header('Content-Type: application/json');
    echo json_encode($alertas);
    exit;
}
