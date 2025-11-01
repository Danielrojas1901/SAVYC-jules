<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modelo\Compras;

class ComprasTest extends TestCase
{
    /**
     * @var Compras
     */
    protected $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new Compras();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->sut = null;
    }

    /**
     * @test
     * @group unit
     */
    public function setdatac_conDatosValidos_noDebeRetornarErrores()
    {
        // Arrange
        $datosValidos = [
            'cod_prov' => 1,
            'condicion' => 'contado',
            'subtotal' => 95.00,
            'total_general' => 110.20,
            'impuesto_total' => 15.20,
            'fecha' => '2025-05-10',
            'fecha_v' => '2025-06-10'
        ];

        // Act
        $this->sut->setdatac($datosValidos);
        $errores = $this->sut->getErrores();

        // Assert
        $this->assertIsArray($errores);
        $this->assertEmpty($errores, 'No se esperaban errores con datos válidos.');
        $this->assertEquals(110.20, $this->sut->gettotal(), 'El total no se estableció correctamente.');
    }

    /**
     * @test
     * @group unit
     */
    public function setdatac_conDatosInvalidos_debeRetornarErrores()
    {
        // Arrange
        $datosInvalidos = [
            'cod_prov' => 'abc', // Inválido
            'condicion' => 'contado',
            'subtotal' => 'abc', // Inválido
            'total_general' => 'xyz', // Inválido
            'impuesto_total' => 'def', // Inválido
            'fecha' => 'no-es-una-fecha', // Inválido
            'fecha_v' => 'otra-fecha-invalida' // Inválido
        ];

        // Act
        $this->sut->setdatac($datosInvalidos);
        $errores = $this->sut->getErrores();

        // Assert
        $this->assertIsArray($errores);
        $this->assertNotEmpty($errores, 'Se esperaban errores con datos inválidos.');
        $this->assertArrayHasKey('subtotal', $errores, 'Falta el error del subtotal.');
        $this->assertArrayHasKey('total', $errores, 'Falta el error del total.');
        $this->assertArrayHasKey('impuesto', $errores, 'Falta el error del impuesto.');
        $this->assertArrayHasKey('fecha', $errores, 'Falta el error de la fecha.');
        $this->assertArrayHasKey('fecha_v', $errores, 'Falta el error de la fecha de vencimiento.');
    }

    /**
     * @test
     * @group unit
     */
    public function setdatap_conDatosValidos_noDebeRetornarErrores()
    {
        // Arrange
        $datosValidos = [
            'tipo_pago' => 'transferencia',
            'montopagado' => 100.50,
            'cod_compra' => 1,
            'fecha' => '2025-05-10 10:00:00',
            'monto_pagar' => 100.50,
            'vuelto_data' => '',
            'pago' => [
                ['cod_tipo_pago' => 1, 'monto' => 100.50]
            ]
        ];

        // Act
        $this->sut->setdatap($datosValidos);
        $errores = $this->sut->getErrores();

        // Assert
        $this->assertIsArray($errores);
        $this->assertEmpty($errores, 'No se esperaban errores con datos de pago válidos.');
    }
}