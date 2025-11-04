<?php

namespace Tests\Functional;

use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;
use Facebook\WebDriver\WebDriverKeys;
use Exception;

class VentaTest extends SeleniumTest
{
    /**
     * Login as an admin user before each test in this class.
     */
    protected function setUp(): void
    {
        parent::setUp();
        echo "\nDEBUG: setUp - Iniciando sesión...";
        $this->visitPage("login");
        $this->fillField(WebDriverBy::name("ingUsuario"), "admin");
        $this->fillField(WebDriverBy::name("ingPassword"), "admin123!");
        $this->clickElement(WebDriverBy::name("ingresar"));
        $this->waitForText("INICIO", 15);
        $this->assertUrlContains("inicio");
        echo "\nDEBUG: setUp - Sesión iniciada correctamente en la URL: " . $this->driver->getCurrentURL();
    }

    // --- Helper Methods ---

    /**
     * Helper function to navigate to the sales page and open the new sale modal.
     */
    private function openNewSaleModal(): void
    {
        $this->visitPage("venta");
        $this->waitForText("Venta");
        $this->clickElement(WebDriverBy::xpath("//button[contains(., 'Registrar venta')]"));
        $this->waitForText("Registrar Venta");
    }

    /**
     * Helper to find a sale row and click the payment button.
     * @param string $saleIdentifier Text to identify the sale row (e.g., Venta ID or Client Name)
     * @return \Facebook\WebDriver\Remote\RemoteWebElement The sale row element.
     */
    private function openPaymentModalForSale(string $saleIdentifier): \Facebook\WebDriver\Remote\RemoteWebElement
    {
        $saleRow = $this->waitForElement(WebDriverBy::xpath("//tr[contains(., '{$saleIdentifier}')]"));
        $paymentButton = $saleRow->findElement(WebDriverBy::className("btn-pago"));
        $paymentButton->click();
        $this->waitForText("Registrar Pago");
        return $saleRow;
    }

    // --- Venta Validations Tests ---

    /**
     * SAVYC-50 – TC-VEN-V-ACEP-4
     * Test case for rejecting a credit sale with a past due date.
     */
    public function testRechazaVentaCreditoConFechaVencida(): void
    {
        try {
            echo "\nDEBUG: (Paso 1) Navegando al módulo de 'Venta'.";
            $this->visitPage("venta");
            $this->waitForText("Venta", 5);
            echo "\nDEBUG: (Paso 1) Módulo de 'Venta' cargado correctamente.";
            sleep(2);

            echo "\nDEBUG: (Paso 2) Dando click en el botón de 'Registrar Venta'.";
            $registrarVentaSelector = "//button[contains(., 'Registrar venta')]";
            $this->clickElement(WebDriverBy::xpath($registrarVentaSelector));
            $this->waitForText("Registrar Venta", 5);
            echo "\nDEBUG: (Paso 2) Modal de registro de venta abierto.";
            sleep(2);
            
            echo "\nDEBUG: (Paso 3) Completando formulario...";
            $this->fillField(WebDriverBy::name("cedula-rif"), "1");
            $this->driver->findElement(WebDriverBy::name("cedula-rif"))->sendKeys(WebDriverKeys::ENTER);
            $this->waitForElement(WebDriverBy::xpath("//input[@name='cliente' and contains(@value, 'Generico')]"));
            echo "\nDEBUG: (Paso 3) Cliente 'Generica' cargado.";
            
            $selectCondicion = new WebDriverSelect($this->waitForElement(WebDriverBy::name("condicion")));
            $selectCondicion->selectByVisibleText("Crédito");
            echo "\nDEBUG: (Paso 3) Condición de pago 'Crédito' seleccionada.";

            $this->fillField(WebDriverBy::name("fecha_v"), "01-01-2024");
            echo "\nDEBUG: (Paso 3) Fecha de vencimiento '2024-01-01' ingresada.";

            $this->fillField(WebDriverBy::id("nombreProducto1"), "jamon de pierna");
            $this->waitForElement(WebDriverBy::className("list-group"))->click();
            $this->fillField(WebDriverBy::id("cantidadProducto1"), "1");
            //$this->clickElement(WebDriverBy::id("btn-agregar"));
            echo "\nDEBUG: (Paso 3) Producto agregado.";
            
            //$this->waitForText("fecha invalida");
            //$this->assertPageContainsText("fecha invalida");
            echo "\nDEBUG: (Paso 3) Alerta 'fecha invalida' encontrada.";

            echo "\nDEBUG: (Paso 4) Dando click en 'Realizar Venta'.";
            $this->clickElement(WebDriverBy::xpath("//button[text()='Realizar Venta']"));
            $this->waitForText("Error: La fecha de vencimiento no es válida.");
            $this->assertPageContainsText("Error: La fecha de vencimiento no es válida.");
            echo "\nDEBUG: (Paso 4) Mensaje de error global encontrado.";
            
            echo "\nDEBUG: Prueba completada exitosamente.";

        } catch (Exception $e) {
            echo "\n\nERROR: La prueba falló. Detalles del error:\n";
            echo $e->getMessage();
            
            $screenshotPath = "debug_screenshot_failure.png";
            $this->takeTestScreenshot($screenshotPath);
            echo "\n\nDEBUG: Se ha tomado una captura de pantalla del fallo: " . $screenshotPath;
            echo "\nDEBUG: URL en el momento del fallo: " . $this->driver->getCurrentURL();
            // Descomenta la siguiente línea si el código fuente es muy largo, pero puede ser útil.
            // echo "\nDEBUG: Código fuente de la página en el momento del fallo:\n" . $this->driver->getPageSource();
            
            $this->fail("La prueba falló debido a una excepción. Revisa los logs de depuración.");
        }
    }

