<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modelo\Roles;
use PHPUnit\Framework\Attributes\DataProvider;
use Exception;
use PDO;

// Define la clase "Stub" para evitar la conexión a la base de datos real en las pruebas.
class RolesStub extends Roles
{
    // Sobrescribe el constructor para que no haga nada y evite la conexión.
    public function __construct() {}

    // Sobrescribe el método conectarBD para que no haga nada.
    public function conectarBD() {}
    
    // Sobrescribe el método desconectarBD para que no haga nada.
    public function desconectarBD() {}

    // Este método permite exponer los errores en la prueba.
    
}

final class RolesTest extends TestCase
{
    private RolesStub $sut;

    protected function setUp(): void
    {
        // Instancia el "stub" para evitar la conexión real a la base de datos.
        $this->sut = new RolesStub();
    }
    
    //---------------------------------------------------------
    // Pruebas de Métodos de Validación (set... y check)
    //---------------------------------------------------------

    public static function rolesValidos(): array {
        return [
            'minimo 1'      => ['A'],
            'medio'         => ['Administrador'],
            'maximo 50'     => [str_repeat('a', 50)],
        ];
    }

    public static function rolesInvalidos(): array {
        return [
            'vacio'         => [''],
            'solo espacios' => ['     '],
            'mas de 50'     => [str_repeat('a', 51)],
        ];
    }

    // Prueba de caso de éxito con todos los campos válidos
    /** @test */
    public function setData_conDatosValidos_noDebeTenerErrores()
    {
        // 1. Arrange (Preparar)
        $datosValidos = [
            'rol' => 'Supervisor',
        ];

        // 2. Act (Actuar)
        $this->sut->setRol($datosValidos['rol']);

        // 3. Assert (Verificar)
        $this->assertEmpty($this->sut->getErrores());
    }

    // Pruebas usando DataProvider para roles
    #[DataProvider('rolesValidos')]
    public function testSetRolConDatosValidos(string $rol): void
    {
        $this->sut->setRol($rol);
        $this->assertEmpty($this->sut->getErrores(), "El rol '{$rol}' debería ser válido.");
    }

    #[DataProvider('rolesInvalidos')]
    public function testSetRolConDatosInvalidos(string $rol): void
    {
        $this->sut->setRol($rol);
        $this->assertArrayHasKey('rol', $this->sut->getErrores());
    }

    /** @test */
    public function check_conErrores_debeLanzarExcepcion()
    {
        // 1. Arrange
        $datosInvalidos = [
            'rol' => '', // Rol inválido (vacío)
        ];

        $this->sut->setRol($datosInvalidos['rol']);
        
        // 2. Act & Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }
}