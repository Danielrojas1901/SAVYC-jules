<?php
use Modelo\Tpago;
use Modelo\Divisa;
use Modelo\Bitacora;

$objbitacora = new Bitacora();
$objdivisa=new Divisa();
$obj= new Tpago();

if(isset($_POST['buscar'])){
    if(empty($_SESSION["permisos"]["config_finanza"]["consultar"])) {
        header('Content-Type: application/json');
        echo json_encode(null);
        exit;
    }
    $result=$obj->buscar($_POST['buscar']);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;

}else if(isset($_POST['registrar'])){
    if(empty($_SESSION["permisos"]["config_finanza"]["registrar"])) {
        $registrar = [
            "title" => "Error",
            "message" => "No tiene permiso para registrar tipos de pago",
            "icon" => "error"
        ];
    } else {
        if(!empty($_POST['cod_metodo']) && !empty($_POST['tipo_moneda']) && (!empty($_POST['cod_cuenta_bancaria']) || !empty($_POST['cod_caja']))){

                $result=$obj->registrar($_POST);
                if($result == 1){
                    $registrar = [
                        "title" => "Registrado con éxito",
                        "message" => "El tipo de pago ha sido registrado",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de tipo de pago', $_POST["cod_metodo"], 'Tipo de pago');
                }else{
                    $registrar = [
                        "title" => "Error",
                        "message" => "Hubo un problema al registrar el tipo de pago",
                        "icon" => "error"
                    ];
                }
        } else{
            $registrar = [
                "title" => "Error",
                "message" => "No se permiten campos vacios.",
                "icon" => "error"
            ];
        }
    }

}else if(isset($_POST['editar'])){
    if(empty($_SESSION["permisos"]["config_finanza"]["editar"])) {
        $editar = [
            "title" => "Error",
            "message" => "No tiene permiso para editar tipos de pago",
            "icon" => "error"
        ];
    } else {
        if(!empty($_POST['codigo'])){
            $obj->setstatus($_POST['status']);
            $result=$obj->editar($_POST['codigo']);
            if($result==1){
                $editar = [
                    "title" => "Editado con éxito",
                    "message" => "El estado del tipo de pago ha sido actualizado",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar estado de tipo de pago', $_POST["tpago"], 'Tipo de pago');
            }else {
                $editar = [
                    "title" => "Error",
                    "message" => "Hubo un problema al editar el tipo de pago",
                    "icon" => "error"
                ];
            }
        } else{
            $editar = [
                "title" => "Error",
                "message" => "No se permiten campos vacios.",
                "icon" => "error"
            ];
        }
    }

}else if(isset($_POST['borrar'])){
    if(empty($_SESSION["permisos"]["config_finanza"]["eliminar"])) {
        $eliminar = [
            "title" => "Error",
            "message" => "No tiene permiso para eliminar tipos de pago",
            "icon" => "error"
        ];
    } else {
        if(!empty($_POST['tpagoCodigo'])){
            $result = $obj->eliminar($_POST["tpagoCodigo"]);
            if ($result == 'success') {
                $eliminar = [
                    "title" => "Eliminado con éxito",
                    "message" => "El tipo de pago ha sido eliminado",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar tipo de pago', "Eliminado el tipo de pago con el código ".$_POST["tpagoCodigo"], 'Tipo de pago');
            }elseif ($result == 'error_delete') {
                $eliminar = [
                    "title" => "Error",
                    "message" => "Hubo un problema al eliminar el tipo de pago",
                    "icon" => "error"
                ];
            }elseif ($result == 'error') {
                $eliminar = [
                    "title" => "Error",
                    "message" => "No se puede eliminar ya que tiene pagos asociados",
                    "icon" => "error"
                ];
            }
        }
    }
} else if(isset($_POST['guardarm'])){
    if(empty($_SESSION["permisos"]["config_finanza"]["registrar"])) {
        $registrarm = [
            "title" => "Error",
            "message" => "No tiene permiso para registrar medios de pago",
            "icon" => "error"
        ];
    } else {
        $errores = [];
        try {
            $obj->setmetodo($_POST["medio"]);
            $obj->setmodalidad($_POST["modalidad"]);
            $obj->check(); // Lanza excepción si hay errores
        } catch (Exception $e) {
            $errores[] = $e->getMessage();
        }
        if (!empty($errores)) {
            $registrar = [
                "title" => "Error",
                "message" => implode(" ", $errores),
                "icon" => "error"
            ];
        } else{
            if (!$obj->buscar($_POST['medio'])) {
                $resul = $obj->incluir();
                if ($resul == 1) {
                    $registrarm = [
                        "title" => "Exito",
                        "message" => "¡Registro exitoso!",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de metodo de pago', $_POST["medio"], 'metodo de pago');
                } else {
                    $registrarm = [
                        "title" => "Error",
                        "message" => "Hubo un problema al intentar registrar el metodo de pago..",
                        "icon" => "error"
                    ];
                }
            } else {
                $registrarm = [
                    "title" => "Error",
                    "message" => "No se pudo registrar. El metodo de pago ya existe.",
                    "icon" => "error"
                ];
            }
        }
    }
} else if(isset($_POST['editarm'])) {
    if(empty($_SESSION["permisos"]["config_finanza"]["editar"])) {
        $editarm = [
            "title" => "Error",
            "message" => "No tiene permiso para editar medios de pago",
            "icon" => "error"
        ];
    } else {
        $errores = [];
        try {
            $existing = $obj->buscar($_POST["medio"]);
            if ($existing && $existing['cod_metodo'] != $_POST["cod_metodo"]) {
                throw new Exception("El medio de pago ya existe");
            }


            $obj->setmetodo($_POST["medio"]);
            $obj->setmodalidad($_POST["modalidad"]);
            $obj->setstatus($_POST["status"]);
            $obj->check();
            
            
            $result = $obj->editarMedio($_POST["cod_metodo"]);
            if ($result == 1) {
                $editarm = [
                    "title" => "Éxito",
                    "message" => "Medio de pago actualizado correctamente",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Edición de medio de pago', $_POST["medio"], 'medio de pago');
            } else if ($result == 'error_modalidad_asociada') {
                $editarm = [
                    "title" => "Error",
                    "message" => "No se puede cambiar la modalidad porque el medio de pago tiene tipos de pago asociados",
                    "icon" => "error"
                ];
            } else {
                throw new Exception("Error al actualizar el medio de pago");
            }
        } catch (Exception $e) {
            $editarm = [
                "title" => "Error",
                "message" => $e->getMessage(),
                "icon" => "error"
            ];
        }
    }
} else if(isset($_POST['borrarm'])) {
    if(empty($_SESSION["permisos"]["config_finanza"]["eliminar"])) {
        $borrarm = [
            "title" => "Error",
            "message" => "No tiene permiso para eliminar medios de pago",
            "icon" => "error"
        ];
    } else {
        try {
            $obj->setCodMetodo($_POST["cod_metodo"]);
            $obj->check();
            $result = $obj->eliminarMedio();
            
            if ($result == 'exito') {
                $borrarm = [
                    "title" => "Éxito",
                    "message" => "Medio de pago eliminado correctamente",
                    "icon" => "success"
                ];
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminación de medio de pago', "Eliminado el medio de pago con el código ".$_POST["cod_metodo"], 'medio de pago');
            } else if ($result == 'error_activo') {
                $borrarm = [
                    "title" => "Error",
                    "message" => "No se puede eliminar el medio de pago porque está activo. Debe desactivarlo primero.",
                    "icon" => "error"
                ];
            } else if ($result == 'error_asociado') {
                $borrarm = [
                    "title" => "Error",
                    "message" => "No se puede eliminar el medio de pago porque tiene tipos de pago asociados",
                    "icon" => "error"
                ];
            } else {
                throw new Exception("Error al eliminar el medio de pago");
            }
        } catch (Exception $e) {
            $borrarm = [
                "title" => "Error",
                "message" => $e->getMessage(),
                "icon" => "error"
            ];
        }
    }
}

if(empty($_SESSION["permisos"]["config_finanza"]["consultar"])) {
    $tipos_pago = [];
    $bancos = [];
    $cajas = [];
    $registro = [];
} else {
    $tipos_pago=$obj->medios_activos();
    $medios_pago=$obj->mediopago();
    $bancos=$obj->cuenta();
    $cajas=$obj->caja();
    $registro=$obj->consultar();
}

$_GET['ruta'] = 'tpago';
require_once 'plantilla.php';


