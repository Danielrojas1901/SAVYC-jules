<?php

/*
declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Banco;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

class Bancos extends Banco
{
    public function __construct() {}
    public function conectarBD()
    { 
    }
    public function desconectarBD()
    { 
    } 
}

#[Group('unit')]
final class BancoTest extends TestCase
{

    private Bancos $obj;

    protected function setUp(): void
    {
        $this->obj = new Bancos();
    }

    public static function datos(): array
    {
        return [
            'nombre' => 'Express',
            'origin' => 'venezuela',
            'cod_banco' => 1,
        ];
    }
    //DATAS INDIVIDUALES
    public static function nombreInvalido():array{
        return[
            'min'=>[str_repeat('A', 0)],
            'max'=>[str_repeat('banco', 51)],
            'null'=>[null],
            'numerico'=>['1'],
            'caracteres'=>['!@#$%^&*()'],
        ];
    }

    public static function originInvalido():array{
        return[
            'min'=>[str_repeat('A', 0)],
            'max'=>[str_repeat('origin', 51)],
            'null'=>[null],
            'numerico'=>['1.55'],
            'caracteres'=>['!@#$%^&*()'],
        ];
    }

    public static function cod_bancoInvalido():array{
        return[
            'letra'=>['a'],
            'numerico'=>['1.55'],
            'caracteres'=>['!@#$%^&*()'],
            'vacio'=>[''],
            'null'=>[null],
        ];
    }

    #[DataProvider('datos')]
    public function testSetterGetter(array $datos): void
    {
        {/*NOMBRE INVALIDO=> DEFECTO ENCONTRADO }
        $this->obj->setDatos($datos);
        $this->obj->check();
        $this->assertSame($datos, $this->obj->getDatos());
    }

    // DATOS FALSOS
    public static function datosfalse(): array
    {
        return [
            'caso_invalido' => [[
                'nombre' => 2,
                'origin' => 'q',
                'cod_banco' => 'bnb',
            ]],
        ];
    }

    #[DataProvider('datosfalse')]
    public function testSetterGetterfalse(array $datosfalse): void
    {
        $this->obj->setDatos($datosfalse);
         $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Errores de validación.');
        $this->obj->check();
    }

    #[DataProvider(('nombreInvalido'))]
    public function testNombreInvalido(mixed  $nombre): void
    {
        try {
            $this->obj->setDatos(['nombre' => $nombre]);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema acepta nombres inválidos sin validación: '$nombre'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    

     #[DataProvider(('originInvalido'))]
    public function testOriginInvalido(mixed  $origin): void            
    {
        try {
            $this->obj->setDatos(['origin' => $origin]);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema acepta origin inválidos sin validación: '$origin'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }

    #[DataProvider(('cod_bancoInvalido'))]
    public function testCod_bancoInvalido(mixed  $cod_banco): void
    {
        try {
            $this->obj->setDatos(['cod_banco' => $cod_banco]);
            $this->obj->check();
            $this->fail("DEFECTO: El sistema acepta cod_banco inválidos sin validación: '$cod_banco'");
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
        }
    }
    
}
*/