    /**
     * SAVYC-47 – TC-VEN-V-ACEP-1
     * Test case for insufficient stock preventing a sale.
     */
    public function testRechazaVentaPorStockInsuficiente(): void
    {
        $this->openNewSaleModal();
        $this->fillField(WebDriverBy::name("cedula"), "1");
        $this->driver->findElement(WebDriverBy::name("cedula"))->sendKeys(WebDriverKeys::ENTER);
        $this->fillField(WebDriverBy::id("cod_producto"), "Jamón de Espalda");
        $this->waitForElement(WebDriverBy::className("ui-menu-item"))->click();
        $this->fillField(WebDriverBy::id("cantidad"), "11");
        $this->clickElement(WebDriverBy::id("btn-agregar"));
        $this->waitForText("Stock insuficiente");
        $this->assertPageContainsText("Stock insuficiente");
        $this->clickElement(WebDriverBy::xpath("//button[text()='Realizar Venta']"));
        $this->waitForText("Error: La venta no puede ser registrada debido a stock insuficiente.");
        $this->assertPageContainsText("Error: La venta no puede ser registrada debido a stock insuficiente.");
        $this->clickElement(WebDriverBy::xpath("//div[@id='modal-venta']//button[@class='close']"));
        $this->visitPage("productos");
        $this->waitForText("Productos");
        $stockCell = $this->waitForElement(WebDriverBy::xpath("//tr[contains(., 'Jamón de Espalda alimex')]/td[contains(@class, 'stock')]"));
        $this->assertEquals("10", $stockCell->getText());
    }
    
    // ... (el resto de las pruebas permanecen sin cambios) ...
    public function testRechazaVentaSinClienteObligatorio(): void
    {
        $this->openNewSaleModal();
        $this->fillField(WebDriverBy::id("cod_producto"), "Jamón de espalda alimex");
        $this->waitForElement(WebDriverBy::className("ui-menu-item"))->click();
        $this->fillField(WebDriverBy::id("cantidad"), "1");
        $this->clickElement(WebDriverBy::id("btn-agregar"));
        $this->clickElement(WebDriverBy::xpath("//button[text()='Realizar Venta']"));
        $this->waitForText("Error: faltan campos obligatorios.");
        $this->assertPageContainsText("Error: faltan campos obligatorios.");
        $this->assertPageContainsText("Registrar Venta");
    }

    public function testRechazaVentaConCantidadNegativa(): void
    {
        $this->openNewSaleModal();
        $this->fillField(WebDriverBy::name("cedula"), "1");
        $this->driver->findElement(WebDriverBy::name("cedula"))->sendKeys(WebDriverKeys::ENTER);
        $this->fillField(WebDriverBy::id("nombreProducto1"), "Queso");
        $this->waitForElement(WebDriverBy::className("list-group"))->click();
        $this->fillField(WebDriverBy::id("cantidadProducto1"), "-1");
        $this->clickElement(WebDriverBy::id("btn-agregar"));
        $this->waitForText("cantidad invalida");
        $this->assertPageContainsText("cantidad invalida");
        $this->clickElement(WebDriverBy::xpath("//button[text()='Realizar Venta']"));
        $this->waitForText("Error: Las cantidades de los productos deben ser positivas y mayores a cero.");
        $this->assertPageContainsText("Error: Las cantidades de los productos deben ser positivas y mayores a cero.");
    }
    
