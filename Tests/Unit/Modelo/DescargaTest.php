<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modelo\Descarga;
use Modelo\Conexion;
use PHPUnit\Framework\Attributes\DataProvider;
use PDO;
use PDOStatement;
use Exception;

// Define la clase "Stub" para evitar la conexión a la base de datos real en las pruebas.
class DescargaStub extends Descarga
{
    
    // Sobrescribe el constructor para que no haga nada y evite la conexión.
    public function __construct() {}

    // Sobrescribe el método conectarBD para que no haga nada.
    public function conectarBD() {}
    
    // Sobrescribe el método desconectarBD para que no haga nada.
    public function desconectarBD() {}

    // Este método permite inyectar el "mock" de la conexión PDO.
    public function setConex(PDO $conex)
    {
        $this->conex = $conex;
    }

    // Método para exponer los errores en la prueba

}

final class DescargaTest extends TestCase
{
    private DescargaStub $sut;

    protected function setUp(): void
    {
        // Instancia el "stub" para evitar la conexión real a la base de datos.
        $this->sut = new DescargaStub();
    }
    
    //---------------------------------------------------------
    // Pruebas de Métodos de Validación (set... y check)
    //---------------------------------------------------------

    public static function descripcionesValidas(): array {
        return [
            'minimo 5'      => ['Razon'],
            'medio'         => ['Descripcion de prueba'],
            'maximo 45'     => [str_repeat('a', 45)],
        ];
    }

    public static function descripcionesInvalidas(): array {
        return [
            'vacio'         => [''],
            'solo espacios' => ['     '],
            'menos de 5'    => ['desc'],
            'mas de 45'     => [str_repeat('a', 46)],
        ];
    }

    public static function costosValidos(): array {
        return [
            'entero'        => ['100'],
            'decimal'       => ['50.50'],
            'costo minimo'  => ['1'],
        ];
    }

    public static function costosInvalidos(): array {
        return [
            'vacio'         => [''],
            'solo espacios' => ['    '],
            'no numerico'   => ['abc'],
            'negativo'      => ['-10'],
        ];
    }
    
    public static function cantidadesValidas(): array {
        return [
            'entero'        => ['5'],
            'decimal'       => ['2.5'],
            'cantidad minima' => ['1'],
        ];
    }

    public static function cantidadesInvalidas(): array {
        return [
            'vacio'         => [''],
            'solo espacios' => ['    '],
            'no numerico'   => ['xyz'],
            'negativo'      => ['-5'],
        ];
    }

    // Prueba para un caso de éxito con todos los campos
    /** @test */
    public function setData_conDatosValidos_noDebeTenerErrores()
    {
        // 1. Arrange (Preparar)
        $datosValidos = [
            'descripcion' => 'Descripción de prueba',
            'costo' => '100.50',
            'cantidad' => '50.25',
        ];

        // 2. Act (Actuar)
        $this->sut->setdescripcion($datosValidos['descripcion']);
        $this->sut->setcosto($datosValidos['costo']);
        $this->sut->setcantidad($datosValidos['cantidad']);

        // 3. Assert (Verificar)
        $this->assertEmpty($this->sut->getErrores());
    }

    // Pruebas usando DataProvider para descripciones
    #[DataProvider('descripcionesValidas')]
    public function testSetDescripcionConDatosValidos(string $descripcion): void
    {
        $this->sut->setdescripcion($descripcion);
        $this->assertEmpty($this->sut->getErrores(), "La descripción '{$descripcion}' debería ser válida.");
    }

    #[DataProvider('descripcionesInvalidas')]
    public function testSetDescripcionConDatosInvalidos(string $descripcion): void
    {
        $this->sut->setdescripcion($descripcion);
        $this->assertArrayHasKey('descripcion', $this->sut->getErrores());
    }

    // Pruebas usando DataProvider para costos
    #[DataProvider('costosValidos')]
    public function testSetCostoConDatosValidos(string $costo): void
    {
        $this->sut->setcosto($costo);
        $this->assertEmpty($this->sut->getErrores(), "El costo '{$costo}' debería ser válido.");
    }

    #[DataProvider('costosInvalidos')]
    public function testSetCostoConDatosInvalidos(string $costo): void
    {
        $this->sut->setcosto($costo);
        $this->assertArrayHasKey('costo', $this->sut->getErrores());
    }

    // Pruebas usando DataProvider para cantidades
    #[DataProvider('cantidadesValidas')]
    public function testSetCantidadConDatosValidos(string $cantidad): void
    {
        $this->sut->setcantidad($cantidad);
        $this->assertEmpty($this->sut->getErrores(), "La cantidad '{$cantidad}' debería ser válida.");
    }

    #[DataProvider('cantidadesInvalidas')]
    public function testSetCantidadConDatosInvalidos(string $cantidad): void
    {
        $this->sut->setcantidad($cantidad);
        $this->assertArrayHasKey('cantidad', $this->sut->getErrores());
    }

    /** @test */
    public function check_conErrores_debeLanzarExcepcion()
    {
        // 1. Arrange
        $datosInvalidos = [
            'descripcion' => 'Inv', // descripción inválida
        ];

        $this->sut->setdescripcion($datosInvalidos['descripcion']);
        
        // 2. Act & Assert
        // Se espera que la excepción se lance al llamar a check()
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }
}