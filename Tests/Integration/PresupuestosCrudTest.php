<?php
/**
 * Pruebas de Integración para el Módulo de Presupuestos
 *
 * Comandos de ejecución de PHPUnit:
 * vendor/bin/phpunit --filter test_consulta_y_calculo_exitoso_presupuesto_parcialmente_usado tests/Integration/PresupuestosCrudTest.php
 * vendor/bin/phpunit --filter "PresupuestosCrudTest"
 * vendor/bin/phpunit --filter "PresupuestosCrudTest::test_consulta_y_calculo_exitoso_presupuesto_parcialmente_usado"
 * vendor/bin/phpunit --testdox
 * vendor/bin/phpunit --group "integration"
 */

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Modelo\Presupuestos;
use PDO;
use Exception;

class PresupuestosCrudTest extends TestCase
{
        /**
         * @var Presupuestos
         */
        protected $sut;

        /**
         * @var array IDs de categorías creadas durante las pruebas
         */
        protected array $categoriasCreadas = [];

        protected function setUp(): void
        {
                parent::setUp();
                $this->sut = new Presupuestos();
                $this->categoriasCreadas = [];
        }

        protected function tearDown(): void
        {
                // Limpiar todas las categorías creadas durante la prueba
                foreach ($this->categoriasCreadas as $categoria_id) {
                        $this->limpiarDatosDePrueba($categoria_id);
                }

                parent::tearDown();
                $this->sut = null;
                $this->categoriasCreadas = [];
        }

        private function crearCategoriaDePrueba(
                string $nombre = "Categoria Test",
        ): int {
                $nombre_unico = $nombre . "_" . uniqid();
                $this->sut->conectarBD();

                // Usar IDs existentes: tipo_gasto 1=producto, 2=servicio
                // naturaleza_gasto 1=fijo, 2=variable
                $tipo_id = 1; // producto
                $naturaleza_id = 2; // variable

                // Crear la categoría
                $sql = "INSERT INTO categoria_gasto (cod_tipo_gasto, cod_naturaleza, nombre, fecha, status_cat_gasto)
                VALUES (:tipo, :naturaleza, :nombre, CURDATE(), 1)";
                $stmt = $this->sut->getconex()->prepare($sql);
                $stmt->bindParam(":tipo", $tipo_id, PDO::PARAM_INT);
                $stmt->bindParam(":naturaleza", $naturaleza_id, PDO::PARAM_INT);
                $stmt->bindParam(":nombre", $nombre_unico);
                $stmt->execute();

                $categoria_id = $this->sut->getconex()->lastInsertId();

                // Agregar al tracking para limpieza automática
                $this->categoriasCreadas[] = $categoria_id;

                return $categoria_id;
        }

        private function insertarPresupuestoDePrueba(
                int $cod_cat_gasto,
                float $monto,
                string $notas = "",
        ): int {
                $this->sut->conectarBD();
                $fecha_mes_actual = date("Y-m-01");

                $sql =
                        "INSERT INTO presupuestos (cod_cat_gasto, monto, mes, notas) VALUES (:cat, :monto, :mes, :notas)";
                $stmt = $this->sut->getconex()->prepare($sql);
                $stmt->bindParam(":cat", $cod_cat_gasto, PDO::PARAM_INT);
                $stmt->bindParam(":monto", $monto);
                $stmt->bindParam(":mes", $fecha_mes_actual);
                $stmt->bindParam(":notas", $notas);
                $stmt->execute();

                return $this->sut->getconex()->lastInsertId();
        }

        private function insertarGastoDePrueba(
                int $cod_cat_gasto,
                float $monto,
                string $descripcion = "Gasto Test",
        ): int {
                $this->sut->conectarBD();
                $fecha_actual = date("Y-m-d");

                $condicion_id = 4;

                $sql = "INSERT INTO gasto (cod_cat_gasto, cod_condicion, descripcion, monto, fecha_creacion, status)
                VALUES (:cat, :condicion, :desc, :monto, :fecha, 3)";
                $stmt = $this->sut->getconex()->prepare($sql);
                $stmt->bindParam(":cat", $cod_cat_gasto, PDO::PARAM_INT);
                $stmt->bindParam(":condicion", $condicion_id, PDO::PARAM_INT);
                $stmt->bindParam(":desc", $descripcion);
                $stmt->bindParam(":monto", $monto);
                $stmt->bindParam(":fecha", $fecha_actual);
                $stmt->execute();

                return $this->sut->getconex()->lastInsertId();
        }

