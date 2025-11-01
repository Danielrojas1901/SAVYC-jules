<?php
declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Caja;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

/**
 * Stub que evita BD y expone getters de conveniencia
 * a partir de getData() del propio modelo.
 * NO modifica el modelo original.
 */
final class CajaStub extends Caja
{
    public function __construct() {}
    public function conectarBD()   {}
    public function desconectarBD(){}

    // Getters de conveniencia (leen del getData())
    public function getNombre()    { return $this->getData()['nombre']; }
    public function getCodDivisa() { return $this->getData()['cod_divisa']; }
    public function getSaldo()     { return $this->getData()['saldo']; }
    public function getStatus()    { return $this->getData()['status']; }
    public function getCodCaja()   { return $this->getData()['cod_caja']; }
}

#[Group('Caja'), Group('unit')]
final class CajaTest extends TestCase
{
    private CajaStub $sut;

    protected function setUp(): void
    {
        $this->sut = new CajaStub();
    }

    /** ---------------- Helpers ---------------- */

    /**
     * Ejecuta $act que debería lanzar Exception de validación
     * y afirma que el mensaje contenga TODAS las palabras indicadas
     * (usamos esto como sustituto de getErrores()).
     *
     * @param string[] $needles
     */
    private function expectValidationErrorMessageContains(array $needles, callable $act): void
    {
        try {
            $act();
            $this->fail('Debió lanzar por errores de validación');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
            $msg = $e->getMessage();
            foreach ($needles as $needle) {
                $this->assertStringContainsString($needle, $msg, "El mensaje no contiene '{$needle}'");
            }
        }
    }

    private function data(): array { return $this->sut->getData(); }

