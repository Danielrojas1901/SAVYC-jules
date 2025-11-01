<?php

declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Gasto;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Exception;

class GastoStub extends Gasto
{
    public function __construct() {}
    public function conectarBD()
    { /* no-op */
    }
    public function desconectarBD()
    { /* no-op */
    }
}

#[Group('unit')]
final class GastoTest extends TestCase
{
    private GastoStub $obj;

    protected function setUp(): void
    {
        $this->obj = new GastoStub();
    }

    // --- Data Providers ---

    public static function datosValidos(): array
    {
        return [
            'caso_valido' => [[
                'cod_condicion' => 2,
                'cod_gasto' => 1,
                'cod_cat_gasto' => 1,
                'monto' => 100,
                'descripcion' => 'Pago de internet',
                'fecha_vencimiento' => '2027-10-01',
                'origin' => 'Pago anterior',
                'fecha' => '2025-01-01',
            ]],
        ];
    }

    public static function datosInvalidos(): array
    {
        return [
            'caso_invalido' => [[
                'cod_condicion' => 'dos',
                'cod_gasto' => 'uno',
                'cod_cat_gasto' => 'doce',
                'monto' => 'trecientos dos',
                'descripcion' => 145,
                'fecha_vencimiento' => 20250930,
                'origin' => 389,
                'fecha' => 2022 - 01 - 01,
            ]],
        ];
    }
    public static function datosLimit(): array
    {
        return [
            'limite_inferior' => [[
                'cod_condicion' => '1',
                'cod_gasto' => 1,
                'cod_cat_gasto' => '1',
                'monto' => 0.01,
                'descripcion' => 'ab',        // al menos 2 caracteres
                'fecha_vencimiento' => '2025-01-01',  // formato string
                'origin' => 'ab',             // string válido
                'fecha' => '2025-01-01',
            ]],
            'limite_superior' => [[
                'cod_condicion' => '99',
                'cod_gasto' => 50,
                'cod_cat_gasto' => '99',
                'monto' => 999999.99,
                'descripcion' => str_repeat('a', 50),
                'fecha_vencimiento' => '2099-12-31',
                'origin' => 999,
                'fecha' => '2099-12-31',
            ]],
            'fuera_de_limite' => [[
                'cod_condicion' => '100',
                'cod_gasto' => 51,
                'cod_cat_gasto' => '100',
                'monto' => 1000000.00,
                'descripcion' => str_repeat('b', 51),
                'fecha_vencimiento' => 21000101,
                'origin' => 1000,
                'fecha' => '2100-01-01',
            ]],
        ];
    }


    /*INDIVIDUALES */
    public static function cod_cat_gastoInvalido(): array
    {
        return [
            'texto' => ['texto'],
            'vacio' => [''],
        ];
    }
    public static function originInvalido(): array
    {
        return [
            'max' => [str_repeat('A', 51)],
            'min' => [str_repeat('A', 0)],
            'vacio' => [''],
            'null' => [null],
            'entero' => [1],
        ];
    }

    public static function cod_condicionInvalido(): array
    {
        return [
            'texto' => ['uno'],
            'vacio' => [''],
        ];
    }

    public static function fechaInvalido(): array
    {
        return [
            'vacio' => [''],
            'null' => [null],
        ];
    }

    public static function cod_gastoInvalido(): array
    {
        return [
            'texto' => ['codigo'],
            'vacio' => [''],
            'null' => [null],
        ];
    }

    public static function descripcionInvalido(): array
    {
        return [
            'max' => [str_repeat('a', 51)],
            'min' => [str_repeat('a', 0)],
            'vacio' => [''],
            'null' => [null],
            'entero' => [1],
            'decimal' => [1.1],
            'caracteres' => ['!@#$%^&*()'],
            'acentos' => ['áéíóú'],
        ];
    }

    public static function fecha_vencimientoInvalido(): array
    {
        return [
            'vacio' => [''],
            'null' => [null],
            'fecha' => ['2022-01-01'],
        ];
    }



