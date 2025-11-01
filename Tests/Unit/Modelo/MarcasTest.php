<?php
declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Marcas;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Modelo\Traits\MaliciousDataProvidersTrait;

class MarcasStub extends Marcas
{
    public function __construct() {}
    public function conectarBD()   { /* no-op */ }
    public function desconectarBD(){ /* no-op */ }
}

#[Group('unit')]
final class MarcasTest extends TestCase
{
    use MaliciousDataProvidersTrait;
    
    private MarcasStub $sut;

    protected function setUp(): void
    {
        $this->sut = new MarcasStub();
    }

    // ========================================
    // PRUEBAS DE ESTADO INICIAL
    // ========================================
    
    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getNombre());
        $this->assertNull($this->sut->getStatus());
    }

    // ========================================
    // PRUEBAS VÁLIDAS - CASOS QUE DEBEN PASAR
    // ========================================

    // DataProvider para nombres válidos
    public static function nombresValidos(): array
    {
        return [
            'minimo 2' => ['Hi'],
            'medio' => ['Higiene'],
            'maximo 50' => [str_repeat('a', 50)],
            'con acentos' => ['José María'],
        ];
    }

    #[DataProvider('nombresValidos')]
    public function testSetNombreConValorValidoNoLanza(string $nombre): void
    {
        $this->sut->setNombre($nombre);
        $this->sut->check();
        $this->assertSame($nombre, $this->sut->getNombre());
    }

    // DataProvider para status válidos
    public static function statusValidos(): array
    {
        return [
            'activo' => [1],
            'inactivo' => [0],
        ];
    }

    #[DataProvider('statusValidos')]
    public function testSetStatusConValorValidoNoLanza(int $status): void
    {
        $this->sut->setStatus($status);
        $this->sut->check();
        $this->assertSame($status, $this->sut->getStatus());
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
        $this->sut->setNombre($nombre);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta nombres inválidos sin validación: '$nombre'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para status inválidos
    public static function statusInvalidos(): array
    {
        return [
            'negativo' => [-1],
            'muy grande' => [999],
            'decimal' => [1.5],
            'string' => ['abc'],
            'boolean' => [true],
        ];
    }

    #[DataProvider('statusInvalidos')]
    public function testSetStatusConValorInvalidoLanza($status): void
    {
        $this->sut->setStatus($status);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta status inválidos sin validación: " . var_export($status, true));
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // ========================================
    // PRUEBAS DE MARCA MÍNIMA
    // ========================================

    // DataProvider para marcas mínimas válidas
    public static function marcasMinimasValidas(): array
    {
        return [
            'solo nombre' => [
                'nombre' => 'Marca Test'
            ],
            'con status activo' => [
                'nombre' => 'Marca Activa',
                'status' => 1
            ],
            'con status inactivo' => [
                'nombre' => 'Marca Inactiva',
                'status' => 0
            ],
        ];
    }

    #[DataProvider('marcasMinimasValidas')]
    public function testMarcaMinimaValidaNoLanza(string $nombre, int $status = null): void
    {
        $this->sut->setNombre($nombre);
        if ($status !== null) {
            $this->sut->setStatus($status);
        }
        
        $this->sut->check(); // No debe lanzar excepción
        
        $this->assertSame($nombre, $this->sut->getNombre());
        if ($status !== null) {
            $this->assertSame($status, $this->sut->getStatus());
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
        $this->sut->setNombre($nombre);
        
        try {
            $this->sut->check();
            $this->assertSame($nombre, $this->sut->getNombre());
        } catch (\Exception $e) {
            $this->assertStringContainsString('nombre', $e->getMessage());
        }
    }

    // DataProvider para casos maliciosos - usando trait
    #[DataProvider('casosMaliciosos')]
    public function testCasosMaliciosos(string $valor): void
    {
        $this->sut->setNombre($valor);
        
        try {
            $this->sut->check();
            $this->assertSame($valor, $this->sut->getNombre());
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
            $this->sut->setNombre($valor);
        } else {
            // DEFECTO: Float e Integer se convierten a string sin validación
            // El sistema debería rechazar estos tipos, pero los acepta
            $this->sut->setNombre($valor);
            
            try {
                $this->sut->check();
                $this->fail("DEFECTO: El sistema acepta tipos incorrectos como " . gettype($valor) . " sin validación");
            } catch (\Exception $e) {
                $this->assertStringContainsString('Errores de validación', $e->getMessage());
            }
        }
    }

    // ========================================
    // PRUEBAS DE MÚLTIPLES ERRORES
    // ========================================

    public function testMultiplesErroresAgrupaMensajes(): void
    {
        // Múltiples campos inválidos
        $this->sut->setNombre('A'); // Inválido
        $this->sut->setStatus(99); // Inválido

        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta múltiples datos inválidos sin validación");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // ========================================
    // PRUEBAS DE SETTERS INVÁLIDOS
    // ========================================

    public function testSetterNombreInvalidoNoAsigna(): void
    {
        $this->sut->setNombre('A'); // Inválido
        $errores = $this->sut->getErrores();
        $this->assertArrayHasKey('nombre', $errores);
        $this->assertNull($this->sut->getNombre());
    }

    public function testSetterStatusInvalidoNoAsigna(): void
    {
        $this->sut->setStatus(99); // Inválido
        $errores = $this->sut->getErrores();
        $this->assertArrayHasKey('status', $errores);
        $this->assertNull($this->sut->getStatus());
    }

    // ========================================
    // PRUEBAS DE CASOS DE USO REALES
    // ========================================

    public function testMarcaCompletaValidaNoLanza(): void
    {
        $this->sut->setNombre('Marca Premium');
        $this->sut->setStatus(1);

        $this->sut->check(); // No debe lanzar excepción

        $this->assertSame('Marca Premium', $this->sut->getNombre());
        $this->assertSame(1, $this->sut->getStatus());
    }

    public function testMarcaInactivaValidaNoLanza(): void
    {
        $this->sut->setNombre('Marca Descontinuada');
        $this->sut->setStatus(0);

        $this->sut->check();

        $this->assertSame('Marca Descontinuada', $this->sut->getNombre());
        $this->assertSame(0, $this->sut->getStatus());
    }

    public function testActualizacionMarca(): void
    {
        // Simular actualización de marca existente
        $this->sut->setNombre('Marca Actualizada');
        $this->sut->setStatus(1);

        $this->sut->check();

        $this->assertSame('Marca Actualizada', $this->sut->getNombre());
        $this->assertSame(1, $this->sut->getStatus());
    }

    public function testMarcaConNombreLargo(): void
    {
        $nombreLargo = str_repeat('a', 50); // Máximo permitido
        $this->sut->setNombre($nombreLargo);
        $this->sut->setStatus(1);

        $this->sut->check();

        $this->assertSame($nombreLargo, $this->sut->getNombre());
        $this->assertSame(1, $this->sut->getStatus());
    }

    public function testMarcaConCaracteresEspeciales(): void
    {
        $nombreConAcentos = 'José María';
        $this->sut->setNombre($nombreConAcentos);
        $this->sut->setStatus(1);

        $this->sut->check();

        $this->assertSame($nombreConAcentos, $this->sut->getNombre());
        $this->assertSame(1, $this->sut->getStatus());
    }

    // ========================================
    // PRUEBAS DE VALIDACIÓN DE ERRORES
    // ========================================

    public function testErroresSeAcumulanCorrectamente(): void
    {
        $this->sut->setNombre('A'); // Inválido
        $this->sut->setStatus(99); // Inválido

        $errores = $this->sut->getErrores();
        $this->assertArrayHasKey('nombre', $errores);
        $this->assertArrayHasKey('status', $errores);
        $this->assertCount(2, $errores);
    }

    public function testErroresSeLimpianConValoresValidos(): void
    {
        // Primero establecer valores inválidos
        $this->sut->setNombre('A');
        $this->sut->setStatus(99);
        
        $errores = $this->sut->getErrores();
        $this->assertCount(2, $errores);

        // Luego establecer valores válidos
        $this->sut->setNombre('Marca Válida');
        $this->sut->setStatus(1);

        $this->sut->check(); // No debe lanzar excepción
        $this->assertSame('Marca Válida', $this->sut->getNombre());
        $this->assertSame(1, $this->sut->getStatus());
    }
}