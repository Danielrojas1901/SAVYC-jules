<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
use Modelo\General;
use Modelo\Bitacora;

$objGeneral= new General();
$objBitacora = new Bitacora();

if(isset($_POST['buscar'])){
    $result=$objGeneral->buscar();
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

else if (isset($_POST["guardar"])) {
        if (empty($_SESSION["permisos"]["seguridad"]["registrar"])) {
            $registrar = [
                "title" => "Error",
                "message" => "No tiene permiso para registrar información de la empresa",
                "icon" => "error"
            ];
        } else if (!empty($_POST["rif"]) && !empty($_POST['nombre']) && !empty($_POST['direccion'])) {
            try {
                $objGeneral->setRif($_POST["rif"]);
                $objGeneral->setNom($_POST["nombre"]);
                $objGeneral->setDir($_POST["direccion"]);
                $objGeneral->settlf($_POST["telefono"]);
                $objGeneral->setemail($_POST["email"]);
                $objGeneral->setDescri($_POST["descripcion"]);

                if (isset($_FILES['logo'])) {
                    $rutaLogo = $objGeneral->procesar($_FILES['logo']);
                    $objGeneral->setlogo($rutaLogo);
                }

                $objGeneral->check();
                $resul = $objGeneral->getregistrar($_POST['Horario']);

                if ($resul == 1) {
                    $registrar = [
                        "title" => "Registrado con éxito",
                        "message" => "La informacion de la empresa ha sido registrada",
                        "icon" => "success"
                    ];
                    $objBitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de empresa', $_POST["nombre"], 'Empresas');
                } else {
                    $registrar = [
                        "title" => "Error",
                        "message" => "Hubo un problema al registrar la informacion de la empresa",
                        "icon" => "error"
                    ];
                }
            } catch (Exception $e) {
                $registrar = [
                    "title" => "Error",
                    "message" => $e->getMessage(),
                    "icon" => "error"
                ];
            }
        } else {
            $registrar = [
                "title" => "Error",
                "message" => "Los campos no pueden ir vacíos",
                "icon" => "error"
            ];
        }
} else if (isset($_POST['editar'])) {
    if (empty($_SESSION["permisos"]["seguridad"]["editar"])) {
        $editar = [
            "title" => "Error",
            "message" => "No tiene permiso para editar información de la empresa",
            "icon" => "error"
        ];
    } else {
        try {
            $objGeneral->setRif($_POST['rif']);
            $objGeneral->setNom($_POST['nombre']);
            $objGeneral->setDir($_POST['direccion']);
            $objGeneral->settlf($_POST['telefono']);
            $objGeneral->setemail($_POST['email']);
            $objGeneral->setDescri($_POST['descripcion']);
            $objGeneral->setcod($_POST['cod']);

            if (isset($_FILES['logo1']) && $_FILES['logo1']['error'] === UPLOAD_ERR_OK) {
                $datos = $objGeneral->mostrar();
                $currentLogo = $datos[0]['logo'];
                
                $rutaLogo = $objGeneral->procesar($_FILES['logo1'], $currentLogo);
                $objGeneral->setlogo($rutaLogo);
                
                if ($currentLogo && $currentLogo !== $objGeneral->getlogo()) {
                    $objGeneral->eliminarImagen($currentLogo);
                }
            } else {
                $datos = $objGeneral->mostrar();
                $objGeneral->setlogo($datos[0]['logo']);
            }

            $objGeneral->check();
            $res = $objGeneral->geteditar($_POST['Horario']);

            if ($res == 1) {
                $_SESSION["logo"] = $objGeneral->getlogo();
                $editar = [
                    "title" => "Actualizado con éxito",
                    "message" => "Información actualizada con éxito",
                    "icon" => "success"
                ];
                $objBitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Editar empresa', $_POST["nombre"], 'Empresas');
            } else {
                $editar = [
                    "title" => "Error",
                    "message" => "Error al actualizar",
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
}
$datos=[];
$horarios=[];

if(!empty($_SESSION["permisos"]["seguridad"]["consultar"])){
    $datos=$objGeneral->mostrar();
    $horarios=$objGeneral->horarios();
}


if(!empty($datos)){
    echo '<script> console.log("paso el condicional"); </script>';
        $_SESSION["logo"]=$datos[0]["logo"];
        $_SESSION["n_empresa"]=$datos[0]["nombre"];
        $_SESSION["rif"]=$datos[0]["rif"];
        $_SESSION["telefono"] = $datos[0]["telefono"];
        $_SESSION["email"] = $datos[0]["email"];
        $_SESSION["direccion"] = $datos[0]["direccion"];
    echo  '<script> console.log(' . json_encode($_SESSION) . '); </script>';
    session_write_close();
}
if(isset($_POST["inicio"])){
    $_GET['ruta']=$_POST["inicio"];
} else {
$_GET['ruta']='general';
    }
require_once 'plantilla.php';
/*if(!empty($datos)){

    $_SESSION["logo"] = $datos[0]["logo"];

    //agregado por mi
    $_SESSION["nombre-empresa"] = $datos[0]["nombre"];
    $_SESSION["rif"] = $datos[0]["rif"];


    $_SESSION["dir-empresa"]=$datos[0]["direccion"];
    $_SESSION["tlf-empresa"]=$datos[0]["telefono"];
    $_SESSION["email-empresa"]=$datos[0]["email"];

}*/