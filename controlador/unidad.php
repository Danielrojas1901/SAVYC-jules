<?php
use Modelo\Unidad;
use Modelo\Bitacora;

$objUnidad = new Unidad;
$objbitacora = new Bitacora();

if (isset($_POST['buscar'])) {
    $tipo_medida = $_POST['buscar'];
    $result = $objUnidad->getbuscar($tipo_medida);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;

} else if (isset($_POST["guardar"]) && !empty($_SESSION["permisos"]["config_producto"]["registrar"])|| isset($_POST["guardaru"])) {
  
    if (!empty($_POST["tipo_medida"])) {
       
        $errores = [];
        try {
            
            $objUnidad->setTipo($_POST["tipo_medida"]);
         
        
            $objUnidad->check(); // Lanza excepción si hay errores
          
           
        } catch (Exception $e) {
            $errores[] = $e->getMessage();
        }
          // Si hay errores, se muestra el mensaje de error
    if (!empty($errores)) {
        $registrar = [
            "title" => "Error",
            "message" => implode(" ", $errores),
            "icon" => "error"
        ];
    } else {
            if (!$objUnidad->getbuscar($_POST['tipo_medida'])) {
              
                
                $resul = $objUnidad->getcrearUnidad();

                if ($resul == 1) {
                    $registrar = [
                        "title" => "Exito",
                        "message" => "¡Registro exitoso!",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de unidad de medida', $_POST["tipo_medida"], 'Unidad de medida');
                } else {
                    $registrar = [
                        "title" => "Error",
                        "message" => "Hubo un problema al intentar registrar la unidad de medida..",
                        "icon" => "error"
                    ];
                }
            } else {
                $registrar = [
                    "title" => "Error",
                    "message" => "No se pudo registrar. La unidad de medida ya existe.",
                    "icon" => "error"
                ];
            }
        }
    } 
} else if (isset($_POST['editar']) && !empty($_SESSION["permisos"]["config_producto"]["editar"])) {

    $tipo_medida = $_POST['tipo_medida'];
    $status = $_POST['status'];

        if ($tipo_medida !== $_POST['origin']) {
            // Si la unidad cambió, verificamos si ya existe en la base de datos
            if ($objUnidad->getbuscar($tipo_medida)) {
                $advertencia = [
                    "title" => "Error",
                    "message" => "No se pudo registrar porque el nombre de usuario ya existe.",
                    "icon" => "error"
                ];
            }
        }
          // Si hay errores, se muestra el mensaje de error
          $errores = [];
        // Validaciones
        if (!empty($tipo_medida)){
           try {
                $objUnidad->setCod($_POST["cod_unidad"]);
                $objUnidad->setTipo($_POST["tipo_medida"]);
                $objUnidad->setStatus($status);
                $objUnidad->check(); // Lanza excepción si hay errores
                
                $res = $objUnidad->geteditar();
                if ($res == 1) {
                    $editar = [
                        "title" => "Editado con éxito",
                        "message" => "La unidad ha sido actualizada",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar unidad de medida', $_POST["tipo_medida"], 'Unidad de medida');
                } else {
                    $editar = [
                        "title" => "Error",
                        "message" => "Hubo un problema al editar la unidad de medida",
                        "icon" => "error"
                    ];
                }
            } catch (Exception $e) {
                $errores[] = $e->getMessage();  
            }
            // Si hay errores, se muestra el mensaje de error
            if (!empty($errores)) {
                $editar = [
                    "title" => "Error",
                    "message" => implode(" ", $errores),
                    "icon" => "error"
                ];
            }
            
        } else {
            $editar = [
                "title" => "Error",
                "message" => "No se permiten campos vacios.",
                "icon" => "error"
            ];
        }
        
} else if (isset($_POST['eliminar']) && !empty($_SESSION["permisos"]["config_producto"]["eliminar"])) {
    
    $cod_unidad = $_POST['eliminar'];
    $resul = $objUnidad->geteliminar($cod_unidad);

    if ($resul == 'success') {
        $eliminar = [
            "title" => "Eliminado con éxito",
            "message" => "La unidad de medida ha sido eliminada",
            "icon" => "success"
        ];
        $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Eliminar unidad de medida', "Eliminado la unidad de medida con el código ".$_POST["eliminar"], 'Unidad de medida');
    } else if ($resul == 'error_status') {
        $eliminar = [
            "title" => "Error",
            "message" => "La unidad de medida no se puede eliminar porque tiene status: activo",
            "icon" => "error"
        ];
    } else if ($resul == 'error_associated') {
        $eliminar = [
            "title" => "Error",
            "message" => "La unidad de medida no se puede eliminar porque tiene productos asociados",
            "icon" => "error"
        ];
    } else if ($resul == 'error_delete') {
        $editar = [
            "title" => "Error",
            "message" => "Hubo un problema al eliminar la unidad de medida error delete",
            "icon" => "error"
        ];
    } else if($resul == 'error_query'){
        $editar = [
            "title" => "Error",
            "message" => "Hubo un problema al eliminar la unidad de medida error",
            "icon" => "error"
        ];
    }
}
if(!empty($_SESSION["permisos"]["config_producto"]["consultar"])){
$datos = $objUnidad->consultarUnidad();
}
if(isset($_POST["vista"])){
    $_GET['ruta'] = 'productos';
}else{
    $_GET['ruta'] = 'unidad';
}
require_once 'plantilla.php';
