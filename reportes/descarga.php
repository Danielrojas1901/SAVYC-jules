<?php
session_start();

chdir(__DIR__ . '/..');
require_once "./vendor/autoload.php";
require_once 'config/config.php';
use Modelo\Descarga;

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$objDescarga = new Descarga();

// Obtener parámetros de filtrado
$fechaInicio = $_GET['fechaInicio1'] ?? null;
$fechaFin = $_GET['fechaFin1'] ?? null;

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $datos = $objDescarga->descargafecha($fechaInicio, $fechaFin);
} else {
    $datos = $objDescarga->consultardescargapdf();
}

$fechaActual = date("d/m/Y");

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
        background-color: #5271ff;
        color: #ffffff;
    }
</style>
    
<page backtop="7mm" backbottom="10mm">
    <table id="membrete" style="width:100%; border:none;">
    <tr>
        <td style="text-align:left; border: none";>';

if (isset($_SESSION["logo"])) {
    $html .= '<img src="' . $_SESSION["logo"] . '" style="width:100px; max-width:200px;">';
} else {
    $html .= '<img src="vista/dist/img/logo_generico.png" alt="Quesera Don Pedro" style="width:100%; max-width:200px;">';
}

$html .= '
        </td>
        <td style="text-align:right; border: none;">
            <h3 style="margin-bottom: 5px;">' . $_SESSION["n_empresa"] . '</h3>
            <p style="margin: 0;">' . $_SESSION["rif"] . '</p>
            <p style="margin: 0;">' . $_SESSION["telefono"] . '</p>
            <p style="margin: 0;">' . $_SESSION["email"] . '</p>
        </td>
    </tr>
    </table>
    <br>
    <p class="fecha-generacion"><i>Fecha de generación: ' . $fechaActual . '</i></p>
    <hr style="border:0.5px;">';

if (!empty($fechaInicio) && !empty($fechaFin)) {
    $html .= '<p class="rango-fechas">Rango del reporte: ' . date('d/m/Y', strtotime($fechaInicio)) . ' al ' . date('d/m/Y', strtotime($fechaFin)) . '</p>';
} 
$html .= '
        <h1 style="text-align:center;">Listado de Descarga de productos</h1>
        <table id="t">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th> 
                    <th>Descripcion</th>
                    <th>Lote</th>
                    <th>Fecha</th>
                    <th>Cantidad descargada</th>
                </tr>
            </thead>
            <tbody>';
    
    foreach ($datos as $d) {
        $fechabd = $d['fecha'];
        $fecha = date('d-m-Y', strtotime($fechabd));
        $producto= $d['nombre'].' '.$d['marca'].($d['presentacion'] ? ' x '.$d['presentacion'] : '');

        $html .= '<tr>
            <td>' . htmlspecialchars($d['cod_presentacion']) . '</td>
            <td>' . htmlspecialchars($producto) . '</td>
            <td>' . htmlspecialchars($d['descripcion']) . '</td>
            <td>' . htmlspecialchars($d['lote']) . '</td>
            <td>' . htmlspecialchars($fecha) . '</td>
            <td style="text-align:center;">' . htmlspecialchars($d['cantidad']) . '</td>
        </tr>';
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
    $html2pdf->output('reporte-descarga.pdf');
