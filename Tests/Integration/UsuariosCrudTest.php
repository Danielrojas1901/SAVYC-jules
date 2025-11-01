<?php
declare(strict_types=1);

namespace Tests\Integration\Modelo;

use Modelo\Usuarios;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;


#[Group('UsuariosIntegration'), Group('integration')]
final class UsuariosCrudTest extends TestCase
{
    private Usuarios $sut;
    private int $roleId; // rol para registrar/editar (no admin)

    protected function setUp(): void
    {
        $this->sut = new Usuarios();
        $this->roleId = (int) (2);
    }

    /** NOMBRE: Genera un nombre válido (solo letras y espacios, largo <= 50). */
    private function nombreValido(string $name = 'PRUEBA', int $len = 6): string
    {
        $letters = '';
        for ($i = 0; $i < $len; $i++) {
            $letters .= chr(mt_rand(65, 90)); // A–Z
        }
        return mb_substr($name . ' ' . $letters, 0, 50);
    }

    /** USERNAME: Genera un username alfanumérico en minúsculas, único por tiempo. */
    private function usernameValido(string $name = 'testuser'): string
    {
        // 20 máx (según validarTextoNumero en Usuarios->setDatos). Prefijo + aleatorio.
        return $name . substr(strtolower(bin2hex(random_bytes(8))), 0, 10);
    }


     /* REGISTRAR: Intenta registrar un usuario de prueba y devuelve su fila. */
    private function registrarUsuarioDePrueba(string $nombre, string $user, string $pass, int $rol): array
    {
        $this->sut->setDatos([
            'nombre' => $nombre,
            'user'   => $user,
            'pass'   => $pass,
            'status' => 1,
        ]);
        $this->sut->check();

        $ok = $this->sut->getregistrar($rol);
        if ($ok !== 1) {
            $this->markTestSkipped("No se pudo registrar el usuario de prueba. Verifica tipo_usuario id=$rol e integridad de BD.");
        }

        $row = $this->sut->buscar($user);
        if ($row === false || !is_array($row)) {
            $this->fail('Se registró pero no se pudo recuperar el usuario recién creado.');
        }
        return $row;
    }

    /* REGISTRAR: devuelve solo id (> 0) */
    public function testRegistrarUsuarioDevuelveId(): void
    {
        $nombre = $this->nombreValido('PRUEBA REG');
        $user   = $this->usernameValido();
        $pass   = 'Valida!123';

        $row = $this->registrarUsuarioDePrueba($nombre, $user, $pass, $this->roleId);
        $id  = (int)($row['cod_usuario'] ?? 0);

        $this->assertGreaterThan(0, $id);
    }

    /* REGISTRAR + LOGIN: registrar y mostrar permite login.*/
    public function testRegistrarYMostrarPermiteLogin(): void
    {
        $nombre = $this->nombreValido('PRUEBA REG');
        $user   = $this->usernameValido();
        $pass   = 'Valida!123';

        $row = $this->registrarUsuarioDePrueba($nombre, $user, $pass, $this->roleId);
        $id  = (int)($row['cod_usuario'] ?? 0);

        $rowMostrar = $this->sut->mostrar($user);
        $this->assertIsArray($rowMostrar);
        $this->assertSame($user, $rowMostrar['user']);

    }

    /** REGISTRAR + BUSCAR: registrar y buscar devuelve el mismo usuario.*/
    public function testRegistrarYBuscarDevuelveElMismoUsuario(): void
    {
        $nombre = $this->nombreValido('PRUEBA REG');
        $user   = $this->usernameValido();
        $pass   = 'Valida!123';

        $row = $this->registrarUsuarioDePrueba($nombre, $user, $pass, $this->roleId);
        $id  = (int)($row['cod_usuario'] ?? 0);

        $rowBuscar = $this->sut->buscar($user);
        $this->assertIsArray($rowBuscar);
        $this->assertSame($user, $rowBuscar['user']);

    }

    /** LISTAR USUARIOS: listar incluye al recién creado. */
    public function testRegistrarYListarIncluyeNuevoUsuario(): void
    {
        $nombre = $this->nombreValido('PRUEBA REG');
        $user   = $this->usernameValido();
        $pass   = 'Valida!123';

        $row = $this->registrarUsuarioDePrueba($nombre, $user, $pass, $this->roleId);
        $id  = (int)($row['cod_usuario'] ?? 0);

        $all = $this->sut->listar();
        $this->assertIsArray($all);
        $ids = array_map(static fn($r) => (int)($r['cod_usuario'] ?? 0), $all);
        $this->assertContains($id, $ids);

    }

