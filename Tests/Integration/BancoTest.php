<?php

use PHPUnit\Framework\TestCase;
use Modelo\Banco;
use PHPUnit\Framework\Attributes\Group;
class BancoTest extends TestCase
{
    
    private $banco; #Instancia de la clase banco
    private $arreglo = [];
    public function setUp(): void
    { #Método llamado antes de cada prueba, inicializa los objetos a usar (funcion de phpUnit)
        $this->banco = new Banco();
    }

    #[Group('integration')]

    ##########
    # TESTS DEL CRUD DE banco #
    ##########
    //Resultado esperado 1 = éxito
    public function testCrearbanco()
    {
        $this->arreglo = [
            'nombre' => 'Bicentenario1',
        ];
        $this->banco->setDatos($this->arreglo);
        $this->banco->check();
        $resultado = $this->banco->getRegistrar();
        $this->assertEquals(1, $resultado);
    }
    //Resultado esperado 0 = error
    public function testCrearbancoFalse()
    {
        $this->arreglo = [
            'nombre' => 'Express',
        ];
        $this->banco->setDatos($this->arreglo);
        $this->banco->check();
        $resultado = $this->banco->getRegistrar();
        $this->assertEquals('error_nombre', $resultado);
    }

    //CONSULTA GLOBAL
    public function testConsultarbanco()
    {
        $resultado = $this->banco->consultar();
        $this->assertIsArray($resultado);
    }
    /*public function testConsultarbancoVacio()
    {
        $resultado = $this->banco->consultar();
        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado); // Verifica que el array está vacío
    }*/
    //BUSQUEDA DE banco
    public function testBuscarbanco()
    {
        $this->arreglo = [
            'nombre' => 'Bancrecer',
        ];
        $this->banco->setDatos($this->arreglo);
        $this->banco->check();
        $resultado = $this->banco->buscarPorNombre();
        $this->assertIsArray($resultado);
    }

    public function testBuscarbancofalse()
    {
        $this->arreglo = [
            'nombre' => 'vocé',
        ];
        $this->banco->setDatos($this->arreglo);
        $this->banco->check();
        $resultado = $this->banco->buscarPorNombre();
        $this->assertEquals(false, $resultado);
    }

    //EDICIÓN DE banco
    public function testEditarbanco()
    {
        $this->arreglo = [
            'nombre' => 'Mi Banco1',
            'origin' => 'Bicentenario1',
            'cod_banco' => 20,
        ];
        $this->banco->setDatos($this->arreglo);
        $this->banco->check();
        $resultado = $this->banco->getactualizar();
        $this->assertEquals(1, $resultado);
    }
    public function testEditarbancofalse()
    {
        $this->arreglo = [
            'nombre' => 'Bicentenario',
            'origin' => 'Express',
            'cod_banco' => 11,
        ];
        $this->banco->setDatos($this->arreglo);
        $this->banco->check();
        $resultado = $this->banco->getactualizar();
        $this->assertEquals('error_nombre', $resultado);
    }
    public function testEliminarbanco()
    {
        $this->arreglo = [
            'cod_banco' => 24,
        ];
        $this->banco->setDatos($this->arreglo);
        $this->banco->check();

        $resultado = $this->banco->getEliminar();
        $this->assertEquals(1, $resultado);
    }
    //INCLUIRÁ CUALQUIER ERROR QUE NO PERMITA ELIMINAR
    //FUNCIONARÁ SI EL RESULTADO ES DISTINTO DE SUCCESS
    public function testEliminarbancofalse()
    {
        $this->arreglo = [
            'cod_banco' => 2,
        ];
        $this->banco->setDatos($this->arreglo);
        $this->banco->check();

        $resultado = $this->banco->getEliminar();
        $this->assertEquals('error_cuenta', $resultado);
    }
}
