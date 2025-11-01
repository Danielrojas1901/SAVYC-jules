<?php
declare(strict_types=1);

namespace Tests\Integration\Modelo;

use Modelo\CuentasPend;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('CuentasPendIntegration'), Group('integration')]
final class CuentasPendCrudTest extends TestCase
{
    private CuentasPend $sut;

    protected function setUp(): void
    {
        $this->sut = new CuentasPend();
    }

    // -----------------------------------------------------
    // DATA PROVIDERS
    // -----------------------------------------------------

    public static function boxMethodsProvider(): array
    {
        return [
            'box_cobrar' => ['method' => 'getboxcobrar', 'key' => 'total_cobrar'],
            'box_pagar'  => ['method' => 'getboxpagar',  'key' => 'total_pagar'],
        ];
    }

    // -----------------------------------------------------
    // TESTS DE LOS MÉTODOS BOX (COBRAR / PAGAR)
    // -----------------------------------------------------

    #[DataProvider('boxMethodsProvider')]
    public function testBoxMethodsSiempreRetornanArray(string $method, string $key): void
    {
        $res = $this->sut->{$method}();

        $this->assertIsArray($res, "El método $method debe retornar un array");

        if (!empty($res)) {
            $this->assertArrayHasKey($key, $res[0], "El resultado debe contener la clave '$key'");
            $valor = $res[0][$key];

            // DEFECTO: cuando no hay registros, MySQL puede devolver NULL
            if ($valor === null) {
                $this->fail("DEFECTO: $method devuelve NULL en lugar de 0. Sugiere usar COALESCE(SUM(...),0).");
            }

            $this->assertIsNumeric($valor, "El valor de '$key' debe ser numérico");
        } else {
            // En caso de BD vacía, debe retornar al menos un array vacío, no false
            $this->assertSame([], $res, "El método $method debe retornar [] cuando no hay datos.");
        }
    }

    // -----------------------------------------------------
    // TESTS: LISTADO POR CLIENTE (mostrar)
    // -----------------------------------------------------

    public function testMostrarClienteRetornaArrayYColumnasEsperadas(): void
    {
        $res = $this->sut->getmostrarcliente();

        $this->assertIsArray($res, 'El método getmostrarcliente debe devolver un array.');
        if (!empty($res)) {
            $row = $res[0];
            $expected = ['cod_cliente', 'cliente', 'cedula_rif', 'total_ventas', 'total', 'total_cobrado', 'total_pendiente'];
            foreach ($expected as $col) {
                $this->assertArrayHasKey($col, $row, "Falta la columna '$col' en el resultado.");
            }

            // DEFECTO: si total_pendiente < 0 → sobrepago no truncado
            if (isset($row['total_pendiente']) && $row['total_pendiente'] < 0) {
                $this->fail('DEFECTO: total_pendiente negativo. Sugerencia: usar GREATEST(total - pagos, 0).');
            }
        }
    }

    // -----------------------------------------------------
    // TESTS: DETALLE DE CLIENTE (mostrar2)
    // -----------------------------------------------------

    public function testMostrar2RetornaDatosDelCliente(): void
    {
        // Se elige un cliente cualquiera con datos en BD (si no hay, no falla el test)
        $cliente = 1;
        $res = $this->sut->getmostrar2($cliente);

        $this->assertIsArray($res, 'getmostrar2() debe devolver un array.');
        if (!empty($res)) {
            $row = $res[0];
            $cols = ['cod_venta', 'total', 'monto_pagado', 'saldo_pendiente', 'estado', 'nombre', 'cedula_rif'];
            foreach ($cols as $col) {
                $this->assertArrayHasKey($col, $row, "Falta columna '$col' en mostrar2().");
            }

            // DEFECTO: saldo negativo por sobrepago
            if ((float)$row['saldo_pendiente'] < 0) {
                $this->fail('DEFECTO: saldo_pendiente negativo en mostrar2(). Debe truncarse a 0.');
            }

            // Validar que el estado sea uno de los esperados
            $this->assertContains(
                $row['estado'],
                ['Pendiente', 'Pago parcial', 'Pagado', 'Vencido'],
                'Estado no válido retornado por mostrar2().'
            );
        }
    }

    // -----------------------------------------------------
    // TESTS: MOSTRAR3 (GLOBAL)
    // -----------------------------------------------------

    public function testMostrar3RetornaArrayDeVentas(): void
    {
        $res = $this->sut->getmostrar3();

        $this->assertIsArray($res, 'El método getmostrar3 debe devolver un array.');
        if (!empty($res)) {
            $row = $res[0];
            $keys = ['cod_venta', 'total', 'fecha', 'estado', 'nombre'];
            foreach ($keys as $k) {
                $this->assertArrayHasKey($k, $row, "Falta la columna '$k' en getmostrar3().");
            }

            // Comprobación de consistencia: status 1 o 2 únicamente
            $this->assertNotSame('Pagado', $row['estado'], 'DEFECTO: Se incluyeron ventas con estado Pagado (status=3).');
        }
    }

    // -----------------------------------------------------
    // TESTS: CUENTAS POR PAGAR (vista)
    // -----------------------------------------------------

    public function testMostrarCuentasPagarRetornaArray(): void
    {
        $res = $this->sut->getmostrarCuentasPagar();
        $this->assertIsArray($res, 'getmostrarCuentasPagar() debe devolver un array.');
        if (!empty($res)) {
            $this->assertIsArray($res[0]);
        }
    }

    public function testCuentasPagarPorFechaRetornaArray(): void
    {
        $res = $this->sut->cuentaspagarporfecha('2025-01-01', '2025-12-31');
        $this->assertIsArray($res, 'cuentaspagarporfecha() debe devolver un array.');

        if (!empty($res) && isset($res[0]['fecha'])) {
            $fecha = $res[0]['fecha'];
            if ($fecha < '2025-01-01' || $fecha > '2025-12-31') {
                $this->fail('DEFECTO: cuentaspagarporfecha() no está filtrando correctamente por rango de fechas.');
            }
        }
    }
}
