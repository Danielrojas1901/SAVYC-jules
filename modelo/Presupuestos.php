<?php

namespace Modelo;

use Modelo\Conexion;
use Modelo\Traits\ValidadorTrait;
use Modelo\Traits\UtilsTrait;
use PDO;
use PDOException;
use Exception;

class Presupuestos extends Conexion
{
        use ValidadorTrait, UtilsTrait;
        private $errores = [];
        private $datos = [];

        private $cod_cat_gasto;
        private $mes;
        private $monto;
        private $descripcion;

        //filtrar
        private $mes_inicio;
        private $año_inicio;
        private $mes_fin;
        private $año_fin;

        public function __construct()
        {
                global $_ENV;
                parent::__construct(
                        $_ENV["_DB_HOST_"],
                        $_ENV["_DB_NAME_"],
                        $_ENV["_DB_USER_"],
                        $_ENV["_DB_PASS_"],
                );
        }

        public function getconex()
        {
                return $this->conex;
        }

        public function check()
        {
                if (!empty($this->errores)) {
                        $mensajes = implode(" | ", $this->errores);
                        throw new Exception("Errores de validación: $mensajes");
                }
        }

        public function getErrores()
        {
                return $this->errores;
        }

        public function setDatos(array $datos)
        {
                foreach ($datos as $key => $value) {
                        switch ($key) {
                                case "cod_cat_gasto":
                                        if (is_numeric($value)) {
                                                $this->cod_cat_gasto = $value;
                                        } else {
                                                $this->errores[] = "El campo $key debe ser numérico.";
                                        }
                                        break;
                                case "mes":
                                        if (!empty($value)) {
                                                $this->mes = $value;
                                        } else {
                                                $this->errores[] = "El campo $key no puede estar vacío.";
                                        }
                                        break;
                                case "monto":
                                        if (is_numeric($value) && $value > 0) {
                                                $this->monto = $value;
                                        } else {
                                                $this->errores[] = "El campo $key debe ser un número positivo.";
                                        }
                                        break;
                                case "descripcion":
                                        if (empty($value)) {
                                                $this->descripcion = "";
                                        } else {
                                                $res = $this->validarAlfanumerico(
                                                        $value,
                                                        $key,
                                                        0,
                                                        200,
                                                );
                                                if ($res === true) {
                                                        $this->descripcion = $value;
                                                } else {
                                                        $this->errores[] = $res;
                                                }
                                        }
                                        break;
                                case "mes_inicio":
                                        if (is_numeric($value)) {
                                                $this->mes_inicio = $value;
                                        }
                                        break;
                                case "año_inicio":
                                        if (is_numeric($value)) {
                                                $this->año_inicio = $value;
                                        }
                                        break;
                                case "mes_fin":
                                        if (is_numeric($value)) {
                                                $this->mes_fin = $value;
                                        }
                                        break;
                                case "año_fin":
                                        if (is_numeric($value)) {
                                                $this->año_fin = $value;
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

        /*======================================================
    OBTENER PRESUPUESTOS
    ========================================================*/
        public function obtenerPresupuestos()
        {
                return $this->consultarPresupuestos();
        }

        private function consultarPresupuestos()
        {
                $this->conectarBD();
                $sql = "SELECT
                cg.cod_cat_gasto,
                cg.nombre as categoria,
                p.monto as presupuesto,
                COALESCE(SUM(g.monto), 0) as gasto_real,
                CASE
                    WHEN p.monto IS NOT NULL THEN p.monto - COALESCE(SUM(g.monto), 0)
                    ELSE NULL
                END as diferencia,
                CASE
                    WHEN p.monto IS NOT NULL AND p.monto > 0 THEN (COALESCE(SUM(g.monto), 0) / p.monto * 100)
                    ELSE NULL
                END as porcentaje_utilizado,
                CASE
                    WHEN p.monto IS NULL THEN NULL
                    WHEN COALESCE(SUM(g.monto), 0) <= p.monto THEN 'success'
                    ELSE 'danger'
                END as estado,
                p.notas
                FROM categoria_gasto cg
                LEFT JOIN presupuestos p ON p.cod_cat_gasto = cg.cod_cat_gasto
                    AND DATE_FORMAT(p.mes, '%Y-%m') = DATE_FORMAT(CURRENT_DATE(), '%Y-%m')
                LEFT JOIN gasto g ON g.cod_cat_gasto = cg.cod_cat_gasto
                    AND DATE_FORMAT(g.fecha_creacion, '%Y-%m') = DATE_FORMAT(CURRENT_DATE(), '%Y-%m')
                    AND g.status = 3
                GROUP BY cg.cod_cat_gasto, cg.nombre, p.monto, p.notas
                ORDER BY cg.nombre";

                try {
                        $strExec = $this->conex->prepare($sql);
                        $resul = $strExec->execute();
                        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
                        $this->desconectarBD();
                        return $resul ? $datos : [];
                } catch (PDOException $e) {
                        error_log(
                                "Error en obtenerPresupuestos: " .
                                        $e->getMessage(),
                        );
                        $this->desconectarBD();
                        return [];
                }
        }

        /*======================================================
    OBTENER CATEGORÍAS
    ========================================================*/
        public function obtenerCategorias()
        {
                return $this->consultarCategorias();
        }

        private function consultarCategorias()
        {
                $this->conectarBD();
                $sql =
                        "SELECT cod_cat_gasto, nombre FROM categoria_gasto ORDER BY nombre";

                try {
                        $strExec = $this->conex->prepare($sql);
                        $resul = $strExec->execute();
                        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);
                        $this->desconectarBD();
                        return $resul ? $datos : [];
                } catch (PDOException $e) {
                        error_log(
                                "Error en obtenerCategorias: " .
                                        $e->getMessage(),
                        );
                        $this->desconectarBD();
                        return [];
                }
        }

        /*======================================================
    OBTENER DATOS PARA GRÁFICO
    ========================================================*/
        public function obtenerDatosGraficoPresupuestos()
        {
                return $this->consultarDatosGraficoPresupuestos();
        }

        private function consultarDatosGraficoPresupuestos()
        {
                $this->conectarBD();
                $params = [];

                // si no hay un periodo, usa los últimos 6 meses
                if (
                        !$this->mes_inicio ||
                        !$this->año_inicio ||
                        !$this->mes_fin ||
                        !$this->año_fin
                ) {
                        $sql = "WITH RECURSIVE meses AS (
                        SELECT DATE_SUB(CURRENT_DATE(), INTERVAL 5 MONTH) as mes
                        UNION ALL
                        SELECT DATE_ADD(mes, INTERVAL 1 MONTH)
                        FROM meses
                        WHERE mes < CURRENT_DATE()
                    )";
                } else {
                        // strings de fecha
                        $fecha_inicio = date(
                                "Y-m-d",
                                strtotime(
                                        "{$this->año_inicio}-{$this->mes_inicio}-01",
                                ),
                        );
                        $fecha_fin = date(
                                "Y-m-d",
                                strtotime(
                                        "{$this->año_fin}-{$this->mes_fin}-01",
                                ),
                        );

                        $sql = "WITH RECURSIVE meses AS (
                        SELECT :fecha_inicio as mes
                        UNION ALL
                        SELECT DATE_ADD(mes, INTERVAL 1 MONTH)
                        FROM meses
                        WHERE mes < DATE_ADD(:fecha_fin, INTERVAL 1 MONTH)
                    )";
                        $params = [
                                ":fecha_inicio" => $fecha_inicio,
                                ":fecha_fin" => $fecha_fin,
                        ];
                }

                $categoryCondition = "";
                if ($this->cod_cat_gasto) {
                        $categoryCondition =
                                "AND p.cod_cat_gasto = :cod_cat_gasto";
                        $params[":cod_cat_gasto"] = $this->cod_cat_gasto;
                }

                $sql .=
                        " SELECT
                    m.mes as mes_fecha,
                    (
                        SELECT COALESCE(SUM(monto), 0)
                        FROM presupuestos p
                        WHERE DATE_FORMAT(p.mes, '%Y-%m') = DATE_FORMAT(m.mes, '%Y-%m')
                        {$categoryCondition}
                    ) as presupuesto,
                    (
                        SELECT COALESCE(SUM(g.monto), 0)
                        FROM gasto g
                        WHERE DATE_FORMAT(g.fecha_creacion, '%Y-%m') = DATE_FORMAT(m.mes, '%Y-%m')
                        AND g.status = 3
                        " .
                        ($this->cod_cat_gasto
                                ? "AND g.cod_cat_gasto = :cod_cat_gasto"
                                : "") .
                        "
                    ) as gasto_real
                FROM meses m
                GROUP BY m.mes
                ORDER BY m.mes ASC";

                try {
                        $strExec = $this->conex->prepare($sql);

                        foreach ($params as $param => $value) {
                                $strExec->bindValue($param, $value);
                        }

                        $resul = $strExec->execute();
                        if (!$resul) {
                                error_log(
                                        "Query execution failed: " .
                                                print_r(
                                                        $strExec->errorInfo(),
                                                        true,
                                                ),
                                );
                                $this->desconectarBD();
                                return [
                                        "labels" => [],
                                        "presupuesto" => [],
                                        "gasto_real" => [],
                                        "categoria" => "Error",
                                ];
                        }

                        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);

                        if (!empty($datos)) {
                                $labels = [];
                                $presupuesto = [];
                                $gasto_real = [];

                                $categoria = "Global";
                                if ($this->cod_cat_gasto) {
                                        $sqlCategoria =
                                                "SELECT nombre FROM categoria_gasto WHERE cod_cat_gasto = :cod_cat_gasto";
                                        $stmtCategoria = $this->conex->prepare(
                                                $sqlCategoria,
                                        );
                                        $stmtCategoria->bindValue(
                                                ":cod_cat_gasto",
                                                $this->cod_cat_gasto,
                                        );
                                        $stmtCategoria->execute();
                                        $rowCategoria = $stmtCategoria->fetch(
                                                PDO::FETCH_ASSOC,
                                        );
                                        $categoria = $rowCategoria
                                                ? $rowCategoria["nombre"]
                                                : "Categoría Desconocida";
                                }

                                foreach ($datos as $row) {
                                        $labels[] = $this->formatearFechaEspanol(
                                                $row["mes_fecha"],
                                        );
                                        $presupuesto[] = floatval(
                                                $row["presupuesto"],
                                        );
                                        $gasto_real[] = floatval(
                                                $row["gasto_real"],
                                        );
                                }

                                $this->desconectarBD();
                                return [
                                        "labels" => $labels,
                                        "presupuesto" => $presupuesto,
                                        "gasto_real" => $gasto_real,
                                        "categoria" => $categoria,
                                ];
                        }

                        $this->desconectarBD();
                        return [
                                "labels" => [],
                                "presupuesto" => [],
                                "gasto_real" => [],
                                "categoria" => "Sin datos",
                        ];
                } catch (PDOException $e) {
                        error_log(
                                "Error en obtenerDatosGraficoPresupuestos: " .
                                        $e->getMessage(),
                        );
                        $this->desconectarBD();
                        return [
                                "labels" => [],
                                "presupuesto" => [],
                                "gasto_real" => [],
                                "categoria" => "Error",
                        ];
                }
        }

