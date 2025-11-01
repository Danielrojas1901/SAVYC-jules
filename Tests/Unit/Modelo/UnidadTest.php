<?php

declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Unidad;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

class Unidads extends Unidad
{
    public function __construct() {}
    public function conectarBD() {}
    public function desconectarBD() {}
}

#[Group('unit')]
final class UnidadTest extends TestCase
{

    private Unidads $obj;

    protected function setUp(): void
    {
        $this->obj = new Unidads();
    }
    //AQUÍ SOLO SE VALIDA UN PARAMETRO COSA QUE ESTA MAL
    public static function tipo_medida():array{
        return[
            'tipo_medida'=>['gramo'],
            'tipo_medida'=>['tonelada'],
            'tipo_medida'=>['kilo'],
            'tipo_medida'=>['litro'],
            'tipo_medida'=>['metro'],
        ];
    }
    public static function tipo_medidaInvalid():array{
        return[
            'min'=>[str_repeat('a',1)],
            'max'=>[str_repeat('b',51)],
            'numerico'=>['1234'],
            'caracteres'=>['!@#$%'],
            'vacio'=>[''],
            'null'=>[null],
        ];
    }

    #[DataProvider('tipo_medida')]
    public function testSetTipo(mixed $tipo_medida): void
    {
        $this->obj->setTipo($tipo_medida);
        $this->obj->check();
        $this->assertSame($tipo_medida, $this->obj->getTipo());
    }
    #[DataProvider('tipo_medidaInvalid')]
    public function testSetTipoInvalido(mixed $tipo_medida): void
    {
        $this->obj->setTipo($tipo_medida);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Errores de validación.*/');
        $this->obj->check();
    }



}