        private function buscarCategoriaNenResultados(
                array $resultados,
                int $cod_cat_gasto,
        ): ?array {
                foreach ($resultados as $resultado) {
                        if ($resultado["cod_cat_gasto"] == $cod_cat_gasto) {
                                return $resultado;
                        }
                }
                return null;
        }

        /**
         * Limpia los datos de prueba creados de forma robusta
         * Solo limpia gasto, presupuestos y categoria_gasto
         * (tipo_gasto y naturaleza_gasto usan IDs existentes)
         */
        private function limpiarDatosDePrueba(int $cod_cat_gasto): void
        {
                if ($cod_cat_gasto <= 0) {
                        return;
                }

                try {
                        $this->sut->conectarBD();
                        $conex = $this->sut->getconex();

                        // Limpiar en orden inverso de dependencias
                        // No necesitamos deshabilitar FOREIGN_KEY_CHECKS ya que
                        // tipo_gasto y naturaleza_gasto no se eliminan

                        // Eliminar gastos relacionados
                        $stmt1 = $conex->prepare(
                                "DELETE FROM gasto WHERE cod_cat_gasto = ?",
                        );
                        $stmt1->execute([$cod_cat_gasto]);

                        // Eliminar presupuestos relacionados
                        $stmt2 = $conex->prepare(
                                "DELETE FROM presupuestos WHERE cod_cat_gasto = ?",
                        );
                        $stmt2->execute([$cod_cat_gasto]);

                        // Eliminar la categoría (tipo_gasto y naturaleza_gasto quedan intactos)
                        $stmt3 = $conex->prepare(
                                "DELETE FROM categoria_gasto WHERE cod_cat_gasto = ?",
                        );
                        $stmt3->execute([$cod_cat_gasto]);
                } catch (Exception $e) {
                        error_log(
                                "Error limpiando datos de prueba para categoria $cod_cat_gasto: " .
                                        $e->getMessage(),
                        );
                }
        }

        // --- CASOS DE PRUEBA DE INTEGRACIÓN ---

        /**
         * TC-PRESU-R-INT-1: Consulta y Cálculo Exitoso (Presupuesto Parcialmente Usado)
         *
         * @test
         * @group integration
         */
        public function test_consulta_y_calculo_exitoso_presupuesto_parcialmente_usado()
        {
                $categoria_id = $this->crearCategoriaDePrueba("Categoria A");
                $this->insertarPresupuestoDePrueba(
                        $categoria_id,
                        1000.0,
                        "Presupuesto de prueba",
                );
                $this->insertarGastoDePrueba(
                        $categoria_id,
                        400.0,
                        "Gasto de prueba A",
                );
                $resultados = $this->sut->obtenerPresupuestos();

                $categoria_resultado = $this->buscarCategoriaNenResultados(
                        $resultados,
                        $categoria_id,
                );

                $this->assertNotNull(
                        $categoria_resultado,
                        "La categoría A debe estar en los resultados",
                );

                $this->assertEquals(
                        1000.0,
                        floatval($categoria_resultado["presupuesto"]),
                        "El presupuesto debe ser 1000.00",
                );
                $this->assertEquals(
                        400.0,
                        floatval($categoria_resultado["gasto_real"]),
                        "El gasto real debe ser 400.00",
                );
                $this->assertEquals(
                        600.0,
                        floatval($categoria_resultado["diferencia"]),
                        "La diferencia debe ser 600.00",
                );
                $this->assertEquals(
                        40.0,
                        floatval($categoria_resultado["porcentaje_utilizado"]),
                        "El porcentaje utilizado debe ser 40.00",
                );
                $this->assertEquals(
                        "success",
                        $categoria_resultado["estado"],
                        "El estado debe ser success",
                );

                // La limpieza se hace automáticamente en tearDown()
        }

        /**
         * TC-PRESU-R-INT-2: Integridad de Datos (Gasto sin Presupuesto)
         *
         * @test
         * @group integration
         */
        public function test_integridad_de_datos_gasto_sin_presupuesto()
        {
                $categoria_id = $this->crearCategoriaDePrueba("Categoria B");
                $this->insertarGastoDePrueba(
                        $categoria_id,
                        250.0,
                        "Gasto de prueba B",
                );

                $resultados = $this->sut->obtenerPresupuestos();

                $categoria_resultado = $this->buscarCategoriaNenResultados(
                        $resultados,
                        $categoria_id,
                );

                $this->assertNotNull(
                        $categoria_resultado,
                        "La categoría B debe estar en los resultados",
                );

                $this->assertNull(
                        $categoria_resultado["presupuesto"],
                        "El presupuesto debe ser NULL",
                );
                $this->assertEquals(
                        250.0,
                        floatval($categoria_resultado["gasto_real"]),
                        "El gasto real debe ser 250.00",
                );
                $this->assertNull(
                        $categoria_resultado["diferencia"],
                        "La diferencia debe ser NULL",
                );
                $this->assertNull(
                        $categoria_resultado["porcentaje_utilizado"],
                        "El porcentaje utilizado debe ser NULL",
                );
                $this->assertNull(
                        $categoria_resultado["estado"],
                        "El estado debe ser NULL",
                );

                // La limpieza se hace automáticamente en tearDown()
        }

