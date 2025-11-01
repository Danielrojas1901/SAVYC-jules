<?php
use Modelo\Usuarios;
use Modelo\Roles;
use Modelo\Bitacora;

$objuser= new Usuarios();
$objroles = new Roles();
$objbitacora = new Bitacora();
$roles = $objroles->getconsultarUsuario(); // Obtener los roles para pasarlos a la vista

//Buscar si existe un usuario con el mismo username
if(isset($_POST['buscar'])){
    $user = $_POST['buscar']; 
    $result = $objuser->buscar($user);
    header('Content-Type: application/json'); 
    echo json_encode($result); 
    exit;

}else if (isset($_POST['guardar']) && !empty($_SESSION["permisos"]["seguridad"]["registrar"])){

    if(!empty($_POST['nombre']) && !empty($_POST['user']) && !empty($_POST['pass']) && !empty($_POST['rol'])){
        if (!$objuser->buscar($_POST["user"])){ 
            
            $errores=[];

            try {
                $objuser->setDatos($_POST);
                $objuser->check();

                $result = $objuser->getregistrar($_POST['rol']);

                    if($result == 1){
                    $registrar = [
                        "title" => "Registrado con éxito",
                        "message" => "El usuario ha sido registrado",
                        "icon" => "success"
                    ];
                        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de usuario', $_POST["user"], 'Usuarios');
                    }else{
                        $registrar = [
                            "title" => "Error",
                            "message" => "Hubo un problema al registrar el usuario",
                            "icon" => "error"
                        ];
                    }

            } catch (Exception $e) {
                $errores[] = $e->getMessage();
                if (!empty($errores)) {
                    $registrar = [
                        "title" => "Error",
                        "message" => implode(" ", $errores),
                        "icon" => "error"
                    ];
                    } 
                }
                
            } else {
                $registrar = [
                    "title" => "Error",
                    "message" => "No se pudo registrar porque el nombre de usuario ya existe.",
                    "icon" => "error"
                    ];
            }
    }else{
        $registrar = [
            "title" => "Error",
            "message" => "No se permiten campos vacíos",
            "icon" => "error"
        ];
    }


} else if (isset($_POST['actualizar']) && !empty($_SESSION["permisos"]["seguridad"]["editar"])) {

    $passwordCambiada = 0;
    $errores = [];

    if (!empty($_POST['nombre']) && !empty($_POST['user']) && !empty($_POST['roles']) && isset($_POST['status'])) {

        if ($_POST['user'] !== $_POST['origin']) {
            // Si el user cambió, verificamos si ya existe en la base de datos
            if ($objuser->buscar($_POST['user'])) {
                $advertencia = [
                    "title" => "Error",
                    "message" => "No se pudo registrar porque el nombre de usuario ya existe.",
                    "icon" => "error"
                ];
            }
        }
        try {
            $objuser->setDatos($_POST);
            $objuser->check();

            if (!empty($_POST['pass']) && !isset($objuser->getErrores()['pass'])) {
                $passwordCambiada = 1;
            }

            if ($passwordCambiada == 1) {
                // Si se cambió la contraseña, usamos el método que también actualiza la contraseña
                $result = $objuser->editar2($_POST['codigo'], $_POST['roles']);
                
            } else {
                // Si no se cambió la contraseña, usamos el método que no la modifica
                $result = $objuser->editar($_POST['codigo'], $_POST['roles']);
            }

            if ($result == 1) {
                $editar = [
                    "title" => "Editado con éxito",
                    "message" => "El usuario ha sido actualizado correctamente",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar usuario', $_POST["user"], 'Usuarios');
            } else {
                $editar = [
                    "title" => "Error",
                    "message" => "Hubo un problema al editar el usuario",
                    "icon" => "error"
                ];
            }

        } catch (Exception $e) {
            $errores[] = $e->getMessage();
            
            if (!empty($errores)) {
            $editar = [
                "title" => "Error",
                "message" => implode(" ", $errores),
                "icon" => "error"
            ];
            }
        }
        
    } else {
        $editar = [
            "title" => "Error",
            "message" => "No se permiten campos vacíos",
            "icon" => "error"
        ];
    }

}else if(isset($_POST['borrar']) && !empty($_SESSION["permisos"]["seguridad"]["eliminar"])){

    try{
        $objuser->setDatos($_POST);
        $objuser->check();

        $result = $objuser->eliminar($_POST["usercode"]);
        
            if ($result == 'success') {
                $eliminar = [
                    "title" => "Eliminado con éxito",
                    "message" => "El usuario ha sido eliminado",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar usuario', "Eliminado el usuario con el código ".$_POST["usercode"], 'Usuarios');
            
            } elseif ($result == 'error_ultimo') {
                    $eliminar = [
                        "title" => "Error",
                        "message" => "El usuario no se puede eliminar porque es el último administrador",
                        "icon" => "error"
                    ];
            } elseif ($result == 'error_delete') {
                $eliminar = [
                    "title" => "Error",
                    "message" => "Hubo un problema al eliminar el usuario",
                    "icon" => "error"
                ];
            } 

    }catch(Exception $e) {
        $errores[] = $e->getMessage();
        if (!empty($errores)) {
        $eliminar = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
            ];
        }
    }
}

if(!empty($_SESSION["permisos"]["seguridad"]["consultar"])){
$registro = $objuser->listar();
}
$_GET['ruta'] = 'usuarios';
require_once 'plantilla.php';