        /*======================================================
    OBTENER DATOS PARA GRÁFICO GLOBAL
    =========================================================*/
        public function obtenerDatosGraficoGlobal()
        {
                return $this->consultarDatosGraficoGlobal();
        }

        private function consultarDatosGraficoGlobal()
        {
                $this->conectarBD();

                // ver si hay datos
                $sqlCheck = "SELECT COUNT(*) as count FROM presupuestos";
                $stmtCheck = $this->conex->prepare($sqlCheck);
                $stmtCheck->execute();
                $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if ($result["count"] == 0) {
                        $this->desconectarBD();
                        return [
                                "labels" => [],
                                "presupuesto" => [],
                                "gasto_real" => [],
                                "categoria" => "Global",
                        ];
                }

                // si no hay un periodo, usa los últimos 6 meses
                if (
                        !$this->mes_inicio ||
                        !$this->año_inicio ||
                        !$this->mes_fin ||
                        !$this->año_fin
                ) {
                        $sql = "WITH RECURSIVE meses AS (
                        SELECT DATE_SUB(CURRENT_DATE(), INTERVAL 5 MONTH) as mes
                        UNION ALL
                        SELECT DATE_ADD(mes, INTERVAL 1 MONTH)
                        FROM meses
                        WHERE mes < CURRENT_DATE()
                    )";
                        $params = [];
                } else {
                        // strings de fecha
                        $fecha_inicio = date(
                                "Y-m-d",
                                strtotime(
                                        "{$this->año_inicio}-{$this->mes_inicio}-01",
                                ),
                        );
                        $fecha_fin = date(
                                "Y-m-d",
                                strtotime(
                                        "{$this->año_fin}-{$this->mes_fin}-01",
                                ),
                        );

                        $sql = "WITH RECURSIVE meses AS (
                        SELECT :fecha_inicio as mes
                        UNION ALL
                        SELECT DATE_ADD(mes, INTERVAL 1 MONTH)
                        FROM meses
                        WHERE mes < DATE_ADD(:fecha_fin, INTERVAL 1 MONTH)
                    )";
                        $params = [
                                ":fecha_inicio" => $fecha_inicio,
                                ":fecha_fin" => $fecha_fin,
                        ];
                }

                $sql .= " SELECT
                    m.mes as mes_fecha,
                    (
                        SELECT COALESCE(SUM(monto), 0)
                        FROM presupuestos p
                        WHERE DATE_FORMAT(p.mes, '%Y-%m') = DATE_FORMAT(m.mes, '%Y-%m')
                    ) as presupuesto,
                    (
                        SELECT COALESCE(SUM(monto), 0)
                        FROM gasto g
                        WHERE DATE_FORMAT(g.fecha_creacion, '%Y-%m') = DATE_FORMAT(m.mes, '%Y-%m')
                        AND g.status = 3
                    ) as gasto_real
                FROM meses m
                GROUP BY m.mes
                ORDER BY m.mes ASC";

                try {
                        $strExec = $this->conex->prepare($sql);

                        foreach ($params as $param => $value) {
                                $strExec->bindValue($param, $value);
                        }

                        $resul = $strExec->execute();
                        if (!$resul) {
                                error_log(
                                        "Query execution failed: " .
                                                print_r(
                                                        $strExec->errorInfo(),
                                                        true,
                                                ),
                                );
                        }
                        $datos = $strExec->fetchAll(PDO::FETCH_ASSOC);

                        if ($resul && !empty($datos)) {
                                $labels = [];
                                $presupuesto = [];
                                $gasto_real = [];

                                foreach ($datos as $row) {
                                        $mes_formateado = $this->formatearFechaEspanol(
                                                $row["mes_fecha"],
                                        );
                                        if (
                                                !in_array(
                                                        $mes_formateado,
                                                        $labels,
                                                )
                                        ) {
                                                $labels[] = $mes_formateado;
                                                $presupuesto[] = floatval(
                                                        $row["presupuesto"] ??
                                                                0,
                                                );
                                                $gasto_real[] = floatval(
                                                        $row["gasto_real"] ?? 0,
                                                );
                                        }
                                }

                                $result = [
                                        "labels" => $labels,
                                        "presupuesto" => $presupuesto,
                                        "gasto_real" => $gasto_real,
                                        "categoria" => "Global",
                                ];
                                $this->desconectarBD();
                                return $result;
                        }

                        $this->desconectarBD();
                        return [
                                "labels" => [],
                                "presupuesto" => [],
                                "gasto_real" => [],
                                "categoria" => "Global",
                        ];
                } catch (PDOException $e) {
                        error_log(
                                "Error en obtenerDatosGraficoGlobal: " .
                                        $e->getMessage(),
                        );
                        $this->desconectarBD();
                        return [
                                "labels" => [],
                                "presupuesto" => [],
                                "gasto_real" => [],
                                "categoria" => "Global",
                        ];
                }
        }

        /*======================================================
    VALIDAR PRESUPUESTO EXISTENTE
    ========================================================*/
        public function validarPresupuestoExistente()
        {
                $this->conectarBD();
                $sql = "SELECT COUNT(*) as count
                FROM presupuestos
                WHERE cod_cat_gasto = :cod_cat_gasto
                AND DATE_FORMAT(mes, '%Y-%m') = DATE_FORMAT(:mes, '%Y-%m')";

                try {
                        $strExec = $this->conex->prepare($sql);
                        $strExec->bindValue(
                                ":cod_cat_gasto",
                                $this->cod_cat_gasto,
                        );
                        $strExec->bindValue(":mes", $this->mes);
                        $strExec->execute();
                        $result = $strExec->fetch(PDO::FETCH_ASSOC);
                        $this->desconectarBD();
                        return $result["count"] > 0;
                } catch (PDOException $e) {
                        error_log(
                                "Error en validarPresupuestoExistente: " .
                                        $e->getMessage(),
                        );
                        $this->desconectarBD();
                        return false;
                }
        }

        /*======================================================
    REGISTRAR PRESUPUESTO
    ========================================================*/
        public function registrarPresupuesto()
        {
                return $this->insertPresupuesto();
        }

        private function insertPresupuesto()
        {
                $this->conectarBD();
                $sql = "INSERT INTO presupuestos (cod_cat_gasto, mes, monto, notas, create_at)
                VALUES (:cod_cat_gasto, :mes, :monto, :notas, NOW())";

                try {
                        $strExec = $this->conex->prepare($sql);
                        $strExec->bindValue(
                                ":cod_cat_gasto",
                                $this->cod_cat_gasto,
                        );
                        $strExec->bindValue(":mes", $this->mes);
                        $strExec->bindValue(":monto", $this->monto);
                        $strExec->bindValue(":notas", $this->descripcion ?? "");

                        $resultado = $strExec->execute();
                        $this->desconectarBD();
                        return $resultado;
                } catch (PDOException $e) {
                        error_log(
                                "Error en registrarPresupuesto: " .
                                        $e->getMessage(),
                        );
                        $this->desconectarBD();
                        return false;
                }
        }

        /*======================================================
    EDITAR PRESUPUESTO
    ========================================================*/
        public function editarPresupuesto()
        {
                return $this->updatePresupuesto();
        }

        private function updatePresupuesto()
        {
                $this->conectarBD();
                $sql = "UPDATE presupuestos
                SET monto = :monto,
                    notas = :notas
                WHERE cod_cat_gasto = :cod_cat_gasto
                AND DATE_FORMAT(mes, '%Y-%m') = DATE_FORMAT(:mes, '%Y-%m')";

                try {
                        error_log("Ejecutando SQL de edición: " . $sql);
                        error_log(
                                "Parámetros: " .
                                        json_encode([
                                                "cod_cat_gasto" =>
                                                        $this->cod_cat_gasto,
                                                "mes" => $this->mes,
                                                "monto" => $this->monto,
                                                "notas" => $this->descripcion,
                                        ]),
                        );

                        $strExec = $this->conex->prepare($sql);
                        $strExec->bindValue(
                                ":cod_cat_gasto",
                                $this->cod_cat_gasto,
                        );
                        $strExec->bindValue(":mes", $this->mes);
                        $strExec->bindValue(":monto", $this->monto);
                        $strExec->bindValue(":notas", $this->descripcion ?? "");

                        $resultado = $strExec->execute();
                        error_log("Filas afectadas: " . $strExec->rowCount());
                        $this->desconectarBD();
                        return $resultado;
                } catch (PDOException $e) {
                        error_log(
                                "Error en editarPresupuesto: " .
                                        $e->getMessage(),
                        );
                        $this->desconectarBD();
                        return false;
                }
        }
        /*======================================================
    ELIMINAR PRESUPUESTO
    ========================================================*/
        public function eliminarPresupuesto()
        {
                return $this->deletePresupuesto();
        }

        private function deletePresupuesto()
        {
                $this->conectarBD();
                $sql = "DELETE FROM presupuestos
                WHERE cod_cat_gasto = :cod_cat_gasto
                AND DATE_FORMAT(mes, '%Y-%m') = DATE_FORMAT(:mes, '%Y-%m')";

                try {
                        error_log("Ejecutando SQL de eliminación: " . $sql);
                        error_log(
                                "Parámetros: " .
                                        json_encode([
                                                "cod_cat_gasto" =>
                                                        $this->cod_cat_gasto,
                                                "mes" => $this->mes,
                                        ]),
                        );

                        $strExec = $this->conex->prepare($sql);
                        $strExec->bindValue(
                                ":cod_cat_gasto",
                                $this->cod_cat_gasto,
                        );
                        $strExec->bindValue(":mes", $this->mes);

                        $resultado = $strExec->execute();
                        error_log("Filas afectadas: " . $strExec->rowCount());
                        $this->desconectarBD();
                        return $resultado;
                } catch (PDOException $e) {
                        error_log(
                                "Error en eliminarPresupuesto: " .
                                        $e->getMessage(),
                        );
                        $this->desconectarBD();
                        return false;
                }
        }
}