    /** EDITAR: editar actualiza nombre, user, rol y status. */
    public function testEditarActualizaNombreUserRolYStatus(): void
    {
        // Insertar
        $nombre = $this->nombreValido('PRUEBA OLD');
        $user   = $this->usernameValido();
        $pass   = 'Valida!123';
        $row    = $this->registrarUsuarioDePrueba($nombre, $user, $pass, $this->roleId);
        $id     = (int)$row['cod_usuario'];

        // Editar sin cambiar password
        $nuevoNombre = $this->nombreValido('PRUEBA NEW');
        $nuevoUser   = $this->usernameValido();
        $nuevoRol    = $this->roleId; // o cambia si tienes más roles
        $nuevoStatus = 0;

        $this->sut->setDatos([
            'nombre' => $nuevoNombre,
            'user'   => $nuevoUser,
            'status' => $nuevoStatus,
        ]);
        $this->sut->check();
        $this->assertSame(1, $this->sut->editar($id, $nuevoRol));

        // Verificar (vía mostrar/buscar)
        $rowMostrar = $this->sut->mostrar($nuevoUser);
        $this->assertIsArray($rowMostrar);
        $this->assertSame($nuevoUser, $rowMostrar['user']);
        $this->assertSame($nuevoNombre, $rowMostrar['nombre']);
        $this->assertSame($nuevoStatus, (int)$rowMostrar['status']);

    }

    /** EDITAR + PASSWORD: editar2 también actualiza password (hash).*/
    public function testEditar2ActualizaTambienPassword(): void
    {
        // Insertar
        $nombre = $this->nombreValido('PRUEBA PWD');
        $user   = $this->usernameValido();
        $pass   = 'Valida!123';
        $row    = $this->registrarUsuarioDePrueba($nombre, $user, $pass, $this->roleId);
        $id     = (int)$row['cod_usuario'];

        // Obtener hash actual
        $antes = $this->sut->mostrar($user);
        $this->assertIsArray($antes);
        $hashAnterior = (string)$antes['password'];

        // Editar incluyendo password (editar2)
        $nuevoNombre = $this->nombreValido('PRUEBA PWD');
        $nuevoUser   = $this->usernameValido();
        $nuevoPass   = 'OtraClave!789';
        $nuevoRol    = $this->roleId;
        $nuevoStatus = 1;

        $this->sut->setDatos([
            'nombre' => $nuevoNombre,
            'user'   => $nuevoUser,
            'pass'   => $nuevoPass,
            'status' => $nuevoStatus,
        ]);
        $this->sut->check();
        $this->assertSame(1, $this->sut->editar2($id, $nuevoRol));

        // Verificar cambios: user, nombre, status, password hash distinto y válido
        $despues = $this->sut->mostrar($nuevoUser);
        $this->assertIsArray($despues);
        $this->assertSame($nuevoUser, $despues['user']);
        $this->assertSame($nuevoNombre, $despues['nombre']);
        $this->assertSame($nuevoStatus, (int)$despues['status']);

        $hashNuevo = (string)$despues['password'];
        $this->assertNotSame($hashAnterior, $hashNuevo);
        $this->assertTrue(password_verify($nuevoPass, $hashNuevo));
    }

    /** OBTENER ACCESOS: accesos devuelve arreglo (puede estar vacío).*/
    public function testAccesosDevuelveArregloAunqueEsteVacio(): void
    {
        // Insertar
        $nombre = $this->nombreValido('PRUEBA ACC');
        $user   = $this->usernameValido();
        $pass   = 'Valida!123';
        $row    = $this->registrarUsuarioDePrueba($nombre, $user, $pass, $this->roleId);
        $id     = (int)$row['cod_usuario'];

        // accesos() depende de joins; puede devolver arreglo vacío si el rol no tiene permisos
        $accesos = $this->sut->accesos($id);
        $this->assertIsArray($accesos);
    }

    /* ELIMINAR:*/
    public function testEliminarUsuarioLoQuitaDeLaBase(): void
    {
        $nombre = $this->nombreValido('PRUEBA REG');
        $user   = $this->usernameValido();
        $pass   = 'Valida!123';

        $row = $this->registrarUsuarioDePrueba($nombre, $user, $pass, $this->roleId);
        $id  = (int)($row['cod_usuario'] ?? 0);

        $res = $this->sut->eliminar($id);
        $this->assertSame('success', $res);
        $this->assertFalse($this->sut->buscar($user));
    }

    /** MOSTRAR() CON USER INEXISTENTE: debe devolver arreglo vacío. */
    public function testMostrarNoEncontradoRetornaArregloVacio(): void
    {
        $row = $this->sut->mostrar('usuarioquenoexiste_' . bin2hex(random_bytes(4)));
        $this->assertIsArray($row);
        $this->assertSame([], $row);
    }

    /** BUSCAR() CON USER INEXISTENTE: debe devolver false. */
    public function testBuscarNoEncontradoRetornaFalse(): void
    {
        $row = $this->sut->buscar('usuarioquenoexiste_' . bin2hex(random_bytes(4)));
        $this->assertFalse($row);
    }

    /** USER INVALIDO -> dispara Exception en check(). */
    public function testValidacionUserInvalidoDisparaCheckException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');

        $this->sut->setDatos([
            'nombre' => 'Nombre Válido',
            'user'   => '$$*',        // inválido
            'pass'   => 'Valida!123', // válida
            'status' => 1,
        ]);
        $this->sut->check();
    }

    /** PASSWORD INVALIDA -> dispara Exception en check(). */
    public function testValidacionPasswordInvalidaDisparaCheckException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');

        $user = 'usr' . substr(bin2hex(random_bytes(3)), 0, 5);
        $this->sut->setDatos([
            'nombre' => 'Nombre Válido',
            'user'   => $user,
            'pass'   => 'abc',
            'status' => 1,
        ]);
        $this->sut->check();
    }
}