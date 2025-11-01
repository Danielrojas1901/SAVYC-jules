<?php
declare(strict_types=1);

namespace Tests\Unit\Modelo;

use Modelo\Usuarios;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use Tests\Unit\Modelo\Traits\MaliciousDataProvidersTrait;


class UsuariosStub extends Usuarios
{
    public function __construct() {}
    public function conectarBD()   {}
    public function desconectarBD(){}
}

#[Group('Usuarios'), Group('unit')]
final class UsuariosTest extends TestCase
{
    use MaliciousDataProvidersTrait;
    private UsuariosStub $sut;
    protected function setUp(): void
    {
        $this->sut = new UsuariosStub();
    }

    // ========================================
    // PRUEBAS DE ESTADO INICIAL
    // ========================================

    public function testInicialmenteGettersSonNull(): void
    {
        $this->assertNull($this->sut->getNombre());
        $this->assertNull($this->sut->getUser());
        $this->assertNull($this->sut->getPassword());
        $this->assertNull($this->sut->getStatus());
        $this->assertSame([], $this->sut->getErrores());
    }

    // ========================================
    // PRUEBAS VÁLIDAS - CASOS QUE DEBEN PASAR
    // ========================================

    //NOMBRES
    public static function nombresValidos(): array
    {
        return [
            'min 2'      => ['Al'],
            'medio'      => ['Liliana Pérez'],
            'max 50'     => [str_repeat('a', 50)],
        ];
    }

    #[DataProvider('nombresValidos')]
    public function testSetDatosConNombreValidoYStatusValidoNoLanza(string $nombre): void
    {
        $this->sut->setDatos(['nombre' => $nombre, 'status' => 1]);
        $this->sut->check();

        $this->assertSame($nombre, $this->sut->getNombre());
        $this->assertSame(1, $this->sut->getStatus());
    }

    //USERS
    public static function usuariosValidos(): array
    {
        return [
            'min 2'       => ['AB'],
            'alfanum'     => ['user123'],
            'con mayúsc'  => ['MiUsuario'], // debe quedar en minúsculas
            'max 20'      => [str_repeat('a', 20)],
        ];
    }

    #[DataProvider('usuariosValidos')]
    public function testUserValidoSeSeteaEnMinusculas(string $entrada): void
    {
        $this->sut->setDatos(['user' => $entrada]);
        $this->sut->check();

        $this->assertSame(mb_strtolower($entrada), $this->sut->getUser());
    }

    //PASSWORD
    public function testPasswordValidaSeHasheaYCheckNoLanza(): void
    {
        $this->sut->setDatos([
            'user' => 'MiUsuario',
            'pass' => 'Secreto!123', // >=8, tiene especial, distinta al user
            'nombre' => 'Nombre Valido',
            'status' => 1,
        ]);
        $this->sut->check();

        $hash = $this->sut->getPassword();
        $this->assertIsString($hash);
        $this->assertNotSame('Secreto!123', $hash);
        $this->assertTrue(password_verify('Secreto!123', $hash));
        $this->assertSame('miusuario', $this->sut->getUser());
    }

    // ========================================
    // PRUEBAS INVÁLIDAS - CASOS QUE DEBE LANZAR ERROR
    // ========================================

    //NOMBRES
    public static function nombresInvalidos(): array
    {
        return [
            'vacío'       => [''],
            '1 caracter'  => ['A'],
            '51 chars'    => [str_repeat('a', 51)],
        ];
    }

