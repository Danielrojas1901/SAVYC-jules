<?php
session_start();

chdir(__DIR__ . '/..');

require_once "./vendor/autoload.php";
require_once 'config/config.php';
use Modelo\General;
use Modelo\Venta;

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$obj=new Venta();
$general=new General();

$dato=$obj->datos();
$fechaActual = date("d/m/Y");
$fechaInicio = $_GET['fechaInicio1'] ?? null;
$fechaFin = $_GET['fechaFin1'] ?? null;
$fechas = $_GET['fechas'] ?? 'false';


$html = '
<style>
    body {
        font-family: Arial, sans-serif;
    }
    #t {
        width: 100%;
        border-collapse: collapse;
        margin: auto;
        font-size: 10px;
    }
    th, td {
        border: 1px solid black;
        padding: 5px;
    }
    th {
        background-color: #5271ff;
        color: white;
        text-align: center;
        text-transform: uppercase;
    }
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }
    .fecha-generacion, .rango-fechas, .resumen {
        text-align: right;
        font-size: 10px;
        margin-top: 5px;
    }
    .info-empresa td {
        font-size: 11px;
        border: none;
    }
</style>
<page backtop="7mm" backbottom="10mm">
    <table id="membrete" style="width:100%; border:none;">
    <tr>
        <td style="text-align:left; border: none";>';
    if (isset($_SESSION["logo"])) {
        $html .= '<img src="' . $_SESSION["logo"] . '" style="width:100px; max-width:200px;">'; //ajustar el tama;o
    } else {
        $html .= '<img src="vista/dist/img/logo_generico.png" alt="Quesera Don Pedro" style="width:100%; max-width:200px;">';
    }
    $html .= '
        </td>
            <td style="text-align:rigth; border: none;">';
    $html .= '  
                <h3 style="margin-bottom: 5px;">' . $_SESSION["n_empresa"] . '</h3>
                <p style="margin-top: 0; margin-bottom: 5px;">' . $_SESSION["rif"] . '</p>
                <p style="margin-top: 0; margin-bottom: 5px;">' . $_SESSION["telefono"] . '</p>
                <p style="margin-top: 0;">' . $_SESSION["email"] . '</p>
                </td>
        </tr>
    </table>
    <br>
    <p class="fecha-generacion"><i>Fecha de generación: ' . $fechaActual . '</i></p>
    <hr style="border:0.5px;">';

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $html .= '<p class="rango-fechas">Rango del reporte: ' . date('d/m/Y', strtotime($fechaInicio)) . ' al ' . date('d/m/Y', strtotime($fechaFin)) . '</p>';
}
$html .= '<h2 style="text-align:center;">Reporte de clientes y sus ventas</h2>';
$html .= '<table id="t">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Cédula/RIF</th>
                <th>Teléfono</th>
                <th>Cantidad de Ventas</th>
                <th>Monto Total</th>
            </tr>
        </thead>
        <tbody>';

foreach ($dato as $datos) {
    if ($datos['cantidad_ventas'] > 0) {
        $html .= '
            <tr>
                <td>' . htmlspecialchars($datos["nombre"] . ' ' . $datos["apellido"]) . '</td>
                <td>' . htmlspecialchars($datos['cedula_rif']) . '</td>
                <td>' . htmlspecialchars($datos['telefono']) . '</td>
                <td style="text-align:center;">' . $datos['cantidad_ventas'] . '</td>
                <td style="text-align:right;">' . number_format($datos['monto_total'], 2, ',', '.') . ' Bs</td>
            </tr>';
    }
}

$html .= '
        </tbody>
    </table>

    <page_footer>
        <div style="text-align:center; font-size:10px;">
            Página [[page_cu]] de [[page_nb]]<br>
            ' . $_SESSION["telefono"] . ' | ' . ($_SESSION["direccion"] ?? 'Dirección no registrada') . ' | ' . $_SESSION["email"] . '
        </div>
    </page_footer>
</page>';

$html2pdf->writeHTML($html);
$html2pdf->output();