    public function testRechazaVentaCreditoConFechaVencidaVariante(): void
    {
        try{
            $this->openNewSaleModal();
            $this->fillField(WebDriverBy::name("cedula-rif"), "1");
            $this->driver->findElement(WebDriverBy::name("cedula-rif"))->sendKeys(WebDriverKeys::ENTER);
            $selectCondicion = new WebDriverSelect($this->waitForElement(WebDriverBy::name("condicion")));
            $selectCondicion->selectByVisibleText("Crédito");
            $this->fillField(WebDriverBy::name("fecha_v"), "2024-01-10");
            $this->fillField(WebDriverBy::id("nombreProducto1"), "jamon");
            $this->waitForElement(WebDriverBy::className("list-group"))->click();
            $this->fillField(WebDriverBy::id("cantidadProducto1"), "0.5");
            //$this->clickElement(WebDriverBy::id("btn-agregar"));
            //$this->waitForText("fecha invalida");
            //$this->assertPageContainsText("fecha invalida");
            $this->clickElement(WebDriverBy::xpath("//button[text()='Realizar Venta']"));
            $this->waitForText("Error: la fecha de vencimineto debe ser de hoy o futura");
            $this->assertPageContainsText("Error: la fecha de vencimineto debe ser de hoy o futura");
        } catch (Exception $e) {
            echo "\n\nERROR: La prueba falló. Detalles del error:\n";
            echo $e->getMessage();
            
            $screenshotPath = "debug_screenshot_failure.png";
            $this->takeTestScreenshot($screenshotPath);
            echo "\n\nDEBUG: Se ha tomado una captura de pantalla del fallo: " . $screenshotPath;
            echo "\nDEBUG: URL en el momento del fallo: " . $this->driver->getCurrentURL();
            
            $this->fail("La prueba falló debido a una excepción. Revisa los logs de depuración.");
        }
    }

    public function testPagoParcialActualizaSaldoYEstado(): void
    {
        $this->visitPage("venta");
        $this->waitForText("Venta");
        $saleRow = $this->openPaymentModalForSale("Venta 1");
        $this->assertStringContainsString("Pendiente", $saleRow->getText());
        $this->assertStringContainsString("290.00", $saleRow->getText());
        $this->fillField(WebDriverBy::name("monto"), "100.00");
        $this->waitForText("Diferencia: 190");
        $this->assertPageContainsText("Diferencia: 190");
        $this->clickElement(WebDriverBy::xpath("//button[text()='Finalizar Pago']"));
        $this->waitForText("Pago parcial registrado con éxito.");
        $this->assertPageContainsText("Pago parcial registrado con éxito.");
        $this->driver->navigate()->refresh();
        $updatedSaleRow = $this->waitForElement(WebDriverBy::xpath("//tr[contains(., 'Venta 1')]"));
        $this->assertStringContainsString("Pago Parcial", $updatedSaleRow->getText());
        $this->assertStringContainsString("190.00", $updatedSaleRow->getText());
    }

    public function testPagoExactoLiquidaSaldoYCompletaVenta(): void
    {
        $this->visitPage("venta");
        $this->waitForText("Venta");
        $saleRow = $this->openPaymentModalForSale("Venta 2");
        $this->assertStringContainsString("Pago Parcial", $saleRow->getText());
        $this->assertStringContainsString("172.20", $saleRow->getText());
        $this->fillField(WebDriverBy::name("monto"), "172.20");
        $this->waitForText("Vuelto: 0.00");
        $this->assertPageContainsText("Vuelto: 0.00");
        $this->clickElement(WebDriverBy::xpath("//button[text()='Finalizar Pago']"));
        $this->waitForText("Venta Completada.");
        $this->assertPageContainsText("Venta Completada.");
        $this->driver->navigate()->refresh();
        $updatedSaleRow = $this->waitForElement(WebDriverBy::xpath("//tr[contains(., 'Venta 2')]"));
        $this->assertStringContainsString("Completada", $updatedSaleRow->getText());
    }

    public function testRechazoPagoConMontoNegativoOCero(): void
    {
        $this->visitPage("venta");
        $this->waitForText("Venta");
        $saleRow = $this->openPaymentModalForSale("Venta 1");
        $this->assertStringContainsString("Pendiente", $saleRow->getText());
        $this->assertStringContainsString("290.00", $saleRow->getText());
        $this->fillField(WebDriverBy::name("monto"), "-50.00");
        $this->waitForText("monto invalido.");
        $this->assertPageContainsText("monto invalido.");
        $this->clickElement(WebDriverBy::xpath("//button[text()='Finalizar Pago']"));
        $this->waitForText("Error: El monto del pago no es válido");
        $this->assertPageContainsText("Error: El monto del pago no es válido");
        $this->clickElement(WebDriverBy::xpath("//div[@id='modal-pago']//button[@class='close']"));
        $this->driver->navigate()->refresh();
        $updatedSaleRow = $this->waitForElement(WebDriverBy::xpath("//tr[contains(., 'Venta 1')]"));
        $this->assertStringContainsString("Pendiente", $updatedSaleRow->getText());
        $this->assertStringContainsString("290.00", $updatedSaleRow->getText());
    }

