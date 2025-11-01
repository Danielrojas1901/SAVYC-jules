<?php
declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\General;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Modelo\Traits\MaliciousDataProvidersTrait;

class GeneralStub extends General
{
    public function __construct() {}
    public function conectarBD()   { /* no-op */ }
    public function desconectarBD(){ /* no-op */ }
}

#[Group('unit')]
final class GeneralTest extends TestCase
{
    use MaliciousDataProvidersTrait;
    
    private GeneralStub $sut;

    protected function setUp(): void
    {
        $this->sut = new GeneralStub();
    }

    // ========================================
    // PRUEBAS DE ESTADO INICIAL
    // ========================================
    
    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getRif());
        $this->assertNull($this->sut->getNom());
        $this->assertNull($this->sut->getDir());
        $this->assertNull($this->sut->gettlf());
        $this->assertNull($this->sut->getemail());
        $this->assertNull($this->sut->getDescri());
        $this->assertNull($this->sut->getlogo());
    }

    // ========================================
    // PRUEBAS VÁLIDAS - CASOS QUE DEBEN PASAR
    // ========================================

    // DataProvider para RIFs válidos
    public static function rifsValidos(): array
    {
        return [
            'minimo 7' => ['J-1234567'],
            'medio' => ['V-12345678'],
            'maximo 15' => ['J-1234567890123'],
            'con guiones' => ['E-12345678-9'],
            'con puntos' => ['J.12345678.9'],
        ];
    }

    #[DataProvider('rifsValidos')]
    public function testSetRifConValorValidoNoLanza(string $rif): void
    {
        $this->sut->setRif($rif);
        $this->sut->check();
        $this->assertSame($rif, $this->sut->getRif());
    }

    // DataProvider para nombres válidos
    public static function nombresValidos(): array
    {
        return [
            'minimo 2' => ['Hi'],
            'medio' => ['Empresa ABC'],
            'maximo 50' => [str_repeat('a', 50)],
            'con numeros' => ['Empresa 123'],
            'con espacios' => ['Empresa de Prueba'],
        ];
    }

    #[DataProvider('nombresValidos')]
    public function testSetNomConValorValidoNoLanza(string $nombre): void
    {
        $this->sut->setNom($nombre);
        $this->sut->check();
        $this->assertSame($nombre, $this->sut->getNom());
    }

    // DataProvider para códigos válidos
    public static function codigosValidos(): array
    {
        return [
            'minimo 1' => ['1'],
            'medio' => ['123'],
            'maximo 5' => ['12345'],
        ];
    }

    #[DataProvider('codigosValidos')]
    public function testSetcodConValorValidoNoLanza(string $codigo): void
    {
        $this->sut->setcod($codigo);
        $this->sut->check();
        // No hay getter público para cod, solo verificamos que no lance excepción
        $this->assertTrue(true);
    }

    // DataProvider para direcciones válidas (opcional)
    public static function direccionesValidas(): array
    {
        return [
            'minimo 5' => ['Calle 1'],
            'medio' => ['Av. Principal #123, Sector Centro'],
            'maximo 100' => [str_repeat('a', 100)],
            'vacio' => [''], // Dirección es opcional
        ];
    }

    #[DataProvider('direccionesValidas')]
    public function testSetDirConValorValidoNoLanza(string $direccion): void
    {
        $this->sut->setDir($direccion);
        $this->sut->check();
        $this->assertSame($direccion === '' ? null : $direccion, $this->sut->getDir());
    }

    // DataProvider para teléfonos válidos (opcional)
    public static function telefonosValidos(): array
    {
        return [
            'formato basico' => ['0412-1234567'],
            'con parentesis' => ['(0412) 123-4567'],
            'solo numeros' => ['04121234567'],
            'vacio' => [''], // Teléfono es opcional
        ];
    }

    #[DataProvider('telefonosValidos')]
    public function testSettlfConValorValidoNoLanza(string $telefono): void
    {
        $this->sut->settlf($telefono);
        $this->sut->check();
        $this->assertSame($telefono === '' ? null : $telefono, $this->sut->gettlf());
    }

    // DataProvider para emails válidos (opcional)
    public static function emailsValidos(): array
    {
        return [
            'email basico' => ['test@example.com'],
            'email complejo' => ['usuario.nombre+tag@dominio.co.ve'],
            'vacio' => [''], // Email es opcional
        ];
    }

    #[DataProvider('emailsValidos')]
    public function testSetemailConValorValidoNoLanza(string $email): void
    {
        $this->sut->setemail($email);
        $this->sut->check();
        $this->assertSame($email === '' ? null : $email, $this->sut->getemail());
    }

    // DataProvider para descripciones válidas (opcional)
    public static function descripcionesValidas(): array
    {
        return [
            'minimo 5' => ['Desc.'],
            'medio' => ['Empresa dedicada a la venta de productos'],
            'maximo 100' => [str_repeat('a', 100)],
            'vacio' => [''], // Descripción es opcional
        ];
    }

    #[DataProvider('descripcionesValidas')]
    public function testSetDescriConValorValidoNoLanza(string $descripcion): void
    {
        $this->sut->setDescri($descripcion);
        $this->sut->check();
        $this->assertSame($descripcion === '' ? null : $descripcion, $this->sut->getDescri());
    }

    // ========================================
    // PRUEBAS INVÁLIDAS - CASOS QUE DEBEN FALLAR
    // ========================================

    // DataProvider para RIFs inválidos
    public static function rifsInvalidos(): array
    {
        return [
            'vacio' => [''],
            'solo 6 chars' => ['J-1234'],
            'mas de 15' => [str_repeat('J', 16)],
        ];
    }

    #[DataProvider('rifsInvalidos')]
    public function testSetRifConValorInvalidoLanza(string $rif): void
    {
        $this->sut->setRif($rif);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

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
    public function testSetNomConValorInvalidoLanza(string $nombre): void
    {
        $this->sut->setNom($nombre);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // DataProvider para códigos inválidos
    public static function codigosInvalidos(): array
    {
        return [
            'vacio' => [''],
            'no numerico' => ['abc'],
            'mas de 5' => ['123456'],
        ];
    }

    #[DataProvider('codigosInvalidos')]
    public function testSetcodConValorInvalidoLanza(string $codigo): void
    {
        $this->sut->setcod($codigo);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // DataProvider para direcciones inválidas
    public static function direccionesInvalidas(): array
    {
        return [
            'solo 4 chars' => ['Casa'],
            'mas de 100' => [str_repeat('a', 101)],
        ];
    }

    #[DataProvider('direccionesInvalidas')]
    public function testSetDirConValorInvalidoLanza(string $direccion): void
    {
        $this->sut->setDir($direccion);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // DataProvider para teléfonos inválidos
    public static function telefonosInvalidos(): array
    {
        return [
            'con letras' => ['0412-abc-123'],
            'caracteres raros' => ['0412@123#456'],
        ];
    }

    #[DataProvider('telefonosInvalidos')]
    public function testSettlfConValorInvalidoLanza(string $telefono): void
    {
        $this->sut->settlf($telefono);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // DataProvider para emails inválidos
    public static function emailsInvalidos(): array
    {
        return [
            'sin @' => ['testexample.com'],
            'sin dominio' => ['test@'],
            'formato invalido' => ['@example.com'],
        ];
    }

    #[DataProvider('emailsInvalidos')]
    public function testSetemailConValorInvalidoLanza(string $email): void
    {
        $this->sut->setemail($email);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // DataProvider para descripciones inválidas
    public static function descripcionesInvalidas(): array
    {
        return [
            'solo 4 chars' => ['Desc'],
            'mas de 100' => [str_repeat('a', 101)],
        ];
    }

    #[DataProvider('descripcionesInvalidas')]
    public function testSetDescriConValorInvalidoLanza(string $descripcion): void
    {
        $this->sut->setDescri($descripcion);
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // ========================================
    // PRUEBAS DE EMPRESA MÍNIMA
    // ========================================

    // DataProvider para empresas mínimas válidas
    public static function empresasMinimasValidas(): array
    {
        return [
            'solo rif y nombre' => [
                'rif' => 'J-12345678',
                'nombre' => 'Empresa Mínima'
            ],
            'con codigo' => [
                'rif' => 'V-87654321',
                'nombre' => 'Empresa Test',
                'codigo' => '123'
            ],
        ];
    }

    #[DataProvider('empresasMinimasValidas')]
    public function testEmpresaMinimaValidaNoLanza(string $rif, string $nombre, string $codigo = null): void
    {
        $this->sut->setRif($rif);
        $this->sut->setNom($nombre);
        if ($codigo !== null) {
            $this->sut->setcod($codigo);
        }
        
        $this->sut->check(); // No debe lanzar excepción
        
        $this->assertSame($rif, $this->sut->getRif());
        $this->assertSame($nombre, $this->sut->getNom());
        // Los demás campos pueden ser null
        $this->assertNull($this->sut->getDir());
        $this->assertNull($this->sut->gettlf());
        $this->assertNull($this->sut->getemail());
        $this->assertNull($this->sut->getDescri());
    }

    // ========================================
    // PRUEBAS EDGE CASES Y MALICIOSAS
    // ========================================

    // DataProvider para casos límite
    public static function casosLimite(): array
    {
        return [
            'rif minimo exacto' => [str_repeat('J', 7)],
            'rif maximo exacto' => [str_repeat('J', 15)],
            'nombre minimo exacto' => [str_repeat('a', 2)],
            'nombre maximo exacto' => [str_repeat('a', 50)],
            'direccion minimo exacto' => [str_repeat('a', 5)],
            'direccion maximo exacto' => [str_repeat('a', 100)],
            'descripcion minimo exacto' => [str_repeat('a', 5)],
            'descripcion maximo exacto' => [str_repeat('a', 100)],
            'unicode' => ['áéíóú'],
            'emojis' => ['😀😀'],
            'caracteres especiales' => ['!@#$%^&*()'],
        ];
    }

    #[DataProvider('casosLimite')]
    public function testCasosLimite($valor): void
    {
        $this->sut->setRif($valor);
        
        try {
            $this->sut->check();
            $this->assertSame($valor, $this->sut->getRif());
        } catch (\Exception $e) {
            $this->assertStringContainsString('rif', $e->getMessage());
        }
    }

    // DataProvider para casos maliciosos - usando trait

    #[DataProvider('casosMaliciosos')]
    public function testCasosMaliciosos($valor): void
    {
        $this->sut->setRif($valor);
        
        try {
            $this->sut->check();
            $this->assertSame($valor, $this->sut->getRif());
        } catch (\Exception $e) {
            $this->assertStringContainsString('rif', $e->getMessage());
        }
    }

    // DataProvider para tipos de datos problemáticos - usando trait

    #[DataProvider('tiposDatosProblematicos')]
    public function testTiposDatosProblematicos($valor): void
    {
        // DEFECTO DOCUMENTADO: El sistema no valida tipos de datos
        if (is_array($valor) || is_object($valor) || is_resource($valor) || is_callable($valor)) {
            $this->expectException(\TypeError::class);
            $this->sut->setRif($valor);
        } else {
            // DEFECTO: Float e Integer se convierten a string y luego se validan
            $this->sut->setRif($valor);
            
            try {
                $this->sut->check();
                $this->fail("DEFECTO: El sistema acepta tipos incorrectos como " . gettype($valor) . " sin validación");
            } catch (\Exception $e) {
                // El sistema convierte float/integer a string y luego valida
                $this->assertStringContainsString('rif', $e->getMessage());
            }
        }
    }

    // ========================================
    // PRUEBAS DE MÚLTIPLES ERRORES
    // ========================================

    public function testMultiplesErroresAgrupaMensajes(): void
    {
        // Múltiples campos inválidos
        $this->sut->setRif(''); // Inválido
        $this->sut->setNom('A'); // Inválido
        $this->sut->setcod('abc'); // Inválido
        $this->sut->setDir('Casa'); // Inválido (menos de 5 chars)
        $this->sut->settlf('abc-123'); // Inválido
        $this->sut->setemail('invalid-email'); // Inválido
        $this->sut->setDescri('Desc'); // Inválido (menos de 5 chars)

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // ========================================
    // PRUEBAS DE CAMPOS OPCIONALES
    // ========================================

    public function testCamposOpcionalesPuedenSerVacios(): void
    {
        $this->sut->setRif('J-12345678');
        $this->sut->setNom('Empresa ABC');
        $this->sut->setcod('123');
        // Campos opcionales vacíos
        $this->sut->setDir('');
        $this->sut->settlf('');
        $this->sut->setemail('');
        $this->sut->setDescri('');

        $this->sut->check(); // No debe lanzar excepción

        $this->assertSame('J-12345678', $this->sut->getRif());
        $this->assertSame('Empresa ABC', $this->sut->getNom());
        $this->assertNull($this->sut->getDir());
        $this->assertNull($this->sut->gettlf());
        $this->assertNull($this->sut->getemail());
        $this->assertNull($this->sut->getDescri());
    }

    // ========================================
    // PRUEBAS DE LOGO
    // ========================================

    public function testSetlogoAsignaValor(): void
    {
        $logo = 'logo_empresa.png';
        $this->sut->setlogo($logo);
        $this->assertSame($logo, $this->sut->getlogo());
    }

    // ========================================
    // PRUEBAS DE CASO FELIZ
    // ========================================

    public function testTodosLosCamposValidosNoLanza(): void
    {
        $this->sut->setRif('J-12345678');
        $this->sut->setNom('Empresa ABC');
        $this->sut->setcod('123');
        $this->sut->setDir('Av. Principal #123');
        $this->sut->settlf('0412-1234567');
        $this->sut->setemail('contacto@empresa.com');
        $this->sut->setDescri('Empresa dedicada a la venta de productos');
        $this->sut->setlogo('logo.png');

        $this->sut->check(); // No debe lanzar excepción

        // Verificar que todos los valores se asignaron correctamente
        $this->assertSame('J-12345678', $this->sut->getRif());
        $this->assertSame('Empresa ABC', $this->sut->getNom());
        $this->assertSame('Av. Principal #123', $this->sut->getDir());
        $this->assertSame('0412-1234567', $this->sut->gettlf());
        $this->assertSame('contacto@empresa.com', $this->sut->getemail());
        $this->assertSame('Empresa dedicada a la venta de productos', $this->sut->getDescri());
        $this->assertSame('logo.png', $this->sut->getlogo());
    }

    // ========================================
    // PRUEBAS DE SETTERS INVÁLIDOS
    // ========================================

    public function testSetterRifInvalidoNoAsigna(): void
    {
        $this->sut->setRif('A'); // Inválido
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    public function testSetterNomInvalidoNoAsigna(): void
    {
        $this->sut->setNom('A'); // Inválido
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    public function testSetterCodInvalidoNoAsigna(): void
    {
        $this->sut->setcod('abc'); // Inválido
        
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // ========================================
    // PRUEBAS DE CASOS DE USO REALES
    // ========================================

    public function testRegistroEmpresaCompleta(): void
    {
        // Simular registro de empresa completa
        $this->sut->setRif('J-12345678901');
        $this->sut->setNom('Mi Empresa S.A.');
        $this->sut->setcod('001');
        $this->sut->setDir('Av. Principal #123, Sector Centro, Caracas');
        $this->sut->settlf('(0212) 123-4567');
        $this->sut->setemail('info@miempresa.com');
        $this->sut->setDescri('Empresa dedicada al comercio y servicios');
        $this->sut->setlogo('mi_empresa_logo.png');
        
        $this->sut->check();
        
        $this->assertSame('J-12345678901', $this->sut->getRif());
        $this->assertSame('Mi Empresa S.A.', $this->sut->getNom());
        $this->assertSame('Av. Principal #123, Sector Centro, Caracas', $this->sut->getDir());
        $this->assertSame('(0212) 123-4567', $this->sut->gettlf());
        $this->assertSame('info@miempresa.com', $this->sut->getemail());
        $this->assertSame('Empresa dedicada al comercio y servicios', $this->sut->getDescri());
        $this->assertSame('mi_empresa_logo.png', $this->sut->getlogo());
    }

    public function testRegistroEmpresaMinima(): void
    {
        // Simular registro de empresa mínima
        $this->sut->setRif('V-87654321');
        $this->sut->setNom('Empresa Básica');
        
        $this->sut->check();
        
        $this->assertSame('V-87654321', $this->sut->getRif());
        $this->assertSame('Empresa Básica', $this->sut->getNom());
        $this->assertNull($this->sut->getDir());
        $this->assertNull($this->sut->gettlf());
        $this->assertNull($this->sut->getemail());
        $this->assertNull($this->sut->getDescri());
    }

    public function testActualizacionEmpresa(): void
    {
        // Simular actualización de empresa existente
        $this->sut->setRif('J-987654321');
        $this->sut->setNom('Empresa Actualizada');
        $this->sut->setcod('002');
        $this->sut->setDir('Nueva Dirección #456');
        $this->sut->settlf('0414-9876543');
        $this->sut->setemail('nuevo@empresa.com');
        $this->sut->setDescri('Descripción actualizada');
        $this->sut->setlogo('nuevo_logo.png');
        
        $this->sut->check();
        
        $this->assertSame('J-987654321', $this->sut->getRif());
        $this->assertSame('Empresa Actualizada', $this->sut->getNom());
        $this->assertSame('Nueva Dirección #456', $this->sut->getDir());
        $this->assertSame('0414-9876543', $this->sut->gettlf());
        $this->assertSame('nuevo@empresa.com', $this->sut->getemail());
        $this->assertSame('Descripción actualizada', $this->sut->getDescri());
        $this->assertSame('nuevo_logo.png', $this->sut->getlogo());
    }
}