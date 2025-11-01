<?php

declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\CategoriaGasto;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

class CategoriaGastos extends CategoriaGasto
{
    public function __construct() {}
    public function conectarBD() {}
    public function desconectarBD() {}
}

#[Group('unit')]
final class CategoriagastoTest extends TestCase
{

    private CategoriaGastos $obj;

    protected function setUp(): void
    {
        $this->obj = new CategoriaGastos();
    }

    public static function datosValidos(): array
    {
        return [
            'caso_valido' => [[
                'status_cat_gasto' => 0,
                'frecuenciaC' => 2,
                'frecuencia' => 'Mensual',
                'naturaleza' => 1,
                'cod_cat_gasto' => 1,
                'tipogasto' => 1,
                'dias' => 1,
                'nombre' => 'Express',
                'origin' => 'venezuela',
                'fecha' => '2022-01-01',
            ]]
        ];
    }
    //DATOS INVALIDOS FORMA GENERAL
    public static function datosInvalidos(): array
    {
        return [
            'caso_invalido' => [[
                'status_cat_gasto' => 'hola',
                'frecuenciaC' => 'zazz',
                'frecuencia' => 1,
                'naturaleza' => 'sin naturaleza',
                'cod_cat_gasto' => 'sin codigo',
                'tipogasto' => 'sin tipo',
                'dias' => 'sin dias',
                'nombre' => 45,
                'origin' => 'v',
                'fecha' => 552,
            ]]
        ];
    }
    //DATOS INVALIDOS INDIVIDUALES
    public static function status_cat_gastoInvalid(): array
    {
        return [
            'decimal' => ['1.55'],
            'caracteres' => ['!@#$%^&*()'],
            'vacio' => [''],
            'null' => [null],
        ];
    }
    public static function frecuenciaCInvalid(): array
    {
        return [
            'decimal' => ['2.46'],
            'caracteres' => ['!@#$%^&*()'],
            'vacio' => [''],
            'null' => [null],
        ];
    }
    public static function naturalezaInvalid(): array
    {
        return [
            'decimal' => ['2.46'],
            'caracteres' => ['!@#$%^&*()'],
            'vacio' => [''],
            'null' => [null],
        ];
    }
    public static function cod_cat_gastoInvalid(): array
    {
        return [
            'decimal' => ['2.11'],
            'caracteres' => ['!@#$%^&*()'],
            'vacio' => [''],
            'null' => [null],
        ];
    }

    public static function tipogastoInvalid(): array
    {
        return [
            'decimal' => ['1.11'],
            'caracteres' => ['!@#$%^&*()'],
            'vacio' => [''],
            'null' => [null],
        ];
    }

    public static function diasInvalid(): array
    {
        return [
            'min' => [str_repeat('A', 0)],
            'max' => [str_repeat('A', 2)],
            'caracteres' => ['!@#$%^&*()'],
            'vacio' => [''],
            'null' => [null],
        ];
    }

    public static function nombreInvalid(): array
    {
        return [
            'min' => [str_repeat('A', 0)],
            'max' => [str_repeat('Hello', 52)],
            'caracteres' => ['!@#$%^&*()'],
            'vacio' => [''],
            'null' => [null],
        ];
    }

    public static function originInvalid(): array
    {
        return [
            'min' => [str_repeat('v', 0)],
            'max' => [str_repeat('ineter', 52)],
            'caracteres' => ['!@#$%^&*()'],
            'vacio' => [''],
            'null' => [null],
        ];
    }
    public static function fechaInvalid(): array
    {
        return [
            'formato' => ['2022/13/01'],
            'texto' => ['fecha invalida'],
            'numerico' => ['123456'],
            'vacio' => [''],
            'null' => [null],
        ];
    }
    public static function frecuenciaInavlid(): array
    {
        return [
            'min' => [str_repeat('A', 0)],
            'max' => [str_repeat('Hello', 52)],
            'caracteres' => ['!@#$%^&*()'],
            'vacio' => [''],
            'null' => [null],
        ];
    }

