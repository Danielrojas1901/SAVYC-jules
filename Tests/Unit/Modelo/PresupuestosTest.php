<?php

namespace Tests\Unit\Modelo;

use Modelo\Presupuestos;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Exception;

// Stub para evitar conexión real a BD
class PresupuestosStub extends Presupuestos
{
        public function __construct()
        {
                // No llamar al constructor padre para evitar conexión BD
        }
        public function conectarBD() {}
        public function desconectarBD() {}
}

class PresupuestosTest extends TestCase
{
        private $presupuestos;

        protected function setUp(): void
        {
                $this->presupuestos = new PresupuestosStub();
        }

        // Test inicial - verificar que no hay errores al inicio
        public function testInicialmenteNoHayErrores()
        {
                $errores = $this->presupuestos->getErrores();
                $this->assertEmpty(
                        $errores,
                        "Inicialmente no debería haber errores",
                );

                $datos = $this->presupuestos->getDatos();
                $this->assertEmpty(
                        $datos,
                        "Inicialmente no debería haber datos",
                );
        }

        // Data providers para casos válidos
        public static function codCatGastoValido(): array
        {
                return [
                        "entero positivo" => [1],
                        "string numérico" => ["5"],
                        "decimal" => [10.5],
                        "cero" => [0],
                ];
        }

        #[DataProvider("codCatGastoValido")]
        public function testSetCodCatGastoConValorValidoNoLanza(
                $codCatGasto,
        ): void {
                $datos = ["cod_cat_gasto" => $codCatGasto];

                $this->presupuestos->setDatos($datos);
                $this->presupuestos->check();

                $this->assertEmpty($this->presupuestos->getErrores());
        }

        public static function mesValido(): array
        {
                return [
                        "mes enero" => ["enero"],
                        "mes con número" => ["12"],
                        "mes con espacio" => ["mes actual"],
                ];
        }

        #[DataProvider("mesValido")]
        public function testSetMesConValorValidoNoLanza($mes): void
        {
                $datos = ["mes" => $mes];

                $this->presupuestos->setDatos($datos);
                $this->presupuestos->check();

                $this->assertEmpty($this->presupuestos->getErrores());
        }

        public static function montoValido(): array
        {
                return [
                        "entero positivo" => [100],
                        "decimal positivo" => [99.99],
                        "string numérico positivo" => ["50.25"],
                ];
        }

        #[DataProvider("montoValido")]
        public function testSetMontoConValorValidoNoLanza($monto): void
        {
                $datos = ["monto" => $monto];

                $this->presupuestos->setDatos($datos);
                $this->presupuestos->check();

                $this->assertEmpty($this->presupuestos->getErrores());
        }

        public static function descripcionValida(): array
        {
                return [
                        "texto normal" => ["Gastos de oficina"],
                        "vacío" => [""],
                        "con números" => ["Pago 123"],
                        "texto largo válido" => [str_repeat("a", 200)],
                ];
        }

        #[DataProvider("descripcionValida")]
        public function testSetDescripcionConValorValidoNoLanza(
                $descripcion,
        ): void {
                $datos = ["descripcion" => $descripcion];

                $this->presupuestos->setDatos($datos);
                $this->presupuestos->check();

                $this->assertEmpty($this->presupuestos->getErrores());
        }

        // Tests para casos inválidos
        public static function codCatGastoInvalido(): array
        {
                return [
                        "string no numérico" => ["abc"],
                        "array" => [[]],
                        "null" => [null],
                ];
        }

        #[DataProvider("codCatGastoInvalido")]
        public function testSetCodCatGastoConValorInvalidoLanza(
                $codCatGastoInvalido,
        ): void {
                $datos = ["cod_cat_gasto" => $codCatGastoInvalido];

                $this->presupuestos->setDatos($datos);

                $this->expectException(Exception::class);
                $this->expectExceptionMessage("Errores de validación");
                $this->presupuestos->check();
        }

        public static function mesInvalido(): array
        {
                return [
                        "string vacío" => [""],
                        "null" => [null],
                        "array vacío" => [[]],
                ];
        }

