<?php
declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Divisa;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Modelo\Traits\MaliciousDataProvidersTrait;

class DivisaStub extends Divisa
{
    public function __construct() {}
    public function conectarBD()   { /* no-op */ }
    public function desconectarBD(){ /* no-op */ }
}

#[Group('unit')]
final class DivisaTest extends TestCase
{
    use MaliciousDataProvidersTrait;
    
    private DivisaStub $sut;

    protected function setUp(): void
    {
        $this->sut = new DivisaStub();
    }

    // ========================================
    // PRUEBAS DE ESTADO INICIAL
    // ========================================
    
    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getnombre());
        $this->assertNull($this->sut->getsimbolo());
        $this->assertNull($this->sut->getStatus());
        $this->assertNull($this->sut->get_tasa());
        $this->assertNull($this->sut->getfecha());
    }

    // ========================================
    // PRUEBAS VÁLIDAS - CASOS QUE DEBEN PASAR
    // ========================================

    // DataProvider para nombres válidos
    public static function nombresValidos(): array
    {
        return [
            'minimo 2' => ['Hi'],
            'medio' => ['Dólar Americano'],
            'maximo 50' => [str_repeat('a', 50)],
            'con acentos' => ['Euro'],
        ];
    }

    #[DataProvider('nombresValidos')]
    public function testSetNombreConValorValidoNoLanza(string $nombre): void
    {
        $this->sut->setnombre($nombre);
        $this->sut->check();
        $this->assertSame($nombre, $this->sut->getnombre());
    }

    // DataProvider para símbolos válidos
    public static function simbolosValidos(): array
    {
        return [
            'minimo 2' => ['US'],
            'medio' => ['USD'],
            'maximo 10' => [str_repeat('x', 10)],
            'con numeros' => ['US1'],
            'con guiones' => ['US-D'],
        ];
    }

    #[DataProvider('simbolosValidos')]
    public function testSetSimboloConValorValidoNoLanza(string $simbolo): void
    {
        $this->sut->setsimbolo($simbolo);
        $this->sut->check();
        $this->assertSame($simbolo, $this->sut->getsimbolo());
    }

    // DataProvider para status válidos
    public static function statusValidos(): array
    {
        return [
            'activo' => ['1'],
            'inactivo' => ['0'],
        ];
    }

    #[DataProvider('statusValidos')]
    public function testSetStatusConValorValidoNoLanza(string $status): void
    {
        $this->sut->setstatus($status);
        $this->sut->check();
        $this->assertSame($status, $this->sut->getStatus());
    }

    // DataProvider para tasas válidas
    public static function tasasValidas(): array
    {
        return [
            'cero' => ['0'],
            'entero' => ['12345'],
            'maximo borde' => ['1000000'],
        ];
    }

    #[DataProvider('tasasValidas')]
    public function testSetTasaConValorValidoNoLanza(string $tasa): void
    {
        $this->sut->set_tasa($tasa);
        $this->sut->check();
        $this->assertSame($tasa, $this->sut->get_tasa());
    }

    // ========================================
    // PRUEBAS INVÁLIDAS - CASOS QUE DEBEN FALLAR
    // ========================================

    // DataProvider para nombres inválidos
    public static function nombresInvalidos(): array
    {
        return [
            'vacio' => [''],
            'solo 1 char' => ['A'],
            'mas de 50' => [str_repeat('a', 51)],
        ];
    }

    #[DataProvider('nombresInvalidos')]
    public function testSetNombreConValorInvalidoLanza(string $nombre): void
    {
        $this->sut->setnombre($nombre);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta nombres inválidos sin validación: '$nombre'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para símbolos inválidos
    public static function simbolosInvalidos(): array
    {
        return [
            'vacio' => [''],
            'menos de 2' => ['A'],
            'mas de 10' => [str_repeat('y', 11)],
            'caracter no permitido' => ['US$'],
        ];
    }

    #[DataProvider('simbolosInvalidos')]
    public function testSetSimboloConValorInvalidoLanza(string $simbolo): void
    {
        $this->sut->setsimbolo($simbolo);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta símbolos inválidos sin validación: '$simbolo'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para status inválidos
    public static function statusInvalidos(): array
    {
        return [
            'negativo' => ['-1'],
            'muy grande' => ['999'],
            'decimal' => ['1.5'],
            'string' => ['abc'],
            'boolean' => [true],
            'null' => [null],
        ];
    }

    #[DataProvider('statusInvalidos')]
    public function testSetStatusConValorInvalidoLanza($status): void
    {
        $this->sut->setstatus($status);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta status inválidos sin validación: " . var_export($status, true));
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para tasas inválidas
    public static function tasasInvalidas(): array
    {
        return [
            'negativo' => ['-1'],
            'no numerico' => ['12.3'],
            'muy grande' => [str_repeat('9', 1000001)],
        ];
    }

    #[DataProvider('tasasInvalidas')]
    public function testSetTasaConValorInvalidoLanza(string $tasa): void
    {
        $this->sut->set_tasa($tasa);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta tasas inválidas sin validación: '$tasa'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // ========================================
    // PRUEBAS DE DIVISA MÍNIMA
    // ========================================

    // DataProvider para divisas mínimas válidas
    public static function divisasMinimasValidas(): array
    {
        return [
            'solo nombre y status' => [
                'nombre' => 'Dólar',
                'status' => '1'
            ],
            'con simbolo' => [
                'nombre' => 'Euro',
                'simbolo' => 'EUR',
                'status' => '1'
            ],
        ];
    }

    #[DataProvider('divisasMinimasValidas')]
    public function testDivisaMinimaValidaNoLanza(string $nombre, string $status, string $simbolo = null): void
    {
        $this->sut->setnombre($nombre);
        if ($simbolo !== null) {
            $this->sut->setsimbolo($simbolo);
        }
        $this->sut->setstatus($status);
        
        $this->sut->check(); // No debe lanzar excepción
        
        $this->assertSame($nombre, $this->sut->getnombre());
        $this->assertSame($status, $this->sut->getStatus());
        if ($simbolo !== null) {
            $this->assertSame($simbolo, $this->sut->getsimbolo());
        }
    }

    // ========================================
    // PRUEBAS EDGE CASES Y MALICIOSAS
    // ========================================

    // DataProvider para casos límite de nombres
    public static function casosLimiteNombres(): array
    {
        return [
            'minimo exacto' => [str_repeat('a', 2)],
            'maximo exacto' => [str_repeat('a', 50)],
            'un caracter mas' => [str_repeat('a', 51)],
            'unicode' => ['áéíóú'],
            'emojis' => ['😀😀'],
            'caracteres especiales' => ['!@#$%^&*()'],
        ];
    }

    #[DataProvider('casosLimiteNombres')]
    public function testCasosLimiteNombres(string $nombre): void
    {
        $this->sut->setnombre($nombre);
        
        try {
            $this->sut->check();
            $this->assertSame($nombre, $this->sut->getnombre());
        } catch (\Exception $e) {
            $this->assertStringContainsString('nombre', $e->getMessage());
        }
    }

    // DataProvider para casos maliciosos - usando trait
    #[DataProvider('casosMaliciosos')]
    public function testCasosMaliciosos(string $valor): void
    {
        $this->sut->setnombre($valor);
        
        try {
            $this->sut->check();
            $this->assertSame($valor, $this->sut->getnombre());
            // Si llega aquí, el sistema aceptó datos maliciosos - esto puede ser un defecto de seguridad
            $this->fail("DEFECTO DE SEGURIDAD: El sistema acepta datos maliciosos sin validación: '$valor'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('nombre', $e->getMessage());
        }
    }

    // DataProvider para tipos de datos problemáticos - usando trait
    #[DataProvider('tiposDatosProblematicos')]
    public function testTiposDatosProblematicos($valor): void
    {
        // DEFECTO DOCUMENTADO: El sistema no valida tipos de datos correctamente
        
        if (is_array($valor) || is_object($valor) || is_resource($valor) || is_callable($valor)) {
            // Estos casos SÍ causan TypeError - defecto del sistema
            $this->expectException(\TypeError::class);
            $this->sut->setnombre($valor);
        } else {
            // DEFECTO: Float e Integer se convierten a string sin validación
            // El sistema debería rechazar estos tipos, pero los acepta
            $this->sut->setnombre($valor);
            
            try {
                $this->sut->check();
                $this->fail("DEFECTO: El sistema acepta tipos incorrectos como " . gettype($valor) . " sin validación");
            } catch (\Exception $e) {
                $this->assertStringContainsString('Errores de validación', $e->getMessage());
            }
        }
    }

    // DataProvider para símbolos con caracteres especiales - usando trait
    #[DataProvider('caracteresEspeciales')]
    public function testSimbolosConCaracteresEspeciales(string $simbolo): void
    {
        $this->sut->setsimbolo($simbolo);
        
        try {
            $this->sut->check();
            $this->assertSame($simbolo, $this->sut->getsimbolo());
        } catch (\Exception $e) {
            $this->assertStringContainsString('simbolo', $e->getMessage());
        }
    }

    // ========================================
    // PRUEBAS DE MÚLTIPLES ERRORES
    // ========================================

    public function testMultiplesErroresAgrupaMensajes(): void
    {
        // Múltiples campos inválidos
        $this->sut->setnombre('A'); // Inválido
        $this->sut->setsimbolo('B'); // Inválido
        $this->sut->setstatus('99'); // Inválido
        $this->sut->set_tasa('abc'); // Inválido

        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta múltiples datos inválidos sin validación");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // ========================================
    // PRUEBAS DE CAMPOS OPCIONALES
    // ========================================

    public function testCamposOpcionalesConValoresMinimos(): void
    {
        $this->sut->setnombre('Dólar');
        $this->sut->setstatus('1');
        // Campos opcionales con valores válidos mínimos
        $this->sut->setsimbolo('US');
        $this->sut->set_tasa('0');

        $this->sut->check(); // No debe lanzar excepción

        $this->assertSame('Dólar', $this->sut->getnombre());
        $this->assertSame('1', $this->sut->getStatus());
        $this->assertSame('US', $this->sut->getsimbolo());
        $this->assertSame('0', $this->sut->get_tasa());
    }

    // ========================================
    // PRUEBAS DE FECHA
    // ========================================

    public function testSetFechaYGetFecha(): void
    {
        $fecha = '2025-01-15';
        $this->sut->setfecha($fecha);
        $this->assertSame($fecha, $this->sut->getfecha());
    }

    // ========================================
    // PRUEBAS DE CASOS DE USO REALES
    // ========================================

    public function testDivisaCompletaValidaNoLanza(): void
    {
        $this->sut->setnombre('Dólar Americano');
        $this->sut->setsimbolo('USD');
        $this->sut->setstatus('1');
        $this->sut->set_tasa('1000000');
        $this->sut->setfecha('2025-01-15');

        $this->sut->check(); // No debe lanzar excepción

        $this->assertSame('Dólar Americano', $this->sut->getnombre());
        $this->assertSame('USD', $this->sut->getsimbolo());
        $this->assertSame('1', $this->sut->getStatus());
        $this->assertSame('1000000', $this->sut->get_tasa());
        $this->assertSame('2025-01-15', $this->sut->getfecha());
    }

    public function testDivisaMinimaConSoloNombreYStatus(): void
    {
        $this->sut->setnombre('Euro');
        $this->sut->setstatus('0');

        $this->sut->check();

        $this->assertSame('Euro', $this->sut->getnombre());
        $this->assertSame('0', $this->sut->getStatus());
        $this->assertNull($this->sut->getsimbolo());
        $this->assertNull($this->sut->get_tasa());
    }

    public function testActualizacionDivisa(): void
    {
        // Simular actualización de divisa existente
        $this->sut->setnombre('Dólar Canadiense');
        $this->sut->setsimbolo('CAD');
        $this->sut->setstatus('1');
        $this->sut->set_tasa('500000');
        $this->sut->setfecha('2025-01-20');

        $this->sut->check();

        $this->assertSame('Dólar Canadiense', $this->sut->getnombre());
        $this->assertSame('CAD', $this->sut->getsimbolo());
        $this->assertSame('1', $this->sut->getStatus());
        $this->assertSame('500000', $this->sut->get_tasa());
        $this->assertSame('2025-01-20', $this->sut->getfecha());
    }
}