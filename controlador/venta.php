<?php
use Modelo\Venta;
use Modelo\Tpago;
use Modelo\Pago;
use Modelo\Bitacora;
use Modelo\Movimientos;

$obj=new Venta();
$objbitacora = new Bitacora();
$objpago=new Tpago();
$objp=new Pago();
$objmov=new Movimientos();
if(isset($_POST['buscar'])){
    $result=$obj->getb_productos($_POST['buscar']);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}else if(isset($_POST['registrarv']) && !empty($_SESSION["permisos"]["venta"]["registrar"])){
    if(!empty($_POST['cod_cliente']) && !empty($_POST['total_general']) && !empty($_POST['fecha_hora']) && !empty($_POST['condicion'])){
        if(isset($_POST['productos'])){
            $obj->setdatav($_POST);
            $resul=$obj->getregistrar($_POST['cod_cliente'], $_POST['productos']);
            header('Content-Type: application/json');
            if($resul>0){
                $objmov->rmovimiento($resul, 1);
                echo json_encode([
                    'success'=>true,
                    'cod_venta'=>$resul,
                    'total'=>$_POST['total_general'],
                    'fecha'=>$_POST['fecha_hora'],
                    'cliente'=>$_POST['nombre-cliente'],
                    'message' => 'Venta registrada exitosamente'
                ]);
                $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de venta', $_POST["total_general"], 'Venta');
            }else{
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al registrar la venta'
                ]);
            }
            exit;
        }else{
            echo json_encode([
                'success' => false,
                'message' => 'No se encontraron productos en la solicitud'
            ]);
            exit;
        }
    }else{
        echo json_encode([
            'success' => false,
            'message' => 'Faltan campos obligatorios'
        ]);
        exit;
    }

}else if(isset($_POST['finalizarp']) && !empty($_SESSION["permisos"]["venta"]["registrar"]) || isset($_POST['finalizarpcuentas']) && !empty($_SESSION["permisos"]["cuentas_pendiente"]["registrar"])){
    if(!empty($_POST['nro_venta']) && !empty($_POST['monto_pagado']) && !empty($_POST['fecha_pago'])){
            if(isset($_POST['pago'])){
                $errores=[];
                try{
                $objp->setdatap($_POST);
                $objp->check();
                $resul=$objp->get_registrar($_POST['pago'], $_POST['monto_pagar']);
                }catch(Exception $e){
                    //echo '<script>console.log("Error al registrar el pago: '.$e->getMessage().'")</script>';
                    $errores[] = $e->getMessage();
                }
                if(!empty($errores)){
                    $registrarp = [
                        "title" => "Error al registrar el pago.",
                        "message" => implode(" ", $errores),
                        "icon" => "error"
                    ];
                } else if($resul===0){
                    $objmov->mpagos($objp->get_cod_pago(), 4, 3);
                    $registrarp = [
                        "title" => "El pago de la venta ha sido registrado exitosamente.",
                        "message" => "La venta se ha completado en su totalidad.",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de pago completo', 'Venta #'.$_POST['nro_venta'].' - Monto: '.$_POST["monto_pagado"], (!isset($_POST['finalizarpcuentas']) ? 'Ventas - Pago recibido' : 'Cuentas por Cobrar - Pago recibido'));
                }else if($resul>0){
                    $objmov->mpagos($objp->get_cod_pago(), 4, 3);
                    $registrarp = [
                        "title" => "Se ha registrado un pago parcial.",
                        "message" => "El monto pendiente es de ".$resul."Bs.",
                        "icon" => "success"
                    ];
                    $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Registro de pago parcial', 'Venta #'.$_POST['nro_venta'].' - Monto: '.$_POST["monto_pagado"], (!isset($_POST['finalizarpcuentas']) ? 'Ventas - Pago recibido' : 'Cuentas por Cobrar - Pago recibido'));
                }else {
                    $registrarp = [
                        "title" => "Error al registrar el pago.",
                        "message" => "Inténtelo de nuevo o contacte a soporte.",
                        "icon" => "error"
                    ];
                }
            }
    }
}else if(isset($_POST['anular']) && !empty($_SESSION["permisos"]["venta"]["eliminar"])){
    if(!empty($_POST['cventa'])){
        $resul=$obj->anular($_POST['cventa']);
        if($resul==1){
            $anular = [
                "title" => "La venta ha sido anulada exitosamente.",
                "message" => "Todos los registros asociados han sido actualizados.",
                "icon" => "success"
            ];
            $objbitacora->registrarEnBitacora($_SESSION['cod_usuario'], 'Anulación de venta', $_POST["cventa"], 'Venta');
        }else{
            $anular = [
                "title" => "Ocurrió un error al intentar anular la venta.",
                "message" => "Inténtelo de nuevo o contacte a soporte.",
                "icon" => "error"
            ];
        }
    }
}

if(!empty($_SESSION["permisos"]["venta"]["eliminar"]) || !empty($_SESSION["permisos"]["cuentas_pendiente"]["consultar"])){
$datos=$obj->datos();
$opciones=$objpago->consultar();
$consulta=$obj->consultar();
}

if(isset($_POST["finalizarpcuentas"])){ 
    $_GET['ruta'] = 'cuentaspend';
}else{
    $_GET['ruta'] = 'venta';
}
require_once 'plantilla.php';

