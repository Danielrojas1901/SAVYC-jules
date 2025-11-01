<?php
namespace Modelo;

use PDO;
use PDOException;
use Exception;
use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;

class Finanzas extends Conexion {
    use ValidadorTrait;
    private $errores = [];
    private $cod_cuenta;
    private $mes_inicio;
    private $año_inicio;
    private $mes_fin;
    private $año_fin;

    public function setDatos($datos){
        foreach($datos as $key => $value){
            switch($key){
                case 'cod_cuenta':
                    $r = $this->validarCodigoSelect($value, 'cod_cuenta');
                    if ($r === true) {
                        $this->cod_cuenta = $value;
                    } else {
                        $this->errores['cod_cuenta'] = $r;
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

    public function getDatos(){
        return [
            'cod_cuenta' => $this->cod_cuenta,
            'mes_inicio' => $this->mes_inicio,
            'año_inicio' => $this->año_inicio,
            'mes_fin' => $this->mes_fin,
            'año_fin' => $this->año_fin
        ];
    }

    public function check(){
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }

    public function __construct() {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    /*======================================================
    OBTENER MOVIMIENTOS CUENTA CONTABLE (NIVEL 5 ÚNICAMENTE)
    ========================================================*/
    private function consultarMovimientosCuentaContable() {
        $this->conectarBD();
    
        
        $sql = "WITH meses AS (
                    SELECT 
                        DATE_FORMAT(
                            DATE_ADD(
                                DATE(CONCAT(:anio_inicio, '-', LPAD(:mes_inicio, 2, '0'), '-01')),
                                INTERVAL n MONTH
                            ),
                            '%Y-%m'
                        ) as periodo
                    FROM (
                        SELECT a.N + b.N * 10 + c.N * 100 as n
                        FROM (SELECT 0 as N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) a,
                             (SELECT 0 as N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) b,
                             (SELECT 0 as N UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) c
                    ) numeros
                    WHERE DATE_FORMAT(
                            DATE_ADD(
                                DATE(CONCAT(:anio_inicio, '-', LPAD(:mes_inicio, 2, '0'), '-01')),
                                INTERVAL n MONTH
                            ),
                            '%Y-%m'
                          ) <= DATE_FORMAT(
                            LAST_DAY(DATE(CONCAT(:anio_fin, '-', LPAD(:mes_fin, 2, '0'), '-01'))),
                            '%Y-%m'
                          )
                ),
                datos_asientos AS (
                    SELECT 
                        DATE_FORMAT(ac.fecha, '%Y-%m') as mes_asiento,
                        SUM(CASE WHEN da.tipo = 'Debe' THEN da.monto ELSE 0 END) as total_debe,
                        SUM(CASE WHEN da.tipo = 'Haber' THEN da.monto ELSE 0 END) as total_haber
                    FROM asientos_contables ac
                    INNER JOIN detalle_asientos da ON ac.cod_asiento = da.cod_asiento
                    WHERE ac.status = 1
                    AND da.cod_cuenta = :cod_cuenta
                    GROUP BY DATE_FORMAT(ac.fecha, '%Y-%m')
                )
                SELECT 
                    m.periodo,
                    COALESCE(da.total_debe, 0) as debe,
                    COALESCE(da.total_haber, 0) as haber
                FROM meses m
                LEFT JOIN datos_asientos da ON m.periodo = da.mes_asiento
                ORDER BY m.periodo ASC";
        
        try {

            $stmt = $this->conex->prepare($sql);
            $stmt->execute([
                ':cod_cuenta' => $this->cod_cuenta,
                ':mes_inicio' => $this->mes_inicio,
                ':anio_inicio' => $this->año_inicio,
                ':mes_fin' => $this->mes_fin,
                ':anio_fin' => $this->año_fin
            ]);
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $this->desconectarBD();
            return $resultados;
            
        } catch (PDOException $e) {
            $this->desconectarBD();
            return [];
        }
    }

    public function obtenerMovimientosCuentaContable(){
        return $this->consultarMovimientosCuentaContable();
    }



    /*======================================================
    OBTENER CUENTAS CONTABLES (NIVEL 5)
    ========================================================*/
    public function obtenerCuentasContables() {
        return $this->consultarCuentasContables();
    }
    
    private function consultarCuentasContables() {
        $this->conectarBD();
        
        $sql = "SELECT 
                    cod_cuenta,
                    CONCAT(codigo_contable, ' - ', nombre_cuenta) as nombre_cuenta
                FROM cuentas_contables 
                WHERE status = 1 
                AND nivel = 5 
                ORDER BY codigo_contable ASC";
        
        try {
            $stmt = $this->conex->prepare($sql);
            $stmt->execute();
            $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $this->desconectarBD();
            return $resultados;
            
        } catch (PDOException $e) {
            $this->desconectarBD();
            return [];
        }
    }

    

    public function obtenerBalanceSemanal($dias = 7) {
        try {
            parent::conectarBD();
            
            $ventas = $this->consultarVentas($dias);
            $gastos = $this->consultarGastos($dias);
            $compras = $this->consultarCompras($dias);

            $diasSemana = [
                0 => 'dom',
                1 => 'lun',
                2 => 'mar',
                3 => 'mie',
                4 => 'jue',
                5 => 'vie',
                6 => 'sab'
            ];
            
            $nombresDias = [
                'dom' => 'Domingo',
                'lun' => 'Lunes',
                'mar' => 'Martes',
                'mie' => 'Miércoles',
                'jue' => 'Jueves',
                'vie' => 'Viernes',
                'sab' => 'Sábado'
            ];

            $balance_semanal = [];

            for ($i = 6; $i >= 0; $i--) {
                $fecha = date('Y-m-d', strtotime("-$i days"));
                $diaSemana = $diasSemana[date('w', strtotime($fecha))];
                $balance_semanal[$diaSemana] = [
                    'ingreso' => 0,
                    'egreso' => 0,
                    'fecha_completa' => $fecha
                ];
            }

            foreach ($ventas as $venta) {
                $fecha = $venta['fecha_dia'];
                $diaSemana = $diasSemana[date('w', strtotime($fecha))];
                if (isset($balance_semanal[$diaSemana])) {
                    $balance_semanal[$diaSemana]['ingreso'] += (float)$venta['total_ventas'];
                }
            }

            foreach ($gastos as $gasto) {
                $fecha = $gasto['fecha_dia'];
                $diaSemana = $diasSemana[date('w', strtotime($fecha))];
                if (isset($balance_semanal[$diaSemana])) {
                    $balance_semanal[$diaSemana]['egreso'] += (float)$gasto['total_gastos'];
                }
            }

            foreach ($compras as $compra) {
                $fecha = $compra['fecha_dia'];
                $diaSemana = $diasSemana[date('w', strtotime($fecha))];
                if (isset($balance_semanal[$diaSemana])) {
                    $balance_semanal[$diaSemana]['egreso'] += (float)$compra['total_compras'];
                }
            }

            $response = [
                'balance' => $balance_semanal,
                'datos_grafico' => [
                    'labels' => [],
                    'ingresos' => [],
                    'egresos' => []
                ]
            ];

            foreach ($balance_semanal as $dia => $valores) {
                $response['datos_grafico']['labels'][] = ucfirst($dia);
                $response['datos_grafico']['ingresos'][] = $valores['ingreso'];
                $response['datos_grafico']['egresos'][] = $valores['egreso'];
            }

            parent::desconectarBD();
            return $response;

        } catch (PDOException $e) {
            error_log("Error en obtenerBalanceSemanal (PDO): " . $e->getMessage());
            parent::desconectarBD();
            throw new Exception("Error al consultar la base de datos");
        } catch (Exception $e) {
            error_log("Error en obtenerBalanceSemanal: " . $e->getMessage());
            parent::desconectarBD();
            throw $e;
        }
    }

    private function consultarVentas($dias) {
        try {
            $queryVentas = $this->conex->prepare("
                SELECT 
                    DATE(fecha) AS fecha_dia,
                    COALESCE(SUM(total), 0) AS total_ventas
                FROM 
                    ventas
                WHERE 
                    fecha >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
                    AND status = 3
                GROUP BY 
                    DATE(fecha)
                ORDER BY 
                    fecha_dia DESC
            ");
            $queryVentas->execute([':dias' => $dias]);
            return $queryVentas->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerVentasSemana: " . $e->getMessage());
            throw new Exception("Error al obtener datos de ventas");
        }
    }

    private function consultarGastos($dias) {
        try {
            $queryGastos = $this->conex->prepare("
                SELECT 
                    fecha_creacion AS fecha_dia,
                    COALESCE(SUM(monto), 0) AS total_gastos
                FROM 
                    gasto
                WHERE 
                    fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
                    AND status = 3
                GROUP BY 
                    fecha_creacion
                ORDER BY 
                    fecha_dia DESC
            ");
            $queryGastos->execute([':dias' => $dias]);
            return $queryGastos->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerGastosSemana: " . $e->getMessage());
            throw new Exception("Error al obtener datos de gastos");
        }
    }

    private function consultarCompras($dias) {
        try {
            $queryCompras = $this->conex->prepare("
                SELECT 
                    fecha AS fecha_dia,
                    COALESCE(SUM(total), 0) AS total_compras
                FROM 
                    compras
                WHERE 
                    fecha >= DATE_SUB(CURDATE(), INTERVAL :dias DAY)
                    AND status = 3
                GROUP BY 
                    fecha
                ORDER BY 
                    fecha_dia DESC
            ");
            $queryCompras->execute([':dias' => $dias]);
            return $queryCompras->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en obtenerComprasSemana: " . $e->getMessage());
            throw new Exception("Error al obtener datos de compras");
        }
    }
} 


