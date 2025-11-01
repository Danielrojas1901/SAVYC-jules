<?php
####### REPORTES ########
use PHPUnit\Framework\TestCase;
use Reportes\ReporteClientes;

class ClientereporteTest extends TestCase
{
    /**
     * @group integration
     */
    private function setUpSessionVariables()
    {
        $_SESSION = [
            //"logo" => "./vista/dist/img/logos/logo_generico.png",
            "n_empresa" => "Empresa PRUEBA01",
            "rif" => "J-12345678-9",
            "telefono" => "123-4567890",
            "email" => "contacto@empresa.com",
            "direccion" => "Calle Falsa 123"
        ];
    }

    /**
     * @group integration
     */
    public function testGenerarClientes()
    {
        $this->setUpSessionVariables();

        $reporte = new ReporteClientes();
        $pdfContent = $reporte->generarPDF();

        // Verificar que se generó contenido PDF (no vacío)
        $this->assertNotEmpty($pdfContent);
        $this->assertStringStartsWith('%PDF-', $pdfContent);
    }
}

//NOTA: ME FALTA TRABAJAR EN EL PASE DE LA IMAGEN PARA QUE SEA EL PASE DE INFORMACIÓN DE SESSION COMPLETO.
