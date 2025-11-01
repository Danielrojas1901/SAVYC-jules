<?php
declare(strict_types=1);
namespace Tests\Unit\Modelo;
use Modelo\CatalogoCuentas;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

class CatalogoCuentasStub extends CatalogoCuentas
{
    public function __construct() {}
    public function conectarBD()   {  }  
    public function desconectarBD(){  }  
}

#[Group('CatalogoCuentas'), Group('unit')]
final class CatalogoCuentasTest extends TestCase
{
    private CatalogoCuentasStub $sut;
    protected function setUp(): void
    {
        $this->sut = new CatalogoCuentasStub();
    }

    // ===================== DATA PROVIDERS =====================

    public static function providerNombreValido(): array
    {
        return [
            'min 2'        => ['AB'],
            'alfaNum'      => ['Cuenta123'],
            'espacios'     => ['Caja General'],
            'max 50'       => [str_repeat('x', 50)],
        ];
    }

    public static function providerNombreInvalido(): array
    {
        return [
            'vacío'       => [''],
            'muy corto'   => ['A'],
            'muy largo'   => [str_repeat('x', 51)],
        ];
    }

    public static function providerNaturalezas(): array
    {
        return [
            'naturaleza'        => [['naturaleza' => 'deudora'],   'deudora'],
            'naturalezaHidden'  => [['naturalezaHidden' => 'acreedora'], 'acreedora'],
            'naturalezae'       => [['naturalezae' => 'deudora'],  'deudora'],
            'naturalezah'       => [['naturalezah' => 'acreedora'],'acreedora'],
        ];
    }

    // ===================== TESTS ESTADO INICIAL =====================

    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getCodigoContable());
        $this->assertNull($this->sut->getNombre());
        $this->assertNull($this->sut->getNaturaleza());
        $this->assertNull($this->sut->getCuentaPadreid());
        $this->assertNull($this->sut->getNivel());
        $this->assertNull($this->sut->getSaldo());
    }
    // ===================== TESTS setDatos(): nombre ==================

    #[DataProvider('providerNombreValido')]
    public function testSetDatosNombreValido_NoAcumulaErrores(string $nombre): void
    {
        $this->sut->setDatos(['nombreCuenta' => $nombre]);
        $this->sut->check(); // no debe lanzar
        $this->assertSame($nombre, $this->sut->getNombre());
    }

    #[DataProvider('providerNombreInvalido')]
    public function testSetDatosNombreInvalido_AcumulaErrores(string $nombre): void
    {
        $this->sut->setDatos(['nombreCuenta' => $nombre]);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // ===================== TESTS setDatos(): naturaleza =====================

    #[DataProvider('providerNaturalezas')]
    public function testSetDatosNaturalezaAceptaCualquieraDeLasLlaves(array $input, string $esperado): void
    {
        $this->sut->setDatos($input);
        $this->sut->check();
        $this->assertSame($esperado, $this->sut->getNaturaleza());
    }

    public function testSetDatosNaturalezaInvalida_AcumulaErrores(): void
    {
        $this->sut->setDatos(['naturaleza' => 'X']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // ===================== TESTS setDatos(): nivel y cuentaPadre =====================

    public function testSetDatosNivelYCuentaPadreValidos_NoLanza(): void
    {
        $this->sut->setDatos(['nivel' => 3, 'cuentaPadre' => 7]);
        $this->sut->check();
        $this->assertSame(3, $this->sut->getNivel());
        $this->assertSame(7, $this->sut->getCuentaPadreid());
    }

    public function testSetDatosNivelInvalido_AcumulaErrores(): void
    {
        $this->sut->setDatos(['nivel' => '']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    public function testSetDatosCuentaPadreInvalida_AcumulaErrores(): void
    {
        $this->sut->setDatos(['cuentaPadre' => 'abc']); // debería fallar validarCodigoSelect
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // ===================== TESTS setDatos(): saldo =====================

    public function testSetDatosSaldoDecimalValido_NoLanza(): void
    {
        $this->sut->setDatos(['saldo' => '1234.56']);
        $this->sut->check();
        $this->assertSame('1234.56', $this->sut->getSaldo());
    }

    public function testSetDatosSaldoInvalido_AcumulaErrores(): void
    {
        $this->sut->setDatos(['saldo' => '12.345.67']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // ===================== TESTS setDatos(): codigoContable =====================

    public function testSetDatosCodigoContableVacio_AcumulaErrores(): void
    {
        // Requiere que corrijas el bug en el modelo:
        // if (!empty($datos['codigoContable'])) { ... }
        $this->sut->setDatos(['codigoContable' => '']);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
        $this->fail('ERROR: no acumuló error para códigoContable vacío');
    }

    public function testSetDatosCodigoContableValido_NoLanza(): void
    {
        $this->sut->setDatos(['codigoContable' => '1.1.01']);
        $this->sut->check();
        $this->assertSame('1.1.01', $this->sut->getCodigoContable());
    }

    // ===================== TESTS combinados (caso feliz) =====================

    public function testCasoFeliz_CombinaTodosLosCamposValidos(): void
    {
        $data = [
            'codigoContable' => '1.1.01',
            'nombreCuenta'   => 'Caja General',
            'naturaleza'     => 'deudora',
            'nivel'          => 4,
            'cuentaPadre'    => 10,
            'saldo'          => '0.00',
        ];
        $this->sut->setDatos($data);
        $this->sut->check();

        $this->assertSame('1.1.01', $this->sut->getCodigoContable());
        $this->assertSame('Caja General', $this->sut->getNombre());
        $this->assertSame('deudora', $this->sut->getNaturaleza());
        $this->assertSame(4, $this->sut->getNivel());
        $this->assertSame(10, $this->sut->getCuentaPadreid());
        $this->assertSame('0.00', $this->sut->getSaldo());
    }

    // ===================== TESTS acumulación de errores =====================

    public function testAcumulaMultiplesErroresEnCheck(): void
    {
        $this->sut->setDatos([
            'nombreCuenta'   => '',          // inválido
            'naturaleza'     => 'X',         // inválido
            'nivel'          => '',          // inválido
            'cuentaPadre'    => 'abc',       // inválido
            'saldo'          => '12.3.4',    // inválido
            'codigoContable' => '',          // inválido (con bug corregido)
        ]);

        try {
            $this->sut->check();
            $this->fail('Debió lanzar excepción de validación.');
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            $this->assertStringContainsString('Errores de validación', $msg);
            $this->assertGreaterThanOrEqual(3, substr_count($msg, '|')); // varios errores concatenados
        }
    }
}
