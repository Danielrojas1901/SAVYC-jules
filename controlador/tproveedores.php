<?php
use Modelo\Tlf_proveedor;
use Modelo\Bitacora;

$objbitacora =new Bitacora();
$objTlfroveedores = new Tlf_proveedor();

if (isset($_POST['buscar'])) {
    if (empty($_SESSION["permisos"]["proveedor"]["consultar"])) {
        header('Content-Type: application/json');
        echo json_encode(null);
        exit;
    }
    $resul = $objTlfroveedores->getbusca($_POST['buscar']);
    header('Content-Type: application/json');
    echo json_encode($resul);
    if (!empty($_SESSION["permisos"]["proveedor"]["consultar"])) {
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Buscar teléfono', $_POST['buscar'], 'Teléfonos de proveedores');
    }
    exit;
} elseif (isset($_POST["okk"])) {
    if (empty($_SESSION["permisos"]["proveedor"]["registrar"])) {
        $registrar = [
            "title" => "Error",
            "message" => "No tiene permiso para registrar teléfonos",
            "icon" => "error"
        ];
    } else {
        try {
            $objTlfroveedores->settelefono($_POST["telefono"]);
            $objTlfroveedores->setCod1($_POST['cod_prov']); 
            $objTlfroveedores->check();

            $dato = $objTlfroveedores->getbusca($_POST["telefono"]);
            if ($dato) {
                $registrar = [
                    "title" => "Error",
                    "message" => "El teléfono ya está registrado",
                    "icon" => "error"
                ];
            } else {
                $resul = $objTlfroveedores->getregistra();
                if ($resul == 1) {
                    $registrar = [
                        "title" => "Registrado con éxito",
                        "message" => "El teléfono ha sido registrado.",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de teléfono', $_POST["telefono"], 'Teléfonos de proveedores');
                } else {
                    $registrar = [
                        "title" => "Error",
                        "message" => "Hubo un problema al registrar el teléfono.",
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
} elseif (isset($_POST['editar'])) {
    if (empty($_SESSION["permisos"]["proveedor"]["editar"])) {
        $editar = [
            "title" => "Error",
            "message" => "No tiene permiso para editar teléfonos",
            "icon" => "error"
        ];
    } else {
        try {
            $objTlfroveedores->setcod_tlf($_POST['cod_tlf']);
            $objTlfroveedores->settelefono($_POST['telefono']);
            $objTlfroveedores->check();

            if ($_POST['telefono'] !== $_POST['origin']) {
                $dato = $objTlfroveedores->getbusca($_POST['telefono']);
                if ($dato) {
                    $editar = [
                        "title" => "Error",
                        "message" => "El teléfono ya está registrado",
                        "icon" => "error"
                    ];
                    return;
                }
            }

            $resul = $objTlfroveedores->geteditar();
            if ($resul == 1) {
                $editar = [
                    "title" => "Editado con éxito",
                    "message" => "El teléfono ha sido actualizado.",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar teléfono', $_POST["telefono"], 'Teléfonos de proveedores');
            } else {
                $editar = [
                    "title" => "Error",
                    "message" => "Hubo un problema al editar el teléfono.",
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
            "message" => "No tiene permiso para eliminar teléfonos",
            "icon" => "error"
        ];
    } else {
        $resul = $objTlfroveedores->geteliminar($_POST["cod_tlf"]);
        if ($resul == 1) {
            $eliminar = [
                "title" => "Eliminado con éxito",
                "message" => "El teléfono ha sido eliminado.",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar teléfono', $_POST["cod_tlf"], 'Teléfonos de proveedores');
        } else {
            $eliminar = [
                "title" => "Error",
                "message" => "Hubo un problema al eliminar el teléfono.",
                "icon" => "error"
            ];
        }
    }
}

if (empty($_SESSION["permisos"]["proveedor"]["consultar"])) {
    $registro = [];
} else {
    $registro = $objTlfroveedores->getconsulta();
}

$_GET['ruta'] = 'proveedores';
require_once 'plantilla.php';