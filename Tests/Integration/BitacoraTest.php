<?php

use PHPUnit\Framework\TestCase;
use Modelo\Bitacora;

class BitacoraTest extends TestCase
{

    private $bitacora; #Instancia de la clase Bitacora
    public function setUp():void{ #Método llamado antes de cada prueba, inicializa los objetos a usar (funcion de phpUnit)
        $this->bitacora = new Bitacora();
    }

    //Resultado esperado 1 = éxito
    public function testCrearBitacora()
    {
        $resultado = $this->bitacora->registrarEnBitacora(1,'Registrar','Purebas unitarias en bitacora', 'bitacora');
        $this->assertEquals(null, $resultado);
    }

    //CONSULTA GLOBAL
    public function testConsultarBitacora()
    {
        $resultado = $this->bitacora->obtenerRegistros();
        $this->assertIsArray($resultado);
    }
    //INCLUIRÁ CUALQUIER ERROR QUE NO PERMITA ELIMINAR
    //FUNCIONARÁ SI EL RESULTADO ES DISTINTO DE SUCCESS
    public function testEliminarBitacorafalse(){
        $resultado = $this->bitacora->eliminarPorFechas('01-10-2005', '02-10-2005');
        $this->assertNotEquals(1, $resultado);
    }
    
}

