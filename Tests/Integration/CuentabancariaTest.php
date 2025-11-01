<?php

use PHPUnit\Framework\TestCase;
use Modelo\CuentaBancaria;

class CuentabancariaTest extends TestCase
{
    private $cuenta; #Instancia de la clase cuenta
    private $arreglo = [];
    public function setUp(): void
    { #Método llamado antes de cada prueba, inicializa los objetos a usar (funcion de phpUnit)
        $this->cuenta = new CuentaBancaria();
    }

    ##########
    # TESTS DEL SETTER Y GETTER
    ##########

    public function testSetAndGet()
    {
        $this->arreglo = [
            'numero_cuenta' => '1234567891123456',
            'cod_tipo_cuenta' => 1,
            'origin' => '12345678911234567',
            'cod_banco' => 1,
            'saldo' => 12,
            'divisa' => 1,
            'status' => 1,
            'cod_cuenta_bancaria' => 1,
        ];
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check(); // no lanza excepción
        $datos = $this->cuenta->getData();
        $this->assertEquals($this->arreglo, $datos);
    }
    public function testSetAndGetfalse()
    {
        $this->arreglo = [
            'numero_cuenta' => 'numeros de cuenta#$%&/()=',
            'cod_tipo_cuenta' => 1,
            'origin' => 'Numero de cuenta original*',
            'cod_banco' => '1',
            'saldo' => '12',
            'divisa' => '1',
            'status' => '1',
            'cod_cuenta_bancaria' => '1',
        ];
        $this->cuenta->setData($this->arreglo);
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Errores de validación.*/');
        $this->cuenta->check();
    }
    //CONTINUO MAÑANA

    ##########
    # TESTS DEL CRUD DE cuenta #
    ##########
    //Resultado esperado 1 = éxito
    public function testCrearcuenta()
    {
        $this->arreglo = [
            'numero_cuenta' => '1234567891123456',
            'cod_banco' => 1,
            'saldo' => 12.54,
            'divisa' => 1,
            'cod_tipo_cuenta' => 1,
        ];
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check();
        $resultado = $this->cuenta->getcrearCuenta();
        $this->assertEquals(1, $resultado);
    }
    //Resultado esperado 0 = error
    public function testCrearcuentaFalse()
    {
        $this->arreglo = [
            'numero_cuenta' => '1234567891123456',
            'cod_banco' => 1,
            'saldo' => 25,
            'divisa' => 1,
            'cod_tipo_cuenta' => 2,
        ];
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('La cuenta ya existe.');
        $resultado = $this->cuenta->getcrearCuenta();
    }

    //CONSULTA CUENTA
    public function testConsultarcuenta()
    {
        $resultado = $this->cuenta->consultarCuenta();
        $this->assertIsArray($resultado);
    }
    public function testConsultartipo()
    {
        $resultado = $this->cuenta->consultarTipo();
        $this->assertIsArray($resultado);
    }
    //BUSQUEDA DE cuenta
    public function testBuscarcuenta()
    {
        //NO FUNCIONA AL SER LLAMADA INTERNAMENTE NO TIENE LA CONEXIÓN A LA BASE DE DATOS, ESTA MAL DESARROLLADO, LO REGISTRARE PARA ACOMODARLO LUEGO. ES UN ERROR GRAVE
        $numero_cuenta = '01910073172173072299';
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check();
        $resultado = $this->cuenta->getbuscar($numero_cuenta);
        $this->assertIsArray($resultado);
    }

    //EDICIÓN DE cuenta
    public function testEditarcuenta()
    {
        $this->arreglo = [
            'numero_cuenta' => '789456123789456123',
            'cod_tipo_cuenta' => 1,
            'origin' => '01910073172173072299',
            'cod_banco' => 1,
            'saldo' => 125.45,
            'divisa' => 1,
            'status' => 0,
            'cod_cuenta_bancaria' => 1,
        ];
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check();
        $resultado = $this->cuenta->geteditar();
        $this->assertEquals(1, $resultado);
    }
    public function testEditarcuentafalse()
    {
        $this->arreglo = [
            'numero_cuenta' => '01910073172173072299', //QUE SEA UN DUPLICADO
            'cod_tipo_cuenta' => 1,
            'origin' => '1234567891123456',
            'cod_banco' => 1,
            'saldo' => 125.45,
            'divisa' => 1,
            'status' => 2,
            'cod_cuenta_bancaria' => 1,
        ];
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('La cuenta bancaria ya se encuentra en otro registro.');
        $resultado = $this->cuenta->geteditar();
    }
    public function testEliminarcuenta()
    {
        $valor = 6;
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check();

        $resultado = $this->cuenta->geteliminar($valor);
        $this->assertEquals('success', $resultado);
    }
    public function testEliminarcuentafalse1()
    {
        $valor = 19;
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check();

        $resultado = $this->cuenta->geteliminar($valor);
        $this->assertEquals('error_query', $resultado);
    }
    public function testEliminarcuentafalse2()
    {
        $valor = 3; //error status
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check();

        $resultado = $this->cuenta->geteliminar($valor);
        $this->assertEquals('error_status', $resultado);
    }
    public function testEliminarcuentafalse3()
    {
        $valor = 2; //tipo pago
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check();

        $resultado = $this->cuenta->geteliminar($valor);
        $this->assertEquals('error_tipo_pago', $resultado);
    }
    public function testEliminarcuentafalse4()
    {
        $valor = 5;
        $this->cuenta->setData($this->arreglo);
        $this->cuenta->check();

        $resultado = $this->cuenta->geteliminar($valor);
        $this->assertEquals('error_saldo', $resultado);
    }

    public function testObtenermovimientos()
    {
        $valor = 1;
        $fecha_inicio = '2022-01-01';
        $fecha_fin = '2022-12-31';

        $resultado = $this->cuenta->obtenerMovimientosCuentaBancaria($valor, $fecha_inicio, $fecha_fin);
        $this->assertIsArray($resultado);
    }
}
