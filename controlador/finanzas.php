<?php

use Modelo\Finanzas;
use Modelo\StockMensual;
use Modelo\Proyecciones;
use Modelo\Bitacora;
use Modelo\AnalisisRentabilidad;
use Modelo\Presupuestos;

$objFinanzas = new Finanzas();
$objStock = new StockMensual();
$objProyecciones = new Proyecciones();
$objBitacora = new Bitacora();
$objRentabilidad = new AnalisisRentabilidad();
$objPresupuestos = new Presupuestos();

if(isset($_POST['accion'])) {
    $respuesta = [];
    
    switch($_POST['accion']) {
        //accion
        case 'obtener_cuentas_contables':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar cuentas contables'
                ];
                break;
            }
            $cuentas = $objFinanzas->obtenerCuentasContables();
            $respuesta = [
                'success' => true,
                'cuentas' => $cuentas
            ];
            break;
        //accion
        case 'obtener_movimientos_cuenta':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar movimientos'
                ];
                break;
            }
            try {
                $objFinanzas->setDatos($_POST);
                $objFinanzas->check();
                $resultados = $objFinanzas->obtenerMovimientosCuentaContable();
                $respuesta = [
                    'success' => true,
                    'datos' => $resultados
                ];
            } catch (Exception $e) {
                $respuesta = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
            break;
        //accion
        case 'obtener_datos_proyecciones':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar proyecciones'
                ];
                break;
            }

            try {   
            $objProyecciones->setDatos(['periodo' => 6]);
            $objProyecciones->check();
            $proyecciones = $objProyecciones->obtenerProyeccionesFuturas();
            $historico = $objProyecciones->obtenerHistoricoVentas();
            $proyecciones_historicas = $objProyecciones->obtenerProyeccionesHistoricas();
            $datos_grafico = $objProyecciones->obtenerDatosGrafico();
            
            $respuesta = [
                'success' => true,
                'proyecciones' => $proyecciones,
                'proyecciones_historicas' => $proyecciones_historicas,
                'historico' => $historico,
                'datos_grafico_proyecciones' => $datos_grafico
            ];
            } catch (Exception $e) {
                $respuesta = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
            break;
        //accion
        case 'obtener_datos_presupuestos':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar presupuestos'
                ];
                break;
            }
            $presupuestos = $objPresupuestos->obtenerPresupuestos();
            $datos_presupuestos = $objPresupuestos->obtenerDatosGraficoGlobal();
            
            $respuesta = [
                'success' => true,
                'presupuestos' => $presupuestos,
                'datos_presupuestos' => $datos_presupuestos
            ];
            break;
        //accion
        case 'obtener_detalle_producto':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar detalles de productos'
                ];
                break;
            }
            //proyecciones
                try {
                    $objProyecciones->setDatos($_POST);
                    
                    $objProyecciones->check();
                    
                    if ($_POST['tipo'] === 'futuro') {
                        $detalles = $objProyecciones->obtenerProyeccionesFuturasProducto();
                        
                        if ($detalles) {
                            $respuesta = [
                                'success' => true,
                                'datos' => $detalles
                            ];
                        }
                    } else {
                        $detalles = $objProyecciones->obtenerProyeccionesHistoricasProducto();
                        
                        if ($detalles) {
                            $respuesta = [
                                'success' => true,
                                'datos' => [
                                    'labels' => array_column($detalles, 'mes'),
                                    'proyectado' => array_map('floatval', array_column($detalles, 'valor_proyectado')),
                                    'real' => array_map('floatval', array_column($detalles, 'valor_real')),
                                    'precision' => array_map('floatval', array_column($detalles, 'precision_valor'))
                                ]
                            ];
                        }
                    }
                    
                    if (!isset($respuesta)) {
                        $respuesta = [
                            'success' => false,
                            'message' => 'No se encontraron datos para el producto'
                        ];
                    }
                } catch (Exception $e) {
                    $respuesta = [
                        'success' => false,
                        'message' => $e->getMessage()
                    ];
                }
            
            break;
        //accion
        case 'registrar_presupuesto':
            if(empty($_SESSION["permisos"]["finanza"]["registrar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para registrar presupuestos'
                ];
                break;
            }
            if(isset($_POST['cod_cat_gasto']) && isset($_POST['mes']) && isset($_POST['monto'])) {
                $objPresupuestos->setDatos([
                    'cod_cat_gasto' => $_POST['cod_cat_gasto'],
                    'mes' => $_POST['mes'],
                    'monto' => $_POST['monto'],
                    'descripcion' => $_POST['descripcion'] ?? ''
                ]);

                try {
                    $objPresupuestos->check();
                    $resultado = $objPresupuestos->registrarPresupuesto();
                    $respuesta = [
                        'success' => $resultado,
                        'message' => $resultado ? 'Presupuesto registrado correctamente' : 'Error al registrar el presupuesto'
                    ];
                    $objBitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de presupuesto', $_POST['descripcion'], 'Finanzas');
                } catch (Exception $e) {
                    $respuesta = [
                        'success' => false,
                        'message' => $e->getMessage()
                    ];
                }
            } else {
                $respuesta = [
                    'success' => false,
                    'message' => 'Faltan datos requeridos'
                ];
            }
            break;
        //accion
        case 'validar_presupuesto_existente':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar presupuestos'
                ];
                break;
            }
            if(isset($_POST['cod_cat_gasto']) && isset($_POST['mes'])) {
                $objPresupuestos->setDatos([
                    'cod_cat_gasto' => $_POST['cod_cat_gasto'],
                    'mes' => $_POST['mes']
                ]);
                $exists = $objPresupuestos->validarPresupuestoExistente();
                $respuesta = ['exists' => $exists];
            }
            break;
        //accion
        case 'obtener_datos_grafico_presupuestos':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar gráficos de presupuestos'
                ];
                break;
            }
            $datos = [
                'cod_cat_gasto' => isset($_POST['categoria']) ? $_POST['categoria'] : null,
                'mes_inicio' => isset($_POST['mes_inicio']) ? $_POST['mes_inicio'] : null,
                'año_inicio' => isset($_POST['año_inicio']) ? $_POST['año_inicio'] : null,
                'mes_fin' => isset($_POST['mes_fin']) ? $_POST['mes_fin'] : null,
                'año_fin' => isset($_POST['año_fin']) ? $_POST['año_fin'] : null
            ];
            
            $objPresupuestos->setDatos($datos);
            $tipo = isset($_POST['tipo']) ? $_POST['tipo'] : 'global';

            if ($tipo === 'global') {
                $datos = $objPresupuestos->obtenerDatosGraficoGlobal();
            } else {
                $datos = $objPresupuestos->obtenerDatosGraficoPresupuestos();
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'datos' => $datos]);
            exit;
            break;
        //accion
        case 'editar_presupuesto':
            if(empty($_SESSION["permisos"]["finanza"]["editar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para editar presupuestos'
                ];
                break;
            }
            if(isset($_POST['cod_cat_gasto']) && isset($_POST['mes']) && isset($_POST['monto'])) {
                $objPresupuestos->setDatos([
                    'cod_cat_gasto' => $_POST['cod_cat_gasto'],
                    'mes' => $_POST['mes'],
                    'monto' => $_POST['monto'],
                    'descripcion' => $_POST['descripcion'] ?? ''
                ]);

                try {
                    $objPresupuestos->check();
                    $resultado = $objPresupuestos->editarPresupuesto();
                    $respuesta = [
                        'success' => $resultado,
                        'message' => $resultado ? 'Presupuesto actualizado correctamente' : 'Error al actualizar el presupuesto'
                    ];
                    
                    $objBitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Edición de presupuesto', $_POST['descripcion'].' - Monto: '.$_POST['monto'], 'Finanzas');

                } catch (Exception $e) {
                    $respuesta = [
                        'success' => false,
                        'message' => $e->getMessage()
                    ];
                }
            } else {
                $respuesta = [
                    'success' => false,
                    'message' => 'Faltan datos requeridos'
                ];
            }
            break;
        //accion
        case 'eliminar_presupuesto':
            if(empty($_SESSION["permisos"]["finanza"]["eliminar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para eliminar presupuestos'
                ];
                break;
            }
            if(isset($_POST['cod_cat_gasto']) && isset($_POST['mes'])) {
                $objPresupuestos->setDatos([
                    'cod_cat_gasto' => $_POST['cod_cat_gasto'],
                    'mes' => $_POST['mes']
                ]);

                try {
                    $resultado = $objPresupuestos->eliminarPresupuesto();
                    $respuesta = [
                        'success' => $resultado,
                        'message' => $resultado ? 'Presupuesto eliminado correctamente' : 'Error al eliminar el presupuesto'
                    ];
                    $objBitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminación de presupuesto', $_POST['descripcion'], 'Finanzas');
                } catch (Exception $e) {
                    $respuesta = [
                        'success' => false,
                        'message' => $e->getMessage()
                    ];
                }
            } else {
                $respuesta = [
                    'success' => false,
                    'message' => 'Faltan datos requeridos'
                ];
            }
            break;
        //accion
        case 'obtener_presupuestos':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar presupuestos'
                ];
                break;
            }
            $presupuestos = $objPresupuestos->obtenerPresupuestos();
            $respuesta = [
                'success' => true,
                'presupuestos' => array_map(function($row) {
                    $row['presupuesto'] = $row['presupuesto'] === null ? null : (float)$row['presupuesto'];
                    $row['gasto_real'] = (float)$row['gasto_real'];
                    $row['diferencia'] = (float)$row['diferencia'];
                    $row['porcentaje_utilizado'] = (float)$row['porcentaje_utilizado'];
                    return $row;
                }, $presupuestos)
            ];
            break;
        //accion
        case 'obtener_rentabilidad':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar rentabilidad'
                ];
                break;
            }
            $datos = [
                'mes_inicio' => isset($_POST['mes_inicio']) ? $_POST['mes_inicio'] : null,
                'año_inicio' => isset($_POST['año_inicio']) ? $_POST['año_inicio'] : null,
                'mes_fin' => isset($_POST['mes_fin']) ? $_POST['mes_fin'] : null,
                'año_fin' => isset($_POST['año_fin']) ? $_POST['año_fin'] : null
            ];
            
            $objRentabilidad->setDatos($datos);
            $resultado = $objRentabilidad->obtenerRentabilidad();
            
            $respuesta = [
                'success' => true,
                'rentabilidad' => $resultado['rentabilidad'],
                'metricas' => $resultado['metricas']
            ];
            break;
        //accion
        case 'obtener_detalle_rentabilidad':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar detalles de rentabilidad'
                ];
                break;
            }
            if(isset($_POST['cod_producto'])) {
                $datos = [
                    'cod_producto' => $_POST['cod_producto'],
                    'mes_inicio' => isset($_POST['mes_inicio']) ? $_POST['mes_inicio'] : null,
                    'año_inicio' => isset($_POST['año_inicio']) ? $_POST['año_inicio'] : null,
                    'mes_fin' => isset($_POST['mes_fin']) ? $_POST['mes_fin'] : null,
                    'año_fin' => isset($_POST['año_fin']) ? $_POST['año_fin'] : null
                ];
                
                $objRentabilidad->setDatos($datos);
                $resultado = $objRentabilidad->obtenerDetalleRentabilidad();
                
                $respuesta = [
                    'success' => true,
                    'datos' => $resultado
                ];
            } else {
                $respuesta = [
                    'success' => false,
                    'message' => 'Falta el código del producto'
                ];
            }
            break;
        //accion
        case 'obtener_stock_mensual':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar stock mensual'
                ];
                break;
            }
            try {
                $objStock->setDatos([
                    'mes' => $_POST['mes'],
                    'año' => $_POST['año']
                ]);
                $objStock->check();
                $stock = $objStock->obtenerStockMensual();
                
                $respuesta = [
                    'success' => true,
                    'stock' => $stock
                ];
            } catch (Exception $e) {
                $respuesta = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
            break;
        //accion
        case 'obtener_detalle_stock':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar detalles de stock'
                ];
                break;
            }
            try {
                $objStock->setDatos([
                    'cod_presentacion' => $_POST['cod_presentacion']
                ]);
                $objStock->check();
                $stock = $objStock->obtenerStockProducto();
                $respuesta = [
                    'success' => true,
                    'datos' => $stock
                ];
            } catch (Exception $e) {
                $respuesta = [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
            break;
        //accion
        case 'obtener_periodos_stock':
            if(empty($_SESSION["permisos"]["finanza"]["consultar"])) {
                $respuesta = [
                    'success' => false,
                    'message' => 'No tiene permiso para consultar periodos de stock'
                ];
                break;
            }
            $periodos = $objStock->obtenerPeriodosDisponibles();
            $respuesta = [
                'success' => true,
                'periodos' => $periodos
            ];
            break;
    }   
    
    if (!empty($respuesta)) {
        header('Content-Type: application/json');
        echo json_encode($respuesta);
        exit;
    }
}