    // --- PRUEBAS ---
    /* PRUEBAS CON DATOS LIMITES*/
    #[DataProvider('datosLimit')]
    public function testValoresLimite(mixed  $datos): void
    {
        $this->obj->setDatos($datos);

        try {
            // validamos los datos
            $this->obj->check();

            // Si llega aquí, significa que Ntodo 'Ok'
            if (
                ($datos['cod_condicion'] === '100') ||
                ($datos['cod_gasto'] === 51) ||
                ($datos['descripcion'] === str_repeat('b', 51))
            ) {
                // Si llegamos aquí y los datos eran inválidos, el test debe fallar xd
                $this->fail('Se esperaba una excepción por datos fuera de los límites, pero no ocurrió.');
            } else {
                // Casos válidos: confirmamos que los datos fueron procesados correctamente
                $this->assertIsArray($this->obj->getDatos());
                $this->assertNotEmpty($this->obj->getDatos());
            }
        } catch (\Exception $e) {
            // Si entra aquí, significa que sí hubo una excepción

            // Para los casos fuera de límite, validamos el mensaje
            if (
                ($datos['cod_condicion'] === '100') ||
                ($datos['cod_gasto'] === 51) ||
                ($datos['descripcion'] === str_repeat('b', 51))
            ) {
                $this->assertStringContainsString('Errores de validación', $e->getMessage());
            } else {
                // Si los datos eran válidos pero lanzó excepción fue error
                $this->fail('No se esperaba una excepción en datos válidos: ' . $e->getMessage());
            }
        }
    }

    /*PRUEBA CON DATOS VALIDOS*/
    #[DataProvider('datosValidos')]
    public function testSetterGetter(array $datos): void
    {
        $this->obj->setDatos($datos);
        $this->obj->check();
        $this->assertSame($datos, $this->obj->getDatos());
    }

    /*PRUEBA CON DATOS INVALIDOS*/

    #[DataProvider('datosInvalidos')]
    public function testSetterGetterInvalido(array $datos): void
    {
        $this->obj->setDatos($datos);
        $this->expectException(\Exception::class); //INDICO LA LLAMADA DE EXCEPCIÓN DE FORMA GLOBAL
        $this->expectExceptionMessageMatches('/Errores de validación.*/');
        $this->obj->check();
    }

    /* INDIVIDUALES DATOS INVALIDOS*/
    #[DataProvider(('cod_cat_gastoInvalido'))]
    public function testCod_cat_gastoInvalido(mixed  $cod_cat_gasto): void
    {
        try {
            $this->obj->setDatos(['cod_cat_gasto' => $cod_cat_gasto]);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema acepta cod_cat_gasto inválidos sin validación: '$cod_cat_gasto'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    #[DataProvider(('originInvalido'))]
    public function testOriginInvalido(mixed  $origin): void
    {
        try {
            $this->obj->setDatos(['origin' => $origin]);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema acepta origin inválidos sin validación: '$origin'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    #[DataProvider(('cod_condicionInvalido'))]
    public function testCod_condicionInavlido(mixed  $cod_condicion): void
    {
        try {
            $this->obj->setDatos(['cod_condicion' => $cod_condicion]);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema acepta cod_condicion inválidos sin validación: '$cod_condicion'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    #[DataProvider(('fechaInvalido'))]
    public function testFechaInvalido(mixed  $fecha): void
    {
        try {
            $this->obj->setDatos(['fecha' => $fecha]);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema acepta fecha inválidos sin validación: '$fecha'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    #[DataProvider(('cod_gastoInvalido'))]
    public function testCod_gastoInvalido(mixed  $cod_gasto): void
    {
        try {
            $this->obj->setDatos(['cod_gasto' => $cod_gasto]);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema acepta cod_gasto inválidos sin validación: '$cod_gasto'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    #[DataProvider(('descripcionInvalido'))]
    public function testDescripcionInvalido(mixed  $descripcion): void
    {
        try {
            $this->obj->setDatos(['descripcion' => $descripcion]);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema acepta descripcion inválidos sin validación: '$descripcion'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    #[DataProvider(('fecha_vencimientoInvalido'))]
    public function testFecha_vencimientoInvalido(mixed  $fecha_vencimiento): void
    {
        try {
            $this->obj->setDatos(['fecha_vencimiento' => $fecha_vencimiento]);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema acepta fecha_vencimiento inválidos sin validación: '$fecha_vencimiento'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
}
