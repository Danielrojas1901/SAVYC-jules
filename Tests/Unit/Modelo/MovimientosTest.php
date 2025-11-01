<?php

use PHPUnit\Framework\TestCase;
use Modelo\Movimientos;
use Modelo\Conexion;
use PDO;
use Exception;

class MovimientosTest extends TestCase
{
    private $movimientos;
    private $pdoMock;
    private $stmtMock;

    protected function setUp(): void
    {
        // Crear un mock del objeto PDO
        $this->pdoMock = $this->createMock(PDO::class);

        // Crear un mock del objeto PDOStatement
        $this->stmtMock = $this->createMock(PDOStatement::class);

        // Mockear la clase Conexion para evitar la conexión real a la base de datos
        /*Se define un método `conectarBD` que devuelve nuestro mock de PDO
        $this->conexionMock = $this->getMockBuilder(Conexion::class)
                                ->disableOriginalConstructor()
                                ->getMock();

        // Configurar el mock de Conexion para que al llamar a `conectarBD`
        // devuelva nuestro mock de PDO
        $this->conexionMock->method('conectarBD')->willReturn(null);
        $this->conexionMock->conex = $this->pdoMock;

        // Instanciar Movimientos y sobrescribir el constructor para usar el mock
        $this->movimientos = new Movimientos();
        $this->movimientos->conex = $this->pdoMock;*/
    }

    // --- Tests para el método setDatos ---

    public function testSetDatosAceptaDatosValidos()
    {
        // Configurar el mock para el método validarDetalles que usa la BD
        $this->pdoMock->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetchAll')->willReturn([]);

        $datos = [
            'descripcion' => 'Descripción de prueba',
            'fecha' => '2025-09-16',
            'status' => 'manual',
            'detalles' => [
                ['cuenta' => 1, 'debe' => 100],
                ['cuenta' => 2, 'haber' => 100],
            ]
        ];

        $this->movimientos->setDatos($datos);

        $this->assertEquals('Descripción de prueba', $this->movimientos->getDescripcion());
        $this->assertEquals('2025-09-16', $this->movimientos->getFecha());
        $this->assertEquals('manual', $this->movimientos->getStatus());
        $this->assertCount(2, $this->movimientos->getDetalles());
    }

    public function testSetDatosRechazaStatusInvalido()
    {
        $datos = [
            'status' => 'invalido',
            'descripcion' => 'test',
            'fecha' => '2025-09-16',
        ];

        $this->movimientos->setDatos($datos);
        
        $this->expectException(Exception::class);
        $this->movimientos->check();
    }
}