// ruta si no hay accion(es acceso al modulo, no solicitudes ajax)
if (!isset($_POST['accion'])) {
    if (!empty($_SESSION["permisos"]["finanza"]["registrar"])) {
        try {

            $horas_frecuencia = $_ENV['FRECUENCIA_FINANZAS'] ?? 24;

            $resultado_limpieza = $objProyecciones->limpiarProyecciones($horas_frecuencia);//(convierte futuras en históricas)
            error_log("Limpieza de proyecciones: " . ($resultado_limpieza['success'] ? "exitosa" : "fallida") . " - " . $resultado_limpieza['message']);
            
            $resultado_proyecciones = $objProyecciones->generarProyecciones($horas_frecuencia);
            error_log("Generación de proyecciones: " . ($resultado_proyecciones['success'] ? "exitosa" : "fallida"));
            
            $resultado_stock = $objStock->generarStockMensual($horas_frecuencia);
            error_log("Generación de stock mensual: " . ($resultado_stock['success'] ? "exitosa" : "fallida"));
            
            $resultado_rentabilidad = $objRentabilidad->generarAnalisisRentabilidad($horas_frecuencia);
            error_log("Generación de análisis de rentabilidad: " . ($resultado_rentabilidad['success'] ? "exitosa" : "fallida"));
            
        } catch (Exception $e) {
            error_log("Error en generación automática de reportes: " . $e->getMessage());
        }
    }
    
    if (!empty($_SESSION["permisos"]["finanza"]["consultar"])) {
        $cuentas_contables = $objFinanzas->obtenerCuentasContables();
        $categorias_gasto = $objPresupuestos->obtenerCategorias();
    } else {
        $cuentas_contables = [];
        $categorias_gasto = [];
    }
    
    $datos_iniciales = [
        'cuentas_contables' => $cuentas_contables,
        'categorias_gasto' => $categorias_gasto,
        'empresa' => [
            'nombre' => $_SESSION['n_empresa'] ?? '',
            'rif' => $_SESSION['rif'] ?? '',
            'direccion' => $_SESSION['direccion'] ?? '',
            'telefono' => $_SESSION['telefono'] ?? '',
            'email' => $_SESSION['email'] ?? '',
            'logo' => $_SESSION['logo'] ?? ''
        ]
    ];
    
    
    $_GET['ruta'] = 'finanzas';
    
    $objBitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Acceso a Finanzas', '', 'Finanzas');
    require_once 'vista/plantilla.php';
}


