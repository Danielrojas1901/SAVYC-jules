<?php

namespace Modelo;

use PDO;
use PDOException;
use Exception;
use DateTime;
use Modelo\Traits\ValidadorTrait;
use Modelo\Traits\UtilsTrait;

class Proyecciones extends Conexion {
    use ValidadorTrait, UtilsTrait;
    private $errores = [];

    //  filtrado de detalle
    private $cod_producto;
    private $mes_inicio;
    private $año_inicio;
    private $mes_fin;
    private $año_fin;
    private $periodo;

    public function __construct() {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function check() {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    public function getErrores() {
        return $this->errores;
    }

    public function setDatos(array $datos) {
        foreach ($datos as $key => $value) {
            switch ($key) {
                case 'cod_producto':
                    $r = $this->validarNumerico($value, 'cod_producto', 1, 9999);
                    if ($r === true) {
                        $this->cod_producto = $value;
                    } else {
                        $this->errores['cod_producto'] = $r;
                    }
                    break;
                case 'mes_inicio':
                    $r = $this->validarNumerico($value, 'mes_inicio', 1, 12);
                    if ($r === true) {
                        $this->mes_inicio = $value;
                    } else {
                        $this->errores['mes_inicio'] = $r;
                    }
                    break;
                case 'año_inicio':
                    $r = $this->validarNumerico($value, 'año_inicio', 1, 9999);
                    if ($r === true) {
                        $this->año_inicio = $value;
                    } else {
                        $this->errores['año_inicio'] = $r;
                    }
                    break;
                case 'mes_fin':
                    $r = $this->validarNumerico($value, 'mes_fin', 1, 12);
                    if ($r === true) {
                        $this->mes_fin = $value;
                    } else {
                        $this->errores['mes_fin'] = $r;
                    }
                    break;
                case 'año_fin':
                    $r = $this->validarNumerico($value, 'año_fin', 1, 9999);
                    if ($r === true) {
                        $this->año_fin = $value;
                    } else {
                        $this->errores['año_fin'] = $r;
                    }
                    break;
                case 'periodo':
                    $r = $this->validarNumerico($value, 'periodo', 1, 12);
                    if ($r === true) {
                        $this->periodo = $value;
                    } else {
                        $this->errores['periodo'] = $r;
                    }
                    break;
            }
        }
    }
    /*======================================================
    OBTENER PROYECCIONES FUTURAS
    ========================================================*/
    private function consultarProyeccionesFuturas() {
        $this->conectarBD();
        $sql = "SELECT 
            p.cod_producto,
            p.nombre as producto,
            COALESCE((
                SELECT SUM(dv.importe)
                FROM detalle_ventas dv
                JOIN ventas v ON dv.cod_venta = v.cod_venta
                JOIN detalle_productos dp ON dv.cod_detallep = dp.cod_detallep
                JOIN presentacion_producto pp ON dp.cod_presentacion = pp.cod_presentacion
                WHERE pp.cod_producto = p.cod_producto
                AND v.fecha >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
                AND v.fecha <= CURRENT_DATE()
                AND v.status = 3
            ), 0) as ventas_actuales,
            (
                SELECT COALESCE(SUM(pf2.valor_proyectado), 0)
                FROM proyecciones_futuras pf2
                WHERE pf2.cod_producto = p.cod_producto
                AND pf2.mes >= DATE_FORMAT(DATE_ADD(CURRENT_DATE(), INTERVAL 1 MONTH), '%Y-%m-01')
                AND pf2.mes < DATE_FORMAT(DATE_ADD(CURRENT_DATE(), INTERVAL 4 MONTH), '%Y-%m-01')
            ) as proyeccion_3m,
            (
                SELECT COALESCE(SUM(pf3.valor_proyectado), 0)
                FROM proyecciones_futuras pf3
                WHERE pf3.cod_producto = p.cod_producto
                AND pf3.mes >= DATE_FORMAT(DATE_ADD(CURRENT_DATE(), INTERVAL 1 MONTH), '%Y-%m-01')
                AND pf3.mes < DATE_FORMAT(DATE_ADD(CURRENT_DATE(), INTERVAL 7 MONTH), '%Y-%m-01')
            ) as proyeccion_6m,
            (
                SELECT COALESCE(SUM(pf4.valor_proyectado), 0)
                FROM proyecciones_futuras pf4
                WHERE pf4.cod_producto = p.cod_producto
                AND pf4.mes >= DATE_FORMAT(DATE_ADD(CURRENT_DATE(), INTERVAL 1 MONTH), '%Y-%m-01')
                AND pf4.mes < DATE_FORMAT(DATE_ADD(CURRENT_DATE(), INTERVAL 13 MONTH), '%Y-%m-01')
            ) as proyeccion_12m
            FROM productos p
            WHERE EXISTS (
                SELECT 1 
                FROM proyecciones_futuras pf 
                WHERE pf.cod_producto = p.cod_producto
            )
            GROUP BY p.cod_producto, p.nombre
            ORDER BY p.nombre";
        
        try {
            $strExec = $this->conex->prepare($sql);
            $resul = $strExec->execute();
            $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
            
            // calcula r tendencia:: proyeccion de 6 meses vs ventas actuales
            foreach ($datos as &$row) {
                $row['tendencia'] = ($row['proyeccion_6m'] > $row['ventas_actuales']) ? 'up' : 'down';
            }
            
            $this->desconectarBD();
            return $resul ? $datos : [];
        } catch (PDOException $e) {
            error_log("Error en obtenerProyeccionesFuturas: " . $e->getMessage());
            $this->desconectarBD();
            return [];
        }
    }

    public function obtenerProyeccionesFuturas() {
        return $this->consultarProyeccionesFuturas();
    }

    /*======================================================
    OBTENER HISTÓRICO DE VENTAS
    ========================================================*/
    private function consultarHistoricoVentas() {
        $this->conectarBD();
        $sql = "SELECT 
                DATE_FORMAT(v.fecha, '%Y-%m-01') as mes,
                SUM(dv.importe) as total_ventas
                FROM ventas v
                JOIN detalle_ventas dv ON v.cod_venta = dv.cod_venta
                WHERE v.fecha >= DATE_SUB(CURRENT_DATE(), INTERVAL ? MONTH)
                AND v.status = 3
                GROUP BY DATE_FORMAT(v.fecha, '%Y-%m-01')
                ORDER BY v.fecha";
        
        $strExec = $this->conex->prepare($sql);
        $strExec->bindParam(1, $this->periodo, PDO::PARAM_INT);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        
        // formatear fechas antes de retornar
        $datosFormateados = array_map(function($row) {
            $row['mes'] = $this->formatearFechaEspanol($row['mes']);
            return $row;
        }, $datos);
        
        $this->desconectarBD();
        
        return $resul ? $datosFormateados : [];
    }

    public function obtenerHistoricoVentas() {
        return $this->consultarHistoricoVentas();
    }

    /*======================================================
    OBTENER DATOS PARA GRÁFICO PRINCIPAL
    ========================================================*/
    private function consultarDatosGrafico($meses_historico = 6, $meses_proyeccion = 12) {
        $this->conectarBD();

        // ver si hay proyecciones
        /*$sqlCheck = "SELECT COUNT(*) as count FROM proyecciones_futuras";
        $stmtCheck = $this->conex->prepare($sqlCheck);
        $stmtCheck->execute();
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            $this->desconectarBD();
            return [
                'historico' => [
                    'labels' => [],
                    'valores' => []
                ],
                'proyecciones' => [
                    'labels' => [],
                    'valores' => []
                ],
                'historicas' => [
                    'labels' => [],
                    'valores' => [],
                    'reales' => []
                ]
            ];
        }*/
        
        // ventas historicas 3 meses - solo productos con proyecciones
        $sql_historico = "SELECT 
            DATE_FORMAT(v.fecha, '%Y-%m-01') as mes,
            COALESCE(SUM(dv.importe), 0) as total_ventas
            FROM ventas v
            JOIN detalle_ventas dv ON v.cod_venta = dv.cod_venta
            JOIN detalle_productos dp ON dv.cod_detallep = dp.cod_detallep
            JOIN presentacion_producto pp ON dp.cod_presentacion = pp.cod_presentacion
            WHERE v.fecha >= DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH)
            AND v.status = 3
            AND EXISTS (
                SELECT 1 
                FROM proyecciones_futuras pf 
                WHERE pf.cod_producto = pp.cod_producto
            )
            GROUP BY DATE_FORMAT(v.fecha, '%Y-%m-01')
            ORDER BY mes";
        
        // proyecciones futuras suma 12 meses
        $sql_proyecciones = "SELECT 
            pf.mes,
            COALESCE(SUM(pf.valor_proyectado), 0) as total_proyectado
            FROM proyecciones_futuras pf
            WHERE pf.mes >= CURRENT_DATE()
            AND pf.mes <= DATE_ADD(CURRENT_DATE(), INTERVAL 12 MONTH)
            GROUP BY pf.mes
            ORDER BY pf.mes";
            
        // proyecciones historicas suma ult 6 meses
        $sql_historicas = "SELECT 
            ph.mes,
            COALESCE(SUM(ph.valor_proyectado), 0) as total_proyectado,
            COALESCE(SUM(ph.valor_real), 0) as total_real
            FROM proyecciones_historicas ph
            WHERE ph.mes >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
            AND ph.mes <= CURRENT_DATE()
            GROUP BY ph.mes
            ORDER BY ph.mes";

        try {
            //datos historicos 3 meses
            $stmt_historico = $this->conex->prepare($sql_historico);
            $stmt_historico->execute();
            $datos_historico = $stmt_historico->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Datos históricos para gráfico:");
            error_log("SQL histórico: " . $sql_historico);
            foreach ($datos_historico as $dato) {
                error_log("   {$dato['mes']} => {$dato['total_ventas']}");
            }
            
            //proyecciones12 meses
            $stmt_proyecciones = $this->conex->prepare($sql_proyecciones);
            $stmt_proyecciones->execute();
            $datos_proyecciones = $stmt_proyecciones->fetchAll(PDO::FETCH_ASSOC);
            
            //proyecciones historicas
            $stmt_historicas = $this->conex->prepare($sql_historicas);
            $stmt_historicas->execute();
            $datos_historicas = $stmt_historicas->fetchAll(PDO::FETCH_ASSOC);
            
            //datos para grafico
            $resultado = [
                'historico' => [
                    'labels' => array_map(function($row) {
                        return $this->formatearFechaEspanol($row['mes']);
                    }, $datos_historico),
                    'valores' => array_map(function($row) {
                        return floatval($row['total_ventas']);
                    }, $datos_historico)
                ],
                'proyecciones' => [
                    'labels' => array_map(function($row) {
                        return $this->formatearFechaEspanol($row['mes']);
                    }, $datos_proyecciones),
                    'valores' => array_map(function($row) {
                        return floatval($row['total_proyectado']);
                    }, $datos_proyecciones)
                ],
                'historicas' => [
                    'labels' => array_map(function($row) {
                        return $this->formatearFechaEspanol($row['mes']);
                    }, $datos_historicas),
                    'valores' => array_map(function($row) {
                        return floatval($row['total_proyectado']);
                    }, $datos_historicas),
                    'reales' => array_map(function($row) {
                        return floatval($row['total_real']);
                    }, $datos_historicas)
                ]
            ];
            
            return $resultado;
        } catch (PDOException $e) {
            error_log("Error en obtenerDatosGrafico: " . $e->getMessage());
            $this->desconectarBD();
            return [
                'historico' => [
                    'labels' => [],
                    'valores' => []
                ],
                'proyecciones' => [
                    'labels' => [],
                    'valores' => []
                ],
                'historicas' => [
                    'labels' => [],
                    'valores' => [],
                    'reales' => []
                ]
            ];
        } finally {
            $this->desconectarBD();
        }
    }

    public function obtenerDatosGrafico($meses_historico = 6, $meses_proyeccion = 12) {
        return $this->consultarDatosGrafico($meses_historico, $meses_proyeccion);
    }

    /*======================================================
    OBTENER PROYECCIONES HISTÓRICAS
    =========================================================*/
    private function consultarProyeccionesHistoricas() {
        $this->conectarBD();
        $sql = "SELECT 
            p.cod_producto,
            p.nombre as producto,
            ROUND(AVG(ph.precision_valor), 2) as precision_promedio,
            MAX(ph.precision_valor) as mejor_precision,
            MIN(ph.precision_valor) as peor_precision
            FROM productos p
            JOIN proyecciones_historicas ph ON p.cod_producto = ph.cod_producto
            WHERE ph.mes >= DATE_SUB(CURRENT_DATE(), INTERVAL ? MONTH)
            AND ph.precision_valor IS NOT NULL
            GROUP BY p.cod_producto, p.nombre
            ORDER BY precision_promedio DESC";
        
        try {
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(1, $this->periodo, PDO::PARAM_INT);
            $resul = $strExec->execute();
            $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
            
            $this->desconectarBD();
            return $resul ? $datos : [];
        } catch (PDOException $e) {
            error_log("Error en obtenerProyeccionesHistoricas: " . $e->getMessage());
            $this->desconectarBD();
            return [];
        }
    }

    public function obtenerProyeccionesHistoricas() {
        return $this->consultarProyeccionesHistoricas();
    }

    /*======================================================
    OBTENER PROYECCIONES FUTURAS POR PRODUCTO
    ========================================================*/
    private function consultarProyeccionesFuturasProducto() {
        $this->conectarBD();
        
        $sqlHistorico = "SELECT 
            DATE_FORMAT(v.fecha, '%Y-%m-01') as mes,
            COALESCE(SUM(dv.importe), 0) as ventas_totales
            FROM ventas v
            JOIN detalle_ventas dv ON v.cod_venta = dv.cod_venta
            JOIN detalle_productos dp ON dv.cod_detallep = dp.cod_detallep
            JOIN presentacion_producto pp ON dp.cod_presentacion = pp.cod_presentacion
            WHERE pp.cod_producto = ?
            AND v.fecha >= DATE_SUB(CURRENT_DATE(), INTERVAL 3 MONTH)
            AND v.status = 3
            GROUP BY DATE_FORMAT(v.fecha, '%Y-%m-01')
            ORDER BY v.fecha";
            
        $sqlProyecciones = "SELECT 
            pf.mes,
            pf.valor_proyectado
            FROM proyecciones_futuras pf
            WHERE pf.cod_producto = ?
            AND pf.mes >= DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01')
            AND pf.mes <= DATE_ADD(CURRENT_DATE(), INTERVAL ? MONTH)
            ORDER BY pf.mes";
        
        try {
            $stmtHistorico = $this->conex->prepare($sqlHistorico);
            $stmtHistorico->bindParam(1, $this->cod_producto, PDO::PARAM_INT);
            $stmtHistorico->execute();
            $datosHistorico = $stmtHistorico->fetchAll(PDO::FETCH_ASSOC);
            
            $stmtProyecciones = $this->conex->prepare($sqlProyecciones);
            $stmtProyecciones->bindParam(1, $this->cod_producto, PDO::PARAM_INT);
            $stmtProyecciones->bindParam(2, $this->periodo, PDO::PARAM_INT);
            $stmtProyecciones->execute();
            $datosProyecciones = $stmtProyecciones->fetchAll(PDO::FETCH_ASSOC);
            
            $this->desconectarBD();
            
            // formatear fechas en los datos históricos
            $datosHistoricoFormateados = array_map(function($row) {
                $row['mes'] = $this->formatearFechaEspanol($row['mes']);
                return $row;
            }, $datosHistorico);
            
            // formatear fechas en las proyecciones
            $datosProyeccionesFormateados = array_map(function($row) {
                $row['mes'] = $this->formatearFechaEspanol($row['mes']);
                return $row;
            }, $datosProyecciones);
            
            return [
                'historico' => $datosHistoricoFormateados,
                'proyecciones' => $datosProyeccionesFormateados
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerProyeccionesFuturasProducto: " . $e->getMessage());
            $this->desconectarBD();
            return [
                'historico' => [],
                'proyecciones' => []
            ];
        }
    }

    public function obtenerProyeccionesFuturasProducto() {
        return $this->consultarProyeccionesFuturasProducto();
    }

    /*======================================================
    OBTENER PROYECCIONES HISTÓRICAS POR PRODUCTO
    ========================================================*/
    private function consultarProyeccionesHistoricasProducto() {
        $this->conectarBD();
        
        $dateFilter = "";
        if ($this->mes_inicio && $this->año_inicio && $this->mes_fin && $this->año_fin) {
            $fecha_inicio = "{$this->año_inicio}-{$this->mes_inicio}-01";
            $fecha_fin = "{$this->año_fin}-{$this->mes_fin}-01";
            $dateFilter = "AND ph.mes BETWEEN :fecha_inicio AND :fecha_fin";
        } else {
            $dateFilter = "AND ph.mes <= CURRENT_DATE()";
        }
        
        $sql = "SELECT 
                ph.mes,
                ph.valor_proyectado,
                ph.valor_real,
                ph.precision_valor
                FROM proyecciones_historicas ph
                WHERE ph.cod_producto = :cod_producto
                $dateFilter
                ORDER BY ph.mes";
        
        try {
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':cod_producto', $this->cod_producto, PDO::PARAM_INT);
            
            if ($this->mes_inicio && $this->año_inicio && $this->mes_fin && $this->año_fin) {
                $strExec->bindParam(':fecha_inicio', $fecha_inicio);
                $strExec->bindParam(':fecha_fin', $fecha_fin);
            }
            
            $resul = $strExec->execute();
            $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
            
            // formatear fechas antes de retornar
            $datosFormateados = array_map(function($row) {
                $row['mes'] = $this->formatearFechaEspanol($row['mes']);
                return $row;
            }, $datos);
            
            $this->desconectarBD();
            return $resul ? $datosFormateados : [];
        } catch (PDOException $e) {
            error_log("Error en obtenerProyeccionesHistoricasProducto: " . $e->getMessage());
            $this->desconectarBD();
            return [];
        }
    }

    public function obtenerProyeccionesHistoricasProducto() {
        return $this->consultarProyeccionesHistoricasProducto();
    }

    /*======================================================
    GENERAR PROYECCIONES FUTURAS
    ========================================================*/
    public function generarProyecciones($horas = 24) {
        try {
            $this->conectarBD();
            $hoy = new DateTime();
            
            $productos = $this->conex->query("SELECT cod_producto FROM productos")->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($productos)) {
                $this->desconectarBD();
                return [
                    'success' => true,
                    'message' => 'No hay productos para generar proyecciones'
                ];
            }
            
            $stmtCheckExistente = $this->conex->prepare("
                SELECT 1 FROM proyecciones_futuras 
                WHERE cod_producto = :cod_producto AND mes = :mes AND create_at >= DATE_SUB(NOW(), INTERVAL :horas HOUR)
            ");
            
            $stmtInsert = $this->conex->prepare("
                INSERT INTO proyecciones_futuras (cod_producto, mes, valor_proyectado, ventana_ma, create_at)
                VALUES (:cod_producto, :mes, :valor_proyectado, :ventana_ma, :create_at)
                ON DUPLICATE KEY UPDATE
                    valor_proyectado = VALUES(valor_proyectado),
                    ventana_ma = VALUES(ventana_ma),
                    create_at = VALUES(create_at)
            ");
            
            $this->conex->beginTransaction();
            $proyeccionesGeneradas = [];
            $tamañoVentana = 6;
            error_log("PROYECCIONES FUTURAS::");
            foreach ($productos as $cod_producto) {
                $sql = "
                    SELECT 
                        DATE_FORMAT(v.fecha, '%Y-%m-01') as mes,
                        SUM(dv.importe) as total_ventas
                    FROM detalle_ventas dv
                    INNER JOIN ventas v ON dv.cod_venta = v.cod_venta
                    INNER JOIN detalle_productos dp ON dv.cod_detallep = dp.cod_detallep
                    INNER JOIN presentacion_producto pp ON dp.cod_presentacion = pp.cod_presentacion
                    WHERE v.status = 3 
                    AND pp.cod_producto = :cod_producto
                    AND v.fecha >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
                    AND v.fecha <= CURRENT_DATE()
                    GROUP BY mes
                    ORDER BY mes
                ";

                $stmt = $this->conex->prepare($sql);
                $stmt->execute([':cod_producto' => $cod_producto]);
                $ventasMensuales = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    
                error_log("Producto $cod_producto - Ventas mensuales:");
                error_log("SQL ejecutado: " . str_replace(':cod_producto', $cod_producto, $sql));
                $totalVentas = 0;
                foreach ($ventasMensuales as $mes => $valor) {
                    error_log("   $mes => $valor");
                    $totalVentas += floatval($valor);
                }
                error_log("   Total ventas últimos 6 meses: $totalVentas");
    
                if (empty($ventasMensuales)) continue;
    
                $valores = array_values($ventasMensuales);
                $fechas = array_keys($ventasMensuales);
                
                $valoresConProyecciones = $valores;
    
                for ($i = 1; $i <= 12; $i++) {
                    $mesFuturo = (clone $hoy)->modify("+$i month")->format('Y-m-01');
                    
                    $stmtCheckExistente->execute([
                        ':cod_producto' => $cod_producto,
                        ':mes' => $mesFuturo,
                        ':horas' => $horas
                    ]);
                    $existe = $stmtCheckExistente->fetchColumn();
                    if ($existe) continue;
                    
                    $inicioVentana = max(0, count($valoresConProyecciones) - $tamañoVentana);
                    $ventana = array_slice($valoresConProyecciones, $inicioVentana);
                    $promedio = array_sum($ventana) / count($ventana);
    
    
                    $datosProyeccion = [
                        'cod_producto' => $cod_producto,
                        'mes' => $mesFuturo,
                        'valor_proyectado' => round($promedio, 2),
                        'ventana_ma' => count($ventana),
                        'create_at' => $hoy->format('Y-m-d H:i:s')
                    ];
                    
                    $valoresConProyecciones[] = $datosProyeccion['valor_proyectado'];
                    
                    $stmtInsert->execute([
                        ':cod_producto' => $datosProyeccion['cod_producto'],
                        ':mes' => $datosProyeccion['mes'],
                        ':valor_proyectado' => $datosProyeccion['valor_proyectado'],
                        ':ventana_ma' => $datosProyeccion['ventana_ma'],
                        ':create_at' => $datosProyeccion['create_at']
                    ]);
                    
                    $proyeccionesGeneradas[] = $datosProyeccion;
                }
            }
            
            $this->conex->commit();
            $this->desconectarBD();
            
            return [
                'success' => true,
                'message' => count($proyeccionesGeneradas) > 0 
                    ? 'Se generaron ' . count($proyeccionesGeneradas) . ' nuevas proyecciones'
                    : 'No se requirieron nuevas proyecciones',
                'proyecciones' => $proyeccionesGeneradas
            ];
            
        } catch (Exception $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            error_log("Error en generarProyecciones: " . $e->getMessage());
            $this->desconectarBD();
            return [
                'success' => false,
                'message' => 'Error al generar proyecciones: ' . $e->getMessage()
            ];
        }
    }

    /*======================================================
    ========================================================*/
    public function limpiarProyecciones($horas = 24) {
        try {
            $this->conectarBD();
            
            // verificar si ya se ejecutó recientemente para evitar procesamiento innecesario
            $sqlCheck = "SELECT 1 FROM proyecciones_historicas 
                        WHERE create_at >= DATE_SUB(NOW(), INTERVAL :horas HOUR) 
                        LIMIT 1";
            $stmtCheck = $this->conex->prepare($sqlCheck);
            $stmtCheck->execute([':horas' => $horas]);
            
            if ($stmtCheck->fetchColumn()) {
                $this->desconectarBD();
                return [
                    'success' => true,
                    'message' => 'Limpieza ya ejecutada recientemente (últimas ' . $horas . ' horas)'
                ];
            }

            $sql = "SELECT 
                    pf.cod_proyeccion,
                    pf.cod_producto,
                    pf.mes,
                    pf.valor_proyectado,
                    pf.ventana_ma,
                    pf.create_at,
                    COALESCE((
                        SELECT SUM(dv.importe)
                        FROM detalle_ventas dv
                        JOIN ventas v ON dv.cod_venta = v.cod_venta
                        JOIN detalle_productos dp ON dv.cod_detallep = dp.cod_detallep
                        JOIN presentacion_producto pp ON dp.cod_presentacion = pp.cod_presentacion
                        WHERE pp.cod_producto = pf.cod_producto
                        AND DATE_FORMAT(v.fecha, '%Y-%m-01') = DATE_FORMAT(pf.mes, '%Y-%m-01')
                        AND v.status = 3
                    ), 0) as valor_real
                    FROM proyecciones_futuras pf
                    WHERE pf.mes < DATE_FORMAT(CURRENT_DATE(), '%Y-%m-01')
                    AND NOT EXISTS (
                        SELECT 1 FROM proyecciones_historicas ph 
                        WHERE ph.cod_producto = pf.cod_producto 
                        AND ph.mes = pf.mes
                    )";
            
            $stmt = $this->conex->prepare($sql);
            $stmt->execute();
            $proyecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($proyecciones)) {
                $this->desconectarBD();
                return [
                    'success' => true,
                    'message' => 'No hay proyecciones futuras de meses pasados pendientes de limpiar'
                ];
            }

            error_log("LIMPIEZA DE PROYECCIONES: Encontradas " . count($proyecciones) . " proyecciones futuras de meses pasados para convertir a históricas");
            
            $stmtInsert = $this->conex->prepare("
                INSERT INTO proyecciones_historicas 
                    (cod_producto, mes, valor_proyectado, valor_real, precision_valor, ventana_ma, create_at)
                VALUES 
                    (:cod_producto, :mes, :valor_proyectado, :valor_real, :precision_valor, :ventana_ma, :create_at)
                ON DUPLICATE KEY UPDATE
                    valor_real = VALUES(valor_real),
                    precision_valor = VALUES(precision_valor),
                    create_at = VALUES(create_at)
            ");
            
            $stmtDelete = $this->conex->prepare("
                DELETE FROM proyecciones_futuras 
                WHERE cod_proyeccion = :cod_proyeccion
            ");
            
            $this->conex->beginTransaction();
            
            foreach ($proyecciones as $p) {
                $precision = null;
                if ($p['valor_proyectado'] > 0 && $p['valor_real'] > 0) {
                    $diferencia = abs($p['valor_proyectado'] - $p['valor_real']);
                    $precision = 100 - round(($diferencia / $p['valor_proyectado']) * 100);
                    $precision = max(0, min(100, $precision));
                }
                
                error_log("  - Procesando: Producto {$p['cod_producto']}, Mes {$p['mes']}, Proyectado: {$p['valor_proyectado']}, Real: {$p['valor_real']}, Precisión: " . ($precision ?? 'N/A') . "%");
                
                $stmtInsert->execute([
                    ':cod_producto' => $p['cod_producto'],
                    ':mes' => $p['mes'],
                    ':valor_proyectado' => $p['valor_proyectado'],
                    ':valor_real' => $p['valor_real'],
                    ':precision_valor' => $precision,
                    ':ventana_ma' => $p['ventana_ma'],
                    ':create_at' => date('Y-m-d H:i:s')
                ]);
                
                $stmtDelete->execute([':cod_proyeccion' => $p['cod_proyeccion']]);
            }
            
            $this->conex->commit();
            
            $this->desconectarBD();
            return [
                'success' => true,
                'message' => 'Se limpiaron ' . count($proyecciones) . ' proyecciones antiguas',
                'procesadas' => count($proyecciones)
            ];
            
        } catch (Exception $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            error_log("Error en limpiarProyecciones: " . $e->getMessage());
            $this->desconectarBD();
            return [
                'success' => false,
                'message' => 'Error al limpiar proyecciones: ' . $e->getMessage()
            ];
        }
    }


}