<?php
namespace Modelo;
use Modelo\Conexion;
use Modelo\Traits\UtilsTrait;
use Modelo\Traits\ValidadorTrait;
use PDO;
use PDOException;
use DateTime;
use Exception;

class StockMensual extends Conexion {
    use UtilsTrait;
    use ValidadorTrait;
    private $errores = [];
    private $datos = [];
    private $mes;
    private $año;
    private $cod_presentacion;

    public function __construct() {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function setDatos(array $datos) {
        foreach ($datos as $key => $value) {
            switch ($key) {
                case 'mes':
                    $r = $this->validarNumerico($value, 'mes', 1, 12);
                    if ($r === true) {
                        $this->mes = $value;
                    } else {
                        $this->errores['mes'] = $r;
                    }
                    break;
                case 'año':
                    $r = $this->validarNumerico($value, 'año', 1, 9999);
                    if ($r === true) {
                        $this->año = $value;
                    } else {
                        $this->errores['año'] = $r;
                    }
                    break;
                case 'cod_presentacion':
                    $r = $this->validarNumerico($value, 'cod_presentacion', 1, 9999);
                    if ($r === true) {
                        $this->cod_presentacion = $value;
                    } else {
                        $this->errores['cod_presentacion'] = $r;
                    }
                    break;
            }
            $this->datos[$key] = $value;
        }
    }

    public function getDatos() {
        return $this->datos;
    }

    public function check() {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    /*======================================================
    OBTENER STOCK MENSUAL
    ========================================================*/
    public function obtenerStockMensual() {
        return $this->consultarStockMensual();
    }

    public function consultarStockMensual() {
        $this->conectarBD();
        
        $fecha = sprintf("%04d-%02d-01", $this->datos['año'], $this->datos['mes']);
        error_log("Consultando stock mensual para fecha: " . $fecha);
        
        $sqlCheck = "SELECT COUNT(*) as count FROM stock_mensual WHERE DATE_FORMAT(mes, '%Y-%m-01') = :fecha";
        $checkStmt = $this->conex->prepare($sqlCheck);
        $checkStmt->execute([
            ':fecha' => $fecha
        ]);
        $count = $checkStmt->fetch(PDO::FETCH_ASSOC)['count'];
        error_log("Registros encontrados para el mes: " . $count);

        if ($count == 0) {
            error_log("No hay datos para el mes seleccionado");
            $this->desconectarBD();
            return [];
        }
        
        $sql = "WITH HistoricoRotacion AS (
            SELECT 
                sm2.cod_presentacion,
                sm2.mes,
                AVG(sm3.dias_rotacion) OVER (
                    PARTITION BY sm2.cod_presentacion
                    ORDER BY sm2.mes 
                    ROWS BETWEEN 2 PRECEDING AND CURRENT ROW
                ) as promedio_dias_rotacion,
                LAG(sm2.dias_rotacion, 1) OVER (
                    PARTITION BY sm2.cod_presentacion 
                    ORDER BY sm2.mes
                ) as dias_rotacion_anterior
            FROM stock_mensual sm2
            LEFT JOIN stock_mensual sm3 ON 
                sm3.cod_presentacion = sm2.cod_presentacion AND
                sm3.mes <= sm2.mes AND
                sm3.mes >= DATE_SUB(sm2.mes, INTERVAL 2 MONTH)
            GROUP BY sm2.cod_presentacion, sm2.mes, sm2.dias_rotacion
        )
        SELECT 
            sm.cod_presentacion,
            CONCAT(
                COALESCE(p.nombre, ''), ' ',
                COALESCE(m.nombre, ''),
                CASE 
                    WHEN pp.presentacion IS NOT NULL OR pp.cantidad_presentacion IS NOT NULL OR um.tipo_medida IS NOT NULL 
                    THEN CONCAT(' - ', 
                        COALESCE(pp.presentacion, ''),
                        CASE 
                            WHEN pp.cantidad_presentacion IS NOT NULL 
                            THEN CONCAT(' x ', pp.cantidad_presentacion)
                            ELSE ''
                        END,
                        CASE 
                            WHEN um.tipo_medida IS NOT NULL 
                            THEN CONCAT(' ', um.tipo_medida)
                            ELSE ''
                        END
                    )
                    ELSE ''
                END
            ) as producto,
            sm.stock_inicial,
            sm.stock_final,
            sm.ventas_cantidad,
            ROUND(sm.rotacion, 2) as rotacion,
            ROUND(sm.dias_rotacion, 2) as dias_rotacion,
            ROUND(hr.promedio_dias_rotacion, 2) as promedio_dias_rotacion,
            CASE 
                WHEN sm.dias_rotacion > hr.promedio_dias_rotacion THEN 'alto'
                WHEN sm.dias_rotacion < hr.promedio_dias_rotacion THEN 'bajo'
                ELSE 'normal'
            END as estado_rotacion,
            CASE 
                WHEN sm.dias_rotacion < COALESCE(hr.dias_rotacion_anterior, sm.dias_rotacion) THEN 'mejorando'
                WHEN sm.dias_rotacion > COALESCE(hr.dias_rotacion_anterior, sm.dias_rotacion) THEN 'empeorando'
                ELSE 'estable'
            END as tendencia_rotacion,
            CASE 
                WHEN sm.stock_final = 0 THEN 'sin_stock'
                WHEN sm.stock_final <= (sm.ventas_cantidad * 0.5) THEN 'critico'
                WHEN sm.stock_final <= sm.ventas_cantidad THEN 'bajo'
                WHEN sm.stock_final >= (sm.ventas_cantidad * 3) THEN 'exceso'
                ELSE 'normal'
            END as estado_stock
        FROM stock_mensual sm
        LEFT JOIN presentacion_producto pp ON sm.cod_presentacion = pp.cod_presentacion
        LEFT JOIN productos p ON pp.cod_producto = p.cod_producto
        LEFT JOIN marcas m ON p.cod_marca = m.cod_marca
        LEFT JOIN unidades_medida um ON pp.cod_unidad = um.cod_unidad
        LEFT JOIN HistoricoRotacion hr ON sm.cod_presentacion = hr.cod_presentacion 
            AND sm.mes = hr.mes
        WHERE DATE_FORMAT(sm.mes, '%Y-%m-01') = :fecha
        ORDER BY 
            CASE 
                WHEN sm.stock_final = 0 THEN 1
                WHEN sm.stock_final <= (sm.ventas_cantidad * 0.5) THEN 2
                WHEN sm.stock_final <= sm.ventas_cantidad THEN 3
                ELSE 4
            END,
            sm.dias_rotacion DESC";
        
        try {
            $strExec = $this->conex->prepare($sql);
            $resul = $strExec->execute([
                ':fecha' => $fecha
            ]);
            
            if (!$resul) {
                error_log("Error ejecutando consulta: " . print_r($strExec->errorInfo(), true));
                $this->desconectarBD();
                return [];
            }
            
            $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($datos)) {
                error_log("obtenerStockMensual: No data returned for period {$fecha}");
            } else {
                error_log("obtenerStockMensual: Found " . count($datos) . " records");
                error_log("Sample data: " . print_r(array_slice($datos, 0, 2), true));
            }
            
            $this->desconectarBD();
            return $datos;
        } catch (PDOException $e) {
            error_log("Error en obtenerStockMensual: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            $this->desconectarBD();
            return [];
        }
    }

    /*======================================================
    OBTENER STOCK POR PRESENTACION
    ========================================================*/
    public function obtenerStockProducto() {
        return $this->consultarStockProducto();
    }

    public function consultarStockProducto() {
        $cod_presentacion = $this->datos['cod_presentacion'];
        error_log("Obteniendo stock para presentacion: " . $cod_presentacion);
        $this->conectarBD();
        
        $checkSql = "SELECT pp.cod_presentacion, p.nombre as producto, COALESCE(m.nombre, '') as marca 
                     FROM presentacion_producto pp
                     JOIN productos p ON pp.cod_producto = p.cod_producto
                     LEFT JOIN marcas m ON p.cod_marca = m.cod_marca
                     WHERE pp.cod_presentacion = :cod_presentacion";
        
        try {
            $checkStmt = $this->conex->prepare($checkSql);
            $checkStmt->execute([
                ':cod_presentacion' => $cod_presentacion
            ]);
            $presentacion = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$presentacion) {
                error_log("Presentacion no encontrada: " . $cod_presentacion);
                $this->desconectarBD();
                return null;
            }
            
            error_log("Presentacion encontrada: " . json_encode($presentacion));
        } catch (PDOException $e) {
            error_log("Error verificando presentacion: " . $e->getMessage());
            $this->desconectarBD();
            return null;
        }
        
        $sql = "WITH HistoricoRotacion AS (
            SELECT 
                sm2.mes,
                AVG(sm3.dias_rotacion) OVER (
                    ORDER BY sm2.mes 
                    ROWS BETWEEN 2 PRECEDING AND CURRENT ROW
                ) as promedio_dias_rotacion,
                LAG(sm2.dias_rotacion, 1) OVER (ORDER BY sm2.mes) as dias_rotacion_anterior
            FROM stock_mensual sm2
            LEFT JOIN stock_mensual sm3 ON 
                sm3.cod_presentacion = sm2.cod_presentacion AND
                sm3.mes <= sm2.mes AND
                sm3.mes >= DATE_SUB(sm2.mes, INTERVAL 2 MONTH)
            WHERE sm2.cod_presentacion = :cod_presentacion
            GROUP BY sm2.mes, sm2.dias_rotacion
        )
        SELECT 
            CONCAT(
                p.nombre,
                CASE WHEN m.nombre IS NOT NULL THEN CONCAT(' ', m.nombre) ELSE '' END,
                ' - ',
                COALESCE(pp.presentacion, ''),
                ' x ',
                pp.cantidad_presentacion,
                ' ',
                um.tipo_medida
            ) as producto,
            sm.mes,
            sm.stock_inicial,
            sm.stock_final,
            sm.ventas_cantidad,
            ROUND(sm.rotacion, 2) as rotacion,
            ROUND(sm.dias_rotacion, 2) as dias_rotacion,
            ROUND(hr.promedio_dias_rotacion, 2) as promedio_dias_rotacion,
            CASE 
                WHEN sm.dias_rotacion > hr.promedio_dias_rotacion THEN 'alto'
                WHEN sm.dias_rotacion < hr.promedio_dias_rotacion THEN 'bajo'
                ELSE 'normal'
            END as estado_rotacion,
            CASE 
                WHEN sm.dias_rotacion < COALESCE(hr.dias_rotacion_anterior, sm.dias_rotacion) THEN 'mejorando'
                WHEN sm.dias_rotacion > COALESCE(hr.dias_rotacion_anterior, sm.dias_rotacion) THEN 'empeorando'
                ELSE 'estable'
            END as tendencia_rotacion,
            CASE 
                WHEN sm.stock_final = 0 THEN 'sin_stock'
                WHEN sm.stock_final <= (sm.ventas_cantidad * 0.5) THEN 'critico'
                WHEN sm.stock_final <= sm.ventas_cantidad THEN 'bajo'
                WHEN sm.stock_final >= (sm.ventas_cantidad * 3) THEN 'exceso'
                ELSE 'normal'
            END as estado_stock
        FROM stock_mensual sm
        JOIN presentacion_producto pp ON sm.cod_presentacion = pp.cod_presentacion
        JOIN productos p ON pp.cod_producto = p.cod_producto
        LEFT JOIN marcas m ON p.cod_marca = m.cod_marca
        JOIN unidades_medida um ON pp.cod_unidad = um.cod_unidad
        LEFT JOIN HistoricoRotacion hr ON sm.mes = hr.mes
        WHERE sm.cod_presentacion = :cod_presentacion
        ORDER BY sm.mes DESC";
        
