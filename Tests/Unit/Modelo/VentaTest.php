<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modelo\Venta;
use PHPUnit\Framework\Attributes\DataProvider;

// Define la clase "Stub" para evitar la conexión a la base de datos real.
class VentaStub extends Venta
{
    public function __construct() {}
}

final class VentaTest extends TestCase
{
    private VentaStub $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new VentaStub();
    }

    //---------------------------------------------------------
    // Pruebas de casos exitosos
    //---------------------------------------------------------
    
    /** @test */
    public function setdatav_debeAsignarValoresCorrectosYNoTenerErroresConDatosValidos()
    {
        // 1. Arrange: Datos de venta válidos
        $data = [
            'total_general' => '100.50',
            'fecha_hora' => '2025-09-12 10:00:00',
            'fecha_v' => '2025-10-12',
            'subtotal' => '90.00',
            'impuesto' => '10.50',
            'condicion' => 'contado'
        ];

        // 2. Act: Establece los datos de la venta
        $this->sut->setdatav($data);
        $resultado = $this->sut->check();

        // 3. Assert: Verifica que la validación fue exitosa
        $this->assertTrue($resultado, 'La validación debe ser exitosa con datos válidos.');
        $this->assertEmpty($this->sut->getErrores(), 'No debe haber errores.');
    }

    //---------------------------------------------------------
    // Pruebas de casos de fallos (cada uno por separado)
    //---------------------------------------------------------

    public static function datosInvalidosDecimal(): array
    {
        return [
            'texto' => ['texto'],
            'vacio' => [''],
            'negativo' => ['-10.00'],
        ];
    }
    
    #[DataProvider('datosInvalidosDecimal')]
    public function testSetdatav_conTotalGeneralInvalido_debeTenerError(string $valor)
    {
        // Arrange
        $data = [
            'total_general' => $valor,
            'fecha_hora' => '2025-09-12 10:00:00',
            'subtotal' => '90.00',
            'impuesto' => '10.50',
            'condicion' => 'contado'
        ];

        // Act
        $this->sut->setdatav($data);
        $errores = $this->sut->getErrores();

        // Assert
        $this->assertArrayHasKey('total', $errores, 'Debe haber un error en el campo "total".');
    }

    /** @test */
    public function testSetdatav_conFechaHoraInvalida_debeTenerError()
    {
        // Arrange
        $data = [
            'total_general' => '100.50',
            'fecha_hora' => 'fecha invalida',
            'subtotal' => '90.00',
            'impuesto' => '10.50',
            'condicion' => 'contado'
        ];

        // Act
        $this->sut->setdatav($data);
        $errores = $this->sut->getErrores();

        // Assert
        $this->assertArrayHasKey('fecha', $errores, 'Debe haber un error en el campo "fecha".');
    }

    /** @test */
    public function testSetdatav_conFechaVencimientoInvalida_debeTenerError()
    {
        // Arrange
        $data = [
            'total_general' => '100.50',
            'fecha_hora' => '2025-09-12 10:00:00',
            'fecha_v' => '32/13/2025',
            'subtotal' => '90.00',
            'impuesto' => '10.50',
            'condicion' => 'credito'
        ];

        // Act
        $this->sut->setdatav($data);
        $errores = $this->sut->getErrores();

        // Assert
        $this->assertArrayHasKey('fecha_v', $errores, 'Debe haber un error en el campo "fecha_v".');
    }

    #[DataProvider('datosInvalidosDecimal')]
    public function testSetdatav_conSubtotalInvalido_debeTenerError(string $valor)
    {
        // Arrange
        $data = [
            'total_general' => '100.50',
            'fecha_hora' => '2025-09-12 10:00:00',
            'subtotal' => $valor,
            'impuesto' => '10.50',
            'condicion' => 'contado'
        ];

        // Act
        $this->sut->setdatav($data);
        $errores = $this->sut->getErrores();

        // Assert
        $this->assertArrayHasKey('subtotal', $errores, 'Debe haber un error en el campo "subtotal".');
    }
    
    #[DataProvider('datosInvalidosDecimal')]
    public function testSetdatav_conImpuestoInvalido_debeTenerError(string $valor)
    {
        // Arrange
        $data = [
            'total_general' => '100.50',
            'fecha_hora' => '2025-09-12 10:00:00',
            'subtotal' => '90.00',
            'impuesto' => $valor,
            'condicion' => 'contado'
        ];

        // Act
        $this->sut->setdatav($data);
        $errores = $this->sut->getErrores();

        // Assert
        $this->assertArrayHasKey('impuesto', $errores, 'Debe haber un error en el campo "impuesto".');
    }
    
}