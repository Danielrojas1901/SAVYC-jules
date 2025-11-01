<?php
use Modelo\Divisa;
use Modelo\Bitacora;

$obj = new Divisa();
$objbitacora = new Bitacora();

if (isset($_POST['buscar'])) {
    if (empty($_SESSION["permisos"]["config_finanza"]["consultar"])) {
        header('Content-Type: application/json');
        echo json_encode(null);
        exit;
    }
    $resul = $obj->buscar($_POST['buscar']);
    header('Content-Type: application/json');
    echo json_encode($resul);
    if (!empty($_SESSION["permisos"]["config_finanza"]["consultar"])) {
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Buscar divisa', $_POST['buscar'], 'Divisas');
    }
    exit;
} else if (isset($_POST['registrar'])) {
    if (empty($_SESSION["permisos"]["config_finanza"]["registrar"])) {
        $registrar = [
            "title" => "Error",
            "message" => "No tiene permiso para registrar divisas",
            "icon" => "error"
        ];
    } else {
        try {
            $obj->setnombre($_POST['nombre']);
            $obj->setsimbolo($_POST['simbolo']);
            $obj->set_tasa($_POST['tasa']);
            $obj->setfecha($_POST['fecha']);
            $obj->check();

            $dato = $obj->buscar($_POST["nombre"]);
            if ($dato) {
                $registrar = [
                    "title" => "Error",
                    "message" => "La divisa ya está registrada",
                    "icon" => "error"
                ];
            } else {
                $resul = $obj->incluir();
                if ($resul == 1) {
                    $registrar = [
                        "title" => "Registrado con éxito",
                        "message" => "La divisa ha sido registrada",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de divisa', $_POST["nombre"], 'Divisas');
                } else {
                    $registrar = [
                        "title" => "Error",
                        "message" => "Hubo un problema al registrar la divisa",
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
} else if (isset($_POST['actualizar'])) {
    if (empty($_SESSION["permisos"]["config_finanza"]["editar"])) {
        $editar = [
            "title" => "Error",
            "message" => "No tiene permiso para editar divisas",
            "icon" => "error"
        ];
    } else {
        try {
            $obj->setnombre($_POST['nombre']);
            $obj->setsimbolo($_POST['simbolo']);
            $obj->setstatus($_POST['status']);
            $obj->check();

            if ($_POST['nombre'] !== $_POST['origin']) {
                $dato = $obj->buscar($_POST['nombre']);
                if ($dato) {
                    $editar = [
                        "title" => "Error",
                        "message" => "La divisa ya está registrada",
                        "icon" => "error"
                    ];
                    return;
                }
            }

            $resul = $obj->editar($_POST['codigo']);
            if ($resul == 1) {
                $editar = [
                    "title" => "Editado con éxito",
                    "message" => "Los datos de la divisa han sido actualizados",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar divisa', $_POST["nombre"], 'Divisas');
            } else {
                $editar = [
                    "title" => "Error",
                    "message" => "Hubo un problema al editar los datos de la divisa",
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
} else if (isset($_POST['borrar'])) {
    $errores = [];
    if (empty($_SESSION["permisos"]["config_finanza"]["eliminar"])) {
        $eliminar = [
            "title" => "Error",
            "message" => "No tiene permiso para eliminar divisas",
            "icon" => "error"
        ];
    } else {
        try{
        $result = $obj->eliminar($_POST["divisaCodigo"]);
        }catch(Exception $e){
            $errores[] = $e->getMessage();
        }
        if (!empty($errores)) {
            $eliminar = [
                "title" => "Error",
                "message" => implode(" ", $errores),
                "icon" => "error"
            ];
        }else if ($result == 1) {
            $eliminar = [
                "title" => "Eliminado con éxito",
                "message" => "La divisa ha sido eliminada",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar divisa', "Eliminada la Divisa con Codigo " . $_POST["divisaCodigo"], 'Divisas');
        } else if ($result == 0) {
            $eliminar = [
                "title" => "Error",
                "message" => "La divisa no se puede eliminar ya que tiene registros asociados",
                "icon" => "error"
            ];
        } else {
            $eliminar = [
                "title" => "Error",
                "message" => "Hubo un problema al eliminar la divisa",
                "icon" => "error"
            ];
        }
    }
} else if (isset($_POST['r_tasa'])) {
    if (empty($_SESSION["permisos"]["config_finanza"]["editar"])) {
        $editar = [
            "title" => "Error",
            "message" => "No tiene permiso para actualizar tasas",
            "icon" => "error"
        ];
    } else if (isset($_POST['tasa'])) {
        $data = $_POST['tasa'];
        if (isset($data['tasa']) && isset($data['fecha']) && isset($data['cod_divisa'])) {
            $data = [$data];
        }
        $resul = $obj->tasa($data);
        if ($resul == true) {
            $editar = [
                "title" => "Actualizado con éxito",
                "message" => "La tasa de cambio ha sido actualizada",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Actualizar tasa', "Actualizada la tasa de la divisa con código " . $data[0]['cod_divisa'], 'Divisas');
        } else {
            $editar = [
                "title" => "Error",
                "message" => "Hubo un problema al actualizar la tasa",
                "icon" => "error"
            ];
        }
    }
} else if (isset($_POST['sen'])) {
    set_time_limit(20);

    $python = "C:\\xampp\\htdocs\\SAVYCG\\sistema\\dolarbcv.exe"; 

    $dolar = shell_exec($python); 

    error_log("Salida del script Python: " . $dolar);

    header('Content-Type: application/json');
    if (trim($dolar) === "") { 
        echo json_encode("error");
    } else {
        echo json_encode(trim($dolar)); 
    }
    exit();
}

if (empty($_SESSION["permisos"]["config_finanza"]["consultar"])) {
    $consulta = [];
    $historial = [];
} else {
    $consulta = $obj->consultarDivisas();
    $historial = $obj->historial();
}

$_GET['ruta'] = 'divisa';
require_once 'plantilla.php';
