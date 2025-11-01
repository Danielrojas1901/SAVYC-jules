<?php

namespace Modelo;

use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Modelo\Traits\UtilsTrait;
use PDO;
use PDOException;
use Exception;
use DateTime;

class AnalisisRentabilidad extends Conexion {
    use ValidadorTrait, UtilsTrait;
    private $errores = [];

    private $mes_inicio;
    private $año_inicio;
    private $mes_fin;
    private $año_fin;
    private $cod_producto;

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
            }
        }
    }


    /*======================================================
    OBTENER ANÁLISIS DE RENTABILIDAD
    ========================================================*/
    private function consultarRentabilidad() {
        $this->conectarBD();

        // Construir fechas para el filtro
        $fecha_inicio = $this->año_inicio . '-' . str_pad($this->mes_inicio, 2, '0', STR_PAD_LEFT) . '-01';
        $fecha_fin = $this->año_fin . '-' . str_pad($this->mes_fin, 2, '0', STR_PAD_LEFT) . '-01';

        $sql = "SELECT 
                p.cod_producto,
                CASE 
                    WHEN m.nombre IS NOT NULL THEN CONCAT(p.nombre, ' - ', m.nombre)
                    ELSE p.nombre
                END as producto,
                SUM(ar.ventas_totales) as ventas_totales,
                SUM(ar.costo_ventas) as costo_ventas,
                SUM(ar.margen_bruto) as margen_bruto,
                (SUM(ar.margen_bruto) / SUM(ar.costo_ventas) * 100) as rentabilidad
                FROM analisis_rentabilidad ar
                JOIN productos p ON ar.cod_producto = p.cod_producto
                LEFT JOIN marcas m ON p.cod_marca = m.cod_marca
                WHERE ar.mes BETWEEN :fecha_inicio AND :fecha_fin
                GROUP BY p.cod_producto, p.nombre, m.nombre
                ORDER BY rentabilidad DESC";
        
        try {
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':fecha_inicio', $fecha_inicio);
            $strExec->bindParam(':fecha_fin', $fecha_fin);
            $resul = $strExec->execute();
            $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);

            // Calcular métricas generales
            $metricas = $this->calcularMetricasGenerales($fecha_inicio, $fecha_fin);
            
            $this->desconectarBD();
            return [
                'rentabilidad' => $datos,
                'metricas' => $metricas
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerRentabilidad: " . $e->getMessage());
            $this->desconectarBD();
            return [
                'rentabilidad' => [],
                'metricas' => [
                    'rentabilidad_promedio' => 0,
                    'margen_bruto_total' => 0
                ]
            ];
        }
    }

    public function obtenerRentabilidad() {
        return $this->consultarRentabilidad();
    }

    /*======================================================
    CALCULAR MÉTRICAS GENERALES
    ========================================================*/
    private function calcularMetricasGenerales($fecha_inicio, $fecha_fin) {
        $sql = "SELECT 
                AVG((margen_bruto / costo_ventas) * 100) as rentabilidad_promedio,
                SUM(margen_bruto) as margen_bruto_total
                FROM analisis_rentabilidad
                WHERE mes BETWEEN :fecha_inicio AND :fecha_fin";

        try {
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':fecha_inicio', $fecha_inicio);
            $strExec->bindParam(':fecha_fin', $fecha_fin);
            $strExec->execute();
            return $strExec->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en calcularMetricasGenerales: " . $e->getMessage());
            return [
                'rentabilidad_promedio' => 0,
                'margen_bruto_total' => 0
            ];
        }
    }

    public function obtenerMetricasGenerales($fecha_inicio, $fecha_fin) {
        return $this->calcularMetricasGenerales($fecha_inicio, $fecha_fin);
    }

    /*======================================================
    OBTENER DETALLE DE RENTABILIDAD POR PRODUCTO
    ========================================================*/
    private function consultarDetalleRentabilidad() {
        try {
            $this->conectarBD();

            // Validar y construir fechas para el filtro
            if (!isset($this->mes_inicio) || !isset($this->año_inicio) || 
                !isset($this->mes_fin) || !isset($this->año_fin) || 
                !isset($this->cod_producto)) {
                throw new Exception('Faltan parámetros requeridos');
            }

            // Asegurar que los meses tengan dos dígitos
            $mes_inicio = str_pad($this->mes_inicio, 2, '0', STR_PAD_LEFT);
            $mes_fin = str_pad($this->mes_fin, 2, '0', STR_PAD_LEFT);
            
            $fecha_inicio = $this->año_inicio . '-' . $mes_inicio . '-01';
            $fecha_fin = $this->año_fin . '-' . $mes_fin . '-01';

            // Primero verificar si el producto existe
            $sqlProducto = "SELECT 
                            CASE 
                                WHEN m.nombre IS NOT NULL THEN CONCAT(p.nombre, ' - ', m.nombre)
                                ELSE p.nombre
                            END as producto
                           FROM productos p
                           LEFT JOIN marcas m ON p.cod_marca = m.cod_marca
                           WHERE p.cod_producto = :cod_producto";

            $strExecProducto = $this->conex->prepare($sqlProducto);
            $strExecProducto->bindParam(':cod_producto', $this->cod_producto);
            $strExecProducto->execute();
            $productoResult = $strExecProducto->fetch(PDO::FETCH_ASSOC);
            
            if (!$productoResult) {
                throw new Exception('Producto no encontrado');
            }
            
            $nombreProducto = $productoResult['producto'];

            // Obtener datos de rentabilidad
            $sql = "SELECT 
                    ar.mes as fecha,
                    ar.ventas_totales,
                    ar.costo_ventas,
                    ar.margen_bruto,
                    (ar.margen_bruto / ar.costo_ventas * 100) as rentabilidad
                    FROM analisis_rentabilidad ar
                    WHERE ar.cod_producto = :cod_producto
                    AND ar.mes BETWEEN :fecha_inicio AND :fecha_fin
                    ORDER BY ar.mes ASC";

            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':cod_producto', $this->cod_producto);
            $strExec->bindParam(':fecha_inicio', $fecha_inicio);
            $strExec->bindParam(':fecha_fin', $fecha_fin);
            $strExec->execute();
            $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);

            if (empty($datos)) {
                return [
                    'producto' => $nombreProducto,
                    'labels' => [],
                    'ventas_totales' => [],
                    'costo_ventas' => [],
                    'detalle' => []
                ];
            }

            // Preparar datos para el gráfico y la tabla
            $datosFormateados = [
                'producto' => $nombreProducto,
                'labels' => array_map(function($row) {
                    return $this->formatearFechaEspanol($row['fecha']);
                }, $datos),
                'ventas_totales' => array_column($datos, 'ventas_totales'),
                'costo_ventas' => array_column($datos, 'costo_ventas'),
                'detalle' => array_map(function($row) {
                    return [
                        'fecha' => $this->formatearFechaEspanol($row['fecha']),
                        'ventas_totales' => floatval($row['ventas_totales']),
                        'costo_ventas' => floatval($row['costo_ventas']),
                        'margen_bruto' => floatval($row['margen_bruto']),
                        'rentabilidad' => floatval($row['rentabilidad'])
                    ];
                }, $datos)
            ];
            
            $this->desconectarBD();
            return $datosFormateados;
            
        } catch (Exception $e) {
            error_log("Error en obtenerDetalleRentabilidad: " . $e->getMessage());
            $this->desconectarBD();
            return [
                'producto' => 'Error al obtener datos',
                'labels' => [],
                'ventas_totales' => [],
                'costo_ventas' => [],
                'detalle' => []
            ];
        }
    }

    public function obtenerDetalleRentabilidad() {
        return $this->consultarDetalleRentabilidad();
    }

    /*======================================================
    GENERAR ANÁLISIS DE RENTABILIDAD
    ========================================================*/
    public function generarAnalisisRentabilidad($horas = 24) {
        try {
            $this->conectarBD();
            $hoy = new DateTime();
            
            $productos = $this->conex->query("
                SELECT DISTINCT pp.cod_producto
                FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.cod_venta = v.cod_venta
                INNER JOIN detalle_productos dp ON dv.cod_detallep = dp.cod_detallep
                INNER JOIN presentacion_producto pp ON dp.cod_presentacion = pp.cod_presentacion
                WHERE v.status = 3
            ")->fetchAll(PDO::FETCH_COLUMN);
            
            if (empty($productos)) {
                $this->desconectarBD();
                return [
                    'success' => true,
                    'message' => 'No hay productos con ventas para analizar'
                ];
            }
            
            $stmtCheckExistente = $this->conex->prepare("
                SELECT 1 FROM analisis_rentabilidad 
                WHERE cod_producto = :cod_producto AND mes = :mes AND fecha_creacion >= DATE_SUB(NOW(), INTERVAL :horas HOUR)
            ");
            
            $stmtVentas = $this->conex->prepare("
                SELECT 
                    DATE_FORMAT(v.fecha, '%Y-%m-01') as mes,
                    SUM(dv.importe) as ventas_totales,
                    SUM(dv.costo_unitario * dv.cantidad) as costo_ventas
                FROM detalle_ventas dv
                INNER JOIN ventas v ON dv.cod_venta = v.cod_venta
                INNER JOIN detalle_productos dp ON dv.cod_detallep = dp.cod_detallep
                INNER JOIN presentacion_producto pp ON dp.cod_presentacion = pp.cod_presentacion
                WHERE v.status = 3 AND pp.cod_producto = :cod_producto
                GROUP BY mes
                ORDER BY mes
            ");
            
            $stmtInsert = $this->conex->prepare("
                INSERT INTO analisis_rentabilidad 
                    (cod_producto, mes, ventas_totales, costo_ventas, margen_bruto, fecha_creacion)
                VALUES 
                    (:cod_producto, :mes, :ventas_totales, :costo_ventas, :margen_bruto, :fecha_creacion)
                ON DUPLICATE KEY UPDATE
                    ventas_totales = VALUES(ventas_totales),
                    costo_ventas = VALUES(costo_ventas),
                    margen_bruto = VALUES(margen_bruto),
                    fecha_creacion = VALUES(fecha_creacion)
            ");
            
            $this->conex->beginTransaction();
            $analisisGenerados = [];
            
            foreach ($productos as $cod_producto) {
                $stmtVentas->execute([':cod_producto' => $cod_producto]);
                $ventasMensuales = $stmtVentas->fetchAll(PDO::FETCH_ASSOC);
                
                if (empty($ventasMensuales)) continue;
                
                foreach ($ventasMensuales as $venta) {
                    $stmtCheckExistente->execute([
                        ':cod_producto' => $cod_producto,
                        ':mes' => $venta['mes'],
                        ':horas' => $horas
                    ]);
                    if ($stmtCheckExistente->fetchColumn()) continue;
                    
                    $margenBruto = $venta['ventas_totales'] - $venta['costo_ventas'];
                    
                    $stmtInsert->execute([
                        ':cod_producto' => $cod_producto,
                        ':mes' => $venta['mes'],
                        ':ventas_totales' => $venta['ventas_totales'],
                        ':costo_ventas' => $venta['costo_ventas'],
                        ':margen_bruto' => round($margenBruto, 2),
                        ':fecha_creacion' => $hoy->format('Y-m-d H:i:s')
                    ]);
                    
                    $analisisGenerados[] = [
                        'producto' => $cod_producto,
                        'mes' => $venta['mes'],
                        'ventas' => $venta['ventas_totales'],
                        'costo' => $venta['costo_ventas'],
                        'margen' => round($margenBruto, 2)
                    ];
                }
            }
            
            $this->conex->commit();
            
            $this->desconectarBD();
            return [
                'success' => true,
                'message' => count($analisisGenerados) > 0 
                    ? 'Se generó el análisis de rentabilidad para ' . count($analisisGenerados) . ' registros'
                    : 'No se requirió generar nuevos análisis de rentabilidad',
                'analisis' => $analisisGenerados
            ];
            
        } catch (Exception $e) {
            if ($this->conex->inTransaction()) {
                $this->conex->rollBack();
            }
            error_log("Error en generarAnalisisRentabilidad: " . $e->getMessage());
            $this->desconectarBD();
            return [
                'success' => false,
                'message' => 'Error al generar análisis de rentabilidad: ' . $e->getMessage()
            ];
        }
    }
    
    
}   