    #[DataProvider('nombresInvalidos')]
    public function testSetDatosConNombreInvalidoAcumulaErrorYCheckLanza(string $nombre): void
    {
        $this->sut->setDatos(['nombre' => $nombre, 'status' => 1]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();

        $this->assertArrayHasKey('nombre', $this->sut->getErrores());
    }

    //USERS
    public static function usuariosInvalidos(): array
    {
        return [
            'vacío'        => [''],
            '1 caracter'   => ['x'],
            '21 chars'     => [str_repeat('a', 21)],
            'carácter raro'=> ['user!'], // invalidado por validarTextoNumero
        ];
    }

    #[DataProvider('usuariosInvalidos')]
    public function testUserInvalidoAcumulaErrorYCheckLanza(string $entrada): void
    {
        $this->sut->setDatos(['user' => $entrada]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();

        $this->assertArrayHasKey('user', $this->sut->getErrores());
    }

    //PASSWORD 
    public function testPasswordInvalidaPorCortaAcumulaErrorYCheckLanza(): void
    {
        $this->sut->setDatos([
            'user' => 'usuario',
            'pass' => 'Abc!12', // < 8
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();

        $this->assertArrayHasKey('pass', $this->sut->getErrores());
        $this->assertNull($this->sut->getPassword());
    }

    public function testPasswordInvalidaPorIgualAlUsuarioAcumulaError(): void
    {
        $user = 'usuarioespecial'; // >= 8
        $this->sut->setDatos([
            'user' => $user,
            'pass' => $user, // igual al user -> inválida
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();

        $this->assertArrayHasKey('pass', $this->sut->getErrores());
    }

    public function testPasswordInvalidaSinCaracterEspecialAcumulaError(): void
    {
        $this->sut->setDatos([
            'user' => 'usuario',
            'pass' => 'Secreto123', // falta especial
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();

        $this->assertArrayHasKey('pass', $this->sut->getErrores());
    }

    public function testPasswordInvalidaPorDemasiadoLargaAcumulaError(): void
    {
        $muyLarga = str_repeat('a', 256) . '!'; // >255
        $this->sut->setDatos([
            'user' => 'usuario',
            'pass' => $muyLarga,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();

        $this->assertArrayHasKey('pass', $this->sut->getErrores());
    }

    public function testPasswordNoSeProcesaSiFaltaUser(): void
    {
        $this->sut->setDatos(['pass' => 'Valida!123']);
        // No hay validación de pass si falta user; no debería lanzar ni setear password
        $this->sut->check();

        $this->assertNull($this->sut->getPassword());
    }

    public function testStatusInactivoValidoPorStatusDelete(): void
    {
        $this->sut->setDatos(['statusDelete' => 0]);
        $this->sut->check();
        $this->assertSame(0, $this->sut->getStatus());
    }

    public function testStatusInvalidoAcumulaErrorYCheckLanza(): void
    {
        $this->sut->setDatos(['status' => 99]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Errores de validación');
        $this->sut->check();

        $this->assertArrayHasKey('status', $this->sut->getErrores());
    }

    /** ---- login: ingUsuario debe comportarse como user (lowercase + validación) ---- */
    public function testIngUsuarioNormalizaYValidaIgualQueUser(): void
    {
        $this->sut->setDatos(['ingUsuario' => 'AdminMASTER']);
        $this->sut->check();

        $this->assertSame('adminmaster', $this->sut->getUser());
    }

    /** ---- múltiples errores agrupados ---- */
    public function testCheckAgrupaMultiplesErrores(): void
    {
        $this->sut->setDatos([
            'nombre' => '',           // inválido
            'user'   => '/',          // inválido
            'status' => 99,           // inválido
            'pass'   => 'short',    // inválido

        ]);

        try {
            $this->sut->check();
            $this->fail('Debió lanzar por errores de validación');
        } catch (\Exception $e) {
            $this->assertStringContainsString('Errores de validación', $e->getMessage());
            $errores = $this->sut->getErrores();
            //var_dump($errores); // para ver qué errores se acumularon
            $this->assertCount(4, $errores);
            $this->assertArrayHasKey('nombre', $errores);
            $this->assertArrayHasKey('user', $errores);
            $this->assertArrayHasKey('status', $errores);
            $this->assertArrayHasKey('password', $errores);
        }
    }

    /** ---- robustez: claves desconocidas se ignoran ---- */
    public function testClavesDesconocidasSeIgnoran(): void
    {
        $this->sut->setDatos(['foo' => 'bar', 'bar' => 'baz']);
        $this->sut->check();

        $this->assertNull($this->sut->getNombre());
        $this->assertNull($this->sut->getUser());
        $this->assertNull($this->sut->getPassword());
        $this->assertNull($this->sut->getStatus());
        $this->assertSame([], $this->sut->getErrores());
    }

// ========================================
// MALICIOSOS 
// ========================================

#[DataProvider('casosMaliciosos')]
public function testNombreCasosMaliciosos($valor): void
{
    $this->sut->setDatos(['nombre' => $valor, 'status' => 1]);

    try {
        $this->sut->check();
        $this->fail("DEFECTO: El sistema acepta nombre malicioso sin validación: " . var_export($valor, true));
    } catch (\Exception $e) {
        $this->assertStringContainsString('Errores de validación', $e->getMessage());
        $this->assertArrayHasKey('nombre', $this->sut->getErrores());
    }
}

#[DataProvider('casosMaliciosos')]
public function testUserCasosMaliciosos($valor): void
{
    $this->sut->setDatos(['user' => $valor]);

    try {
        $this->sut->check();
        $this->fail("DEFECTO: El sistema acepta user malicioso sin validación: " . var_export($valor, true));
    } catch (\Exception $e) {
        $this->assertStringContainsString('Errores de validación', $e->getMessage());
        $this->assertArrayHasKey('user', $this->sut->getErrores());
    }
}
/*
#[DataProvider('casosMaliciosos')]
public function testPassCasosMaliciosos($valor): void
{
    // Política: la contraseña puede contener cualquier carácter.
    // Debe procesarse de forma segura (hash) y no en claro.
    $this->sut->setDatos(['user' => 'usuario', 'pass' => $valor]);

    // No debe lanzar
    $this->sut->check();

    // Debe haberse generado un hash y verificar correctamente
    $hash = $this->sut->getPassword();
    $this->assertIsString($hash);
    $this->assertNotSame((string)$valor, $hash);
    $this->assertTrue(password_verify((string)$valor, $hash));
}*/

// ========================================
// TIPOS DE DATOS PROBLEMÁTICOS 
// ========================================

#[DataProvider('tiposDatosProblematicos')]
public function testNombreTiposDatosProblematicos($valor): void
{
    try {
        $this->sut->setDatos(['nombre' => $valor, 'status' => 1]);

        // Si setDatos no tipa fuerte, intentará validar:
        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta nombre con tipo inválido (" . gettype($valor) . ") sin validación");
        } catch (\Exception $e) {
            $this->assertStringContainsString('nombre', $e->getMessage());
        }
    } catch (\TypeError $t) {
        // Si tipa fuerte y lanza TypeError, está OK
        $this->addToAssertionCount(1);
    } finally {
        if (is_resource($valor)) { @fclose($valor); }
    }
}

#[DataProvider('tiposDatosProblematicos')]
public function testUserTiposDatosProblematicos($valor): void
{
    try {
        $this->sut->setDatos(['user' => $valor]);

        try {
            $this->sut->check();
            $this->fail("DEFECTO: El sistema acepta user con tipo inválido (" . gettype($valor) . ") sin validación");
        } catch (\Exception $e) {
            $this->assertStringContainsString('user', $e->getMessage());
        }
    } catch (\TypeError $t) {
        $this->addToAssertionCount(1);
    } finally {
        if (is_resource($valor)) { @fclose($valor); }
    }
}
/*
#[DataProvider('tiposDatosProblematicos')]
public function testPassTiposDatosProblematicos($valor): void
{
    try {
        $this->sut->setDatos(['user' => 'usuario', 'pass' => $valor]);

        try {
            $this->sut->check();
            $this->fail(
                "DEFECTO: Acepta password con tipo inválido (" . gettype($valor) . ") sin validar"
            );
        } catch (\Exception $e) {
            // No dependas del texto del mensaje; valida la clave en el array de errores
            $errores = $this->sut->getErrores();
            $this->assertTrue(
                isset($errores['password']),
                'Se esperaba error en la clave password, errores: ' . json_encode(array_keys($errores))
            );
        }
    } catch (\TypeError $t) {
        $this->addToAssertionCount(1);
    } finally {
        if (is_resource($valor)) { @fclose($valor); }
    }
}*/

}
