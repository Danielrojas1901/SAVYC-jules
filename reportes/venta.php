<?php
session_start();
chdir(__DIR__ . '/..');
require_once "./vendor/autoload.php";
require_once 'config/config.php';
use Modelo\Venta;
use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$obj=new Venta();
//$general=new General();

$fechaInicio = $_GET['fechaInicio1'] ?? null;
$fechaFin = $_GET['fechaFin1'] ?? null;
if (!empty($fechaInicio) && !empty($fechaFin)) {
    $dato=$obj->venta_f($fechaInicio, $fechaFin);
} else {
    $dato=$obj->consultar(); 
}

$html = '
<style>
    #t {
        width: 95%; /* Ajuste del ancho de la tabla */
        border-collapse: collapse;
        margin: auto;
    }
    th{
        border: 1px solid black;
        padding: 8px;
        font-size: 12px;
    }
    th {
        background-color: #5271ff;
        color: white;
        text-align: center;
        color: white;
    }
    #td1 {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }
    #td2 {
        border: 1px solid black;
        padding: 8px;
        text-align: center;
    }
</style>

<page backtop="7mm" backbottom="10mm">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 150px;">
                <img src="' . $_SESSION["logo"] . '" style="width: 110px; height: auto;">
            </td>
            <td style="text-align: center; font-size: 12px; width: 500px;">
                <strong style="color: red; font-size: 15px;">' . $_SESSION["n_empresa"] . '</strong><br>
                <b>Rif: ' . $_SESSION["rif"] . '</b><br>
                <b>Domicilio Fiscal:</b> ' . $_SESSION["direccion"] . '.<br>
                Barquisimeto Lara - <b>Teléfono:</b> ' . $_SESSION["telefono"] . '<br>
                <b>Email:</b> ' . $_SESSION["email"] . '
            </td>
        </tr>
    </table>

    <br>
    <hr style="border: 0.5px solid black;">
    <br>
    <h1 style="text-align: center; font-size: 20px; color: black;">Lista de Clientes y sus ventas</h1>
    <table id="t">
    <thead>
            <tr>
                <th>Nro. de Venta</th>
                <th>Cliente</th>
                <th>Fecha de emision</th>
                <th>monto</th>
                <th>Estado de venta</th>
            </tr>
        </thead>
        <tbody>';
    $comp=0;
    $ncomp=0;
    $pend=0;
    $npend=0;
    $pp=0;
    $npp=0;
    $anu=0;
    foreach ($dato as $datos) {
        if ($datos['status'] == 1) {
            $status = '<span style="background-color: #6c757d; color: white; padding: 5px; border-radius: 5px;">Pendiente</span>';
        } elseif ($datos['status'] == 2) {
            $status = '<span style="background-color: #FFB400; color: black; padding: 5px; border-radius: 5px;">Pagada parcialmente</span>';
        } elseif ($datos['status'] == 3) {
            $status = '<span style="background-color: #56C900; color: white; padding: 5px; border-radius: 5px;">Completada</span>';
        } else {
            $status = '<span style="background-color: #FE4C50; color: white; padding: 5px; border-radius: 5px;">Anulada</span>';
        }
        $html .= '
                <tr>
                    <td id="td1" style="text-align: center;">000' . $datos['cod_venta'] . '</td>
                    <td id="td1">' . $datos["nombre"] . ' ' . $datos["apellido"] . '</td>
                    <td id="td1" style="text-align: center;">' . date("d-m-y", strtotime($datos['fecha'])) . '</td>
                    <td id="td1" style="text-align: right;">' . number_format($datos['total'], 2). ' Bs</td>';
                    if ($datos['status'] == 1) {
                        $html .= '<td id="td2" style="background-color: #9c9c9c; color: white; padding: 5px;">Pendiente</td>';
                    } elseif ($datos['status'] == 2) {
                        $html .= '<td id="td2" style="background-color: #FFB400; color: black; padding: 5px;">Pagada parcialmente</td>';
                    } elseif ($datos['status'] == 3) {
                        $html .= '<td id="td2" style="background-color: #28a745; color: white; padding: 5px;">Completada</td>';
                    } else {
                        $html .= '<td id="td2" style="background-color: #FE4C50; color: white; padding: 5px;">Anulada</td>';
                    }
                $html .= '</tr>';
        if ($datos['status'] == 3) {
            $comp+=$datos['total'];
            $ncomp+=1;
        } elseif ($datos['status'] == 2) {
            $pp+=$datos['total'];
            $npp+=1;
        } elseif ($datos['status'] == 1) {
            $pend+=$datos['total'];
            $npend+=1;
        } else{
            $anu+=1;
        }
    }
        
    $html .= '
        </tbody>
    </table>
    <br>
    <table id="t">
        <tr>
            <td id="td1" ><b>Total de ventas completadas:</b></td>
            <td id="td1" > '.$ncomp.' </td>
            <td id="td1" > '.$comp.' Bs</td>
        </tr>
        <tr>
            <td id="td1" ><b>Total de ventas con Pagos parciales:  </b></td>
            <td id="td1" >'.$npp.'</td>
            <td id="td1" >'.$pp.' Bs</td>
        </tr>
        <tr>
            <td id="td1" ><b>Total de ventas pendientes:  </b></td>
            <td id="td1" >'.$npend.'</td>
            <td id="td1" >'.$pend.' Bs</td>
        </tr>
        <tr>
            <td id="td1" ><b>Total de ventas pendientes:  </b></td>
            <td colspan="2" id="td1" style="text-align: center;">'.$anu.'</td>
        </tr>
    </table>

    <page_footer>
        <div style="text-align: center; font-size: 12px; margin-top: 10px;">
            <p>' . $_SESSION["telefono"] . ' | ' . $_SESSION["direccion"] . ' | ' . $_SESSION["email"] . '</p>
        </div>
    </page_footer>
</page>';

$html2pdf->writeHTML($html);
$html2pdf->output();
