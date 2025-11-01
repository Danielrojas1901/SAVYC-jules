<?php

use PHPUnit\Framework\TestCase;
use Modelo\Unidad;

class UnidadTest extends TestCase
{
    private $unidad; #Instancia de la clase Unidad
    public function setUp(): void
    { #Método llamado antes de cada prueba, inicializa los objetos a usar (funcion de phpUnit)
        $this->unidad = new Unidad();
    }

    ##########
    # TESTS DEL CRUD DE UNIDAD #
    ##########
    //Resultado esperado 1 = éxito
    public function testCrearUnidad()
    {
        $this->unidad->setTipo('gramo');
        $resultado = $this->unidad->getcrearUnidad();
        $this->assertEquals(1, $resultado);
    }
    //Resultado esperado 0 = error
    public function testCrearUnidadFalse()
    {
        $this->unidad->setTipo(55);
        $resultado = $this->unidad->getcrearUnidad();
        $this->assertEquals(0, $resultado);
    }

    //CONSULTA GLOBAL
    public function testConsultarUnidad()
    {
        $resultado = $this->unidad->consultarUnidad();
        $this->assertIsArray($resultado);
    }
    public function testConsultarUnidadVacio()
    {
        $resultado = $this->unidad->consultarUnidad();
        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado); // Verifica que el array está vacío
    }
    //BUSQUEDA DE UNIDAD
    public function testBuscarUnidad()
    {
        $this->unidad->setTipo('gramo');
        $resultado = $this->unidad->getbuscar('gramo');
        $this->assertIsArray($resultado);
    }

    public function testBuscarUnidadfalse()
    {
        $this->unidad->setTipo('kilo');
        $resultado = $this->unidad->getbuscar('kilo');
        $this->assertEquals(false, $resultado);
    }

    //EDICIÓN DE UNIDAD
    public function testEditarUnidad()
    {
        $this->unidad->setCod(2);
        $this->unidad->setTipo('tonelada');
        $this->unidad->setStatus(0);
        $resultado = $this->unidad->geteditar();
        $this->assertEquals(1, $resultado);
    }
    public function testEditarUnidadfalse()
    {
        $this->unidad->setCod(5);
        $this->unidad->setTipo('jj');
        $this->unidad->setStatus(0);
        $resultado = $this->unidad->geteditar();
        $this->assertEquals(0, $resultado);
    }
    public function testEliminarUnidad()
    {
        $resultado = $this->unidad->geteliminar(2);
        $this->assertEquals('success', $resultado);
    }
    //INCLUIRÁ CUALQUIER ERROR QUE NO PERMITA ELIMINAR
    //FUNCIONARÁ SI EL RESULTADO ES DISTINTO DE SUCCESS
    public function testEliminarUnidadfalse()
    {
        $resultado = $this->unidad->geteliminar(1);
        $this->assertNotEquals('success', $resultado);
    }
}
