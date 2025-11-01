<?php
declare(strict_types=1);
namespace Tests\Unit\Modelo;
use Modelo\Categorias;
use PHPUnit\Framework\TestCase;

// Atributos de PHPUnit (PHP 8+). DataProvider permite ejecutar un mismo test con varios datos.
// Group sirve para etiquetar la clase o métodos y luego ejecutar por grupo (--group unit).
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/*
 * Stub que evita conexiones reales a BD.
 * Extendemos la clase real para probar su lógica, pero “anulamos” la conexión.
 */
class CategoriasStub extends Categorias
{
    public function __construct() {}     // Constructor vacío: no llama al padre (no abre conexión ni lee $_ENV)
    public function conectarBD()   {  }  
    public function desconectarBD(){  }  
}


#[Group('Categorias'), Group('unit')]
final class CategoriasTest extends TestCase
{
    private CategoriasStub $sut;

    /*
     * setUp() se ejecuta ANTES de cada test. Aquí instanciamos el SUT para que
     * cada prueba sea independiente (sin “arrastrar” estado de otras).
     */
    protected function setUp(): void
    {
        $this->sut = new CategoriasStub();
    }

    /** ---- PRUEBAS BÁSICAS / ESTADO INICIAL ---- */
    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getNombre());
        $this->assertNull($this->sut->getStatus());
    }

    /** ---- PARTICIÓN + LÍMITES PARA nombre (min=2, max=50) ---- */
    public static function nombresValidos(): array
    {
        return [
            'minimo 2'      => ['Hi'],
            'medio'         => ['Higiene'],
            'maximo 50'     => [str_repeat('a', 50)],
        ];
    }

    #[DataProvider('nombresValidos')]
    public function testSetDatosConNombreValidoNoLanza(string $nombre): void
    {
        $this->sut->setDatos(['nombre' => $nombre, 'status' => 1]);
        $this->sut->check();

        // assertSame compara valor y tipo estrictamente
        $this->assertSame($nombre, $this->sut->getNombre());
        $this->assertSame(1, $this->sut->getStatus());
    }

    public static function nombresInvalidos(): array
    {
        return [
            'vacio'         => [''],
            'solo 1 char'   => ['A'],
            'mas de 50'     => [str_repeat('a', 51)],
        ];
    }

    #[DataProvider('nombresInvalidos')]
    public function testSetDatosConNombreInvalidoLanza(string $nombre): void
    {
        $this->sut->setDatos(['nombre' => $nombre, 'status' => 1]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();

        //$this->assertArrayHasKey('nombre', $this->sut->getErrores()); //muestra los errores
    }

    /** ---- STATUS: partición válida (0/1) vs inválida ---- */
    public function testStatusValidoNoLanza(): void
    {
        $this->sut->setDatos(['nombre' => 'Accesorios', 'status' => 1]);
        $this->sut->check();
        $this->assertSame(1, $this->sut->getStatus());
    }

    public function testStatusInvalidoAcumulaError(): void
    {
        $this->sut->setDatos(['nombre' => 'Accesorios', 'status' => 99]);

        $errores = $this->sut->getErrores();
        $this->assertArrayHasKey('status', $errores);

        $this->expectException(\Exception::class);
        $this->sut->check();
    }

    /** ---- MULTIPLES ERRORES: agrupa mensajes ---- */

    /**
     * Aquí metemos DOS errores a la vez (nombre inválido + status inválido).
     * Se usa try/catch para además inspeccionar el mensaje de excepción
     * y verificar que ambos campos están en el arreglo de errores.
     */
    public function testCheckAgrupaMultiplesErrores(): void
    {
        $this->sut->setDatos(['nombre' => '', 'status' => 99]);

        try {
            $this->sut->check();
            // Si no lanza, forzamos fallo del test.
            $this->fail('Debió lanzar excepción por errores de validación');
        } catch (\Exception $e) {
            // Verificamos que el mensaje incluya el texto base (sin acoplarnos a todo el string).
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
            $this->assertArrayHasKey('nombre', $this->sut->getErrores());
            $this->assertArrayHasKey('status', $this->sut->getErrores());
        }
    }

    /** ---- PRECEDENCIA: si vienen statusDelete y status, gana status (el último en setDatos) ---- */
    public function testPrecedenciaCuandoHayStatusDeleteYStatus(): void
    {
        $this->sut->setDatos([
            'nombre'       => 'Quesos',
            'statusDelete' => 0,  
            'status'       => 1, 
        ]);
        $this->sut->check();
        $this->assertSame(1, $this->sut->getStatus());
    }

    /** ---- ROBUSTEZ: claves desconocidas se ignoran ---- */
    public function testClavesDesconocidasSeIgnoran(): void
    {
        $this->sut->setDatos(['foo' => 'bar']); // no debería romper
        // Sin datos válidos, check no debería lanzar (tampoco hay errores)
        $this->sut->check();

        $this->assertNull($this->sut->getNombre());
        $this->assertNull($this->sut->getStatus());
        $this->assertSame([], $this->sut->getErrores());
    }
}