        /**
         * TC-PRESU-R-INT-3: Estado Crítico y Superado (Alerta)
         *
         * @test
         * @group integration
         */
        public function test_estado_critico_y_superado_alerta()
        {
                $categoria_id = $this->crearCategoriaDePrueba("Categoria C");

                $this->insertarPresupuestoDePrueba(
                        $categoria_id,
                        500.0,
                        "Presupuesto crítico",
                );

                $this->insertarGastoDePrueba(
                        $categoria_id,
                        600.0,
                        "Gasto excedido C",
                );

                $resultados = $this->sut->obtenerPresupuestos();

                $categoria_resultado = $this->buscarCategoriaNenResultados(
                        $resultados,
                        $categoria_id,
                );

                $this->assertNotNull(
                        $categoria_resultado,
                        "La categoría C debe estar en los resultados",
                );

                $this->assertEquals(
                        500.0,
                        floatval($categoria_resultado["presupuesto"]),
                        "El presupuesto debe ser 500.00",
                );
                $this->assertEquals(
                        600.0,
                        floatval($categoria_resultado["gasto_real"]),
                        "El gasto real debe ser 600.00",
                );
                $this->assertEquals(
                        120.0,
                        floatval($categoria_resultado["porcentaje_utilizado"]),
                        "El porcentaje utilizado debe ser 120.00",
                );
                $this->assertEquals(
                        -100.0,
                        floatval($categoria_resultado["diferencia"]),
                        "La diferencia debe ser -100.00",
                );
                $this->assertEquals(
                        "danger",
                        $categoria_resultado["estado"],
                        "El estado debe ser danger",
                );

                // La limpieza se hace automáticamente en tearDown()
        }

        /**
         * Prueba adicional: Verificar que el método maneja múltiples categorías correctamente
         *
         * @test
         * @group integration
         */
        public function test_consulta_con_multiples_categorias_y_diferentes_estados()
        {
                // Crear múltiples categorías de prueba
                $categoria1_id = $this->crearCategoriaDePrueba("Multi Cat 1");
                $categoria2_id = $this->crearCategoriaDePrueba("Multi Cat 2");
                $categoria3_id = $this->crearCategoriaDePrueba("Multi Cat 3");

                // Categoria 1: Presupuesto normal (success)
                $this->insertarPresupuestoDePrueba($categoria1_id, 1000.0);
                $this->insertarGastoDePrueba($categoria1_id, 300.0);

                // Categoria 2: Sin presupuesto, solo gasto
                $this->insertarGastoDePrueba($categoria2_id, 150.0);

                // Categoria 3: Presupuesto excedido (danger)
                $this->insertarPresupuestoDePrueba($categoria3_id, 200.0);
                $this->insertarGastoDePrueba($categoria3_id, 250.0);

                // Consultar presupuestos
                $resultados = $this->sut->obtenerPresupuestos();

                // Verificar que todas las categorías están presentes
                $cat1_resultado = $this->buscarCategoriaNenResultados(
                        $resultados,
                        $categoria1_id,
                );
                $cat2_resultado = $this->buscarCategoriaNenResultados(
                        $resultados,
                        $categoria2_id,
                );
                $cat3_resultado = $this->buscarCategoriaNenResultados(
                        $resultados,
                        $categoria3_id,
                );

                $this->assertNotNull(
                        $cat1_resultado,
                        "Categoría 1 debe estar en resultados",
                );
                $this->assertNotNull(
                        $cat2_resultado,
                        "Categoría 2 debe estar en resultados",
                );
                $this->assertNotNull(
                        $cat3_resultado,
                        "Categoría 3 debe estar en resultados",
                );

                // Verificar estados diferentes
                $this->assertEquals(
                        "success",
                        $cat1_resultado["estado"],
                        "Categoría 1 debe tener estado success",
                );
                $this->assertNull(
                        $cat2_resultado["estado"],
                        "Categoría 2 debe tener estado NULL",
                );
                $this->assertEquals(
                        "danger",
                        $cat3_resultado["estado"],
                        "Categoría 3 debe tener estado danger",
                );

                // Verificar cálculos
                $this->assertEquals(
                        30.0,
                        floatval($cat1_resultado["porcentaje_utilizado"]),
                        "Categoría 1: porcentaje 30%",
                );
                $this->assertNull(
                        $cat2_resultado["porcentaje_utilizado"],
                        "Categoría 2: porcentaje NULL",
                );
                $this->assertEquals(
                        125.0,
                        floatval($cat3_resultado["porcentaje_utilizado"]),
                        "Categoría 3: porcentaje 125%",
                );

                // La limpieza se hace automáticamente en tearDown()
        }

