<?php
use Modelo\Representantes;
use Modelo\Bitacora;


$objRepresentante = new Representantes();
$objbitacora = new Bitacora();

if (isset($_POST['buscar'])) {
    if (empty($_SESSION["permisos"]["proveedor"]["consultar"])) {
        header('Content-Type: application/json');
        echo json_encode(null);
        exit;
    }
    $resul = $objRepresentante->getbuscar($_POST['buscar']);
    header('Content-Type: application/json');
    echo json_encode($resul);
    exit;
} else if (isset($_POST["ok"])) {
    if (empty($_SESSION["permisos"]["proveedor"]["registrar"])) {
        $registrar = [
            "title" => "Error",
            "message" => "No tiene permiso para registrar representantes",
            "icon" => "error"
        ];
    } else {
        try {
            $objRepresentante->setcedula($_POST['cedula']);
            $objRepresentante->setnombre($_POST['nombre']);
            if (!empty($_POST['apellido'])) {
                $objRepresentante->setapellido($_POST['apellido']);
            }
            if (!empty($_POST['telefono'])) {
                $objRepresentante->settelefono($_POST['telefono']);
            }
            $objRepresentante->setCod1($_POST['cod_provREPRE']);
            $objRepresentante->check();

            // Check if cedula exists
            $cedula = $_POST["cedula"];
            $dato = $objRepresentante->getbuscar($cedula);
            if ($dato) {
                $registrar = [
                    "title" => "Error",
                    "message" => "Ya existe un representante con la misma cédula",
                    "icon" => "error"
                ];
            } else {
                $resul = $objRepresentante->getregistra();
                if ($resul == 1) {
                    $registrar = [
                        "title" => "Registrado con éxito",
                        "message" => "El representante ha sido registrado",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de representante', $_POST["nombre"], 'Representantes');
                } else {
                    $registrar = [
                        "title" => "Error",
                        "message" => "Hubo un problema al registrar el representante",
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
} else if (isset($_POST['editarr'])) {
    if (empty($_SESSION["permisos"]["proveedor"]["editar"])) {
        $editar = [
            "title" => "Error",
            "message" => "No tiene permiso para editar representantes",
            "icon" => "error"
        ];
    } else {
        try {
            $objRepresentante->setCod($_POST['cod_representante']);
            $objRepresentante->setcedula($_POST['cedula']);
            $objRepresentante->setnombre($_POST['nombre']);
            if (!empty($_POST['apellido'])) {
                $objRepresentante->setapellido($_POST['apellido']);
            }
            if (!empty($_POST['reptel'])) {
                $objRepresentante->settelefono($_POST['reptel']);
            }
            $objRepresentante->setStatus($_POST['status']);
            $objRepresentante->check();

            if ($_POST['cedula'] !== $_POST['origin']) {
                $dato = $objRepresentante->getbuscar($_POST['cedula']);
                if ($dato) {
                    $editar = [
                        "title" => "Error",
                        "message" => "Ya existe un representante con la misma cédula",
                        "icon" => "error"
                    ];
                    return;
                }
            }

            $resul = $objRepresentante->getedita();
            if ($resul == 1) {
                $editar = [
                    "title" => "Editado con éxito",
                    "message" => "Los datos del representante han sido actualizados.",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar representante', $_POST["nombre"], 'Representantes');
            } else {
                $editar = [
                    "title" => "Error",
                    "message" => "Hubo un problema al editar los datos del representante.",
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
} else if (isset($_POST['eliminar_representante'])) {
    if (empty($_SESSION["permisos"]["proveedor"]["eliminar"])) {
        $eliminar = [
            "title" => "Error",
            "message" => "No tiene permiso para eliminar representantes",
            "icon" => "error"
        ];
    } else if (!empty($_POST['reprCodigo'])) {
        $resul = $objRepresentante->geteliminar($_POST["reprCodigo"]);

        if ($resul === 'success_physical_delete') {
            $eliminar = [
                "title" => "Eliminado con éxito",
                "message" => "El representante ha sido eliminado .",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar representante', "Eliminado el representante con el código ".$_POST["reprCodigo"], 'Representantes');
        } else {
            $eliminar = [
                "title" => "Error",
                "message" => "Hubo un error al eliminar el representante.",
                "icon" => "error"
            ];
        }
    }
}

if (empty($_SESSION["permisos"]["proveedor"]["consultar"])) {
    $registro = [];
} else {
    $registro = $objRepresentante->getconsulta();
}

$_GET['ruta'] = 'proveedores';
require_once 'plantilla.php';
