<?php
use Modelo\Proveedores;
use Modelo\Tlf_proveedor;
use Modelo\Representantes;
use Modelo\Bitacora;

$objbitacora = new Bitacora();
$objRepresentante = new Representantes();
$objProveedores = new Proveedores();
$objtProveedor = new Tlf_proveedor();

if (isset($_POST['buscar'])) {
    $resul = $objProveedores->getbuscar($_POST['buscar']);
    header('Content-Type: application/json');
    echo json_encode($resul);
    if (!empty($_SESSION["permisos"]["proveedor"]["consultar"])) {
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Buscar proveedor', $_POST['buscar'], 'Proveedores');
    }
    exit;
} else if (isset($_POST["guardar"])) {
    if (empty($_SESSION["permisos"]["proveedor"]["registrar"])) {
        $registrar = [
            "title" => "Error",
            "message" => "No tiene permiso para registrar proveedores",
            "icon" => "error"
        ];
    } else {
        $errores = [];

        try {
            $objProveedores->setRif($_POST['rif']);
            $objProveedores->setRazon_Social($_POST['razon_social']);
            if(isset($_POST["email"])) {
                $objProveedores->setemail($_POST['email']); 
            }
            if(isset($_POST["direccion"])) {
                $objProveedores->setDireccion($_POST['direccion']); 
            }
            $objProveedores->check();

            $rif = $_POST["rif"];
            $dato = $objProveedores->getbuscar($rif);
            if ($dato) {
                $registrar = [
                    "title" => "Error",
                    "message" => "Ya existe un proveedor con el mismo documento",
                    "icon" => "error"
                ];
            } else {
                $resul = $objProveedores->getregistra();
                if ($resul == 1) {
                    $registrar = [
                        "title" => "Registrado con éxito",
                        "message" => "El proveedor ha sido registrado",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de proveedor', $_POST["razon_social"], 'Proveedores');
                } else {
                    $registrar = [
                        "title" => "Error",
                        "message" => "Hubo un problema al registrar el proveedor",
                        "icon" => "error"
                    ];
                }
            }
        } catch (Exception $e) {
            $registrar = [
                "title" => "Error",
                "message" => $e->getMessage(),
                "icon" => "error"
            ];
        }
    }
} else if (isset($_POST['editar'])) {
    if (empty($_SESSION["permisos"]["proveedor"]["editar"])) {
        $editar = [
            "title" => "Error",
            "message" => "No tiene permiso para editar proveedores",
            "icon" => "error"
        ];
    } else {
        $errores = [];
        try {
            $objProveedores->setCod($_POST['cod_prov']);
            $objProveedores->setRif($_POST['rif1']);
            $objProveedores->setRazon_Social($_POST['razon1']);
            $objProveedores->setStatus($_POST["status"]);
            if(isset($_POST["email1"])) {
                $objProveedores->setemail($_POST['email1']); 
            }
            if(isset($_POST["dire1"])) {
                $objProveedores->setDireccion($_POST['dire1']); 
            }
            $objProveedores->check();

            if ($_POST['rif1'] !== $_POST['origin']) {
                $dato = $objProveedores->getbuscar($_POST['rif1']);
                if ($dato) {
                    $editar = [
                        "title" => "Error",
                        "message" => "Ya existe un proveedor con el mismo documento",
                        "icon" => "error"
                    ];
                    return;
                }
            }

            $resul = $objProveedores->getedita();
            if ($resul == 1) {
                $editar = [
                    "title" => "Editado con éxito",
                    "message" => "Los datos del proveedor han sido actualizados.",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar proveedor', $_POST["razon1"], 'Proveedores');
            } else {
                $editar = [
                    "title" => "Error",
                    "message" => "Hubo un problema al editar los datos del proveedor.",
                    "icon" => "error"
                ];
            }
        } catch (Exception $e) {
            $editar = [
                "title" => "Error",
                "message" => $e->getMessage(),
                "icon" => "error"
            ];
        }
    }
} elseif (isset($_POST['eliminar'])) {
    if (empty($_SESSION["permisos"]["proveedor"]["eliminar"])) {
        $eliminar = [
            "title" => "Error",
            "message" => "No tiene permiso para eliminar proveedores",
            "icon" => "error"
        ];
    } else {
        $objProveedores->setCod($_POST['provCodigo']);
        $resul = $objProveedores->get_eliminar();
        if ($resul === 'error_cod') {
            $eliminar = [
                "title" => "Error",
                "message" => "No se puede eliminar el proveedor porque no se ha especificado el código.",
                "icon" => "error"
            ];
        }  
        // Mensajes según el resultado de la eliminación
        if ($resul === 'success_eliminado') {
            $eliminar = [
                "title" => "Eliminado con éxito",
                "message" => "El proveedor ha sido eliminado.",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar proveedor', "Eliminado el proveedor con el código ".$_POST["provCodigo"], 'Proveedores');
        } elseif ($resul === 'error_compra_asociada') {
            $eliminar = [
                "title" => "Error",
                "message" => "No se puede eliminar, tiene una compra asociada.",
                "icon" => "error"
            ];
        } elseif ($resul === 'error_status_activo') {
            $eliminar = [
                "title" => "Error", 
                "message" => "No se puede eliminar un proveedor activo. Debe desactivar el proveedor primero.",
                "icon" => "error"
            ];
        }
    }
}

if (empty($_SESSION["permisos"]["proveedor"]["consultar"])) {
    $registro = [];
} else {
    $registro = $objProveedores->getconsulta();
}

if (isset($_POST["vista"])) {
    $_GET['ruta'] = 'compras';
} else {
    $_GET['ruta'] = 'proveedores';
}

require_once 'plantilla.php';
