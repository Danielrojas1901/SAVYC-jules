<?php

namespace Modelo;

use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Exception;
use PDO;
use PDOException;

class Gasto extends Conexion
{
    use ValidadorTrait;
    private $errores = [];
    private $datos = [];

    private $cod_gasto;
    private $descripcion;
    private $monto;
    private $status;
    private $fecha_vencimiento;
    private $cod_cat_gasto;
    private $cod_condicion;
    private $fecha;
    private $origin;

    public function __construct()
    {
        global $_ENV;
        parent::__construct($_ENV['_DB_HOST_'], $_ENV['_DB_NAME_'], $_ENV['_DB_USER_'], $_ENV['_DB_PASS_']);
    }

    public function check()
    {
        if (!empty($this->errores)) {
            $mensajes = implode(" | ", $this->errores);
            throw new Exception("Errores de validación: $mensajes");
        }
    }


    public function get_codgasto()
    {
        return $this->cod_gasto;
    }


    public function getErrores()
    {
        return $this->errores;
    }


    public function setDatos(array $datos)
    {
        foreach ($datos as $key => $value) {
            switch ($key) {
                case 'cod_cat_gasto':
                    if (is_numeric($value)) {
                        $this->cod_cat_gasto = $value;
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;
                case 'origin':
                    $res = $this->validarDescripcion($value, $key, 2, 50);
                    if ($res === true) {
                        $this->origin = $value;
                    } else {
                        $this->errores[] = $res;
                    }

                    break;
                case 'cod_condicion':
                    if (is_numeric($value)) {
                        $this->cod_condicion = $value;
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;
                case 'fecha':
                    $res = $this->validarFecha($value, $key);
                    if ($res === true) {
                        $this->fecha = $value;
                    } else if (!empty($value)) {
                        $this->fecha = $value;
                    } else {
                        $this->errores[] = $res;
                    }
                    break;

                case 'cod_gasto':
                    if (is_numeric($value)) {
                        $this->cod_gasto = $value;
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;
                case 'monto':
                    if (is_numeric($value)) {
                        $this->monto = $value;
                    } else {
                        $this->errores[] = "El campo $key debe ser numérico.";
                    }
                    break;
                case 'descripcion':
                    $res = $this->validarDescripcion($value, $key, 2, 50);
                    if ($res === true) {
                        $this->descripcion = $value;
                    } else {
                        $this->errores[] = $res;
                    }
                    break;
                case 'fecha_vencimiento':
                    $res = $this->validarFecha($value, $key);
                    if ($res === true) {
                        $actual = date('Y-m-d');
                        if ($value < $actual) {
                            $this->errores[] = "La fecha de vencimiento no puede ser menor a la fecha actual.";
                        }
                        $this->fecha_vencimiento = $value;
                    } else {
                        $this->errores[] = $res;
                    }
                    break;
            }
            $this->datos[$key] = $value;
        }
    }
    public function getDatos()
    {
        return $this->datos;
    }

    //FUNCIONES DE GASTOS

    private function registrarG()
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();
            $sql = "SELECT descripcion FROM gasto WHERE descripcion = :descripcion";
            $strExec = $this->conex->prepare($sql);
            $strExec->bindParam(':descripcion', $this->descripcion);
            $res = $strExec->execute();
            $resultado = $strExec->fetch(PDO::FETCH_ASSOC);
            if ($resultado != null) {
                throw new Exception("El gasto ya existe.");
            } else {
                $registro = "INSERT INTO gasto(cod_cat_gasto,cod_condicion,descripcion, monto,fecha_creacion, fecha_vencimiento, status) VALUES(:cod_cat_gasto , :cod_condicion, :descripcion, :monto, :fecha_creacion, :fecha_vencimiento, 1)";
                $strExec = $this->conex->prepare($registro);
                $strExec->bindParam(':cod_cat_gasto', $this->cod_cat_gasto);
                $strExec->bindParam(':cod_condicion', $this->cod_condicion);
                $strExec->bindParam(':descripcion', $this->descripcion);
                $strExec->bindParam(':monto', $this->monto);
                $strExec->bindParam(':fecha_creacion', $this->fecha);
                $strExec->bindParam(':fecha_vencimiento', $this->fecha_vencimiento);
                $resul = $strExec->execute();
                if ($resul == 1) {
                    $r = 1;
                } else {
                    $r = 0;
                }
            }
            $this->conex->commit();
            return $r;
        } catch (PDOException $e) {
            $this->conex->rollBack();
            $this->errores[] = $e->getMessage();
            return false;
        } finally {
            parent::desconectarBD();
        }
    }
    public function publicregistrarg()
    {
        return $this->registrarG();
    }

    private function consultarV()
    {
        $valor = "variable";
        $registro = "SELECT 
    t.nombre,
    n.nombre_naturaleza,
    n.cod_naturaleza,
    g.cod_gasto, 
    g.descripcion, 
    g.monto,
    g.fecha_creacion,
    g.fecha_vencimiento, 
    g.status, 
    c.nombre AS categoria_nombre, 
    c.fecha AS fechac, 
    c.cod_tipo_gasto,
    COALESCE(p.fecha, 'Sin fecha') AS fecha_ultimo_pago,  
    COALESCE(p.monto_total, 0) AS monto_ultimo_pago,  
    COALESCE(tp.total_pagos_emitidos, 0) AS total_pagos_emitidos,  
    COALESCE(v.vuelto_total, 0) AS vuelto_total  
FROM 
    gasto g
LEFT JOIN 
    categoria_gasto c ON g.cod_cat_gasto = c.cod_cat_gasto
LEFT JOIN 
    tipo_gasto t ON c.cod_tipo_gasto = t.cod_tipo_gasto
LEFT JOIN 
    naturaleza_gasto n ON c.cod_naturaleza = n.cod_naturaleza
LEFT JOIN 
    (
        SELECT 
            pe.cod_gasto, 
            MAX(pe.fecha) AS fecha,
            SUM(pe.monto_total) AS monto_total,
            MAX(pe.cod_vuelto_r) AS cod_vuelto_r  
        FROM 
            pago_emitido pe
        GROUP BY 
            pe.cod_gasto
    ) p ON g.cod_gasto = p.cod_gasto
LEFT JOIN 
    vuelto_recibido v ON p.cod_vuelto_r = v.cod_vuelto_r 
LEFT JOIN 
    (
        SELECT 
            cod_gasto, 
            SUM(monto_total) AS total_pagos_emitidos
        FROM 
            pago_emitido
        GROUP BY 
            cod_gasto
    ) tp ON g.cod_gasto = tp.cod_gasto
WHERE 
    n.nombre_naturaleza = :nombre_naturaleza
GROUP BY 
    g.cod_gasto, g.descripcion, g.monto, g.fecha_vencimiento, g.status, 
    c.nombre, c.fecha, c.cod_tipo_gasto, v.vuelto_total, n.nombre_naturaleza";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $strExec->bindParam(':nombre_naturaleza', $valor);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {

            return $datos;
        } else {
            return [];
        }
    }
    public function consultarGastoV()
    {
        return $this->consultarV();
    }
    private function consultarGF()
    {
        $valor = "fijo";
        $registro = "SELECT 
        t.nombre,
        f.dias,
        n.nombre_naturaleza,
        n.cod_naturaleza,
        g.cod_gasto, 
        g.descripcion, 
        g.monto,
        g.fecha_creacion,
        g.fecha_vencimiento,
        g.status, 
        c.nombre AS categoria_nombre, 
        c.fecha AS fechac, 
        c.cod_tipo_gasto,
        COALESCE(p.fecha, 'Sin fecha') AS fecha,  
        COALESCE(p.monto_total, 0) AS monto_ultimo_pago,  
        COALESCE(tp.total_pagos_emitidos, 0) AS total_pagos_emitidos,  
        COALESCE(v.vuelto_total, 0) AS vuelto_total  
FROM 
        gasto g
LEFT JOIN 
        categoria_gasto c ON g.cod_cat_gasto = c.cod_cat_gasto
JOIN    
        frecuencia_gasto f ON c.cod_frecuencia = f.cod_frecuencia
LEFT JOIN 
        tipo_gasto t ON c.cod_tipo_gasto = t.cod_tipo_gasto
LEFT JOIN 
        naturaleza_gasto n ON c.cod_naturaleza = n.cod_naturaleza
LEFT JOIN 
        (
            SELECT 
                pe.cod_gasto, 
                pe.cod_pago_emitido,
                pe.fecha,
                pe.monto_total,
                pe.cod_vuelto_r 
            FROM 
                pago_emitido pe
            INNER JOIN 
                (
                    SELECT 
                        cod_gasto, 
                        MAX(fecha) AS max_fecha
                    FROM 
                        pago_emitido
                    GROUP BY 
                        cod_gasto
                ) max_pe ON pe.cod_gasto = max_pe.cod_gasto AND pe.fecha = max_pe.max_fecha
        ) p ON g.cod_gasto = p.cod_gasto
LEFT JOIN vuelto_recibido v ON p.cod_vuelto_r = v.cod_vuelto_r 
LEFT JOIN 
        (
            SELECT 
                cod_gasto, 
                SUM(monto_total) AS total_pagos_emitidos
            FROM 
                pago_emitido
            GROUP BY 
                cod_gasto
        ) tp ON g.cod_gasto = tp.cod_gasto
WHERE 
        n.nombre_naturaleza = :nombre_naturaleza
GROUP BY 
        g.cod_gasto, g.descripcion, g.monto, g.status, c.nombre, c.fecha, c.cod_tipo_gasto, v.vuelto_total, n.nombre_naturaleza";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $strExec->bindParam(':nombre_naturaleza', $valor);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function consultarGastoF()
    {
        return $this->consultarGF();
    }

    private function totalV()
    {
        $variable = "variable";
        $registro = "SELECT SUM(g.monto) AS total_monto
                 FROM gasto g
                 JOIN categoria_gasto c ON g.cod_cat_gasto = c.cod_cat_gasto
                 JOIN naturaleza_gasto t ON c.cod_naturaleza = t.cod_naturaleza
                 WHERE t.nombre_naturaleza = :nombret AND status != 3";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $strExec->bindParam(':nombret', $variable);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function consultarTotalV()
    {
        return $this->totalV();
    }
    private function totalF()
    {
        $variable = "fijo";
        $registro = "SELECT SUM(g.monto) AS total_monto
                 FROM gasto g
                 JOIN categoria_gasto c ON g.cod_cat_gasto = c.cod_cat_gasto
                 JOIN naturaleza_gasto t ON c.cod_naturaleza = t.cod_naturaleza
                 WHERE t.nombre_naturaleza = :nombret AND g.status != 3";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $strExec->bindParam(':nombret', $variable);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function consultarTotalF()
    {
        return $this->totalF();
    }
    private function totalG()
    {
        $registro = "SELECT SUM(g.monto) AS total_monto FROM gasto g WHERE g.status != 3";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function consultarTotalG()
    {
        return $this->totalG();
    }

    private function totalP()
    {
        $registro = "SELECT SUM(g.monto) AS total_monto FROM gasto g WHERE g.status = 3";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $resul = $strExec->execute();
        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($resul) {
            return $datos;
        } else {
            return [];
        }
    }
    public function consultarTotalP()
    {
        return $this->totalP();
    }

    private function editarG()
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();

            $comparar = "SELECT descripcion FROM gasto WHERE descripcion = :descripcion";

            $strExec = $this->conex->prepare($comparar);
            $strExec->bindParam(':descripcion', $this->descripcion);
            $res = $strExec->execute();
            $datos = $strExec->fetch(PDO::FETCH_ASSOC);
            if (!$res) {
                throw new Exception("Error al ejecutar la consulta de comparación.");
            }

            if ($datos != null) {
                if ($datos['descripcion'] == $this->descripcion && $this->descripcion != $this->origin) {
                    throw new Exception("El gasto ya existe.");
                } else {

                    $registro = "UPDATE gasto SET descripcion = :descripcion, monto = :monto WHERE cod_gasto = :cod_gasto";
                    $strExec = $this->conex->prepare($registro);
                    $strExec->bindParam(':descripcion', $this->descripcion);
                    $strExec->bindParam(':monto', $this->monto);
                    $strExec->bindParam(':cod_gasto', $this->cod_gasto);
                    $res = $strExec->execute();
                    if ($res) {
                        $re =  1;
                    } else {
                        throw new Exception("Error al actualizar el gasto.");
                    }
                }
            } else {
                if ($this->descripcion != $this->origin) {
                    $registro = "UPDATE gasto SET descripcion = :descripcion, monto = :monto WHERE cod_gasto = :cod_gasto";
                    $strExec = $this->conex->prepare($registro);
                    $strExec->bindParam(':descripcion', $this->descripcion);
                    $strExec->bindParam(':monto', $this->monto);
                    $strExec->bindParam(':cod_gasto', $this->cod_gasto);
                    $res = $strExec->execute();
                    if ($res) {
                        $re =  1;
                    } else {
                        throw new Exception("Error al actualizar el gasto !CUAL ES EL PROBLEM!.");
                    }
                }
            }
            $this->conex->commit();
            return $re;
        } catch (Exception $e) {
            $this->conex->rollBack();
            $this->errores[] = $e->getMessage();
        } finally {
            parent::desconectarBD();
        }
    }

    public function editarGasto()
    {
        return $this->editarG();
    }



    private function buscarG()
    {
        $registro = "SELECT descripcion FROM gasto WHERE descripcion = :descripcion";
        parent::conectarBD();
        $strExec = $this->conex->prepare($registro);
        $strExec->bindParam(':descripcion', $this->descripcion);
        $res = $strExec->execute();
        $datos = $strExec->fetch(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($res) {
            return $datos;
        } else {
            return [];
        }
    }

    public function buscar_gasto()
    {
        return $this->buscarG();
    }

    private function eliminarG()
    {
        try {
            parent::conectarBD();
            $this->conex->beginTransaction();

            $pago = "SELECT COUNT(*) AS n_gasto FROM pago_emitido WHERE cod_gasto = :cod_gasto";
            $strExec = $this->conex->prepare($pago);
            $strExec->bindParam(':cod_gasto', $this->cod_gasto, PDO::PARAM_INT);
            $strExec->execute();
            $resultado = $strExec->fetch(PDO::FETCH_ASSOC);

            if ($resultado['n_gasto'] > 0) {
                var_dump($resultado['n_gasto']);
                $this->conex->rollBack();
                parent::desconectarBD();
                return "error_associated";
            }

            $fisico = "UPDATE gasto SET status = 0 WHERE cod_gasto = :cod_gasto";
            $strExec = $this->conex->prepare($fisico);
            $strExec->bindParam(':cod_gasto', $this->cod_gasto, PDO::PARAM_INT);
            $re = $strExec->execute();

            if ($re) {
                $this->conex->commit();
                parent::desconectarBD();
                return "success";
            } else {
                $this->conex->rollBack();
                parent::desconectarBD();
                return "error_delete";
            }
        } catch (Exception $e) {
            $this->conex->rollBack();
            parent::desconectarBD();
            return "error_query: " . $e->getMessage();
        }
    }

    public function eliminarGasto()
    {
        return $this->eliminarG();
    }

    private function CConsultarProximosPagosF($dias_alerta = 3)
    {
        $hoy = date('Y-m-d');
        $alerta = date('Y-m-d', strtotime("+$dias_alerta days"));

        try {
            $sql = "SELECT 
    g.cod_gasto,
    g.status,
    g.fecha_vencimiento,
    g.descripcion,
    g.fecha_creacion,
    g.fecha_vencimiento,
    f.dias AS frecuencia_dias,
    (
        SELECT MAX(pe.fecha) 
        FROM pago_emitido pe 
        WHERE pe.cod_gasto = g.cod_gasto
    ) AS ultimo_pago
FROM gasto g
LEFT JOIN categoria_gasto c ON g.cod_cat_gasto = c.cod_cat_gasto
LEFT JOIN frecuencia_gasto f ON c.cod_frecuencia = f.cod_frecuencia
WHERE  g.status != 3";
            parent::conectarBD();
            $consulta = $this->conex->prepare($sql);
            $consulta->execute();
            $gastos = $consulta->fetchAll(PDO::FETCH_ASSOC);
            parent::desconectarBD();
        } catch (PDOException $e) {
            error_log("Error en notificacionesGastos: " . $e->getMessage());
            return [];
        }

        $notificaciones = [];
        foreach ($gastos as $gasto) {
            if ($gasto['status'] != 3) {
                if (!empty($gasto['frecuencia_dias']) && $gasto['frecuencia_dias'] > 0) {
                    $fecha_base = $gasto['fecha_vencimiento'];
                    $proximo_pago = date('Y-m-d', strtotime($fecha_base . " +{$gasto['frecuencia_dias']} days"));
                } else {
                    $proximo_pago = $gasto['fecha_vencimiento'];
                }

                $dias_restantes = intval((strtotime($proximo_pago) - strtotime($hoy)) / 86400);

                if ($proximo_pago < $hoy) {
                    $notificaciones[] = [
                        'descripcion'      => 'Pago de gasto: ' . $gasto['descripcion'],
                        'fecha_vencimiento' => $gasto['fecha_vencimiento'],
                        'dias_restantes'   => $dias_restantes,
                        'tipo'             => 'gasto',
                        'estado'           => 'vencida'
                    ];
                } else if ($proximo_pago >= $hoy && $proximo_pago <= $alerta) {
                    $notificaciones[] = [
                        'descripcion'      => 'Pago de gasto: ' . $gasto['descripcion'],
                        'fecha_vencimiento' => $gasto['fecha_vencimiento'],
                        'dias_restantes'   => $dias_restantes,
                        'tipo'             => 'gasto',
                        'estado'           => 'proxima'
                    ];
                }
            }
        }

        return $notificaciones;
    }

    public function consultarProximosPagos($dias_alerta)
    {
        return $this->CConsultarProximosPagosF($dias_alerta);
    }

    private function grafico()
    {
        $actual = date('Y-m-d');
        $trimestre = date('Y-m-d', strtotime('-3 months', strtotime($actual)));
        $semestre = date('Y-m-d', strtotime('-6 months', strtotime($actual)));

        $sql = "SELECT 
                DATE_FORMAT(g.fecha_creacion, '%Y-%m') AS mes,
                n.nombre_naturaleza,
                g.descripcion,
                SUM(g.monto) AS total_monto
            FROM gasto g
            JOIN categoria_gasto c ON g.cod_cat_gasto = c.cod_cat_gasto
            JOIN naturaleza_gasto n ON c.cod_naturaleza = n.cod_naturaleza
            WHERE g.fecha_creacion >= :semestre
            GROUP BY mes, n.nombre_naturaleza, g.descripcion
            ORDER BY mes ASC";
        parent::conectarBD();
        $stmt = $this->conex->prepare($sql);
        $stmt->bindParam(':semestre', $semestre);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();

        $trimestre_arreglo = [
            'fijo' => [],
            'variable' => []
        ];
        $semestre_arreglo = [
            'fijo' => [],
            'variable' => []
        ];
        $etiquetas = [];
        $etiquitasSet = [];
        foreach ($resultados as $fila) {
            $mes = $fila['mes'];
            $naturaleza = $fila['nombre_naturaleza'];
            $descripcion = $fila['descripcion'];
            $monto = floatval($fila['total_monto']);

            if (!in_array($mes, $etiquitasSet)) {
                $etiquitasSet[] = $mes;
            }

            if (!isset($semestre_arreglo[$naturaleza][$descripcion])) {
                $semestre_arreglo[$naturaleza][$descripcion] = [];
            }
            if (!isset($trimestre_arreglo[$naturaleza][$descripcion])) {
                $trimestre_arreglo[$naturaleza][$descripcion] = [];
            }

            $semestre_arreglo[$naturaleza][$descripcion][$mes] = $monto;

            if ($mes >= date('Y-m', strtotime($trimestre))) {
                $trimestre_arreglo[$naturaleza][$descripcion][$mes] = $monto;
            }
        }

        sort($etiquitasSet);
        $etiquetas = $etiquitasSet;

        foreach ([$trimestre_arreglo, $semestre_arreglo] as &$data) {
            foreach ($data as $naturaleza => &$gastos) {
                foreach ($gastos as $descripcion => &$montosmes) {
                    foreach ($etiquetas as $mes) {
                        if (!isset($montosmes[$mes])) {
                            $montosmes[$mes] = 0;
                        }
                    }
                    ksort($montosmes);
                }
            }
        }

        return [
            'labels' => $etiquetas,
            'ultimos3meses' => $trimestre_arreglo,
            'ultimos6meses' => $semestre_arreglo
        ];
    }

    public function grafico_inicio()
    {
        return $this->grafico();
    }


    private function reporteG()
    {

        $sql = "SELECT 
        g.descripcion, 
        g.monto, 
        g.fecha_creacion, 
        g.status, 
        c.nombre_condicion, 
        p.monto_total, 
        p.fecha AS fecha_pago, 
        n.nombre_naturaleza, 
        tg.nombre
    FROM 
        gasto g
    LEFT JOIN 
        pago_emitido p ON p.cod_gasto = g.cod_gasto
    LEFT JOIN 
        condicion_pagoe c ON g.cod_condicion = c.cod_condicion
    LEFT JOIN 
        categoria_gasto cat ON g.cod_cat_gasto = cat.cod_cat_gasto
    JOIN 
        naturaleza_gasto n ON cat.cod_naturaleza = n.cod_naturaleza
    JOIN 
        tipo_gasto tg ON cat.cod_tipo_gasto = tg.cod_tipo_gasto
    LIMIT 0, 25;";
        parent::conectarBD();
        $str = $this->conex->prepare($sql);
        $res = $str->execute();
        $consulta = $str->fetchAll(PDO::FETCH_ASSOC);
        parent::desconectarBD();
        if ($res == 1) {
            return $consulta;
        } else {
            return [];
        }
    }

    public function repSet()
    {
        return $this->reporteG();
    }
}