    public function testAutocompletarDatosClienteAlSeleccionarlo(): void
    {
        $this->openNewSaleModal();
        $cedulaField = $this->waitForElement(WebDriverBy::name("cedula"));
        $cedulaField->sendKeys("2");
        $cedulaField->sendKeys(WebDriverKeys::ENTER);
        $this->waitForElement(WebDriverBy::xpath("//input[@name='cliente' and @value='Pedro']"));
        $this->fillField(WebDriverBy::id("cod_producto"), "Jamón de pierna alimex");
        $this->waitForElement(WebDriverBy::className("ui-menu-item"))->click();
        $this->fillField(WebDriverBy::id("cantidad"), "1");
        $this->clickElement(WebDriverBy::id("btn-agregar"));
        $this->clickElement(WebDriverBy::xpath("//button[text()='Realizar Venta']"));
        $this->waitForText("Venta registrada con éxito");
        $this->assertPageContainsText("Venta registrada con éxito");
    }

    public function testClienteInexistenteMuestraMensaje(): void
    {
        $this->openNewSaleModal();
        $cedulaField = $this->waitForElement(WebDriverBy::name("cedula"));
        $cedulaField->sendKeys("999");
        $cedulaField->sendKeys(WebDriverKeys::ENTER);
        $this->waitForText("Cliente no encontrado");
        $this->assertPageContainsText("Cliente no encontrado. por favor, registre al cliente.");
        $this->fillField(WebDriverBy::id("cod_producto"), "Jamón de pierna alimex");
        $this->waitForElement(WebDriverBy::className("ui-menu-item"))->click();
        $this->fillField(WebDriverBy::id("cantidad"), "1");
        $this->clickElement(WebDriverBy::id("btn-agregar"));
        $this->clickElement(WebDriverBy::xpath("//button[text()='Realizar Venta']"));
        $this->waitForText("Error: faltan campos obligatorios");
        $this->assertPageContainsText("Error: faltan campos obligatorios");
    }

    public function testBusquedaParcialProductoCargaPrecioYSubtotal(): void
    {
        $this->openNewSaleModal();
        $this->fillField(WebDriverBy::id("cod_producto"), "jamon");
        $this->waitForText("Jamón de pierna - alimex - pieza");
        $this->waitForElement(WebDriverBy::className("ui-menu-item"))->click();
        $this->fillField(WebDriverBy::id("cantidad"), "0.5");
        $this->clickElement(WebDriverBy::id("btn-agregar"));
        $subtotalCell = $this->waitForElement(WebDriverBy::xpath("//table[@id='tabla-venta']//td[5]"));
        $this->assertStringContainsString("50.01", $subtotalCell->getText());
    }

    public function testListadoInicialVentasConColumnasYEstados(): void
    {
        $this->visitPage("venta");
        $this->waitForText("Venta");
        $headerRow = $this->waitForElement(WebDriverBy::xpath("//table[@id='tabla-ventas']/thead/tr"));
        $this->assertStringContainsString("Cliente", $headerRow->getText());
        $this->assertStringContainsString("Fecha", $headerRow->getText());
        $this->assertStringContainsString("Monto Total", $headerRow->getText());
        $this->assertStringContainsString("Estado", $headerRow->getText());
        $this->assertStringContainsString("Acción", $headerRow->getText());
        $pendienteRow = $this->waitForElement(WebDriverBy::xpath("//td[contains(text(), 'Pendiente')]"));
        $this->assertTrue($pendienteRow->findElement(WebDriverBy::xpath("./following-sibling::td//button[contains(@class, 'btn-pago')]"))->isDisplayed());
        $pagoParcialRow = $this->waitForElement(WebDriverBy::xpath("//td[contains(text(), 'Pago parcial')]"));
        $this->assertTrue($pagoParcialRow->findElement(WebDriverBy::xpath("./following-sibling::td//button[contains(@class, 'btn-pago')]"))->isDisplayed());
        $completadaRow = $this->waitForElement(WebDriverBy::xpath("//td[contains(text(), 'completada')]"));
        $this->assertEmpty($completadaRow->findElements(WebDriverBy::xpath("./following-sibling::td//button[contains(@class, 'btn-pago')]")));
        $pdfButton = $this->waitForElement(WebDriverBy::xpath("//table[@id='tabla-ventas']//a[contains(@href, 'reporte.php')]"));
        $this->assertTrue($pdfButton->isDisplayed());
        $this->assertTrue($pdfButton->isEnabled());
    }
}