        try {
            $strExec = $this->conex->prepare($sql);
            $strExec->execute([
                ':cod_presentacion' => $cod_presentacion
            ]);
            $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
            
            if (empty($datos)) {
                error_log("No hay datos de stock para la presentacion: " . $cod_presentacion);
                error_log("Producto: " . $presentacion['producto'] . " - " . $presentacion['marca']);
                $this->desconectarBD();
                return null;
            }
            
            error_log("Datos encontrados para presentacion " . $cod_presentacion . ": " . count($datos) . " registros");
            
            $producto = $datos[0]['producto'];
            $labels = array_map(function($row) {
                return $this->formatearFechaEspanol($row['mes']);
            }, $datos);
            $stock_inicial = array_column($datos, 'stock_inicial');
            $stock_final = array_column($datos, 'stock_final');
            $ventas = array_column($datos, 'ventas_cantidad');
            $rotacion = array_column($datos, 'rotacion');
            $dias_rotacion = array_column($datos, 'dias_rotacion');
            $promedio_dias_rotacion = array_column($datos, 'promedio_dias_rotacion');
            $estado_rotacion = array_column($datos, 'estado_rotacion');
            $tendencia_rotacion = array_column($datos, 'tendencia_rotacion');
            $estado_stock = array_column($datos, 'estado_stock');
            
            $this->desconectarBD();
            return [
                'producto' => $producto,
                'labels' => array_reverse($labels),
                'stock_inicial' => array_reverse($stock_inicial),
                'stock_final' => array_reverse($stock_final),
                'ventas' => array_reverse($ventas),
                'rotacion' => array_reverse($rotacion),
                'dias_rotacion' => array_reverse($dias_rotacion),
                'promedio_dias_rotacion' => array_reverse($promedio_dias_rotacion),
                'estado_rotacion' => array_reverse($estado_rotacion),
                'tendencia_rotacion' => array_reverse($tendencia_rotacion),
                'estado_stock' => array_reverse($estado_stock)
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerStockProducto: " . $e->getMessage());
            error_log("SQL State: " . $e->errorInfo[0]);
            error_log("Error Code: " . $e->errorInfo[1]);
            error_log("Message: " . $e->errorInfo[2]);
            $this->desconectarBD();
            return null;
        }
    }

