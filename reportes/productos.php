<?php
session_start();
chdir(__DIR__ . '/..');  
require_once 'vendor/autoload.php';
require_once 'config/config.php';
use Modelo\Productos;

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$objProducto = new Productos();
$datos = $objProducto->getmostrar();
$fechaActual = date("d/m/Y");

if (isset($datos)) {
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
    <p><i>  Fecha de generación:'.$fechaActual.'</i> </p>
    <hr style="border=0.5px;">
    <br>
    <h1 style="text-align:center;">Listado de Productos</h1>
    <table id="t">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Marca</th>
                    <th>Presentacion</th>
                    <th>Costo</th>
                    <th>IVA</th>
                    <th>Precio V.</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>';
    foreach ($datos as $datos) {
        $html .= '<tr>
        <td>' . $datos['cod_producto'] . '</td>
        <td>' . $datos['nombre'] . '</td>
        <td>' . (!empty($datos['marca']) ? $datos['marca'] : 'No disponible') . '</td>
        <td>' . (!empty($datos['presentacion_concat']) ? $datos['presentacion_concat'] : 'No disponible') . '</td>
        <td>' . $datos['costo'] . 'Bs </td>
        <td>';

        // Validación de exento
        if ($datos["excento"] == 1) {
            $html .= 'E'; 
        } else {
            $html .= 'G'; 
        }
        $html .= '</td>
                <td>';

                // Validación de exento y cálculo de precio
                if ($datos["excento"] == 1) {
                    $precioVenta = ($datos["porcen_venta"] / 100 + 1) * $datos["costo"];
                    $html .= number_format($precioVenta, 2, '.', '') . " Bs"; // 2 decimales
                } else {
                    $costoiva = $datos["costo"] * 1.16;
                    $precioVenta = ($datos["porcen_venta"] / 100 + 1) * $costoiva;
                    $html .= number_format($precioVenta, 2, '.', '') . " Bs"; // 2 decimales
                }
        $html .= '</td>
                <td>' . $datos['stock_total'] . '</td>
            </tr>';
    }
    $html .= '
            </tbody>
    </table>
    <page_footer>
                <div style="text-align: center;">
                    <p>' . $_SESSION["telefono"] . '  |  ' . $_SESSION["direccion"] . '  |  ' . $_SESSION["email"] . '</p>
                    </div>
    </page_footer>
</page>';
    $html2pdf->writeHTML($html);
    $html2pdf->output('reporte-productos.pdf');
}