        #[DataProvider("mesInvalido")]
        public function testSetMesConValorInvalidoLanza($mesInvalido): void
        {
                $datos = ["mes" => $mesInvalido];

                $this->presupuestos->setDatos($datos);

                $this->expectException(Exception::class);
                $this->expectExceptionMessage("Errores de validación");
                $this->presupuestos->check();
        }

        public static function montoInvalido(): array
        {
                return [
                        "negativo" => [-10],
                        "cero" => [0],
                        "string no numérico" => ["abc"],
                        "array" => [[]],
                ];
        }

        #[DataProvider("montoInvalido")]
        public function testSetMontoConValorInvalidoLanza($montoInvalido): void
        {
                $datos = ["monto" => $montoInvalido];

                $this->presupuestos->setDatos($datos);

                $this->expectException(Exception::class);
                $this->expectExceptionMessage("Errores de validación");
                $this->presupuestos->check();
        }

        public static function descripcionInvalida(): array
        {
                return [
                        "texto muy largo" => [str_repeat("a", 201)],
                ];
        }

        #[DataProvider("descripcionInvalida")]
        public function testSetDescripcionConValorInvalidoLanza(
                $descripcionInvalida,
        ): void {
                $datos = ["descripcion" => $descripcionInvalida];

                $this->presupuestos->setDatos($datos);

                $this->expectException(Exception::class);
                $this->expectExceptionMessage("Errores de validación");
                $this->presupuestos->check();
        }

        // Test de presupuesto mínimo válido
        public static function presupuestoMinimoValido(): array
        {
                return [
                        "presupuesto básico" => [
                                [
                                        "cod_cat_gasto" => 1,
                                        "mes" => "enero",
                                        "monto" => 100.5,
                                ],
                        ],
                ];
        }

        #[DataProvider("presupuestoMinimoValido")]
        public function testPresupuestoMinimoValidoNoLanza($datos): void
        {
                $this->presupuestos->setDatos($datos);
                $this->presupuestos->check();

                $this->assertEmpty($this->presupuestos->getErrores());
                $datosPersistidos = $this->presupuestos->getDatos();
                $this->assertEquals($datos, $datosPersistidos);
        }

        // Data provider para presupuesto completo válido
        public static function presupuestoCompletoValido(): array
        {
                return [
                        "presupuesto completo básico" => [
                                [
                                        "cod_cat_gasto" => 1,
                                        "mes" => "enero",
                                        "monto" => 500.75,
                                        "descripcion" =>
                                                "Gastos administrativos",
                                        "mes_inicio" => 1,
                                        "año_inicio" => 2024,
                                        "mes_fin" => 12,
                                        "año_fin" => 2024,
                                ],
                        ],
                ];
        }

        #[DataProvider("presupuestoCompletoValido")]
        public function testPresupuestoCompletoValidoNoLanza($datos): void
        {
                $this->presupuestos->setDatos($datos);
                $this->presupuestos->check();

                $this->assertEmpty($this->presupuestos->getErrores());
        }

        // Data provider para casos límite
        public static function casosLimite(): array
        {
                return [
                        "valores límite válidos" => [
                                [
                                        "cod_cat_gasto" => 0,
                                        "mes" => "1",
                                        "monto" => 0.01,
                                        "descripcion" => str_repeat("a", 200),
                                ],
                        ],
                ];
        }

        #[DataProvider("casosLimite")]
        public function testCasosLimite($datos): void
        {
                $this->presupuestos->setDatos($datos);
                $this->presupuestos->check();

                $this->assertEmpty($this->presupuestos->getErrores());
        }

        // Data provider para múltiples errores
        public static function multiplesErrores(): array
        {
                return [
                        "cuatro errores simultáneos" => [
                                [
                                        "cod_cat_gasto" => "abc",
                                        "mes" => "",
                                        "monto" => -10,
                                        "descripcion" => str_repeat("a", 201),
                                ],
                        ],
                ];
        }

        #[DataProvider("multiplesErrores")]
        public function testMultiplesErroresAgrupaMensajes($datos): void
        {
                $this->presupuestos->setDatos($datos);

                $errores = $this->presupuestos->getErrores();
                $this->assertCount(4, $errores);

                $this->expectException(Exception::class);
                $this->expectExceptionMessage("Errores de validación");
                $this->presupuestos->check();
        }
}