        /**
         * Prueba adicional: Verificar que solo se consideran gastos con status 3
         *
         * @test
         * @group integration
         */
        public function test_solo_considera_gastos_con_status_tres()
        {
                // Crear categoría de prueba
                $categoria_id = $this->crearCategoriaDePrueba("Status Test");

                // Insertar presupuesto
                $this->insertarPresupuestoDePrueba($categoria_id, 1000.0);

                // Insertar gastos con diferentes status
                $this->insertarGastoDePrueba(
                        $categoria_id,
                        200.0,
                        "Gasto aprobado",
                ); // Status 3 por defecto

                // Insertar gasto con status diferente a 3 (no debe contabilizarse)
                $this->sut->conectarBD();
                $fecha_actual = date("Y-m-d");
                $sql = "INSERT INTO gasto (cod_cat_gasto, cod_condicion, descripcion, monto, fecha_creacion, status)
                        VALUES (:cat, 4, 'Gasto pendiente', 300.00, :fecha, 1)";
                $stmt = $this->sut->getconex()->prepare($sql);
                $stmt->bindParam(":cat", $categoria_id, PDO::PARAM_INT);
                $stmt->bindParam(":fecha", $fecha_actual);
                $stmt->execute();

                // Consultar presupuestos
                $resultados = $this->sut->obtenerPresupuestos();
                $categoria_resultado = $this->buscarCategoriaNenResultados(
                        $resultados,
                        $categoria_id,
                );

                // Verificar que solo se contabiliza el gasto con status 3
                $this->assertNotNull($categoria_resultado);
                $this->assertEquals(
                        200.0,
                        floatval($categoria_resultado["gasto_real"]),
                        "Solo debe contabilizar gasto con status 3",
                );
                $this->assertEquals(
                        20.0,
                        floatval($categoria_resultado["porcentaje_utilizado"]),
                        "Porcentaje debe ser 20% (200/1000)",
                );

                // La limpieza se hace automáticamente en tearDown()
        }

        /**
         * Prueba adicional: Verificar que solo se consideran datos del mes actual
         *
         * @test
         * @group integration
         */
        public function test_solo_considera_datos_del_mes_actual()
        {
                // Crear categoría de prueba
                $categoria_id = $this->crearCategoriaDePrueba("Mes Test");

                // Insertar presupuesto del mes actual
                $this->insertarPresupuestoDePrueba($categoria_id, 1000.0);

                // Insertar gasto del mes actual
                $this->insertarGastoDePrueba($categoria_id, 200.0);

                // Insertar gasto de mes anterior (no debe contabilizarse)
                $this->sut->conectarBD();
                $fecha_mes_anterior = date("Y-m-d", strtotime("-1 month"));
                $sql = "INSERT INTO gasto (cod_cat_gasto, cod_condicion, descripcion, monto, fecha_creacion, status)
                        VALUES (:cat, 4, 'Gasto mes anterior', 500.00, :fecha, 3)";
                $stmt = $this->sut->getconex()->prepare($sql);
                $stmt->bindParam(":cat", $categoria_id, PDO::PARAM_INT);
                $stmt->bindParam(":fecha", $fecha_mes_anterior);
                $stmt->execute();

                // Consultar presupuestos
                $resultados = $this->sut->obtenerPresupuestos();
                $categoria_resultado = $this->buscarCategoriaNenResultados(
                        $resultados,
                        $categoria_id,
                );

                // Verificar que solo se contabiliza el gasto del mes actual
                $this->assertNotNull($categoria_resultado);
                $this->assertEquals(
                        200.0,
                        floatval($categoria_resultado["gasto_real"]),
                        "Solo debe contabilizar gasto del mes actual",
                );
                $this->assertEquals(
                        20.0,
                        floatval($categoria_resultado["porcentaje_utilizado"]),
                        "Porcentaje debe ser 20%",
                );

                // La limpieza se hace automáticamente en tearDown()
        }
}
