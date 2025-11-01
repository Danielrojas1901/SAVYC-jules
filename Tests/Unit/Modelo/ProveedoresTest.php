<?php
declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Proveedores;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Modelo\Traits\MaliciousDataProvidersTrait;

class ProveedoresStub extends Proveedores
{
    public function __construct() {}
    public function conectarBD()   { /* no-op */ }
    public function desconectarBD(){ /* no-op */ }
}

#[Group('unit')]
final class ProveedoresTest extends TestCase
{
    use MaliciousDataProvidersTrait;
    
    private ProveedoresStub $sut;

    protected function setUp(): void
    {
        $this->sut = new ProveedoresStub();
    }

    // ========================================
    // PRUEBAS DE ESTADO INICIAL
    // ========================================
    
    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getRazon_Social());
        $this->assertNull($this->sut->getRif());
        $this->assertNull($this->sut->get_Email());
        $this->assertNull($this->sut->getDireccion());
        $this->assertNull($this->sut->getStatus());
        $this->assertNull($this->sut->gettCod());
    }

    // ========================================
    // PRUEBAS VÁLIDAS - CASOS QUE DEBEN PASAR
    // ========================================

    // DataProvider para razones sociales válidas
    public static function razonesSocialesValidas(): array
    {
        return [
            'minimo 2' => ['Hi'],
            'medio' => ['Empresa ABC'],
            'maximo 50' => [str_repeat('a', 50)],
            'con acentos' => ['José María'],
            'con numeros' => ['Empresa 123'],
        ];
    }

    #[DataProvider('razonesSocialesValidas')]
    public function testSetRazonSocialConValorValidoNoLanza(string $razonSocial): void
    {
        $this->sut->setRazon_Social($razonSocial);
        $this->sut->check();
        $this->assertSame($razonSocial, $this->sut->getRazon_Social());
    }

    // DataProvider para RIFs válidos
    public static function rifsValidos(): array
    {
        return [
            'minimo 7' => ['J123456'],
            'medio' => ['V1234567890'],
            'maximo 15' => [str_repeat('A', 15)],
            'con guiones' => ['J-1234567'],
            'con puntos' => ['V.12345678.9'],
        ];
    }

    #[DataProvider('rifsValidos')]
    public function testSetRifConValorValidoNoLanza(string $rif): void
    {
        $this->sut->setRif($rif);
        $this->sut->check();
        $this->assertSame($rif, $this->sut->getRif());
    }

    // DataProvider para emails válidos
    public static function emailsValidos(): array
    {
        return [
            'simple' => ['a@b.co'],
            'con punto' => ['usuario.nombre@dominio.com'],
            'con mas' => ['usuario+tag@dominio.com'],
            'complejo' => ['usuario.nombre+tag@dominio.co.ve'],
        ];
    }

    #[DataProvider('emailsValidos')]
    public function testSetEmailConValorValidoNoLanza(string $email): void
    {
        $this->sut->setemail($email);
        $this->sut->check();
        $this->assertSame($email, $this->sut->get_Email());
    }

    // DataProvider para direcciones válidas
    public static function direccionesValidas(): array
    {
        return [
            'minimo 5' => ['abcde'],
            'media' => ['calle 123 sector centro'],
            'maximo 250' => [str_repeat('x', 250)],
            'con numeros' => ['Av. Principal #123'],
        ];
    }

    #[DataProvider('direccionesValidas')]
    public function testSetDireccionConValorValidoNoLanza(string $direccion): void
    {
        $this->sut->setDireccion($direccion);
        $this->sut->check();
        $this->assertSame($direccion, $this->sut->getDireccion());
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

    // DataProvider para razones sociales inválidas
    public static function razonesSocialesInvalidas(): array
    {
        return [
            'vacio' => [''],
            'solo 1 char' => ['A'],
            'mas de 50' => [str_repeat('a', 51)],
        ];
    }

    #[DataProvider('razonesSocialesInvalidas')]
    public function testSetRazonSocialConValorInvalidoLanza(string $razonSocial): void
    {
        $this->sut->setRazon_Social($razonSocial);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta razones sociales inválidas sin validación: '$razonSocial'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para RIFs inválidos
    public static function rifsInvalidos(): array
    {
        return [
            'vacio' => [''],
            'menos de 7' => ['ABC123'],
            'mas de 15' => [str_repeat('1', 16)],
            'caracteres no validos' => ['ABC_1234'],
        ];
    }

    #[DataProvider('rifsInvalidos')]
    public function testSetRifConValorInvalidoLanza(string $rif): void
    {
        $this->sut->setRif($rif);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta RIFs inválidos sin validación: '$rif'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para emails inválidos
    public static function emailsInvalidos(): array
    {
        return [
            'sin arroba' => ['usuario.dominio.com'],
            'sin dominio' => ['a@'],
            'sin usuario' => ['@dominio.com'],
        ];
    }

    #[DataProvider('emailsInvalidos')]
    public function testSetEmailConValorInvalidoLanza(string $email): void
    {
        $this->sut->setemail($email);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta emails inválidos sin validación: '$email'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // DataProvider para direcciones inválidas
    public static function direccionesInvalidas(): array
    {
        return [
            'menos de 5' => ['abcd'],
            'mas de 250' => [str_repeat('y', 251)],
        ];
    }

    #[DataProvider('direccionesInvalidas')]
    public function testSetDireccionConValorInvalidoLanza(string $direccion): void
    {
        $this->sut->setDireccion($direccion);
        
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta direcciones inválidas sin validación: '$direccion'");
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
    // PRUEBAS DE PROVEEDOR MÍNIMO
    // ========================================

    // DataProvider para proveedores mínimos válidos
    public static function proveedoresMinimosValidos(): array
    {
        return [
            'solo razon social' => [
                'razonSocial' => 'Proveedor Test'
            ],
            'con RIF' => [
                'razonSocial' => 'Proveedor ABC',
                'rif' => 'J1234567'
            ],
            'con status' => [
                'razonSocial' => 'Proveedor XYZ',
                'rif' => null,
                'status' => 1
            ],
        ];
    }

    #[DataProvider('proveedoresMinimosValidos')]
    public function testProveedorMinimoValidoNoLanza(string $razonSocial, ?string $rif = null, ?int $status = null): void
    {
        $this->sut->setRazon_Social($razonSocial);
        if ($rif !== null) {
            $this->sut->setRif($rif);
        }
        if ($status !== null) {
            $this->sut->setStatus($status);
        }
        
        $this->sut->check(); // No debe lanzar excepción
        
        $this->assertSame($razonSocial, $this->sut->getRazon_Social());
        if ($rif !== null) {
            $this->assertSame($rif, $this->sut->getRif());
        }
        if ($status !== null) {
            $this->assertSame($status, $this->sut->getStatus());
        }
    }

    // ========================================
    // PRUEBAS DE CAMPOS OPCIONALES
    // ========================================

    public function testEmailVacioSeGuardaComoNull(): void
    {
        $this->sut->setemail('');
        $this->sut->check();
        $this->assertNull($this->sut->get_Email());
    }

    public function testDireccionVaciaSeGuardaComoNull(): void
    {
        $this->sut->setDireccion('');
        $this->sut->check();
        $this->assertNull($this->sut->getDireccion());
    }

    // ========================================
    // PRUEBAS EDGE CASES Y MALICIOSAS
    // ========================================

    // DataProvider para casos límite de razones sociales
    public static function casosLimiteRazonesSociales(): array
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

    #[DataProvider('casosLimiteRazonesSociales')]
    public function testCasosLimiteRazonesSociales(string $razonSocial): void
    {
        $this->sut->setRazon_Social($razonSocial);
        
        try {
            $this->sut->check();
            $this->assertSame($razonSocial, $this->sut->getRazon_Social());
        } catch (\Exception $e) {
            $this->assertStringContainsString('razonSocial', $e->getMessage());
        }
    }

    // DataProvider para casos maliciosos - usando trait
    #[DataProvider('casosMaliciosos')]
    public function testCasosMaliciosos(string $valor): void
    {
        $this->sut->setRazon_Social($valor);
        
        try {
            $this->sut->check();
            $this->assertSame($valor, $this->sut->getRazon_Social());
            // Si llega aquí, el sistema aceptó datos maliciosos - esto puede ser un defecto de seguridad
            $this->fail("DEFECTO DE SEGURIDAD: El sistema acepta datos maliciosos sin validación: '$valor'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('razonSocial', $e->getMessage());
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
            $this->sut->setRazon_Social($valor);
        } else {
            // DEFECTO: Float e Integer se convierten a string sin validación
            // El sistema debería rechazar estos tipos, pero los acepta
            $this->sut->setRazon_Social($valor);
            
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
        $this->sut->setRazon_Social('A'); // Inválido
        $this->sut->setRif('ABC'); // Inválido
        $this->sut->setemail('invalid-email'); // Inválido
        $this->sut->setDireccion('abcd'); // Inválido
        $this->sut->setStatus(99); // Inválido

        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta múltiples datos inválidos sin validación");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    // ========================================
    // PRUEBAS DE CÓDIGO
    // ========================================

    public function testSetCodYGettCod(): void
    {
        $this->sut->setCod(123);
        $this->assertSame(123, $this->sut->gettCod());
    }

    // ========================================
    // PRUEBAS DE CASOS DE USO REALES
    // ========================================

    public function testProveedorCompletoValidoNoLanza(): void
    {
        $this->sut->setRazon_Social('Empresa Proveedora S.A.');
        $this->sut->setRif('J-12345678-9');
        $this->sut->setemail('contacto@empresa.com');
        $this->sut->setDireccion('Av. Principal #123, Sector Centro');
        $this->sut->setStatus(1);

        $this->sut->check(); // No debe lanzar excepción

        $this->assertSame('Empresa Proveedora S.A.', $this->sut->getRazon_Social());
        $this->assertSame('J-12345678-9', $this->sut->getRif());
        $this->assertSame('contacto@empresa.com', $this->sut->get_Email());
        $this->assertSame('Av. Principal #123, Sector Centro', $this->sut->getDireccion());
        $this->assertSame(1, $this->sut->getStatus());
    }

    public function testProveedorMinimoConSoloRazonSocial(): void
    {
        $this->sut->setRazon_Social('Proveedor Básico');

        $this->sut->check();

        $this->assertSame('Proveedor Básico', $this->sut->getRazon_Social());
        $this->assertNull($this->sut->getRif());
        $this->assertNull($this->sut->get_Email());
        $this->assertNull($this->sut->getDireccion());
        $this->assertNull($this->sut->getStatus());
    }

    public function testProveedorInactivoValidoNoLanza(): void
    {
        $this->sut->setRazon_Social('Proveedor Descontinuado');
        $this->sut->setRif('V-87654321');
        $this->sut->setStatus(0);

        $this->sut->check();

        $this->assertSame('Proveedor Descontinuado', $this->sut->getRazon_Social());
        $this->assertSame('V-87654321', $this->sut->getRif());
        $this->assertSame(0, $this->sut->getStatus());
    }

    public function testActualizacionProveedor(): void
    {
        // Simular actualización de proveedor existente
        $this->sut->setCod(123);
        $this->sut->setRazon_Social('Proveedor Actualizado');
        $this->sut->setRif('J-98765432-1');
        $this->sut->setemail('nuevo@proveedor.com');
        $this->sut->setDireccion('Nueva Dirección #456');
        $this->sut->setStatus(1);

        $this->sut->check();

        $this->assertSame(123, $this->sut->gettCod());
        $this->assertSame('Proveedor Actualizado', $this->sut->getRazon_Social());
        $this->assertSame('J-98765432-1', $this->sut->getRif());
        $this->assertSame('nuevo@proveedor.com', $this->sut->get_Email());
        $this->assertSame('Nueva Dirección #456', $this->sut->getDireccion());
        $this->assertSame(1, $this->sut->getStatus());
    }

    public function testProveedorConEmailYDireccionOpcionales(): void
    {
        $this->sut->setRazon_Social('Proveedor Sin Contacto');
        $this->sut->setRif('E-11111111-1');
        $this->sut->setemail(''); // Vacío
        $this->sut->setDireccion(''); // Vacío
        $this->sut->setStatus(1);

        $this->sut->check();

        $this->assertSame('Proveedor Sin Contacto', $this->sut->getRazon_Social());
        $this->assertSame('E-11111111-1', $this->sut->getRif());
        $this->assertNull($this->sut->get_Email());
        $this->assertNull($this->sut->getDireccion());
        $this->assertSame(1, $this->sut->getStatus());
    }

    // ========================================
    // PRUEBAS DE VALIDACIÓN DE ERRORES
    // ========================================

    public function testErroresSeAcumulanCorrectamente(): void
    {
        $this->sut->setRazon_Social('A'); // Inválido
        $this->sut->setRif('ABC'); // Inválido
        $this->sut->setemail('invalid-email'); // Inválido
        $this->sut->setStatus(99); // Inválido

        $errores = $this->sut->getErrores();
        $this->assertArrayHasKey('razonSocial', $errores);
        $this->assertArrayHasKey('rif', $errores);
        $this->assertArrayHasKey('email', $errores);
        $this->assertArrayHasKey('status', $errores);
        $this->assertCount(4, $errores);
    }

    public function testErroresSeLimpianConValoresValidos(): void
    {
        // Primero establecer valores inválidos
        $this->sut->setRazon_Social('A');
        $this->sut->setRif('ABC');
        
        $errores = $this->sut->getErrores();
        $this->assertCount(2, $errores);

        // Luego establecer valores válidos
        $this->sut->setRazon_Social('Proveedor Válido');
        $this->sut->setRif('J1234567');

        $this->sut->check(); // No debe lanzar excepción
        $this->assertSame('Proveedor Válido', $this->sut->getRazon_Social());
        $this->assertSame('J1234567', $this->sut->getRif());
    }
}