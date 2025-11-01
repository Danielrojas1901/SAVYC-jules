<?php
session_start();
chdir(__DIR__ . '/..');  
require_once 'vendor/autoload.php';
require_once 'config/config.php';
use Modelo\General;
use Modelo\Gasto;

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$objGasto = new Gasto();
$objGeneral = new General();
$gastosFijos = $objGasto->consultarGastoF();
$fechaActual = date("d/m/Y");

if (isset($gastosFijos)) {
    $html = '
    <style>
    #t{
        width: 100%;
        border-collapse: collapse;
        margin: auto;
    }
    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }
    th {
        background-color: rgb(0, 99, 219);
        color: white;
    }
    .status-pendiente {
        background-color: #ffcccc;
    }
    .status-pagado {
        background-color: #ccffcc;
    }
    .status-parcial {
        background-color: #ffffcc;
    }
    .total-row {
        font-weight: bold;
        background-color: #f2f2f2;
    }
</style>
    
<page backtop="7mm" backbottom="10mm">
    <table id="membrete" style="width:100%; border:none;">
    <tr>
        <td style="text-align:left; border: none;">';
    if (isset($_SESSION["logo"])) {
        $html .= '<img src="' . $_SESSION["logo"] . '" style="width:100px; max-width:200px;">';
    } else {
        $html .= '<img src="vista/dist/img/logo_generico.png" style="width:100%; max-width:200px;">';
    }
    $html .= '
        </td>
        <td style="text-align:right; border: none;">
            <h3 style="margin-bottom: 5px;">' . $_SESSION["n_empresa"] . '</h3>
            <p style="margin-top: 0; margin-bottom: 5px;">' . $_SESSION["rif"] . '</p>
            <p style="margin-top: 0; margin-bottom: 5px;">' . $_SESSION["telefono"] . '</p>
            <p style="margin-top: 0;">' . $_SESSION["email"] . '</p>
        </td>
    </tr>
    </table>
    <br>
    <p><i>Fecha de generación: '.$fechaActual.'</i></p>
    <hr style="border=0.5px;">
    <br>
    <h1 style="text-align:center;">Reporte de Gastos Fijos</h1>
    <table id="t">
        <thead>
            <tr>
                <th>Descripción</th>
                <th>Monto (Bs)</th>
                <th>Categoría</th>
                <th>Último Pago</th>
                <th>Fecha Últ. Pago</th>
                <th>Total Pagado</th>
                <th>Vuelto</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

    $totalGeneral = 0;
    $totalPagado = 0;
    
    foreach ($gastosFijos as $gasto) {
        // Determinar clase CSS según el status
        $statusClass = '';
        $statusText = '';
        switch($gasto['status']) {
            case 1: 
                $statusClass = 'status-pendiente';
                $statusText = 'Pendiente';
                break;
            case 2: 
                $statusClass = 'status-parcial';
                $statusText = 'Parcial';
                break;
            case 3: 
                $statusClass = 'status-pagado';
                $statusText = 'Pagado';
                break;
            default:
                $statusText = $gasto['status'];
        }

        $totalGeneral += $gasto['monto'];
        $totalPagado += $gasto['total_pagos_emitidos'] ?? 0;

        $html .= '<tr>
            <td>' . $gasto['descripcion'] . '</td>
            <td>' . number_format($gasto['monto'], 2, ',', '.') . ' Bs</td>
            <td>' . $gasto['categoria_nombre'] . '</td>
            <td>' . (isset($gasto['monto_ultimo_pago']) ? number_format($gasto['monto_ultimo_pago'], 2, ',', '.') . ' Bs' : 'N/A') . '</td>
            <td>' . (isset($gasto['fecha']) ? date('d/m/Y', strtotime($gasto['fecha'])) : 'N/A') . '</td>
            <td>' . (isset($gasto['total_pagos_emitidos']) ? number_format($gasto['total_pagos_emitidos'], 2, ',', '.') . ' Bs' : 'N/A') . '</td>
            <td>' . (isset($gasto['vuelto_total']) ? number_format($gasto['vuelto_total'], 2, ',', '.') . ' Bs' : 'N/A') . '</td>
            <td class="' . $statusClass . '">' . $statusText . '</td>
        </tr>';
    }

    // Fila de totales
    $html .= '<tr class="total-row">
        <td colspan="1"><strong>TOTALES:</strong></td>
        <td>' . number_format($totalGeneral, 2, ',', '.') . ' Bs</td>
        <td colspan="3"></td>
        <td>' . number_format($totalPagado, 2, ',', '.') . ' Bs</td>
        <td></td>
        <td></td>
    </tr>';
    
    $html .= '
        </tbody>
    </table>
    <div style="margin-top: 20px;">
        <p><strong>Resumen:</strong></p>
        <p>- Total Gastos Fijos: ' . number_format($totalGeneral, 2, ',', '.') . ' Bs</p>
        <p>- Total Pagado: ' . number_format($totalPagado, 2, ',', '.') . ' Bs</p>
        <p>- Saldo Pendiente: ' . number_format(($totalGeneral - $totalPagado), 2, ',', '.') . ' Bs</p>
    </div>
    <page_footer>
        <div style="text-align: center;">
            <p>' . $_SESSION["telefono"] . '  |  ' . $_SESSION["direccion"] . '  |  ' . $_SESSION["email"] . '</p>
        </div>
    </page_footer>
</page>';

    $html2pdf->writeHTML($html);
    $html2pdf->output('reporte_gastos_fijos.pdf');
} else {
    die("No hay datos de gastos fijos para mostrar");
}