    /** ---------------- Estado inicial ---------------- */
    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getNombre());
        $this->assertNull($this->sut->getCodDivisa());
        $this->assertNull($this->sut->getSaldo());
        $this->assertNull($this->sut->getStatus());
        $this->assertNull($this->sut->getCodCaja());
    }

    /** ---------------- NOMBRE (min=1, max=50) ---------------- */
    public static function nombresValidos(): array
    {
        return [
            'min 1'  => ['A'],
            'medio'  => ['Caja Principal'],
            'max 50' => [str_repeat('x', 50)],
        ];
    }

    #[DataProvider('nombresValidos')]
    public function testNombreValidoNoLanza(string $nombre): void
    {
        $this->sut->setData(['nombre' => $nombre, 'status' => 1]);
        $this->sut->check();
        $this->assertSame($nombre, $this->sut->getNombre());
    }

    public static function nombresInvalidos(): array
    {
        return [
            'vacío'    => [''],
            '51 chars' => [str_repeat('x', 51)],
        ];
    }

    #[DataProvider('nombresInvalidos')]
    public function testNombreInvalidoLanzaYMensajeContieneCampo(string $nombre): void
    {
        $this->sut->setData(['nombre' => $nombre, 'status' => 1]);
        $this->expectValidationErrorMessageContains(['nombre'], fn() => $this->sut->check());
    }

    /** ---------------- COD_DIVISA (numérico) ---------------- */
    public static function codDivisaValidos(): array
    {
        return [
            '1'   => [1],
            '10'  => [10],
            '50'  => [50],
            'str' => ['12'],
        ];
    }

    #[DataProvider('codDivisaValidos')]
    public function testCodDivisaValidoNoLanza($cod): void
    {
        $this->sut->setData(['cod_divisa' => $cod, 'status' => 1]);
        $this->sut->check();
        $this->assertEquals($cod, $this->sut->getCodDivisa());
    }

    public static function codDivisaInvalidos(): array
    {
        return [
            'texto'    => ['abc'],
            'negativo' => [-1],
            'vacio'    => [''],
        ];
    }

    #[DataProvider('codDivisaInvalidos')]
    public function testCodDivisaInvalidoLanzaYMensajeContieneCampo($cod): void
    {
        $this->sut->setData(['cod_divisa' => $cod]);
        $this->expectValidationErrorMessageContains(['cod_divisa'], fn() => $this->sut->check());
    }

    /** ---------------- SALDO (decimal) ---------------- */
    public static function saldosValidos(): array
    {
        return [
            'cero'    => [0],
            'entero'  => [1500],
            'decimal' => [123.45],
            'str'     => ['99.99'],
        ];
    }

    #[DataProvider('saldosValidos')]
    public function testSaldoValidoNoLanza($saldo): void
    {
        $this->sut->setData(['saldo' => $saldo, 'status' => 1]);
        $this->sut->check();
        $this->assertEquals($saldo, $this->sut->getSaldo());
    }

    public static function saldosInvalidos(): array
    {
        return [
            'negativo' => [-0.01],
            'texto'    => ['monto'],
        ];
    }

    #[DataProvider('saldosInvalidos')]
    public function testSaldoInvalidoLanzaYMensajeContieneCampo($saldo): void
    {
        $this->sut->setData(['saldo' => $saldo]);
        $this->expectValidationErrorMessageContains(['saldo'], fn() => $this->sut->check());
    }

    /** ---------------- STATUS (0/1 negocio) ---------------- */
    public static function statusValidos(): array
    {
        return [
            'inactivo 0' => [0],
            'activo 1'   => [1],
        ];
    }

    #[DataProvider('statusValidos')]
    public function testStatusValidoNoLanza(int $status): void
    {
        $this->sut->setData(['status' => $status]);
        $this->sut->check();
        $this->assertSame($status, $this->sut->getStatus());
    }

    public static function statusInvalidos(): array
    {
        return [
            'texto'    => ['x'],
            'vacio'    => [''],
            'negativo' => [-1],
        ];
    }

    #[DataProvider('statusInvalidos')]
    public function testStatusInvalidoLanzaYMensajeContieneCampo($status): void
    {
        $this->sut->setData(['status' => $status]);
        $this->expectValidationErrorMessageContains(['status'], fn() => $this->sut->check());
    }

    /** ---------------- COD_CAJA (numérico) ---------------- */
    public static function codCajaValidos(): array
    {
        return [
            'min 1' => [1],
            '25'    => [25],
            '100'   => [100],
        ];
    }

    #[DataProvider('codCajaValidos')]
    public function testCodCajaValidoNoLanza(int $cod): void
    {
        $this->sut->setData(['cod_caja' => $cod, 'status' => 1]);
        $this->sut->check();
        $this->assertSame($cod, $this->sut->getCodCaja());
    }

    public static function codCajaInvalidos(): array
    {
        return [
            'texto'    => ['abc'],
            'negativo' => [-3],
        ];
    }

    #[DataProvider('codCajaInvalidos')]
    public function testCodCajaInvalidoLanzaYMensajeContieneCampo($cod): void
    {
        $this->sut->setData(['cod_caja' => $cod]);
        $this->expectValidationErrorMessageContains(['cod_caja'], fn() => $this->sut->check());
    }

    /** ---------------- Múltiples errores agrupados ---------------- */
    public function testMultiplesErroresAgrupadosLanzaYMensajeContieneCampos(): void
    {
        $this->sut->setData([
            'nombre'     => '',
            'cod_divisa' => 'abc',
            'saldo'      => 'monto',
            'status'     => 'x',
            'cod_caja'   => -5,
        ]);

        $this->expectValidationErrorMessageContains(
            ['nombre', 'cod_divisa', 'saldo', 'status', 'cod_caja'],
            fn() => $this->sut->check()
        );
    }

    /** ---------------- Claves desconocidas se ignoran ---------------- */
    public function testClavesDesconocidasSeIgnoran(): void
    {
        $this->sut->setData(['foo' => 'bar', 'bar' => 'baz']);
        $this->sut->check();

        $this->assertNull($this->sut->getNombre());
        $this->assertNull($this->sut->getCodDivisa());
        $this->assertNull($this->sut->getSaldo());
        $this->assertNull($this->sut->getStatus());
        $this->assertNull($this->sut->getCodCaja());
    }

    /** ---------------- Camino feliz completo ---------------- */
    public function testSetDataValidoCompletoNoLanza(): void
    {
        $this->sut->setData([
            'nombre'     => 'Caja Secundaria',
            'cod_divisa' => 1,
            'saldo'      => 250.75,
            'status'     => 1,
            'cod_caja'   => 10,
        ]);
        $this->sut->check();

        $this->assertSame('Caja Secundaria', $this->sut->getNombre());
        $this->assertEquals(1, $this->sut->getCodDivisa());
        $this->assertEquals(250.75, (float)$this->sut->getSaldo());
        $this->assertSame(1, $this->sut->getStatus());
        $this->assertSame(10, $this->sut->getCodCaja());
    }
}
