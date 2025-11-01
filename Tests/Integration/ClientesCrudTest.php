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

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Modelo\Clientes;
use Modelo\Conexion;
use PHPUnit\Framework\Attributes\DataProvider;
use PDO;
use PDOStatement;


final class ClientesCrudTest extends TestCase
{
    private Clientes $sut;

    protected function setUp(): void
    {
        // Instancia el "stub" para evitar la conexión real a la base de datos.
        $this->sut = new Clientes();
    }

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

