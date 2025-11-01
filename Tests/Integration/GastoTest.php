<?php

use PHPUnit\Framework\TestCase;
use Modelo\Gasto;
class GastoTest extends TestCase
{ 
    private $Gasto; #Instancia de la clase
    private $arreglo = [];
    public function setUp(): void
    { #Método llamado antes de cada prueba, inicializa los objetos a usar (funcion de phpUnit)
        $this->Gasto = new Gasto();
    }

    ##########
    # TESTS DEL CRUD DE Gasto #
    ##########
    //Resultado esperado 1 = éxito
    public function testCrearGasto()
    {
        $this->arreglo = [
            'cod_condicion' => 2,
            'cod_cat_gasto' => 5,
            'monto' => 17.5,
            'descripcion' => 'para la empresa', 
            'fecha_vencimiento' => '2022-05-01', 
            'fecha' => '2022-01-01', 
        ];
        $this->Gasto->setDatos($this->arreglo);
        $resultado = $this->Gasto->publicregistrarg();
        $this->assertEquals(1, $resultado);
    }
    //Resultado esperado 0 = error
    public function testCrearGastoFalse()
    {
        $this->arreglo = [
            'cod_condicion' => 1,
            'cod_cat_gasto' => 7,
            'monto' => 174.5,
            'descripcion' => 'para la empresa', 
            'fecha_vencimiento' => '2022-05-01', 
            'fecha' => '2022-01-01', 
        ];
        $this->Gasto->setDatos($this->arreglo);
        $resultado = $this->Gasto->publicregistrarg();
        $this->assertEquals(false, $resultado);
    }
        

    //CONSULTA VARIABLE
    public function testConsultarGastoVariable()
    {
        $resultado = $this->Gasto->consultarGastoV();
        $this->assertIsArray($resultado);
    }
    //CONSULTA FIJO
    public function testConsultarGastoFijo()
    {
        $resultado = $this->Gasto->consultarGastoF();
        $this->assertIsArray($resultado);
    }
    //CONSULTAR TOTAL variable
    public function testConsultarTotalVariable()
    {
        $resultado = $this->Gasto->consultarTotalV();
        $this->assertIsArray($resultado);
    }
    //CONSULTAR TOTAL fijo
    public function testConsultarTotalFijo()
    {
        $resultado = $this->Gasto->consultarTotalF();
        $this->assertIsArray($resultado);
    }
    //CONSULTAR TOTAL
    public function testConsultarTotal()
    {
        $resultado = $this->Gasto->consultarTotalG();
        $this->assertIsArray($resultado);
    }
    //CONSULTAR TOTAL PAGADO
    public function testConsultarTotalP()
    {
        $resultado = $this->Gasto->consultarTotalP();
        $this->assertIsArray($resultado);
    }
    //CONSULTAS VACIAS
    //CONSULTA VARIABLE
    
    public function testConsultarGastoVariableVacio()
    {
        $resultado = $this->Gasto->consultarGastoV();
        $this->assertIsArray($resultado);
        //$this->assertEquals(null,$resultado); 
        $this->assertEmpty($resultado);
    }
    //CONSULTA FIJO
    public function testConsultarGastoFijoVacio()
    {
        $resultado = $this->Gasto->consultarGastoF();
        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }
    //CONSULTAR TOTAL variable
    public function testConsultarTotalVariableVacio()
    {
        $resultado = $this->Gasto->consultarTotalV();
        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
        $this->assertArrayHasKey('total_monto', $resultado[0]);
        $this->assertTrue(is_null($resultado[0]['total_monto']) || $resultado[0]['total_monto'] == 0);
    }
    //CONSULTAR TOTAL fijo
    public function testConsultarTotalFijoVacio()
    {
        $resultado = $this->Gasto->consultarTotalF();
        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
        $this->assertArrayHasKey('total_monto', $resultado[0]);
        $this->assertTrue(is_null($resultado[0]['total_monto']) || $resultado[0]['total_monto'] == 0);
    }
    //CONSULTAR TOTAL
    public function testConsultarTotalVacio()
    {
        $resultado = $this->Gasto->consultarTotalG();
        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
        $this->assertArrayHasKey('total_monto', $resultado[0]);
        $this->assertTrue(is_null($resultado[0]['total_monto']) || $resultado[0]['total_monto'] == 0);
    }
    //CONSULTAR TOTAL PAGADO
    public function testConsultarTotalPVacio()
    {
        $resultado = $this->Gasto->consultarTotalP();
        $this->assertIsArray($resultado);
        $this->assertCount(1, $resultado);
        $this->assertArrayHasKey('total_monto', $resultado[0]);
        $this->assertTrue(is_null($resultado[0]['total_monto']) || $resultado[0]['total_monto'] == 0);
    }
    //BUSQUEDA DE Gasto
     public function testBuscarGasto()
    {
        $this->arreglo = [
            'descripcion' => 'Gasto del inter del mes', 
        ];
        $resultado = $this->Gasto->buscar_gasto();
        $this->assertIsArray($resultado); 
    }

    public function testBuscarGastofalse()
    {
        $this->arreglo = [
            'descripcion' => 'Insumos del mes', 
        ];
        $this->Gasto->setDatos($this->arreglo);
        $resultado = $this->Gasto->buscar_gasto();
        $this->assertEquals(false, $resultado);
        
    }

    //EDICIÓN DE Gasto
    public function testEditarGasto(){
        $this->arreglo = [
            'cod_condicion' => 2,
            'cod_gasto' => 1,
            'cod_cat_gasto' => 5,
            'monto' => 1,
            'descripcion' => 'Gasto del inter del mes', 
            'fecha_vencimiento' => '2022-05-01', 
            'origin' => 'para la empresa',
            'fecha' => '2022-01-01', 
        ];
        $this->Gasto->setDatos($this->arreglo);
        $resultado = $this->Gasto->editarGasto();
        $this->assertEquals(1, $resultado);
    }
   public function testEditarGastoFalse(){
        $this->arreglo = [
            'cod_condicion' => 7,
            'cod_gasto' => 6,
            'cod_cat_gasto' => 5,
            'monto' => 239.28,
            'descripcion' => 'Mes de septiembre de inter', 
            'fecha_vencimiento' => '2022-05-07', 
            'origin' => 'para la empresa',
            'fecha' => '2022-01-01', 
        ];
        $this->Gasto->setDatos($this->arreglo);
        $resultado = $this->Gasto->editarGasto();
        $this->assertEquals(false, $resultado);
    }

   public function testEliminarGasto(){
        $this->arreglo = [
            'cod_gasto' => 2
        ];
        $this->Gasto->setDatos($this->arreglo);
        $resultado = $this->Gasto->eliminarGasto();
        $this->assertEquals('success', $resultado);
    }
    
    public function testEliminarGastofalse1(){
        $this->arreglo = [
            'cod_gasto' => 1
        ];
        $this->Gasto->setDatos($this->arreglo);
        $resultado = $this->Gasto->eliminarGasto();
        $this->assertEquals('error_associated', $resultado);
    }
    public function testEliminarGastofalse2(){
        $this->arreglo = [
            'cod_gasto' => 12
        ];
        $this->Gasto->setDatos($this->arreglo);
        $resultado = $this->Gasto->eliminarGasto();
        $this->assertEquals('error_delete', $resultado);
    }
}
