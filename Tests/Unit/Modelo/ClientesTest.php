<?php

/**
 * Comandos de ejecución de PHPUnit
 *
 * vendor/bin/phpunit                  - Ejecuta todas las pruebas en el proyecto.
 * vendor/bin/phpunit --filter "ClientesTest" - Ejecuta todas las pruebas de una clase específica.
 * vendor/bin/phpunit --filter "ClientesTest::test_registrar" - Ejecuta una sola prueba.
 * vendor/bin/phpunit --testdox        - Muestra el resultado de las pruebas de forma más legible.
 * vendor/bin/phpunit --group "integration" - Ejecuta solo las pruebas marcadas con @group integration.
 * vendor/bin/phpunit --coverage-html  - Genera un informe de cobertura de código en HTML.
 *
 */

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Modelo\Clientes;
use Modelo\Conexion;
use PHPUnit\Framework\Attributes\DataProvider;
use PDO;
use PDOStatement;



// Define la clase "Stub" para evitar la conexión a la base de datos real en las pruebas.
class ClientesStub extends Clientes
{
    
    // Sobrescribe el método conectarBD para que no haga nada.
    public function conectarBD() {}
    
    // Sobrescribe el método desconectarBD para que no haga nada.
    public function desconectarBD() {}

    // Este método permite inyectar el "mock" de la conexión PDO.
    public function setConex(PDO $conex)
    {
        $this->conex = $conex;
    }
}

final class ClientesTest extends TestCase
{
    private ClientesStub $sut;

    protected function setUp(): void
    {
        // Instancia el "stub" para evitar la conexión real a la base de datos.
        $this->sut = new ClientesStub();
    }
    
    //---------------------------------------------------------
    // Pruebas de Métodos de Validación (setData y check)
    //---------------------------------------------------------

    public static function nombresValidos(): array {
        return [
            'minimo 2'      => ['Jo'],
            'medio'         => ['Juan'],
            'maximo 50'     => [str_repeat('a', 50)],
        ];
    }

    public static function nombresInvalidos(): array {
        return [
            'vacio'         => [''],
            'solo espacios' => ['   '],
            'uno solo'      => ['J'],
            'mas de 50'     => [str_repeat('a', 51)],
        ];
    }

    public static function apellidosValidos(): array {
        return [
            'minimo 2'      => ['Lo'],
            'medio'         => ['Lopez'],
            'maximo 50'     => [str_repeat('b', 50)],
        ];
    }

    public static function apellidosInvalidos(): array {
        return [
            'vacio'         => [''],
            'solo espacios' => ['   '],
            'uno solo'      => ['L'],
            'mas de 50'     => [str_repeat('b', 51)],
        ];
    }

    public static function cedulaValida(): array {
        return[
            'minimo 6'      => ['123456'],
            'medio'         => ['1234567'],
            'maximo 12'     => [str_repeat('1', 12)],
        ];
    }

    public static function cedulaInvalida(): array {
        return [
            'vacio'         => [''],
            'solo espacios' => ['   '],
            'menos de 6'    => ['12345'],
            'mas de 12'     => [str_repeat('1', 13)],
            'no numerico'   => ['abc123'],
        ];
    }

    public static function telefonosValidos(): array {
        return [
            'formato 0412-1234567' => ['0412-1234567'],
            'formato 0212-7654321' => ['0212-7654321'],
            'formato 04141234567'  => ['04141234567'],
            'formato 02127654321'  => ['02127654321'],
        ];
    }

    public static function telefonosInvalidos(): array {
        return [
            'vacio'             => [''],
            'solo espacios'     => ['   '],
            'formato incorrecto'=> ['1234567'],
            'letras'           => ['0412-ABCDEF'],
            'demasiado corto'   => ['0412-1234'],
            'demasiado largo'   => ['0412-1234567890'],
        ];
    }

    public static function emailsValidos(): array {
        return[
            'formato basico'        => ['formatobasico19@hotmil.com']
        ];
    }

    public static function emailsInvalidos(): array {
        return [
            'vacio'             => [''],
            'solo espacios'     => ['   '],
            'sin arroba'        => ['email.com'],
            'sin dominio'       => ['email@'],
            'formato incorrecto'=> ['email@com'],
            'con espacios'      => ['email @domain.com'],
        ];
    }

    public static function direccionesValidas(): array {
        return [
            'minimo 5'      => ['Calle 1'],
            'medio'         => ['Avenida Siempre Viva 742'],
            'maximo 100'    => [str_repeat('c', 100)],
        ];
    }

    public static function direccionesInvalidas(): array {
        return [
            'vacio'         => [''],
            'solo espacios' => ['   '],
            'menos de 5'    => ['Calle'],
            'mas de 100'    => [str_repeat('c', 101)],
        ];
    }

