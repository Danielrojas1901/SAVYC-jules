<?php
use Modelo\Clientes;
use Modelo\Bitacora;

$objCliente = new Clientes(); 
$objbitacora = new Bitacora();

try {
    if(isset($_POST['buscar'])) {
        $cedula = $_POST['buscar'];
        $result = $objCliente->buscar($cedula);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } 
    
    if(isset($_POST['guardar']) && !empty($_SESSION["permisos"]["cliente"]["registrar"])) { 
        $errores = [];
        $registrar = [];

        try {
            $objCliente->setData($_POST);
            $objCliente->check();
            
            $cedula = $_POST["cedula_rif"];
            $dato = $objCliente->buscar($cedula);
            
            if(!$dato) {
                $result = $objCliente->getRegistrar();
                if($result == 1) {
                    $registrar = [
                        "title" => "Registrado con éxito",
                        "message" => "El cliente ha sido registrado",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de cliente', $_POST["nombre"], 'Clientes');
                } else {
                    throw new Exception("Hubo un problema al registrar el cliente");
                }
            } else {
                throw new Exception("La cédula ya se encuentra registrada. Intente nuevamente");
            }
        } catch (Exception $e) {
            $registrar = [
                "title" => "Error",
                "message" => $e->getMessage(),
                "icon" => "error"
            ];
        }
    }
    
    if(isset($_POST['actualizar']) && !empty($_SESSION["permisos"]["cliente"]["editar"])) {
    $editar = [];
    
    try {
        // Solo verificar campos obligatorios
        if(empty($_POST["nombre"]) || empty($_POST["apellido"]) || empty($_POST["cedula"])) {
            throw new Exception("Todos los campos obligatorios deben estar completos");
        }
        
        if($_POST['cedula'] !== $_POST['origin'] && $objCliente->buscar($_POST['cedula'])) {
            throw new Exception("La cédula del cliente ya existe");
        }
        
        // Campos opcionales pueden estar vacíos
        $objCliente->setData($_POST);
        $objCliente->check();
        
        $result = $objCliente->getactualizar($_POST["codigo"]);
        
        if($result == 1) {
            $editar = [
                "title" => "Editado con éxito",
                "message" => "Los datos del cliente han sido actualizados",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar cliente', "Editado el cliente con el código ".$_POST["codigo"], 'Clientes');
        } else {
            throw new Exception("Hubo un problema al editar los datos del cliente");
        }
    } catch (Exception $e) {
        $editar = [
            "title" => "Error",
            "message" => $e->getMessage(),
            "icon" => "error"
        ];
    }
}
    if(isset($_POST['borrar']) && !empty($_SESSION["permisos"]["cliente"]["eliminar"])) {
        $eliminar = [];
        
        try {
            if(empty($_POST['clienteCodigo'])) {
                throw new Exception("No se proporcionó el código del cliente");
            }
            
            $result = $objCliente->geteliminar($_POST["clienteCodigo"]);
            
            if($result == 'success') {
                $eliminar = [
                    "title" => "Eliminado con éxito",
                    "message" => "El cliente ha sido eliminado",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar cliente', "Eliminado el cliente con el código ".$_POST["clienteCodigo"], 'Clientes');
            } elseif($result == 'venta') {
                throw new Exception("El cliente tiene ventas asociadas");
            } else {
                throw new Exception("Hubo un problema al eliminar el cliente");
            }
        } catch (Exception $e) {
            $eliminar = [
                "title" => "Error",
                "message" => $e->getMessage(),
                "icon" => "error"
            ];
        }
    }

} catch (Exception $e) {
    // Manejo de errores generales
    error_log("Error en controlador de clientes: " . $e->getMessage());
}

$registro = $objCliente->consultar();

if(isset($_POST["vista"])) {
    $_GET['ruta'] = 'venta';
} else {
    $_GET['ruta'] = 'clientes';
}

require_once 'plantilla.php';