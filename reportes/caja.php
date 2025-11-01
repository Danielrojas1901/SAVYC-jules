<?php
session_status() == PHP_SESSION_NONE ? session_start() : null;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED);

chdir(__DIR__ . '/..');
require_once "./vendor/autoload.php";
require_once 'config/config.php';

use Modelo\Caja;
use Modelo\ControlCaja;
use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$obj = new ControlCaja();
$objc = new Caja();

$cajamov = $objc->getHistorialCaja($_GET['cod_caja']);
$movimientos = $obj->getobtenerMovControl($_GET['cod_control']);
$fechaActual = date("Y-m-d H:i:s");
// Variables para totales
$totalEntradas = 0;
$totalSalidas = 0;

$html = '
<style>
    #t {
        width: 100%;
        border-collapse: collapse;
        margin: auto;
        font-size: 14px;
    }
    th, td {
        border: 1px solid black;
        padding: 10px;
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
    .info td {
        border: none;
        font-size: 14px;
        padding: 10px;
    }
    .danger {
        background-color:#FE4C50;
        color: white;
        font-weight: bold;
        text-align: center;
    }
    .success {
        background-color:#56C900;
        color: white;
        font-weight: bold;
        text-align: center;
    }
    .total-row {
        background-color:rgb(163, 163, 237);
        color: white;
        font-weight: bold;
        text-align: right;
    }
    .separador {
        border-top: 1px solid #000;
        margin: 20px 0;
    }
</style>
<page backtop="7mm" backbottom="10mm">
    <table style="width:100%; border:none;">
        <tr>
            <td style="text-align:left; border: none;">';

if (isset($_SESSION["logo"])) {
    $html .= '<img src="' . $_SESSION["logo"] . '" style="width:100px; max-width:200px;">';
} else {
    $html .= '<img src="vista/dist/img/logo_generico.png" alt="Logo" style="width:100px; max-width:200px;">';
}
$html .= '
            </td>
            <td style="text-align:left; border: none;">
                <h3 style="margin-bottom:5px;">' . $_SESSION["n_empresa"] . '</h3>
                <p style="margin:0;">' . $_SESSION["rif"] . '</p>
                <p style="margin:0;">' . $_SESSION["telefono"] . '</p>
                <p style="margin:0;">' . $_SESSION["email"] . '</p>
            </td>
        </tr>
    </table>

    <hr class="separador">
    <p style="text-align:right; font-size:12px;"><i>Fecha de generación: ' . $fechaActual . '</i></p>
    <h2 style="text-align:center;">Reporte de Movimientos de Caja</h2>
    <br>
    <table class="info">';

$m = $cajamov[0] ?? [];
$html .= '
    <tr><td><strong>Nombre:</strong> ' . ($m['nombre_caja'] ?? '-') . '</td></tr>
    <tr><td><strong>Divisa:</strong> ' . ($m['nombre_divisa'] ?? '-') . '</td></tr>
    <tr><td><strong>Responsable del cierre y conteo:</strong> ' . ($m['username'] ?? '---') . '</td></tr>
</table>
<br>';

$html .= '<table id="t">
        <thead>
            <tr>
                <th>Fecha y Hora</th>
                <th>Origen</th>
                <th>Movimiento</th>
                <th>Referencia</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>';
foreach ($movimientos as $mov) {
    $monto = floatval($mov['monto']);
    $montoFormat = number_format($monto, 2, ',', '.');
    $tipo = $mov['tipo_movimiento'];
    $clase = $tipo === 'ENTRADA' ? 'success' : 'danger';
    //$signo = $tipo === 'SALIDA' ? '-' : '';
    
    if ($tipo === 'ENTRADA') {
        $totalEntradas += $monto;
    } else {
        $totalSalidas += $monto;
    }

    $html .= '
            <tr>
                <td>' . $mov['fecha'] . '</td>
                <td>' . $mov['modulo'] . '</td>
                <td class="' . $clase . '">' . $tipo . '</td>
                <td>' . $mov['referencia'] . '</td>
                <td class="' . $clase . '">' . $montoFormat . '</td>
            </tr>';
}

$balanceFinal = $totalEntradas - $totalSalidas;
$html .= '
            <tr class="total-row">
                <td colspan="4">TOTAL ENTRADAS</td>
                <td>' . number_format($totalEntradas, 2, ',', '.') . '</td>
            </tr>
            <tr class="total-row">
                <td colspan="4">TOTAL SALIDAS</td>
<td>-' . number_format(abs($totalSalidas), 2, ',', '.') . '</td>
            </tr>
            <tr class="total-row">
                <td colspan="4">BALANCE FINAL</td>
                <td>' . number_format($balanceFinal, 2, ',', '.') . '</td>
            </tr>
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
$html2pdf->output('movimientos-caja.pdf');
