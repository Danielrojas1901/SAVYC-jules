<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED); 

chdir(__DIR__ . '/..');
require_once "./vendor/autoload.php";
require_once 'config/config.php';
use Modelo\CuentasPend;

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$obcuentas = new CuentasPend();

// Obtener datos
$datos = $obcuentas->getmostrarCuentasPagar();

$fechaActual = date("d/m/Y");
$fechaInicio = $_GET['fechaInicio1'] ?? null;
$fechaFin = $_GET['fechaFin1'] ?? null;
$fechas = $_GET['fechas'] ?? 'false';

if ($fechas === 'true' && !empty($fechaInicio) && !empty($fechaFin)) {
    $datos = $obcuentas->cuentaspagarporfecha($fechaInicio, $fechaFin);
} else {
    $datos = $obcuentas->getmostrarCuentasPagar();
}

// Calcular totales
$total = $pagado = $pendiente = 0;
foreach ($datos as $c) {
    $total += $c['monto_total'];
    $pagado += $c['monto_pagado'];
    $pendiente += $c['monto_pendiente'];
}

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

$html .= '<h2 style="text-align:center;">Reporte de Cuentas por Pagar</h2>';

$vencidas = count(array_filter($datos, fn($c) => $c['status'] === 'Vencido'));
$pendientes = count(array_filter($datos, fn($c) => $c['status'] === 'Pendiente'));
$parciales = count(array_filter($datos, fn($c) => $c['status'] === 'Pago parcial'));

$html .= '<p class="resumen">Resumen: ' . count($datos) . ' transacciones | ' . $vencidas . ' vencidas | ' . $parciales . ' parcial | ' . $pendientes . ' pendientes</p>';

$html .= '<table id="t">
    <thead>
        <tr>
            <th>#</th>
            <th>Asunto</th>
            <th>Fecha de Creación</th>
            <th>Fecha de Vencimiento</th>
            <th>Importe Total</th>
            <th>Monto Pagado</th>
            <th>Saldo Pendiente</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>';

$contador = 1;
$total = 0;
$pagado = 0;
$pendiente = 0;

foreach ($datos as $cuenta) {
    $fecha = (!empty($cuenta['fecha']) && strtotime($cuenta['fecha'])) ? date('d/m/Y', strtotime($cuenta['fecha'])) : '';
    $fechaVenc = (!empty($cuenta['fecha_vencimiento']) && strtotime($cuenta['fecha_vencimiento'])) ? date('d/m/Y', strtotime($cuenta['fecha_vencimiento'])) : 'No disponible';

    $total += $cuenta['monto_total'];
    $pagado += $cuenta['monto_pagado'];
    $pendiente += $cuenta['monto_pendiente'];

    $rowStyle = ($cuenta['status'] === 'Vencido') ? 'style="background-color:#f8d7da;"' : ($cuenta['status'] === 'Pendiente' ? 'style="background-color:#FFD2AA;"' : 'style="background-color:#F7EFB5;"');

    $html .= '<tr ' . $rowStyle . '>
        <td style="text-align:center;">' . $contador++ . '</td>
        <td>' . htmlspecialchars($cuenta['asunto']) . '</td>
        <td style="text-align:center;">' . $fecha . '</td>
        <td style="text-align:center;">' . $fechaVenc . '</td>
        <td style="text-align:right;">' . number_format($cuenta['monto_total'], 2, ',', '.') . '</td>
        <td style="text-align:right;">' . number_format($cuenta['monto_pagado'], 2, ',', '.') . '</td>
        <td style="text-align:right;">' . number_format($cuenta['monto_pendiente'], 2, ',', '.') . '</td>
        <td style="text-align:center;">' . htmlspecialchars($cuenta['status']) . '</td>
    </tr>';
}

$html .= '</tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align:right; font-weight:bold;">TOTALES:</td>
            <td style="text-align:right; font-weight:bold;">' . number_format($total, 2, ',', '.') . '</td>
            <td style="text-align:right; font-weight:bold;">' . number_format($pagado, 2, ',', '.') . '</td>
            <td style="text-align:right; font-weight:bold;">' . number_format($pendiente, 2, ',', '.') . '</td>
            <td style="background-color:#D4D6D5"></td>
        </tr>
    </tfoot>
</table>

<page_footer>
    <div style="text-align:center; font-size:10px;">
        Página [[page_cu]] de [[page_nb]]<br>
        ' . $_SESSION["telefono"] . ' | ' . ($_SESSION["direccion"] ?? 'Dirección no registrada') . ' | ' . $_SESSION["email"] . '
    </div>
</page_footer>
</page>';


$html2pdf->writeHTML($html);
$html2pdf->output('reporte-cuentas-pagar.pdf');

