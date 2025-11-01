<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Modelo\Roles;
use Modelo\Conexion;
use PDO;
use Exception;

final class RolesCrudTest extends TestCase
{
    private Roles $sut;
    private $codigos_creados = []; // Para almacenar los IDs de prueba y eliminarlos después

    protected function setUp(): void
    {
        // Instancia la clase real para la prueba de integración
        $this->sut = new Roles();
    }

    /**
     * @test
     * @group integration
     */
    public function crearRol_debeInsertarRolYPermisosCorrectamente()
    {
        // 1. Arrange: Prepara los datos para la creación del rol
        $nombre_rol = 'Rol de Prueba ';
        $modulos = [1, 2]; // IDs de módulos de prueba
        $permisos = [
            '1' => [1, 2], // permisos de prueba (1=crear, 2=editar)
            '2' => [1, 2],
        ];

        // 2. Act: Llama al método para crear el rol
        $this->sut->setRol($nombre_rol);
        $resultado = $this->sut->getcrearRol($modulos, $permisos);

        // 3. Assert: Verifica que la inserción fue exitosa
        $this->assertEquals(1, $resultado, 'El método debe devolver 1 en caso de éxito.');
        
        $rol_creado = $this->sut->buscar($nombre_rol);
        $this->assertIsArray($rol_creado, 'El rol debe existir en la base de datos.');
        $this->assertEquals($nombre_rol, $rol_creado['rol']);

        // 4. Cleanup: Almacena el ID para eliminarlo después
        $this->codigos_creados[] = $rol_creado['cod_tipo_usuario'];
        $this->limpiarRol($rol_creado['cod_tipo_usuario']);
    }

    /**
     * @test
     * @group integration
     */
    public function consultar_debeDevolverUnaListaDeRoles()
    {
        // 1. Arrange: Crea un rol temporal para asegurar que la lista no esté vacía
        $rol_temporal = 'Temp ';
        $this->sut->setRol($rol_temporal);
        $this->sut->getcrearRol([1], ['1' => [1]]);
        
        // 2. Act: Consulta la lista de roles
        $roles = $this->sut->consultar();

        // 3. Assert: Verifica que el resultado es un array y que contiene al menos un rol
        $this->assertIsArray($roles);
        $this->assertGreaterThan(0, count($roles), 'Debe haber al menos un rol en la lista.');
        
        // 4. Cleanup: Limpia los datos temporales
        $rol_creado = $this->sut->buscar($rol_temporal);
        $this->limpiarRol($rol_creado['cod_tipo_usuario']);
    }

    /**
     * @test
     * @group integration
     */
    public function eliminar_debeEliminarRolSiNoTieneUsuariosAsociados()
    {
        // 1. Arrange: Crea un rol temporal para la prueba de eliminación
        $rol_a_eliminar = 'Eliminable';
        $this->sut->setRol($rol_a_eliminar);
        $this->sut->getcrearRol([1,2], [1=>[1,2], 2=>[1,2]]); // Crea un rol sin permisos
        $rol_creado = $this->sut->buscar($rol_a_eliminar);
        $this->assertIsArray($rol_creado, 'El rol de prueba debe ser creado correctamente.');

        $cod_rol = $rol_creado['cod_tipo_usuario'];

        // 2. Act: Primero, cambia el status del rol a inactivo (0)
        $this->sut->setcodigo($cod_rol);
        $this->sut->setRol($rol_a_eliminar);
        $this->sut->setStatus(2);
        $this->sut->geteditar(); // El método geteditar() realiza la actualización

        // 3. Act: Ahora sí, llama al método de eliminación
        $resultado = $this->sut->geteliminar($cod_rol);
        
        // 4. Assert: Verifica que la eliminación fue exitosa
        $this->assertEquals('success', $resultado, 'El rol debería ser eliminado correctamente ya que su status es inactivo.');
        
        $rol_eliminado = $this->sut->buscarcod($rol_a_eliminar);
        $this->assertFalse($rol_eliminado, 'El rol no debe existir después de ser eliminado.');
    }
    
    /**
     * @test
     * @group integration
     */
    public function editar_debeActualizarLosDatosDelRol()
    {
        // 1. Arrange: Crea un rol temporal para editar
        $rol_original = 'ParaEditar'; // Usar uniqid() aquí
        $rol_nuevo = 'Editado';
        $this->sut->setRol($rol_original);
        $this->sut->getcrearRol([1,2], [1 => [1,2], 2 => [1,2]]);
        $rol_creado = $this->sut->buscar($rol_original);
        $cod_rol = $rol_creado['cod_tipo_usuario'];
        
        // 2. Act: Edita el rol
        $this->sut->setcodigo($cod_rol);
        $this->sut->setRol($rol_nuevo);
        $this->sut->setStatus(0); // Cambia el status
        $resultado = $this->sut->geteditar();

        // 3. Assert: Verifica que la actualización fue exitosa y los datos cambiaron
        $this->assertEquals(1, $resultado, 'El método de edición debe devolver 1.');
        $rol_editado = $this->sut->buscarcod($rol_nuevo);
        $this->assertIsArray($rol_editado);
        $this->assertEquals($rol_nuevo, $rol_editado['rol']);
        $this->assertEquals(0, $rol_editado['status']);

        // 4. Cleanup: Elimina el rol temporal
        $this->limpiarRol($cod_rol);
    }
    
    //---------------------------------------------------------
    // Métodos de Ayuda para Limpieza (Cleanup)
    //---------------------------------------------------------
    
    /**
     * Limpia los registros de un rol de prueba.
     * @param int $codigo_rol El ID del rol a eliminar.
     */
    private function limpiarRol($codigo_rol)
    {
        $rol = new Roles();
        $rol->conectarBD();
        
        $sql1 = 'DELETE FROM tpu_permisos WHERE cod_tipo_usuario = :id';
        $stmt1 = $rol->getConex()->prepare($sql1);
        $stmt1->bindParam(':id', $codigo_rol, PDO::PARAM_INT);
        $stmt1->execute();
        
        $sql2 = 'DELETE FROM tipo_usuario WHERE cod_tipo_usuario = :id';
        $stmt2 = $rol->getConex()->prepare($sql2);
        $stmt2->bindParam(':id', $codigo_rol, PDO::PARAM_INT);
        $stmt2->execute();

        $rol->desconectarBD();
    }
    
    

}