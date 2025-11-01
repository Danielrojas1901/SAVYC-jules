<?php
declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Tpago;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Modelo\Traits\MaliciousDataProvidersTrait;

class TpagoStub extends Tpago
{
    public function __construct() {}
    public function conectarBD()   { /* no-op */ }
    public function desconectarBD(){ /* no-op */ }
}

#[Group('unit')]
final class TpagoTest extends TestCase
{
    use MaliciousDataProvidersTrait;
    
    private TpagoStub $sut;

    protected function setUp(): void
    {
        $this->sut = new TpagoStub();
    }

    // ========================================
    // PRUEBAS DE ESTADO INICIAL
    // ========================================
    
    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getmetodo());
        $this->assertNull($this->sut->getstatus());
        $this->assertNull($this->sut->getmoneda());
        $this->assertNull($this->sut->getmodalidad());
    }

    // ========================================
    // PRUEBAS VÁLIDAS - CASOS QUE DEBEN PASAR
    // ========================================

    // DataProvider para métodos de pago válidos
    public static function metodosValidos(): array
    {
        return [
            'minimo 1' => ['E'],
            'medio' => ['Efectivo'],
            'maximo 50' => [str_repeat('a', 50)],
            'con acentos' => ['Transferencia'],
            'con numeros' => ['Pago ABC'],
        ];
    }

    #[DataProvider('metodosValidos')]
    public function testSetMetodoConValorValidoNoLanza(string $metodo): void
    {
        $this->sut->setmetodo($metodo);
        $this->sut->check();
        $this->assertSame($metodo, $this->sut->getmetodo());
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
        $this->assertSame($status, $this->sut->getstatus());
    }

    // DataProvider para monedas válidas
    public static function monedasValidas(): array
    {
        return [
            'digital' => ['digital'],
            'efectivo' => ['efectivo'],
            'mixto' => ['mixto'],
        ];
    }

    #[DataProvider('monedasValidas')]
    public function testSetMonedaConValorValidoNoLanza(string $moneda): void
    {
        $this->sut->setmoneda($moneda);
        $this->sut->check();
        $this->assertSame($moneda, $this->sut->getmoneda());
    }

    // DataProvider para modalidades válidas
    public static function modalidadesValidas(): array
    {
        return [
            'inmediato' => ['inmediato'],
            'diferido' => ['diferido'],
            'parcial' => ['parcial'],
        ];
    }

    #[DataProvider('modalidadesValidas')]
    public function testSetModalidadConValorValidoNoLanza(string $modalidad): void
    {
        $this->sut->setmodalidad($modalidad);
        $this->sut->check();
        $this->assertSame($modalidad, $this->sut->getmodalidad());
    }

    // ========================================
    // PRUEBAS INVÁLIDAS - CASOS QUE DEBEN FALLAR
    // ========================================

    // DataProvider para métodos de pago inválidos
    public static function metodosInvalidos(): array
    {
        return [
            'vacio' => [''],
            'mas de 50' => [str_repeat('a', 51)],
        ];
    }

    #[DataProvider('metodosInvalidos')]
    public function testSetMetodoConValorInvalidoLanza(string $metodo): void
    {
        $this->sut->setmetodo($metodo);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta métodos de pago inválidos sin validación: '$metodo'");
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
            'null' => [null],
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
    // PRUEBAS DE TIPO DE PAGO MÍNIMO
    // ========================================

    // DataProvider para tipos de pago mínimos válidos
    public static function tiposPagoMinimosValidos(): array
    {
        return [
            'solo metodo' => [
                'metodo' => 'Efectivo'
            ],
            'con status' => [
                'metodo' => 'Transferencia',
                'status' => 1
            ],
            'con modalidad' => [
                'metodo' => 'Tarjeta',
                'status' => null,
                'modalidad' => 'inmediato',
                'moneda' => null
            ],
            'con moneda' => [
                'metodo' => 'Digital',
                'status' => null,
                'modalidad' => null,
                'moneda' => 'digital'
            ],
        ];
    }

    #[DataProvider('tiposPagoMinimosValidos')]
    public function testTipoPagoMinimoValidoNoLanza(string $metodo, int $status = null, string $modalidad = null, string $moneda = null): void
    {
        $this->sut->setmetodo($metodo);
        if ($status !== null) {
            $this->sut->setStatus($status);
        }
        if ($modalidad !== null) {
            $this->sut->setmodalidad($modalidad);
        }
        if ($moneda !== null) {
            $this->sut->setmoneda($moneda);
        }
        
        $this->sut->check(); // No debe lanzar excepción
        
        $this->assertSame($metodo, $this->sut->getmetodo());
        if ($status !== null) {
            $this->assertSame($status, $this->sut->getstatus());
        }
        if ($modalidad !== null) {
            $this->assertSame($modalidad, $this->sut->getmodalidad());
        }
        if ($moneda !== null) {
            $this->assertSame($moneda, $this->sut->getmoneda());
        }
    }

    // ========================================
    // PRUEBAS DE CÓDIGO DE MÉTODO
    // ========================================

    // DataProvider para códigos de método válidos
    public static function codigosMetodoValidos(): array
    {
        return [
            'entero positivo' => [123],
            'entero cero' => [0],
            'entero grande' => [999999],
        ];
    }

    #[DataProvider('codigosMetodoValidos')]
    public function testSetCodMetodoConValorValidoNoLanza(int $codigo): void
    {
        $this->sut->setCodMetodo($codigo);
        $this->sut->check();
        // No hay getter para cod_metodo, solo verificamos que no lance excepción
    }

    // DataProvider para códigos de método inválidos
    public static function codigosMetodoInvalidos(): array
    {
        return [
            'string' => ['abc'],
            'decimal' => [123.45],
            'negativo' => [-123],
        ];
    }

    #[DataProvider('codigosMetodoInvalidos')]
    public function testSetCodMetodoConValorInvalidoLanza($codigo): void
    {
        $this->sut->setCodMetodo($codigo);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta códigos de método inválidos sin validación: " . var_export($codigo, true));
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // ========================================
    // PRUEBAS EDGE CASES Y MALICIOSAS
    // ========================================

    // DataProvider para casos límite de métodos
    public static function casosLimiteMetodos(): array
    {
        return [
            'minimo exacto' => [str_repeat('a', 1)],
            'maximo exacto' => [str_repeat('a', 50)],
            'un caracter mas' => [str_repeat('a', 51)],
            'unicode' => ['áéíóú'],
            'emojis' => ['😀😀'],
            'caracteres especiales' => ['!@#$%^&*()'],
        ];
    }

    #[DataProvider('casosLimiteMetodos')]
    public function testCasosLimiteMetodos(string $metodo): void
    {
        $this->sut->setmetodo($metodo);
        
        try {
            $this->sut->check();
            $this->assertSame($metodo, $this->sut->getmetodo());
        } catch (\Exception $e) {
            $this->assertStringContainsString('Medio pago', $e->getMessage());
        }
    }

    // DataProvider para casos maliciosos - usando trait
    #[DataProvider('casosMaliciosos')]
    public function testCasosMaliciosos(string $valor): void
    {
        $this->sut->setmetodo($valor);
        
        try {
            $this->sut->check();
            $this->assertSame($valor, $this->sut->getmetodo());
            // Si llega aquí, el sistema aceptó datos maliciosos - esto puede ser un defecto de seguridad
            $this->fail("DEFECTO DE SEGURIDAD: El sistema acepta datos maliciosos sin validación: '$valor'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Medio pago', $e->getMessage());
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
            $this->sut->setmetodo($valor);
        } else {
            // DEFECTO: Float e Integer se convierten a string sin validación
            // El sistema debería rechazar estos tipos, pero los acepta
            $this->sut->setmetodo($valor);
            
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
        $this->sut->setmetodo(''); // Inválido
        $this->sut->setStatus(99); // Inválido
        $this->sut->setCodMetodo('abc'); // Inválido

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

    public function testSetterMetodoInvalidoNoAsigna(): void
    {
        $this->sut->setmetodo(''); // Inválido
        $errores = $this->sut->getErrores();
        $this->assertArrayHasKey('rol', $errores);
        $this->assertNull($this->sut->getmetodo());
    }

    public function testSetterStatusInvalidoNoAsigna(): void
    {
        $this->sut->setStatus(99); // Inválido
        $errores = $this->sut->getErrores();
        $this->assertArrayHasKey('status', $errores);
        $this->assertNull($this->sut->getstatus());
    }

    public function testSetterCodMetodoInvalidoNoAsigna(): void
    {
        $this->sut->setCodMetodo('abc'); // Inválido
        $errores = $this->sut->getErrores();
        $this->assertArrayHasKey('cod_metodo', $errores);
    }

    // ========================================
    // PRUEBAS DE CASOS DE USO REALES
    // ========================================

    public function testTipoPagoCompletoValidoNoLanza(): void
    {
        $this->sut->setmetodo('Transferencia Bancaria');
        $this->sut->setStatus(1);
        $this->sut->setmodalidad('inmediato');
        $this->sut->setmoneda('digital');

        $this->sut->check(); // No debe lanzar excepción

        $this->assertSame('Transferencia Bancaria', $this->sut->getmetodo());
        $this->assertSame(1, $this->sut->getstatus());
        $this->assertSame('inmediato', $this->sut->getmodalidad());
        $this->assertSame('digital', $this->sut->getmoneda());
    }

    public function testTipoPagoEfectivoValidoNoLanza(): void
    {
        $this->sut->setmetodo('Efectivo');
        $this->sut->setStatus(1);
        $this->sut->setmodalidad('inmediato');
        $this->sut->setmoneda('efectivo');

        $this->sut->check();

        $this->assertSame('Efectivo', $this->sut->getmetodo());
        $this->assertSame(1, $this->sut->getstatus());
        $this->assertSame('inmediato', $this->sut->getmodalidad());
        $this->assertSame('efectivo', $this->sut->getmoneda());
    }

    public function testTipoPagoInactivoValidoNoLanza(): void
    {
        $this->sut->setmetodo('Tarjeta de Crédito');
        $this->sut->setStatus(0);
        $this->sut->setmodalidad('diferido');
        $this->sut->setmoneda('digital');

        $this->sut->check();

        $this->assertSame('Tarjeta de Crédito', $this->sut->getmetodo());
        $this->assertSame(0, $this->sut->getstatus());
        $this->assertSame('diferido', $this->sut->getmodalidad());
        $this->assertSame('digital', $this->sut->getmoneda());
    }

    public function testActualizacionTipoPago(): void
    {
        // Simular actualización de tipo de pago existente
        $this->sut->setCodMetodo(123);
        $this->sut->setmetodo('Pago Móvil Actualizado');
        $this->sut->setStatus(1);
        $this->sut->setmodalidad('inmediato');
        $this->sut->setmoneda('digital');

        $this->sut->check();

        $this->assertSame('Pago Móvil Actualizado', $this->sut->getmetodo());
        $this->assertSame(1, $this->sut->getstatus());
        $this->assertSame('inmediato', $this->sut->getmodalidad());
        $this->assertSame('digital', $this->sut->getmoneda());
    }

    public function testTipoPagoConMetodoLargo(): void
    {
        $metodoLargo = str_repeat('a', 50); // Máximo permitido
        $this->sut->setmetodo($metodoLargo);
        $this->sut->setStatus(1);

        $this->sut->check();

        $this->assertSame($metodoLargo, $this->sut->getmetodo());
        $this->assertSame(1, $this->sut->getstatus());
    }

    public function testTipoPagoConCaracteresEspeciales(): void
    {
        $metodoConAcentos = 'Pago Móvil';
        $this->sut->setmetodo($metodoConAcentos);
        $this->sut->setStatus(1);

        $this->sut->check();

        $this->assertSame($metodoConAcentos, $this->sut->getmetodo());
        $this->assertSame(1, $this->sut->getstatus());
    }

    // ========================================
    // PRUEBAS DE VALIDACIÓN DE ERRORES
    // ========================================

    public function testErroresSeAcumulanCorrectamente(): void
    {
        $this->sut->setmetodo(''); // Inválido
        $this->sut->setStatus(99); // Inválido
        $this->sut->setCodMetodo('abc'); // Inválido

        $errores = $this->sut->getErrores();
        $this->assertArrayHasKey('rol', $errores);
        $this->assertArrayHasKey('status', $errores);
        $this->assertArrayHasKey('cod_metodo', $errores);
        $this->assertCount(3, $errores);
    }

    public function testErroresSeLimpianConValoresValidos(): void
    {
        // Primero establecer valores inválidos
        $this->sut->setmetodo('');
        $this->sut->setStatus(99);
        
        $errores = $this->sut->getErrores();
        $this->assertCount(2, $errores);

        // Luego establecer valores válidos
        $this->sut->setmetodo('Método Válido');
        $this->sut->setStatus(1);

        $this->sut->check(); // No debe lanzar excepción
        $this->assertSame('Método Válido', $this->sut->getmetodo());
        $this->assertSame(1, $this->sut->getstatus());
    }

    // ========================================
    // PRUEBAS DE CAMPOS SIN VALIDACIÓN
    // ========================================

    public function testMonedaSinValidacionAceptaCualquierValor(): void
    {
        // DEFECTO DOCUMENTADO: El campo moneda no tiene validación
        $valoresIncorrectos = ['ABC', '123.45', 'texto', 'cualquier cosa'];
        
        foreach ($valoresIncorrectos as $valor) {
            $this->sut->setmoneda($valor);
            $this->assertSame($valor, $this->sut->getmoneda());
        }
        
        $this->fail("DEFECTO: El campo moneda no tiene validación y acepta cualquier valor");
    }

    public function testModalidadSinValidacionAceptaCualquierValor(): void
    {
        // DEFECTO DOCUMENTADO: El campo modalidad no tiene validación
        $valoresIncorrectos = ['ABC', '123.45', 'texto', 'cualquier cosa'];
        
        foreach ($valoresIncorrectos as $valor) {
            $this->sut->setmodalidad($valor);
            $this->assertSame($valor, $this->sut->getmodalidad());
        }
        
        $this->fail("DEFECTO: El campo modalidad no tiene validación y acepta cualquier valor");
    }
}