<?php
session_start();


chdir(__DIR__ . '/..');
require_once "./vendor/autoload.php";   
require_once 'config/config.php';
use Modelo\Compras;
use Modelo\General;
use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$obj=new Compras();
$general = new General();

$fechaInicio = $_POST['fechaInicio'] ?? null;
$fechaFin = $_POST['fechaFin'] ?? null;
if (!empty($fechaInicio) && !empty($fechaFin)) {
    $compra=$obj->compra_f($fechaInicio, $fechaFin);
} else {
    $compra=$obj->getconsultar();
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
        background-color:rgb(21, 107, 235);
        color: white;
        text-align: center;
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
    <h1 style="text-align: center; font-size: 18px;">Listado de compras a proveedores</h1>
    <table id="t">
    <thead>
            <tr>
                <th>Nro. de Compra</th>
                <th>Proveedor</th>
                <th>Fecha de recepcion</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>';
    $comp=0;
    $ncomp=0;
    foreach ($compra as $datos) {
        $html .= '
            <tr>
                <td id="td1" style="text-align: center;">000' . $datos['cod_compra'] . '</td>
                <td id="td1">' . $datos["razon_social"] .'</td>
                <td id="td1" style="text-align: center;">' . $datos['fecha'] . '</td>
                <td id="td1" style="text-align: right;">' . number_format($datos['total'], 2). ' Bs</td>
            </tr>';
        $comp+=$datos['total'];
        $ncomp+=1;
    }
        
    $html .= '
        </tbody>
    </table>
    <br>
    <table id="t">
        <tr>
            <td id="td1" ><b>Total de Compras:</b></td>
            <td id="td1" > '.$ncomp.' </td>
            <td id="td1" > '.$comp.' Bs</td>
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
