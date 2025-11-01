<?php

use PHPUnit\Framework\TestCase;
use Modelo\CategoriaGasto;

class CategoriagastoTest extends TestCase
{
    private $gasto; #Instancia de la clase gasto
    private $arreglo = [];
    public function setUp(): void
    { #Método llamado antes de cada prueba, inicializa los objetos a usar (funcion de phpUnit)
        $this->gasto = new CategoriaGasto();
    }
    ##########
    # TESTS DEL CRUD DE frecuencia #
    ##########
    //Resultado esperado 1 = éxito
    public function testCrearF()
    {
        $this->arreglo = [
            'frecuencia' => 'Nuevodia',
            'dias' => 17,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->publicregistrarf();
        $this->assertEquals(1, $resultado);
    }
    //Resultado esperado 0 = error
    public function testCrearFalseF()
    {
        $this->arreglo = [
            'frecuencia' => 'diario',
            'dias' => 7,
        ];
        $this->gasto->setDatos($this->arreglo);
        $resultado = $this->gasto->publicregistrarf();
        $this->assertEquals(false, $resultado);
    }

    //REGISTRO DE CATEGORAS
    public function testCrearC()
    {
        $this->arreglo = [
            'frecuenciaC' => 3, //cod_frecuencia
            'naturaleza' => 1, //cod_naturaleza
            'tipogasto' => 2, // cod_tipo_gasto
            'nombre' => 'Proimca', //nombre
            'fecha' => '2001-01-01', //fecha
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->publicregistrarc();
        $this->assertEquals(1, $resultado);
    }

    //Resultado esperado 0 = error
    public function testCrearCFalse()
    {
        $this->arreglo = [
            'frecuenciaC' => 4, //cod_frecuencia
            'naturaleza' => 2, //cod_naturaleza
            'tipogasto' => 1, // cod_tipo_gasto
            'nombre' => 'Cafeteria', //nombre
            'fecha' => '2011-01-01', //fecha
        ];
        $this->gasto->setDatos($this->arreglo);
        $resultado = $this->gasto->publicregistrarc();
        $this->assertEquals(2, $resultado);
    }

    //CONSULTA FRECUENCIA
    public function testConsultarf()
    {
        $resultado = $this->gasto->consultarFrecuencia();
        $this->assertIsArray($resultado);
    }
    public function testConsultarfVacio()
    {
        $resultado = $this->gasto->consultarFrecuencia();
        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado); // Verifica que el array está vacío
    }

    //CONSULTAR TIPO DE GASTO
    public function testConsultarT()
    {
        $resultado = $this->gasto->consultarTipo();
        $this->assertIsArray($resultado);
    }

    //CONSULTAR CATEGORIA DE GASTO
    public function testConsultarC()
    {
        $resultado = $this->gasto->consultarCategoria();
        $this->assertIsArray($resultado);
    }
    public function testConsultarVacioC()
    {
        $resultado = $this->gasto->consultarCategoria();
        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado); // Verifica que el array está vacío
    }

    //CONSULTAR CONDICION DE PAGO
    public function testConsultarPE()
    {
        $resultado = $this->gasto->consultarCondi();
        $this->assertIsArray($resultado);
    }

    //CONSULTAR CONDICION DE PAGO
    public function testConsultarN()
    {
        $resultado = $this->gasto->consulNaturaleza();
        $this->assertIsArray($resultado);
    }

    //OTRO CONSULTAR XD

    public function testmostrarporN()
    {
        $this->arreglo = [
            'cod_cat_gasto' => 7,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->mostrarFVporN();
        $this->assertIsArray($resultado);
    }

    //BUSQUEDA DE  categoria de gasto
    public function testBuscar()
    {
        $this->arreglo = [
            'nombre' => 'internet',
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->buscarCategoria();
        $this->assertIsArray($resultado);
    }

    public function testBuscarfalse()
    {
        $this->arreglo = [
            'nombre' => 'servitelllllllll',
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->buscarCategoria();
        $this->assertEquals(false, $resultado);
    }

    public function testBuscarTxC()
    {
        $this->arreglo = [
            'cod_cat_gasto' => 5,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->buscarTporCategoria();
        $this->assertIsArray($resultado);
    }

    public function testBuscarTxCfalse()
    {
        $this->arreglo = [
            'cod_cat_gasto' => 25,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->buscarTporCategoria();
        $this->assertEquals(false, $resultado);
    }

    public function testBuscarF()
    {
        $this->arreglo = [
            'frecuencia' => 'Mensual',
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->buscarFrecuencia();
        $this->assertIsArray($resultado);
    }

    public function testBuscarfalseF()
    {
        $this->arreglo = [
            'frecuencia' => '15dias',
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->buscarFrecuencia();
        $this->assertEquals(false, $resultado);
    }

    //EDICIÓN DE gasto
    public function testEditarC()
    {
        $this->arreglo = [
            'nombre' => 'Nuevo inter',
            'origin' => 'Servitel',
            'cod_cat_gasto' => 7,
            'status_cat_gasto' => 1,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->editarC();
        $this->assertEquals(1, $resultado);
    }
    public function testEditarCfalse1()
    {
        $this->arreglo = [
            'nombre' => 'Internets',
            'origin' => 'Internet',
            'cod_cat_gasto' => 15,
            'status_cat_gasto' => 2,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->editarC();
        $this->assertEquals('error_query', $resultado);
    }
    public function testEditarCfalse2()
    {
        $this->arreglo = [
            'nombre' => 'Proimca',
            'origin' => 'Internet',
            'cod_cat_gasto' => 5,
            'status_cat_gasto' => 0,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();
        $resultado = $this->gasto->editarC();
        $this->assertEquals('error_associated', $resultado);
    }

    public function testEliminargasto()
    {
        $this->arreglo = [
            'cod_cat_gasto' => 6,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();

        $resultado = $this->gasto->eliminarCat();
        $this->assertEquals('success', $resultado);
    }
    //LO TERMINO LUEGO LO DEJE EN ELIMINAR, POR EVALUACION DE IO
    public function testEliminargastofalse()
    {
        $this->arreglo = [
            'cod_cat_gasto' => 5,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();

        $resultado = $this->gasto->eliminarCat();
        $this->assertEquals('error_associated', $resultado);
    }
    public function testEliminargastofalse2()
    {
        $this->arreglo = [
            'cod_cat_gasto' => 7,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();

        $resultado = $this->gasto->eliminarCat();
        $this->assertEquals('error_status', $resultado);
    }
    public function testEliminargastofalse3()
    {
        $this->arreglo = [
            'cod_cat_gasto' => 1,
        ];
        $this->gasto->setDatos($this->arreglo);
        $this->gasto->check();

        $resultado = $this->gasto->eliminarCat();
        $this->assertEquals('error_delete', $resultado);
    }

    //TENGO ERRORES EN ELIMINAR
}