    public static function statusValidos(): array {
        return [
            'activo'    => ['1'],
            'inactivo'  => ['0'],
        ];
    }

    public static function statusInvalidos(): array {
        return [
            'vacio'         => [''],
            'solo espacios' => ['   '],
            'no numerico'   => ['abc'],
            'fuera de rango'=> ['20'],
            'negativo'      => ['-1'],
        ];
    }

    // Usando DataProvider para probar nombres válidos
    #[DataProvider('nombresValidos')]
    public function testSetDataConNombreValido(string $nombre): void
    {
        $this->sut->setData(['nombre' => $nombre, 'apellido' => 'Perez', 'cedula' => '12345678', 'status' => '1']);
        $this->sut->check();

        // assertSame compara valor y tipo estrictamente
        $this->assertSame($nombre, $this->sut->getNombre());
        $this->assertSame('Perez', $this->sut->getApellido());
        $this->assertSame('12345678', $this->sut->getCedula());
        $this->assertSame('1', $this->sut->getStatus());
    }

    #[DataProvider('nombresInvalidos')]
    public function testSetDataConNombreInvalido(string $nombre): void
    {
        $this->sut->setData(['nombre' => $nombre, 'apellido' => 'Perez', 'cedula' => '12345678', 'status' => '1']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();

        $this->assertArrayHasKey('nombre', $this->sut->getErrores()); //muestra los errores
    }

    #[DataProvider('apellidosValidos')]
    public function testSerDataConApellidosValidos(string $apellido): void{
        $this->sut->setData(['nombre' => 'Juan', 'apellido' => $apellido, 'cedula' => '12345678', 'status' => '1']);
        $this->sut->check();

        $this->assertSame('Juan', $this->sut->getNombre());
        $this->assertSame($apellido, $this->sut->getApellido());
        $this->assertSame('12345678', $this->sut->getCedula());
        $this->assertSame('1', $this->sut->getStatus());

    }



    /** @test */
    public function ClienteDatosValidos()   //NO DEBE HABER ERROR 
    {
        // 1. Arrange (Preparar)
        $datosValidos = [
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'cedula' => '12345678',
            'telefono' => '0412-1234567',
            'email' => 'juan.perez@example.com',
            'direccion' => 'Calle Falsa 123',
            'status' => '1'
        ];

        // 2. Act (Actuar)
        $this->sut->setData($datosValidos);

        // 3. Assert (Verificar)
        $this->assertEmpty($this->sut->getErrores(), 'No deberían haber errores con datos válidos.');
    }

    /** @test */
    public function ClienteNombreInvalido() // DEBE HABER ERROR
    {
        // 1. Arrange (Preparar)
        $datosInvalidos = [
            'nombre' => 'A', // Nombre muy corto
            'apellido' => 'Perez',
            'cedula' => '12345678'
        ];

        // 2. Act (Actuar)
        $this->sut->setData($datosInvalidos);

        // 3. Assert (Verificar)
        $this->assertArrayHasKey('nombre', $this->sut->getErrores(), 'Debe haber un error para el nombre inválido.');
    }

    /** @test */
    public function ClienteApellidoInvalido() // DEBE HABER ERROR
    {
        // 1. Arrange
        $datosInvalidos = [
            'nombre'   => 'Juan',
            'apellido' => '', // Apellido vacío
            'cedula'   => '12345678'
        ];

        // 2. Act
        $this->sut->setData($datosInvalidos);

        // 3. Assert
        $this->assertArrayHasKey('apellido', $this->sut->getErrores());
    }

    /** @test */
    public function ClienteCedulaInvalida() // DEBE HABER ERROR
    {
        // 1. Arrange
        $datosInvalidos = [
            'nombre'   => 'Juan',
            'apellido' => 'Perez',
            'cedula'   => 'abc' // Cédula no numérica
        ];

        // 2. Act
        $this->sut->setData($datosInvalidos);

        // 3. Assert
        $this->assertArrayHasKey('cedula', $this->sut->getErrores());
    }

    /** @test */
    public function ClienteDatosValidos_CamposObligatorios() 
    {
        // 1. Arrange (Preparar)
        $datosValidos = [
            'nombre' => 'Ana',
            'apellido' => 'mendoza',
            'cedula' => '32145673'
        ];

        // 2. Act (Actuar)
        $this->sut->setData($datosValidos);

        // 3. Assert (Verificar)
        $this->assertEmpty($this->sut->getErrores(), 'No deberían haber errores con datos válidos.');
    }
    
    /** @test */
    public function ClienteTelefonoInvalido() // DEBE HABER ERROR
    {
        // 1. Arrange
        $datosInvalidos = [
            'nombre'   => 'Juan',
            'apellido' => 'Perez',
            'cedula'   => '12345678',
            'telefono' => '123' // Teléfono con formato incorrecto
        ];

        // 2. Act
        $this->sut->setData($datosInvalidos);

        // 3. Assert
        $this->assertArrayHasKey('telefono', $this->sut->getErrores());
    }

    /** @test */
    public function ClienteEmailInvalido() // DEBE HABER ERROR
    {
        // 1. Arrange
        $datosInvalidos = [
            'nombre'   => 'Juan',
            'apellido' => 'Perez',
            'cedula'   => '12345678',
            'email'    => 'email.invalido' // Email sin arroba
        ];

        // 2. Act
        $this->sut->setData($datosInvalidos);

        // 3. Assert
        $this->assertArrayHasKey('email', $this->sut->getErrores());
    }

    /** @test */
    public function ClienteDireccionInvalida() // DEBE HABER ERROR
    {
        // 1. Arrange
        $datosInvalidos = [
            'nombre'    => 'Juan',
            'apellido'  => 'Perez',
            'cedula'    => '12345678',
            'direccion' => '123' // Dirección muy corta
        ];

        // 2. Act
        $this->sut->setData($datosInvalidos);

        // 3. Assert
        $this->assertArrayHasKey('direccion', $this->sut->getErrores());
    }

    ### Pruebas para métodos con conexión a la base de datos (con Mocks)

    /** @test */
    /*public function buscarClientePorCedula()
    {
        // 1. Arrange: Preparar los Mocks
        $mockPdoStatement = $this->createMock(PDOStatement::class);
        $mockPdo = $this->createMock(PDO::class);

        // Esto crea un mock parcial de la clase Clientes.
        // Nos permite mockear métodos específicos mientras se usan los reales para otros.
        $sut = $this->getMockBuilder(Clientes::class)
                    //->disableOriginalConstructor()
                    ->onlyMethods(['conectarBD'])
                    ->getMock();

        // Configura el mock para que conectarBD() no haga nada.
        $sut->method('conectarBD')->willReturn(null);
        

        // El "mock" debe devolver estos datos
        $expectedData = [
            'cod_cliente' => 1,
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'cedula_rif' => '12345678',
            'telefono' => null,
            'email' => null,
            'direccion' => null,
            'status' => 1
        ];

        // Configurar el comportamiento de los "mocks"
        $mockPdoStatement->method('fetch')->willReturn($expectedData);
        $mockPdo->method('prepare')->willReturn($mockPdoStatement);

        // 2. Act: Inyectar el "mock" en el "stub"
        $sut->setConex($mockPdo);

        $result = $sut->buscar('1');

        // 3. Assert: Verificar el resultado
        $this->assertIsArray($result);
        $this->assertArrayHasKey('nombre', $result);
        $this->assertEquals('Juan', $result['nombre']);
    }*/






        
    /*==============================
        Pruebas de integración reales (conexión a BD real)
    ===============================*/

    /** @test */
    public function buscarClientePorCedula_conBaseDeDatosReal()
    {
        // 1. Arrange (Preparar)
        // Asegúrate de que las credenciales de la base de datos
        // están disponibles en tu entorno de prueba (por ej., en phpunit.xml).
        try {
            // Instanciar el modelo de Clientes de forma real.
            $clientes = new Clientes();
            $cedulaExistente = '1'; // Un cliente con esta cédula debe existir en tu BD de prueba.
        } catch (\Exception $e) {
            $this->fail("No se pudo instanciar el modelo Clientes o no se pudo conectar a la BD.");
        }
        
        // 2. Act (Actuar)
        // Llama al método de búsqueda con la cédula real.
        $resultado = $clientes->buscar($cedulaExistente);

        // 3. Assert (Verificar)
        // Si el método buscar funciona, debería devolver un array con los datos.
        $this->assertIsArray($resultado, 'El resultado debe ser un array.');
        $this->assertNotEmpty($resultado, 'Se debe encontrar un cliente con esa cédula.');
        $this->assertArrayHasKey('nombre', $resultado, 'El array debe contener la clave "nombre".');
        $this->assertArrayHasKey('cedula_rif', $resultado, 'El array debe contener la clave "cedula_rif".');
        $this->assertEquals($cedulaExistente, $resultado['cedula_rif'], 'La cédula del cliente debe coincidir con la de la búsqueda.');
    }

    // En tu archivo ClientesTest.php

    /** @test */
    public function registrar_clienteNuevo_debeInsertarDatosEnLaBD()
    {
        // 1. Arrange: Prepara los datos del nuevo cliente.
        $datosNuevoCliente = [
            'nombre' => 'TestIntegracion',
            'apellido' => 'ApellidoTest',
            'cedula' => '123456789', // Usa una cédula única para evitar conflictos.
            'telefono' => null,
            'email' => null,
            'direccion' => null
        ];
        $sut = new Clientes(); // Instancia real para la prueba de integración

        // 2. Act: Inserta el cliente.
        $sut->setData($datosNuevoCliente);
        $resultado = $sut->getRegistrar();

        // 3. Assert: Verifica que la inserción fue exitosa y que el cliente existe en la BD.
        $this->assertEquals(1, $resultado, 'El método registrar debe devolver 1 al insertar exitosamente.');

        // Verifica que el registro realmente existe.
        $clienteEncontrado = $sut->buscar('123456789');
        $this->assertIsArray($clienteEncontrado, 'El cliente debe ser encontrado en la base de datos.');
        $this->assertEquals('TestIntegracion', $clienteEncontrado['nombre'], 'El nombre del cliente debe coincidir.');

        // 4. Cleanup: Limpia la base de datos para la siguiente prueba.
        $sut->geteliminar($clienteEncontrado['cod_cliente']); // Asume que geteliminar funciona.
    }


    /** @test */
    public function consultar_debeDevolverTodosLosClientes()
    {
        // 1. Arrange: Cuenta cuántos clientes hay actualmente.
        $sut = new Clientes();
        $clientesAntes = $sut->consultar();
        $conteoAntes = count($clientesAntes);

        // Inserta un cliente de prueba para asegurar que el conteo cambia.
        $datos = ['nombre' => 'Temp', 'apellido' => 'Temp', 'cedula' => '111111111'];
        $sut->setData($datos);
        $sut->getRegistrar();

        // 2. Act: Vuelve a consultar.
        $clientesDespues = $sut->consultar();

        // 3. Assert: Verifica que la cantidad de clientes haya aumentado en 1.
        $this->assertGreaterThan($conteoAntes, count($clientesDespues));
        $this->assertIsArray($clientesDespues, 'El resultado debe ser un array.');

        // 4. Cleanup: Elimina el cliente temporal.
        $clienteTemp = $sut->buscar('111111111');
        $sut->geteliminar($clienteTemp['cod_cliente']);
    }


    /** @test */
    public function actualizar_clienteExistente_debeModificarDatosEnLaBD()
    {
        // 1. Arrange: Inserta un cliente de prueba.
        $sut = new Clientes();
        $datosIniciales = [
            'nombre' => 'ClienteViejo',
            'apellido' => 'ApellidoViejo',
            'cedula' => '987654321',
        ];
        $sut->setData($datosIniciales);
        $sut->getRegistrar();
        
        // Encuentra el cliente recién insertado para obtener su ID.
        $clienteInsertado = $sut->buscar('987654321');
        $cod_cliente = $clienteInsertado['cod_cliente'];

        // Prepara los nuevos datos.
        $datosNuevos = [
            'nombre' => 'ClienteNuevo',
            'apellido' => 'ApellidoNuevo',
            'cedula' => '987654321', // La cédula no cambia en este ejemplo
            'status' => 1,
        ];

        // 2. Act: Actualiza el cliente con los nuevos datos.
        $sut->setData($datosNuevos);
        $resultado = $sut->getactualizar($cod_cliente);

        // 3. Assert: Verifica que la actualización fue exitosa y que los datos se cambiaron en la BD.
        $this->assertEquals(1, $resultado, 'El método actualizar debe devolver 1 al actualizar exitosamente.');
        
        // Vuelve a buscar el cliente para verificar los cambios.
        $clienteActualizado = $sut->buscar('987654321');
        $this->assertEquals('ClienteNuevo', $clienteActualizado['nombre'], 'El nombre debe haber sido actualizado.');
        $this->assertEquals('ApellidoNuevo', $clienteActualizado['apellido'], 'El apellido debe haber sido actualizado.');

        // 4. Cleanup: Limpia la base de datos.
        $sut->geteliminar($cod_cliente);
    }


    /** @test */
    public function eliminar_clienteExistenteSinVentas_debeEliminarRegistroDeLaBD()
    {
        // 1. Arrange: Inserta un cliente de prueba que será eliminado.
        $sut = new Clientes();
        $datos = [
            'nombre' => 'EliminarTest',
            'apellido' => 'Test',
            'cedula' => '1', // Una cédula única
        ];
        $sut->setData($datos);
        $sut->getRegistrar();

        // Obtén el ID del cliente que se eliminará.
        $clienteInsertado = $sut->buscar('1');
        $cod_cliente = $clienteInsertado['cod_cliente'];

        // 2. Act: Elimina el cliente.
        $resultado = $sut->geteliminar($cod_cliente);

        // 3. Assert: Verifica que la eliminación fue exitosa y que el cliente ya no existe en la BD.
        $this->assertEquals('success', $resultado, 'El método eliminar debe devolver "success" al eliminar exitosamente.');
        
        // Intenta buscar el cliente, debe devolver un array vacío.
        $clienteEliminado = $sut->buscar('1');
        $this->assertEmpty($clienteEliminado, 'El cliente debe haber sido eliminado de la base de datos.');
    }

}