    public static function datos(): array
    {
        return [
            'nombre' => 'Express',
            'origin' => 'venezuela',
            'cod_banco' => 1,
        ];
    }
    //DATAS INDIVIDUALES
    public function nombreInvalido(): array
    {
        return [
            'min' => [str_repeat('A', 0)],
            'max' => [str_repeat('banco', 51)],
            'null' => [null],
            'numerico' => ['1'],
            'caracteres' => ['!@#$%^&*()'],
        ];
    }

    public function originInvalido(): array
    {
        return [
            'min' => [str_repeat('A', 0)],
            'max' => [str_repeat('origin', 51)],
            'null' => [null],
            'numerico' => ['1.55'],
            'caracteres' => ['!@#$%^&*()'],
        ];
    }

    public function cod_bancoInvalido(): array
    {
        return [
            'letra' => ['a'],
            'numerico' => ['1.55'],
            'caracteres' => ['!@#$%^&*()'],
            'vacio' => [''],
            'null' => [null],
        ];
    }

    public static function cod_condicionInvalid(): array
    {
        return [
            'letra' => ['a'],
            'decimal' => ['1.1'],
            'caracteres' => ['!@#$%'],
            'vacio' => [''],
            'null' => [null],
        ];
    }

    #[DataProvider('datosValidos')]
    public function testSetterGetter(mixed $datos): void
    {
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->assertSame($datos, $this->obj->getDatos());
        } catch (\Exception $e) {
            $this->fail("DEFECTO: El sistema no acepta datos válidos: " . $e->getMessage());
        }
    }

    // DATOS FALSOS

    #[DataProvider('datosInvalidos')]
    public function testSetterGetterfalse(mixed $datosInvalidos): void
    {
        try {
            $this->obj->setDatos($datosInvalidos);
            $this->expectException(\Exception::class);
            $this->expectExceptionMessageMatches('/Errores de validación.*/');
            $this->obj->check();
        } catch (\Exception $e) {
            $this->fail("DEFECTO: El sistema acepta datos inválidos sin validación: " . $e->getMessage());
        }
    }

    #[DataProvider('status_cat_gastoInvalid')]
    public function testStatusInvalido(mixed $status_cat_gasto): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['status_cat_gasto'] = $status_cat_gasto;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    #[DataProvider('frecuenciaCInvalid')]
    public function testFrecuenciaInvalido(mixed $frecuenciaC): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['frecuenciaC'] = $frecuenciaC;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    #[DataProvider(('naturalezaInvalid'))]
    public function testNaturalezaInvalido(mixed $naturaleza): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['naturaleza'] = $naturaleza;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    #[DataProvider(('cod_condicionInvalid'))]
    public function testCod_condicionInvalido(mixed $cod_condicion): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['cod_condicion'] = $cod_condicion;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    #[DataProvider('cod_cat_gastoInvalid')]
    public function testCod_cat_gastoInvalido(mixed $cod_cat_gasto): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['cod_cat_gasto'] = $cod_cat_gasto;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    #[DataProvider('tipogastoInvalid')]
    public function testTipogastoInvalido(mixed $tipogasto): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['tipogasto'] = $tipogasto;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    #[DataProvider('diasInvalid')]
    public function testDiasInvalido(mixed $dias): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['dias'] = $dias;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    #[DataProvider('nombreInvalid')]
    public function testNombreInvalido(mixed $nombre): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['nombre'] = $nombre;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    #[DataProvider('originInvalid')]
    public function testOriginInvalido(mixed $origin): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['origin'] = $origin;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    #[DataProvider('fechaInvalid')]
    public function testFechaInvalido(mixed $fecha): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['fecha'] = $fecha;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    #[DataProvider('frecuenciaInavlid')]
    public function testFrecuenciaInavlid(mixed $frecuencia): void
    {
        $datos = self::datosValidos(); //referencia al método estático que devuelve los datos de prueba
        $datos['frecuencia'] = $frecuencia;
        try {
            $this->obj->setDatos($datos);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema aceptó datos inválidos sin lanzar excepción.");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
}
