<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Modelo\Descarga;
use Modelo\Conexion;
use PDO;
use Exception;

final class DescargaCrudTest extends TestCase
{
    private Descarga $sut;

    protected function setUp(): void
    {
        // Instancia la clase real para la prueba de integración
        $this->sut = new Descarga();
    }

    /**
     * @test
     * @group integration
     */
    public function registrar_debeInsertarNuevaDescargaYActualizarStock()
    {
        // 1. Arrange: Prepara los datos para la descarga y el stock inicial
        $cod_detallep_producto = 1; // Asegúrate de que este ID exista en tu tabla `detalle_productos`
        $cantidad_descargada = 2;

        // Necesitas el stock inicial para verificar que se actualice correctamente
        $stock_inicial = $this->obtenerStockActual($cod_detallep_producto);

        $datosDescarga = [
            'fecha' => date('Y-m-d'),
            'descripcion' => 'Prueba de integración de descarga',
            'costo' => 50.00,
        ];
        $detallesDescarga = [
            ['cod_detallep' => $cod_detallep_producto, 'cantidad' => $cantidad_descargada],
        ];

        // 2. Act: Llama al método registrar
        $this->sut->setfecha($datosDescarga['fecha']);
        $this->sut->setdescripcion($datosDescarga['descripcion']);
        $this->sut->setcosto($datosDescarga['costo']);
        $cod_descarga = $this->sut->registrar($detallesDescarga);

        // 3. Assert: Verifica que la descarga se insertó y el stock se actualizó
        $this->assertGreaterThan(0, $cod_descarga, 'El método registrar debe devolver un ID válido.');
        
        $stock_final = $this->obtenerStockActual($cod_detallep_producto);
        $this->assertEquals(
            $stock_inicial - $cantidad_descargada, 
            $stock_final, 
            'El stock debe ser actualizado correctamente.'
        );

        // 4. Cleanup: Limpia los datos insertados en la base de datos
        $this->limpiarDescargaDePrueba($cod_descarga, $cod_detallep_producto, $cantidad_descargada);
    }
    
    /**
     * @test
     * @group integration
     */
    public function consultardescarga_debeDevolverListaDeDescargas()
    {
        // 1. Arrange: Inserta una descarga temporal para asegurarte de que el conteo aumente
        $descargaTemporal = $this->insertarDescargaTemporal();
        $conteo_antes = count($this->sut->consultardescarga());
        
        // 2. Act: Llama al método a probar
        $resultados = $this->sut->consultardescarga();

        // 3. Assert: Verifica que el resultado no sea nulo y que el conteo sea correcto
        $this->assertIsArray($resultados);
        $this->assertGreaterThanOrEqual(1, count($resultados));
        
        // 4. Cleanup: Elimina el registro de prueba
        $this->limpiarDescargaDePrueba($descargaTemporal['cod_descarga'], $descargaTemporal['cod_detallep'], $descargaTemporal['cantidad']);
    }

    /**
     * @test
     * @group integration
     */
    public function consultardetalledescarga_debeDevolverDetallesDeUnaDescarga()
    {
        // 1. Arrange: Inserta una descarga temporal con detalles
        $descargaTemporal = $this->insertarDescargaTemporal();

        // 2. Act: Consulta los detalles de esa descarga
        $detalles = $this->sut->consultardetalledescarga($descargaTemporal['cod_descarga']);

        // 3. Assert: Verifica que los detalles sean correctos
        $this->assertIsArray($detalles);
        $this->assertCount(1, $detalles); // Esperamos 1 detalle
        $this->assertEquals($descargaTemporal['cod_detallep'], $detalles[0]['cod_detallep']);
        $this->assertEquals($descargaTemporal['cantidad'], $detalles[0]['cantidad']);

        // 4. Cleanup: Elimina los registros de prueba
        $this->limpiarDescargaDePrueba($descargaTemporal['cod_descarga'], $descargaTemporal['cod_detallep'], $descargaTemporal['cantidad']);
    }

    /**
     * @test
     * @group integration
     */
    public function buscar_debeDevolverProductosQueCoincidenConNombre()
    {
        // 1. Arrange: Inserta un producto de prueba si no existe
        $producto_a_buscar = 'Agua';
        // Necesitas asegurarte de que este producto y su detalle existan en la base de datos de prueba
        // Para esta prueba, asumimos que existe un producto con nombre 'Agua' y stock != 0

        // 2. Act: Busca productos por nombre
        $resultados = $this->sut->buscar($producto_a_buscar);

        // 3. Assert: Verifica que los resultados sean un array y contengan el producto esperado
        $this->assertIsArray($resultados);
        $this->assertGreaterThan(0, count($resultados));
        $encontrado = false;
        foreach ($resultados as $producto) {
            if (strpos($producto['producto_nombre'], $producto_a_buscar) !== false) {
                $encontrado = true;
                break;
            }
        }
        $this->assertTrue($encontrado, 'Debe encontrar un producto con el nombre "Agua"');
    }


    // --- Métodos de Ayuda para las Pruebas ---
    
    // Método para insertar una descarga temporal
    private function insertarDescargaTemporal()
    {
        $descarga = new Descarga();
        $cod_detallep_producto = 1; // Asume que este existe
        $cantidad = 2;
        $descarga->setfecha(date('Y-m-d'));
        $descarga->setdescripcion('Temporal');
        $descarga->setcosto(1.00);
        $cod_descarga = $descarga->registrar([
            ['cod_detallep' => $cod_detallep_producto, 'cantidad' => $cantidad],
        ]);
        return ['cod_descarga' => $cod_descarga, 'cod_detallep' => $cod_detallep_producto, 'cantidad' => $cantidad];
    }
    
    // Método para limpiar los datos de prueba
    private function limpiarDescargaDePrueba($cod_descarga, $cod_detallep_producto, $cantidad_descargada)
    {
        $descarga = new Descarga();
        $descarga->conectarBD();
        
        // Deshace la actualización de stock
        $sql1 = 'UPDATE detalle_productos SET stock = stock + ? WHERE cod_detallep = ?';
        $stmt1=$descarga->getconex()->prepare($sql1);
        $stmt1->bindParam(1, $cantidad_descargada);
        $stmt1->bindParam(2, $cod_detallep_producto);
        $stmt1->execute();
        
        // Elimina los detalles de descarga
        $sql2 = 'DELETE FROM detalle_descarga WHERE cod_descarga = ?';
        $stmt2 = $descarga->getConex()->prepare($sql2);
        $stmt2->bindParam(1, $cod_descarga);
        $stmt2->execute();

        // Elimina la descarga principal
        $sql3 = 'DELETE FROM descarga WHERE cod_descarga = ?';
        $stmt3 = $descarga->getConex()->prepare($sql3);
        $stmt3->bindParam(1, $cod_descarga);
        $stmt3->execute();

        $descarga->desconectarBD();
    }
    
    // Método auxiliar para obtener el stock
    private function obtenerStockActual($cod_detallep)
    {
        $descarga = new Descarga();
        $descarga->conectarBD();
        $sql = 'SELECT stock FROM detalle_productos WHERE cod_detallep = ?';
        $stmt = $descarga->getConex()->prepare($sql);
        $stmt->bindParam(1, $cod_detallep);
        $stmt->execute();
        $stock = $stmt->fetchColumn();
        $descarga->desconectarBD();
        return $stock;
    }
}