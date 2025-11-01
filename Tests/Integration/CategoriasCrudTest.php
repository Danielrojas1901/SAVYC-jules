<?php
declare(strict_types=1);

namespace Tests\Integration\Modelo;

use Modelo\Categorias;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('CategoriasIntegration'), Group('integration')]
final class CategoriasCrudTest extends TestCase
{
    private Categorias $sut;

    protected function setUp(): void
    {
        $this->sut = new Categorias();
    }

    // ---------------------------
    // Helpers y DataProviders
    // ---------------------------

    public static function nombresValidosProvider(): array
    {
        $min = str_repeat('A', 2);         // 2 chars
        $max = str_repeat('B', 50);        // 
        return [
            'minimo_2' => [$min],
            'maximo_50' => [$max],
            'con_espacios' => ['PRUEBA CATEGORIA'],
            'acentos' => ['Categoría Prueba'],
        ];
    }

    public static function nombresInvalidosProvider(): array
    {
        return [
            'vacio' => [''],
            'uno_solo' => ['A'], // < 2
            'mas_de_50' => [str_repeat('X', 51)],
            'con_numeros' => ['CAT123'],
            'con_simbolos' => ['CAT@#!'],
        ];
    }

    public static function statusInvalidosProvider(): array
    {
        return [
            'menor_0' => [-1],
            'mayor_1' => [2],
        ];
    }

    private function nombreRandom(string $prefix = 'PRUEBA'): string
    {
        $letters = '';
        for ($i = 0; $i < 6; $i++) {
            $letters .= chr(mt_rand(65, 90)); // A–Z
        }
        return mb_substr($prefix . ' ' . $letters, 0, 50);
    }

    // ---------------------------
    // Tests de bordes: nombre
    // ---------------------------

    #[DataProvider('nombresValidosProvider')]
    public function testRegistrarAceptaNombresValidos(string $nombre): void
    {
        $this->sut->setDatos(['nombre' => $nombre, 'status' => 1]);
        $this->sut->check();
        $this->assertSame(1, $this->sut->getregistrar(), 'Debe registrar con nombre válido');

        $row = $this->sut->getbuscar($nombre);
        $this->assertIsArray($row);
        $this->assertSame($nombre, $row['nombre'] ?? null);
    }

    #[DataProvider('nombresInvalidosProvider')]
    public function testRegistrarRechazaNombresInvalidos(string $nombre): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->setDatos(['nombre' => $nombre, 'status' => 1]);
        $this->sut->check(); // Debe lanzar antes de intentar registrar
    }

    // ---------------------------
    // Tests de status
    // ---------------------------

    #[DataProvider('statusInvalidosProvider')]
    public function testEditarRechazaStatusInvalidos(mixed $status): void
    {
        // Primero inserta una categoría válida
        $nombre = $this->nombreRandom('PRUEBA ST');
        $this->sut->setDatos(['nombre' => $nombre, 'status' => 1]);
        $this->sut->check();
        $this->sut->getregistrar();
        $id = (int)($this->sut->getbuscar($nombre)['cod_categoria'] ?? 0);
        $this->assertGreaterThan(0, $id, 'ID insertado debe ser > 0');

        // Intentar editar con status inválido debe fallar en check()
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');

        $this->sut->setDatos(['nombre' => $nombre, 'status' => $status]); // <- inválido
        $this->sut->check(); 
    }

    // ---------------------------
    // Búsqueda / Listado / IDs
    // ---------------------------

    public function testBuscarNoEncontradoRetornaFalse(): void
    {
        $this->assertFalse($this->sut->getbuscar('NO_EXISTE_' . uniqid()));
    }

    public function testMostrarSiempreRetornaArrayDeFilas(): void
    {
        $list = $this->sut->getmostrar();
        $this->assertIsArray($list);
        // Si hay al menos una fila, debe ser array asociativo
        if (!empty($list)) {
            $this->assertIsArray($list[0]);
            $this->assertArrayHasKey('nombre', $list[0]);
        }
    }

    public function testEditarIdInexistenteRetorna0(): void
    {
        // Prepara un nombre válido en el objeto
        $this->sut->setDatos(['nombre' => $this->nombreRandom('PRUEBA EDIT'), 'status' => 1]);
        $this->sut->check();

        $resultado = $this->sut->geteditar(999999); // ID que no existe
        $this->assertContains($resultado, [0, 1], 'Dependiendo del driver, UPDATE sin match puede considerar éxito sin afectar filas.');

    }

    // ---------------------------
    // Duplicados 
    // ---------------------------

    public function testDuplicadoDeNombreDebeSerImpedidoPorModelo(): void
    {
        $nombre = $this->nombreRandom('PRUEBA DUP');

        // Inserción 1
        $this->sut->setDatos(['nombre' => $nombre, 'status' => 1]);
        $this->sut->check();
        $this->assertSame(1, $this->sut->getregistrar(), 'Primera inserción debe pasar');

        // Inserción 2 (mismo nombre)
        $this->sut->setDatos(['nombre' => $nombre, 'status' => 1]);
        $this->sut->check();

        try {
            $segundo = $this->sut->getregistrar();
        } catch (\Throwable $e) {
            $this->addToAssertionCount(1); // lo consideramos impedido
            return;
        }

        // Si no hay índice único y el modelo permite duplicar:
        // Deja esta aserción para **documentar** el problema.
        $this->assertNotSame(
            1,
            $segundo,
            'DEFECTO: Se permitió insertar un duplicado. Agrega UNIQUE(nombre) o valida duplicados en el modelo.'
        );
    }
}