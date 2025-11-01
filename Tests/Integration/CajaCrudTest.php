<?php
declare(strict_types=1);

namespace Tests\Integration\Modelo;

use Modelo\Caja;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('CajaIntegration'), Group('integration')]
final class CajaCrudTest extends TestCase
{
    private Caja $sut;

    protected function setUp(): void
    {
        $this->sut = new Caja();
    }

    // ---------------------------
    // Helpers y DataProviders
    // ---------------------------

    public static function nombresValidosProvider(): array
    {
        return [
            'minimo_1'     => ['A'],
            'normal'       => ['Caja testing'],
            'maximo_50'    => [str_repeat('B', 50)],
            'acentos'      => ['Caja Pública'],
            'con_espacios' => ['Caja Sucursal Oeste'],
        ];
    }

    public static function nombresInvalidosProvider(): array
    {
        return [
            'vacio'        => [''],
            'largo_51'     => [str_repeat('X', 51)],
            'con_numeros'  => ['CAJA123'],
            'con_simbolos' => ['Caja@#!'],
        ];
    }

    public static function statusInvalidosProvider(): array
    {
        // En tu modelo se valida status como numérico entre 1 y 2 (no acepta 0)
        return [
            'menor_1' => [0],
            'mayor_2' => [3],
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
    // Crear / Validar nombre
    // ---------------------------

    #[DataProvider('nombresValidosProvider')]
    public function testCrearAceptaNombresValidos(string $nombre): void
    {
        $this->sut->setData([
            'nombre'     => $nombre,
            'cod_divisa' => 2,          // USD (asumiendo existe en BD)
            'saldo'      => 150.50,
            'status'     => 1
        ]);
        $this->sut->check();

        $this->assertSame(1, $this->sut->getcrearCaja(), 'Debe registrar con datos válidos');

        $row = $this->sut->getbuscar($nombre);
        $this->assertIsArray($row);
        $this->assertSame($nombre, $row['nombre'] ?? null);
        $this->assertSame('150.50', $row['saldo'] ?? null);
        $this->assertTrue(isset($row['cod_divisas']), 'Debe guardar divisa (columna cod_divisas)');
    }

    #[DataProvider('nombresInvalidosProvider')]
    public function testCrearRechazaNombresInvalidos(string $nombre): void
    {
        $this->sut->setData([
            'nombre'     => $nombre,
            'cod_divisa' => 1,
            'saldo'      => 0,
            'status'     => 1
        ]);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check(); // Debe lanzar antes de intentar insertar
    }

    // ---------------------------
    // Búsqueda / Listado
    // ---------------------------

    public function testBuscarNoEncontradoRetornaFalse(): void
    {
        $this->assertFalse($this->sut->getbuscar('NO_EXISTE_' . uniqid()));
    }

    public function testConsultarCajaSiempreRetornaArray(): void
    {
        $list = $this->sut->consultarCaja();
        $this->assertIsArray($list);
        if (!empty($list)) {
            $this->assertIsArray($list[0]);
            $this->assertArrayHasKey('nombre', $list[0]);
            // opcionales si el JOIN existe
            $this->assertArrayHasKey('divisa', $list[0]);
            $this->assertArrayHasKey('status_control', $list[0]);
        }
    }

    // ---------------------------
    // Editar
    // ---------------------------

    public function testEditarActualizaCuandoNoHayAperturaAbierta(): void
    {
        // 1) Crear caja válida
        $nombre = $this->nombreRandom('CAJA EDIT');
        $this->sut->setData([
            'nombre'     => $nombre,
            'cod_divisa' => 1,
            'saldo'      => 0,
            'status'     => 1
        ]);
        $this->sut->check();
        $this->sut->getcrearCaja();

        // 2) Buscar ID
        $row = $this->sut->getbuscar($nombre);
        $this->assertIsArray($row);
        $id = (int)($row['cod_caja'] ?? 0);
        $this->assertGreaterThan(0, $id);

        // 3) Editar
        $nuevoNombre = $this->nombreRandom('CAJA EDITADA');
        $this->sut->setData([
            'cod_caja' => $id,
            'nombre'   => $nuevoNombre,
            'saldo'    => 200.00,
            'status'   => 0
        ]);
        $this->sut->check();

        $resultado = $this->sut->geteditar();
        $this->assertTrue($resultado === true || $resultado === 1, 'Editar debe retornar true/1 en éxito');

        // 4) Verificar cambios
        $row2 = $this->sut->getbuscar($nuevoNombre);
        $this->assertIsArray($row2);
        $this->assertSame('200.00', $row2['saldo'] ?? null);
        $this->assertSame(0, (int)($row2['status'] ?? -1));
    }

    #[DataProvider('statusInvalidosProvider')]
    public function testEditarRechazaStatusInvalidos(mixed $status): void
    {
        // Crear base
        $nombre = $this->nombreRandom('CAJA ST');
        $this->sut->setData([
            'nombre'     => $nombre,
            'cod_divisa' => 1,
            'saldo'      => 0,
            'status'     => 1
        ]);
        $this->sut->check();
        $this->sut->getcrearCaja();

        $id = (int)($this->sut->getbuscar($nombre)['cod_caja'] ?? 0);
        $this->assertGreaterThan(0, $id);

        // Intentar editar con status inválido debe fallar en check()
        $this->sut->setData([
            'cod_caja' => $id,
            'nombre'   => $nombre,
            'saldo'    => 0,
            'status'   => $status
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();
    }

    // ---------------------------
    // Eliminar
    // ---------------------------

    public function testEliminarSuccessConReglasCumplidas(): void
    {
        // Inactiva (status 0), saldo 0, sin tipos de pago, sin control abierto
        $nombre = $this->nombreRandom('CAJA DEL OK');
        $this->sut->setData([
            'nombre'     => $nombre,
            'cod_divisa' => 1,
            'saldo'      => 0.00,
            'status'     => 0
        ]);
        $this->sut->check();
        $this->sut->getcrearCaja();

        $id = (int)($this->sut->getbuscar($nombre)['cod_caja'] ?? 0);
        $this->assertGreaterThan(0, $id);

        $res = $this->sut->geteliminar($id);
        $this->assertSame('success', $res);

        $this->assertFalse($this->sut->getbuscar($nombre), 'La caja eliminada no debería encontrarse');
    }

    // ---------------------------
    // Duplicados (documenta la regla)
    // ---------------------------

    public function testDuplicadoDeNombreDebeSerImpedidoPorModelo(): void
    {
        $nombre = $this->nombreRandom('CAJA DUP');

        $this->sut->setData([
            'nombre'     => $nombre,
            'cod_divisa' => 1,
            'saldo'      => 0,
            'status'     => 1
        ]);
        $this->sut->check();
        $this->assertSame(1, $this->sut->getcrearCaja(), 'Primera inserción debe pasar');

        // Intento de duplicado
        $this->sut->setData([
            'nombre'     => $nombre,
            'cod_divisa' => 1,
            'saldo'      => 0,
            'status'     => 1
        ]);
        $this->sut->check();

        try {
            $segundo = $this->sut->getcrearCaja();
        } catch (\Throwable $e) {
            $this->addToAssertionCount(1); // Impedido por excepción del driver/modelo
            return;
        }

        // Si el modelo retorna 0/false ante duplicado, también vale como impedido
        $this->assertNotSame(1, $segundo, 'DEFECTO: Se permitió duplicado. Asegura UNIQUE(nombre) o validación previa.');
    }
}
