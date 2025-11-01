<?php
declare(strict_types=1);

namespace Tests\Integration\Modelo;

use Modelo\CatalogoCuentas;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;


#[Group('CatalogoCuentasIntegration'), Group('integration'), ]
final class CatalogoCuentasCrudTest extends TestCase
{
    private CatalogoCuentas $sut;

    protected function setUp(): void
    {
        $_SESSION = ['cod_usuario' => 1]; // usuario admin
        $this->sut = new CatalogoCuentas();
    }
    
    public function testRegistrarCuentaDeNivel1(): void
    {
        try {
            $codigo = $this->sut->get_generarCodigo(1, null);
            $this->assertNotEmpty($codigo, 'Debe generar código para nivel 1');

            $this->sut->setDatos([
                'codigoContable' => (string)$codigo,
                'nombreCuenta'   => 'CUENTA NIVEL 1 ' . uniqid(),
                'naturaleza'     => 'deudora',
                'nivel'          => 1,
                'saldo'          => 0,
            ]);
            $this->sut->check();

            $res = $this->sut->getregistrar();
            $this->assertSame(1, $res, 'Debe registrar cuenta de nivel 1 correctamente');

        } catch (\Throwable $e) {
            $this->fail('Error al registrar cuenta de nivel 1: ' . $e->getMessage());
        }
    }

    public function testRegistrarCuentaHijaDeNivel2(): void
    {
        try {
            // Crear cuenta padre (nivel 1)
            $codigoPadre = $this->sut->get_generarCodigo(1, null);
            $nombrePadre = 'PADRE ' . uniqid();
            $this->sut->setDatos([
                'codigoContable' => (string)$codigoPadre,
                'nombreCuenta'   => $nombrePadre,
                'naturaleza'     => 'acreedora',
                'nivel'          => 1,
            ]);
            $this->sut->check();
            $this->assertSame(1, $this->sut->getregistrar(), 'Debe registrar la cuenta padre');

            $padre = $this->sut->getbuscar($nombrePadre);
            $this->assertIsArray($padre, 'Debe encontrar la cuenta padre');
            $idPadre = (int)($padre['cod_cuenta'] ?? 0);
            $this->assertGreaterThan(0, $idPadre, 'Debe tener un ID de padre válido');

            // Crear cuenta hija (nivel 2)
            $codigoHija = $this->sut->get_generarCodigo(2, $idPadre);
            $this->sut->setDatos([
                'codigoContable' => (string)$codigoHija,
                'nombreCuenta'   => 'HIJA ' . uniqid(),
                'naturaleza'     => 'acreedora',
                'cuentaPadre'    => $idPadre,
                'nivel'          => 2,
            ]);
            $this->sut->check();
            $res = $this->sut->getregistrar();
            $this->assertSame(1, $res, 'Debe registrar correctamente la cuenta hija de nivel 2');

        } catch (\Throwable $e) {
            $this->fail('Error al registrar cuenta hija: ' . $e->getMessage());
        }
    }

    public function testBuscarYConsultarCuentas(): void
    {
        try {
            $codigo = $this->sut->get_generarCodigo(1, null);
            $nombre = 'BUSCAR ' . uniqid();

            $this->sut->setDatos([
                'codigoContable' => (string)$codigo,
                'nombreCuenta'   => $nombre,
                'naturaleza'     => 'deudora',
                'nivel'          => 1,
            ]);
            $this->sut->check();
            $this->assertSame(1, $this->sut->getregistrar(), 'Debe registrar para luego buscar');

            $row = $this->sut->getbuscar($nombre);
            $this->assertIsArray($row, 'Debe poder buscar la cuenta registrada');
            $this->assertSame($nombre, $row['nombre_cuenta'] ?? null, 'El nombre debe coincidir');

            $tabla = $this->sut->getconsultar_cuentas();
            $this->assertIsArray($tabla, 'Debe retornar un arreglo al consultar cuentas');

        } catch (\Throwable $e) {
            $this->fail('Error en buscar o consultar cuentas: ' . $e->getMessage());
        }
    }

    public function testEditarCuentaContable(): void
    {
        try {
            // Crear cuenta
            $codigo = $this->sut->get_generarCodigo(1, null);
            $nombre = 'EDITAR ' . uniqid();
            $this->sut->setDatos([
                'codigoContable' => (string)$codigo,
                'nombreCuenta'   => $nombre,
                'naturaleza'     => 'deudora',
                'nivel'          => 1,
            ]);
            $this->sut->check();
            $this->assertSame(1, $this->sut->getregistrar(), 'Debe registrar para luego editar');

            $row = $this->sut->getbuscar($nombre);
            $id  = (int)($row['cod_cuenta'] ?? 0);
            $this->assertGreaterThan(0, $id, 'Debe obtener ID válido');

            // Editar nombre (manteniendo naturaleza)
            $nuevoNombre = $nombre . ' - MOD';
            $res = $this->sut->geteditar([
                'codigocuenta' => $id,
                'nombreCuenta' => $nuevoNombre,
                'naturaleza'   => 'deudora',
            ]);
            $this->assertSame(1, $res, 'Debe retornar 1 al editar');

            $row2 = $this->sut->getbuscar($nuevoNombre);
            $this->assertIsArray($row2, 'Debe encontrar la cuenta editada');

        } catch (\Throwable $e) {
            $this->fail('Error al editar cuenta contable: ' . $e->getMessage());
        }
    }

