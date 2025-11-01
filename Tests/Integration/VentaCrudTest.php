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
use Modelo\Venta;
use PDO;
use Exception;

class VentaCrudTest extends TestCase
{
    /**
     * @var Venta
     */
    protected $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new Venta();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->sut = null;
    }

    private function crearClienteDePrueba(): int
    {
        $cedula = uniqid();
        $this->sut->conectarBD();
        $sql = "INSERT INTO clientes (cedula_rif, nombre, apellido, telefono, email, direccion, status) VALUES (:cedula, 'Test', 'Cliente', '1234567890', 'test@example.com', 'Calle Falsa', 1)";
        $stmt = $this->sut->getconex()->prepare($sql);
        $stmt->bindParam(':cedula', $cedula);
        $stmt->execute();
        return $this->sut->getconex()->lastInsertId();
    }

    private function obtenerVenta(int $cod_venta): ?array
    {
        $this->sut->conectarBD();
        $stmt = $this->sut->getconex()->prepare("SELECT * FROM ventas WHERE cod_venta = :cod_venta");
        $stmt->bindParam(':cod_venta', $cod_venta, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado !== false ? $resultado : null;
    }

    private function obtenerStockActual(int $cod_presentacion): float
    {
        $this->sut->conectarBD();
        $sql = "SELECT SUM(stock) as total_stock FROM detalle_productos WHERE cod_presentacion = :cod_presentacion";
        $stmt = $this->sut->getconex()->prepare($sql);
        $stmt->bindParam(':cod_presentacion', $cod_presentacion, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return floatval($resultado['total_stock']);
    }
    
    /*private function eliminarVenta(int $cod_venta): bool
    {
        $this->sut->conectarBD();
        $conex = $this->sut->getconex();
        $conex->beginTransaction();
        try {
            $conex->exec("DELETE FROM detalle_ventas WHERE cod_venta = $cod_venta");
            $conex->exec("DELETE FROM ventas WHERE cod_venta = $cod_venta");
            $conex->commit();
            return true;
        } catch (Exception $e) {
            $conex->rollBack();
            return false;
        }
    }*/
    
    // --- Pruebas de Métodos de Venta ---

    /**
     * @test
     * @group integration
     */
    public function getregistrar_debeCrearUnaNuevaVentaYActualizarElStockDeProductos()
    {
        $cliente_id = $this->crearClienteDePrueba();
        $productos = [
            ['codigo' => 7, 'cantidad' => 2, 'precio' => 50.00, 'costo' => 30.00],
            ['codigo' => 3, 'cantidad' => 1, 'precio' => 10.00, 'costo' => 5.00]
        ];
        $datos_venta = [
            'total_general' => 110.00, 'fecha_hora' => date('Y-m-d H:i:s'), 'subtotal' => 110.00, 'impuesto' => 0.00, 'condicion' => 'contado'
        ];
        $this->sut->setdatav($datos_venta);
        $stock_inicial_prod1 = $this->obtenerStockActual(7);
        $stock_inicial_prod2 = $this->obtenerStockActual(3);
        $resultado = $this->sut->getregistrar($cliente_id, $productos);
        $this->assertIsNumeric($resultado, 'El método registrar debe devolver el ID de la venta.');
        $this->assertGreaterThan(0, $resultado, 'El ID de la venta debe ser mayor que 0.');
        $stock_final_prod1 = $this->obtenerStockActual(7);
        $stock_final_prod2 = $this->obtenerStockActual(3);

        $expected1 = $stock_inicial_prod1 - 2;
        $expected2 = $stock_inicial_prod2 - 1;

        $this->assertEqualsWithDelta($expected1, $stock_final_prod1, 0.0001, 'El stock del producto 1 no se actualizó correctamente.');
        $this->assertEqualsWithDelta($expected2, $stock_final_prod2, 0.0001, 'El stock del producto 2 no se actualizó correctamente.');
    }

    /**
     * @test
     * @group integration
     */
    public function consultar_debeDevolverUnaListaDeVentasConSusDatosDeClienteYSaldos()
    {
        $cliente_id = $this->crearClienteDePrueba();
        $productos = [['codigo' => 3, 'cantidad' => 1, 'precio' => 50.00, 'costo' => 30.00]];
        $datos_venta = [
            'total_general' => 50.00, 'fecha_hora' => date('Y-m-d H:i:s'), 'subtotal' => 50.00, 'impuesto' => 0.00, 'condicion' => 'contado'
        ];
        $this->sut->setdatav($datos_venta);
        $cod_venta_creado = $this->sut->getregistrar($cliente_id, $productos);
        $ventas = $this->sut->consultar();
        $this->assertIsArray($ventas);
        $venta_encontrada = null;
        foreach ($ventas as $venta) {
            if ($venta['cod_venta'] == $cod_venta_creado) {
                $venta_encontrada = $venta;
                break;
            }
        }
        $this->assertNotNull($venta_encontrada, 'La venta de prueba debe estar en la lista de ventas.');
        $this->assertEquals(50.00, $venta_encontrada['total']);
        $this->assertEquals(0, $venta_encontrada['total_pagado']);
        $this->assertEquals(50.00, $venta_encontrada['saldo_restante']);
    }

    /**
     * @test
     * @group integration
     */
    public function getb_productos_debeDevolverProductosQueCoincidenConLaBusqueda()
    {
        // Asume que un producto con 'Coca' ya existe en la base de datos de prueba
        $resultado = $this->sut->getb_productos('jam');
        $this->assertIsArray($resultado);
        $this->assertGreaterThan(0, count($resultado), 'Debe encontrar al menos un producto que coincida con la búsqueda.');
        $this->assertStringContainsStringIgnoringCase('jam', $resultado[0]['producto_nombre']);
    }

    /**
     * @test
     * @group integration
     */
    public function anular_debeCambiarElEstadoAVentaYReversarElStock()
    {
        $cliente_id = $this->crearClienteDePrueba();
        $productos = [['codigo' => 3, 'cantidad' => 1, 'precio' => 50.00, 'costo' => 30.00]];
        $datos_venta = [
            'total_general' => 50.00, 'fecha_hora' => date('Y-m-d H:i:s'), 'subtotal' => 50.00, 'impuesto' => 0.00, 'condicion' => 'contado'
        ];
        $this->sut->setdatav($datos_venta);
        $cod_venta_creado = $this->sut->getregistrar($cliente_id, $productos);
        $stock_inicial = $this->obtenerStockActual(3);
        $resultado = $this->sut->anular($cod_venta_creado);
        $this->assertEquals(1, $resultado, 'El método anular debe devolver 1 en caso de éxito.');
        $venta_anulada = $this->obtenerVenta($cod_venta_creado);
        $this->assertEquals(0, $venta_anulada['status'], 'El estado de la venta debe ser 0 (anulado).');
        $stock_final = $this->obtenerStockActual(3);
        $this->assertEquals($stock_inicial + 1, $stock_final, 'El stock del producto no se revirtió correctamente después de anular la venta.');
    }

    /**
     * @test
     * @group integration
     */
    public function factura_debeDevolverElDetalleDeUnaVentaPorSuID()
    {
        $cliente_id = $this->crearClienteDePrueba();
        $productos = [['codigo' => 3, 'cantidad' => 1, 'precio' => 50.00, 'costo' => 30.00]];
        $datos_venta = [
            'total_general' => 50.00, 'fecha_hora' => date('Y-m-d H:i:s'), 'subtotal' => 50.00, 'impuesto' => 0.00, 'condicion' => 'contado'
        ];
        $this->sut->setdatav($datos_venta);
        $cod_venta_creado = $this->sut->getregistrar($cliente_id, $productos);
        $detalles = $this->sut->factura($cod_venta_creado);
        $this->assertIsArray($detalles);
        $this->assertNotEmpty($detalles);
        $this->assertEquals($cod_venta_creado, $detalles[0]['cod_venta']);
        $this->assertEquals(1, $detalles[0]['cantidad']);
        $this->assertEquals(50.00, $detalles[0]['importe']);
    }

    /**
     * @test
     * @group integration
     */
    public function v_cliente_debeDevolverDatosAgregadosDeVentasPorCliente()
    {
        $cliente_id = $this->crearClienteDePrueba();
        $clientes = $this->sut->v_cliente();
        $this->assertIsArray($clientes);
        $this->assertGreaterThan(0, count($clientes));
        $cliente_encontrado = null;
        foreach ($clientes as $cliente) {
            if ($cliente['cod_cliente'] == $cliente_id) {
                $cliente_encontrado = $cliente;
                break;
            }
        }
        $this->assertNotNull($cliente_encontrado);
    }

    /**
     * @test
     * @group integration
     */
    public function venta_f_debeDevolverVentasDentroDeUnRangoDeFechas()
    {
        $cliente_id = $this->crearClienteDePrueba();
        $productos = [['codigo' => 3, 'cantidad' => 1, 'precio' => 50.00, 'costo' => 30.00]];
        $datos_venta = [
            'total_general' => 50.00, 'fecha_hora' => date('Y-m-d H:i:s', strtotime('-1 day')), 'subtotal' => 50.00, 'impuesto' => 0.00, 'condicion' => 'contado'
        ];
        $this->sut->setdatav($datos_venta);
        $cod_venta_creado = $this->sut->getregistrar($cliente_id, $productos);
        $fecha_fin = date('Y-m-d');
        $fecha_inicio = date('Y-m-d', strtotime('-2 days'));
        $ventas_filtradas = $this->sut->venta_f($fecha_inicio, $fecha_fin);
        $this->assertIsArray($ventas_filtradas);
        $this->assertGreaterThan(0, count($ventas_filtradas));
        $venta_encontrada = null;
        foreach ($ventas_filtradas as $venta) {
            if ($venta['cod_venta'] == $cod_venta_creado) {
                $venta_encontrada = $venta;
                break;
            }
        }
        $this->assertNotNull($venta_encontrada, 'La venta creada no fue encontrada en el rango de fechas.');
    }
}