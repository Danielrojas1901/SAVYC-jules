<?php
namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Modelo\Compras;
use PDO;
use Exception;

class ComprasCrudTest extends TestCase
{
    /**
     * @var Compras
     */
    protected $sut;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sut = new Compras();
        $this->limpiarDatosDePrueba();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->limpiarDatosDePrueba();
        $this->sut = null;
    }

    // --- Métodos de Ayuda (Helpers) ---

    private function limpiarDatosDePrueba(): void
    {
        try {
            $this->sut->conectarBD();
            $this->sut->getconex()->exec("SET FOREIGN_KEY_CHECKS = 0;");
            $this->sut->getconex()->exec("DELETE FROM detalle_compras WHERE cod_compra IN (SELECT cod_compra FROM compras WHERE cod_prov IN (SELECT cod_prov FROM proveedores WHERE rif LIKE 'C-%'));");
            $this->sut->getconex()->exec("DELETE FROM compras WHERE cod_prov IN (SELECT cod_prov FROM proveedores WHERE rif LIKE 'C-%');");
            $this->sut->getconex()->exec("DELETE FROM detalle_productos WHERE cod_detallep > 2;");
            $this->sut->getconex()->exec("DELETE FROM proveedores WHERE rif LIKE 'C-%';");
            $this->sut->getconex()->exec("SET FOREIGN_KEY_CHECKS = 1;");
            $this->sut->desconectarBD();
        } catch (Exception $e) {
            error_log("Error en la limpieza de la BD: " . $e->getMessage());
        }
    }

    private function crearProveedorDePrueba(): int
    {
        $this->sut->conectarBD();
        $rif = 'C-' . uniqid();
        $sql = "INSERT INTO proveedores (razon_social, rif, email, direccion, status) VALUES ('Test Proveedor', :rif, 'testp@example.com', 'Calle Test', 1)";
        $stmt = $this->sut->getconex()->prepare($sql);
        $stmt->bindParam(':rif', $rif);
        $stmt->execute();
        return $this->sut->getconex()->lastInsertId();
    }

    private function obtenerStockActual(int $cod_detallep): float
    {
        $this->sut->conectarBD();
        $sql = "SELECT stock FROM detalle_productos WHERE cod_detallep = :cod_detallep";
        $stmt = $this->sut->getconex()->prepare($sql);
        $stmt->bindParam(':cod_detallep', $cod_detallep, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->sut->desconectarBD();
        return floatval($resultado['stock']);
    }

    private function obtenerCompra(int $cod_compra): ?array
    {
        $this->sut->conectarBD();
        $stmt = $this->sut->getconex()->prepare("SELECT * FROM compras WHERE cod_compra = :cod_compra");
        $stmt->bindParam(':cod_compra', $cod_compra, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->sut->desconectarBD();
        return $resultado !== false ? $resultado : null;
    }

    // --- Pruebas de Métodos de Compra ---

    /**
     * @test
     * @group integration
     */
    public function getRegistrarr_debeCrearUnaNuevaCompraYActualizarElStockDeProductosExistentes()
    {
        // Arrange
        $cod_prov = $this->crearProveedorDePrueba();
        $productos = [
            ['cod-dp' => 1, 'cantidad' => 2, 'precio' => 50.00, 'iva' => 0, 'cod_presentacion' => 1],
            ['cod-dp' => 2, 'cantidad' => 1, 'precio' => 10.00, 'iva' => 0, 'cod_presentacion' => 2]
        ];
        $datos_compra = [
            'cod_prov' => $cod_prov, 'condicion' => 'contado', 'subtotal' => 110.00, 'total_general' => 110.00, 'impuesto_total' => 0.00, 'fecha' => date('Y-m-d'), 'fecha_v' => null
        ];

        $stock_inicial_prod1 = $this->obtenerStockActual(1);
        $stock_inicial_prod2 = $this->obtenerStockActual(2);

        // Act
        $this->sut->setdatac($datos_compra);
        $cod_compra = $this->sut->getRegistrarr($productos);

        // Assert
        $this->assertIsNumeric($cod_compra, 'El método registrar debe devolver el ID de la compra.');
        $this->assertGreaterThan(0, $cod_compra, 'El ID de la compra debe ser mayor que 0.');

        $stock_final_prod1 = $this->obtenerStockActual(1);
        $stock_final_prod2 = $this->obtenerStockActual(2);

        $this->assertEquals($stock_inicial_prod1 + 2, $stock_final_prod1, 'El stock del producto 1 no se actualizó correctamente.');
        $this->assertEquals($stock_inicial_prod2 + 1, $stock_final_prod2, 'El stock del producto 2 no se actualizó correctamente.');

        $compra_creada = $this->obtenerCompra($cod_compra);
        $this->assertEquals($cod_prov, $compra_creada['cod_prov']);
        $this->assertEquals('110.00', $compra_creada['total']);
    }

    /**
     * @test
     * @group integration
     */
    public function anular_debeCambiarElEstadoAAnuladoYRevertirElStock()
    {
        // Arrange
        $cod_prov = $this->crearProveedorDePrueba();
        $productos = [
            ['cod-dp' => 1, 'cantidad' => 5, 'precio' => 50.00, 'iva' => 0, 'cod_presentacion' => 1]
        ];
        $datos_compra = [
            'cod_prov' => $cod_prov, 'condicion' => 'contado', 'subtotal' => 50.00, 'total_general' => 50.00, 'impuesto_total' => 0.00, 'fecha' => date('Y-m-d'), 'fecha_v' => null
        ];
        $this->sut->setdatac($datos_compra);
        $cod_compra_creada = $this->sut->getRegistrarr($productos);
        $stock_inicial = $this->obtenerStockActual(1);

        // Act
        $resultado = $this->sut->anular($cod_compra_creada);

        // Assert
        $this->assertEquals(1, $resultado, 'El método anular debe devolver 1 en caso de éxito.');

        $compra_anulada = $this->obtenerCompra($cod_compra_creada);
        $this->assertEquals(0, $compra_anulada['status'], 'El estado de la compra debe ser 0 (anulado).');

        $stock_final = $this->obtenerStockActual(1);
        $this->assertEquals($stock_inicial - 5, $stock_final, 'El stock no se revirtió correctamente después de anular la compra.');
    }

    /**
     * @test
     * @group integration
     */
    public function getconsultar_debeDevolverUnaListaDeComprasConDatosDeProveedor()
    {
        // Arrange
        $cod_prov = $this->crearProveedorDePrueba();
        $productos = [
            ['cod-dp' => 1, 'cantidad' => 1, 'precio' => 50.00, 'iva' => 0, 'cod_presentacion' => 1]
        ];
        $datos_compra = [
            'cod_prov' => $cod_prov, 'condicion' => 'contado', 'subtotal' => 50.00, 'total_general' => 50.00, 'impuesto_total' => 0.00, 'fecha' => date('Y-m-d'), 'fecha_v' => null
        ];
        $this->sut->setdatac($datos_compra);
        $cod_compra_creada = $this->sut->getRegistrarr($productos);

        // Act
        $compras = $this->sut->getconsultar();

        // Assert
        $this->assertIsArray($compras);
        $compra_encontrada = null;
        foreach ($compras as $compra) {
            if ($compra['cod_compra'] == $cod_compra_creada) {
                $compra_encontrada = $compra;
                break;
            }
        }
        $this->assertNotNull($compra_encontrada, 'La compra de prueba debe estar en la lista de compras.');
        $this->assertEquals('Test Proveedor', $compra_encontrada['razon_social']);
        $this->assertEquals(50.00, $compra_encontrada['total']);
        $this->assertEquals(0, $compra_encontrada['total_pagos_emitidos']);
    }
}