    /*======================================================
    OBTENER PERIODOS DISPONIBLES
    ========================================================*/
    public function obtenerPeriodosDisponibles() {
        return $this->consultarPeriodosDisponibles();
    }

    public function consultarPeriodosDisponibles() {
        $this->conectarBD();
        
        $sql = "SELECT DISTINCT 
                YEAR(mes) as año,
                MONTH(mes) as mes
                FROM stock_mensual 
                ORDER BY año DESC, mes ASC";
        
        try {
            $strExec = $this->conex->prepare($sql);
            $resul = $strExec->execute();
            $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Periodos disponibles encontrados: " . print_r($datos, true));
            
            $this->desconectarBD();
            return $datos;
        } catch (PDOException $e) {
            error_log("Error en obtenerPeriodosDisponibles: " . $e->getMessage());
            $this->desconectarBD();
            return [];
        }
    }

    /*======================================================
    GENERAR ANÁLISIS DE STOCK MENSUAL
    ========================================================*/
    public function generarStockMensual($horas = 24) {
        try {
            $this->conectarBD();
            $hoy = new DateTime();
            $mesActual = $hoy->format('Y-m-01');
            $fechaHoy = $hoy->format('Y-m-d');
            
            // obtener presentaciones activas
            $presentaciones = $this->conex->query("SELECT cod_presentacion FROM presentacion_producto")->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($presentaciones)) {
                $this->desconectarBD();
                return [
                    'success' => true,
                    'message' => 'No hay presentaciones para analizar'
                ];
            }
            
            $sqlCheckExiste = "SELECT cod_presentacion FROM stock_mensual 
                               WHERE mes = :mes_actual 
                               AND create_at >= DATE_SUB(NOW(), INTERVAL :horas HOUR)";
            $stmtCheckExiste = $this->conex->prepare($sqlCheckExiste);
            $stmtCheckExiste->execute([
                ':mes_actual' => $mesActual,
                ':horas' => $horas
            ]);
            $existeHoy = $stmtCheckExiste->fetchAll(PDO::FETCH_COLUMN);
            
            if (!empty($existeHoy)) {
                $presentaciones = array_diff($presentaciones, $existeHoy);
                if (empty($presentaciones)) {
                    $this->desconectarBD();
                    return [
                        'success' => true,
                        'message' => 'Todas las presentaciones ya fueron analizadas hoy'
                    ];
                }
            }
            
            $stmtCheckStockInicial = $this->conex->prepare("
                SELECT stock_inicial FROM stock_mensual
                WHERE cod_presentacion = :cod_presentacion AND mes = :mes
            ");
            
            $stmtStockPrev = $this->conex->prepare("
                SELECT stock_final FROM stock_mensual
                WHERE cod_presentacion = :cod_presentacion AND mes = :mes
            ");
            
            $stmtStockActual = $this->conex->prepare("
                SELECT COALESCE(SUM(dp.stock), 0) as total_stock 
                FROM detalle_productos dp
                WHERE dp.cod_presentacion = :cod_presentacion
                AND dp.stock > 0
            ");
            
            $stmtEntradas = $this->conex->prepare("
                SELECT COALESCE(SUM(dc.cantidad), 0) as total_entradas,
                       GROUP_CONCAT(CONCAT(dc.cantidad, ' @ ', c.fecha) SEPARATOR ', ') as detalle_entradas
                FROM detalle_carga dc
                INNER JOIN carga c ON c.cod_carga = dc.cod_carga
                INNER JOIN detalle_productos dp ON dp.cod_detallep = dc.cod_detallep
                WHERE dp.cod_presentacion = :cod_presentacion 
                AND c.fecha >= :mes_inicio
                AND c.fecha < DATE_ADD(:mes_inicio, INTERVAL 1 MONTH)
                AND c.status = 1
            ");
            
            $stmtSalidas = $this->conex->prepare("
                SELECT COALESCE(SUM(dd.cantidad), 0) as total_salidas,
                       GROUP_CONCAT(CONCAT(dd.cantidad, ' @ ', d.fecha) SEPARATOR ', ') as detalle_salidas
                FROM detalle_descarga dd
                INNER JOIN descarga d ON d.cod_descarga = dd.cod_descarga
                INNER JOIN detalle_productos dp ON dp.cod_detallep = dd.cod_detallep
                WHERE dp.cod_presentacion = :cod_presentacion 
                AND d.fecha >= :mes_inicio
                AND d.fecha < DATE_ADD(:mes_inicio, INTERVAL 1 MONTH)
                AND d.status = 1
            ");
            
            $stmtInsert = $this->conex->prepare("
                INSERT INTO stock_mensual 
                    (cod_presentacion, mes, stock_inicial, stock_final, ventas_cantidad, rotacion, dias_rotacion, create_at)
                VALUES 
                    (:cod_presentacion, :mes, :stock_inicial, :stock_final, :ventas_cantidad, :rotacion, :dias_rotacion, :create_at)
                ON DUPLICATE KEY UPDATE
                    stock_final = VALUES(stock_final),
                    ventas_cantidad = VALUES(ventas_cantidad),
                    rotacion = VALUES(rotacion),
                    dias_rotacion = VALUES(dias_rotacion),
                    create_at = VALUES(create_at)
            ");
            
            $this->conex->beginTransaction();
            $stockGenerado = [];
            
            foreach ($presentaciones as $cod_presentacion) {
                error_log("=== Procesando presentación $cod_presentacion ===");
                error_log("Mes actual: $mesActual");
                
                $stmtCheckStockInicial->execute([
                    ':cod_presentacion' => $cod_presentacion,
                    ':mes' => $mesActual
                ]);
                $stock_inicial = $stmtCheckStockInicial->fetchColumn();
                
                if ($stock_inicial === false) {
                    $mesAnterior = (new DateTime($mesActual))->modify('-1 month')->format('Y-m-01');
                    $stmtStockPrev->execute([
                        ':cod_presentacion' => $cod_presentacion, 
                        ':mes' => $mesAnterior
                    ]);
                    $stock_inicial = $stmtStockPrev->fetchColumn();
                    
                    if ($stock_inicial === false) {
                        $stmtStockActual->execute([':cod_presentacion' => $cod_presentacion]);
                        $stock_inicial = $stmtStockActual->fetchColumn() ?? 0;
                    }
                }
                
                $params = [
                    ':cod_presentacion' => $cod_presentacion, 
                    ':mes_inicio' => $mesActual
                ];
                
                $stmtEntradas->execute($params);
                $row = $stmtEntradas->fetch(PDO::FETCH_ASSOC);
                $entradas = $row['total_entradas'];
                
                $stmtSalidas->execute($params);
                $row = $stmtSalidas->fetch(PDO::FETCH_ASSOC);
                $salidas = $row['total_salidas'];

                $stock_final = $stock_inicial + $entradas - $salidas;
                $promedio_stock = ($stock_inicial + $stock_final) / 2;
                $rotacion = $promedio_stock > 0 ? round($salidas / $promedio_stock, 2) : 0;
                $dias_rotacion = $rotacion > 0 ? round(30 / $rotacion, 2) : 0;
                
                $stmtInsert->execute([
                    ':cod_presentacion' => $cod_presentacion,
                    ':mes' => $mesActual,
                    ':stock_inicial' => $stock_inicial,
                    ':stock_final' => $stock_final,
                    ':ventas_cantidad' => $salidas,
                    ':rotacion' => $rotacion,
                    ':dias_rotacion' => $dias_rotacion,
                    ':create_at' => $hoy->format('Y-m-d H:i:s')
                ]);
                
                $stockGenerado[] = [
                    'presentacion' => $cod_presentacion,
                    'mes' => $mesActual,
                    'stock_inicial' => $stock_inicial,
                    'stock_final' => $stock_final,
                    'ventas' => $salidas
                ];
            }
            
            $this->conex->commit();
            
            $this->desconectarBD();
            return [
                'success' => true,
                'message' => count($stockGenerado) > 0 
                    ? 'Se generó el análisis de stock para ' . count($stockGenerado) . ' presentaciones'
                    : 'No se requirió generar nuevos análisis de stock',
                'stock' => $stockGenerado
            ];
            
        } catch (Exception $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            error_log("Error en generarStockMensual: " . $e->getMessage());
            $this->desconectarBD();
            return [
                'success' => false,
                'message' => 'Error al generar análisis de stock: ' . $e->getMessage()
            ];
        }
    }
    
} 