    public function testEliminarCuentaDeNivel5(): void
    {
        try {
            // Cadena de cuentas hasta nivel 5 (acreedora)
            // Nivel 1
            $codigo1 = $this->sut->get_generarCodigo(1, null);
            $nombre1 = 'DEL N1 ' . uniqid();
            $this->sut->setDatos([
                'codigoContable' => (string)$codigo1,
                'nombreCuenta'   => $nombre1,
                'naturaleza'     => 'acreedora',
                'nivel'          => 1,
            ]);
            $this->sut->check();
            $this->assertSame(1, $this->sut->getregistrar(), 'Debe registrar N1');
            $n1 = $this->sut->getbuscar($nombre1);

            // Nivel 2
            $codigo2 = $this->sut->get_generarCodigo(2, (int)$n1['cod_cuenta']);
            $nombre2 = 'DEL N2 ' . uniqid();
            $this->sut->setDatos([
                'codigoContable' => (string)$codigo2,
                'nombreCuenta'   => $nombre2,
                'naturaleza'     => 'acreedora',
                'cuentaPadre'    => (int)$n1['cod_cuenta'],
                'nivel'          => 2,
            ]);
            $this->sut->check();
            $this->sut->getregistrar();
            $n2 = $this->sut->getbuscar($nombre2);

            // Nivel 3
            $codigo3 = $this->sut->get_generarCodigo(3, (int)$n2['cod_cuenta']);
            $nombre3 = 'DEL N3 ' . uniqid();
            $this->sut->setDatos([
                'codigoContable' => (string)$codigo3,
                'nombreCuenta'   => $nombre3,
                'naturaleza'     => 'acreedora',
                'cuentaPadre'    => (int)$n2['cod_cuenta'],
                'nivel'          => 3,
            ]);
            $this->sut->check();
            $this->sut->getregistrar();
            $n3 = $this->sut->getbuscar($nombre3);

            // Nivel 4
            $codigo4 = $this->sut->get_generarCodigo(4, (int)$n3['cod_cuenta']);
            $nombre4 = 'DEL N4 ' . uniqid();
            $this->sut->setDatos([
                'codigoContable' => (string)$codigo4,
                'nombreCuenta'   => $nombre4,
                'naturaleza'     => 'acreedora',
                'cuentaPadre'    => (int)$n3['cod_cuenta'],
                'nivel'          => 4,
            ]);
            $this->sut->check();
            $this->sut->getregistrar();
            $n4 = $this->sut->getbuscar($nombre4);

            // Nivel 5 (hoja a eliminar)
            $codigo5 = $this->sut->get_generarCodigo(5, (int)$n4['cod_cuenta']);
            $nombre5 = 'DEL N5 ' . uniqid();
            $this->sut->setDatos([
                'codigoContable' => (string)$codigo5,
                'nombreCuenta'   => $nombre5,
                'naturaleza'     => 'acreedora',
                'cuentaPadre'    => (int)$n4['cod_cuenta'],
                'nivel'          => 5,
            ]);
            $this->sut->check();
            $this->sut->getregistrar();
            $n5 = $this->sut->getbuscar($nombre5);
            $id5 = (int)$n5['cod_cuenta'];

            $res = $this->sut->geteliminar($id5);
            $this->assertSame(1, $res, 'Debe eliminar cuenta de nivel 5');

        } catch (\Throwable $e) {
            $this->fail('Error al eliminar cuenta contable: ' . $e->getMessage());
        }
    }

    public function testListarCuentasPadresPorNivel(): void
    {
        try {
            // Crear al menos una cuenta de nivel 1
            $codigo = $this->sut->get_generarCodigo(1, null);
            $this->sut->setDatos([
                'codigoContable' => (string)$codigo,
                'nombreCuenta'   => 'PADRES ' . uniqid(),
                'naturaleza'     => 'deudora',
                'nivel'          => 1,
            ]);
            $this->sut->check();
            $this->sut->getregistrar();

            $padres = $this->sut->get_listarcuentaspadrespornivel(2);
            $this->assertIsArray($padres, 'Debe listar cuentas padre del nivel anterior');

        } catch (\Throwable $e) {
            $this->fail('Error al listar cuentas padre: ' . $e->getMessage());
        }
    }
}
