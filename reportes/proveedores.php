<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED); // Evita mostrar warnings por strtotime(null)

chdir(__DIR__ . '/..');
require_once "./vendor/autoload.php";
require_once 'config/config.php';
use Modelo\Proveedores;

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$objProveedores = new Proveedores();
$registro = $objProveedores->getconsulta();

$fechaActual = date("d/m/Y");

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

$html .= '<h2 style="text-align:center;">Lista de proveedores</h2>';

$html .= '<table id="t">
    <thead>
        <tr>
            <th>Código</th>
            <th>Rif</th>
            <th>Razon social</th>
            <th>Correo electronico</th>
            <th>Dirección</th>
            <th>Telefonos</th>
            <th>Representante</th>
        </tr>
    </thead>
    <tbody>';
foreach ($registro as $datos) {
    if ($datos["proveedor_status"] == 1) {
        $status = 'Activo';
    } elseif ($datos["proveedor_status"] == 0) {
        $status = 'Inactivo';
    }
    $html .= '
            <tr>
                <td>' . $datos['cod_prov'] . '</td>
                <td>' . $datos['rif'] . '</td>
                <td>' . $datos['razon_social'] . '</td>
                <td>' . (!empty($datos['email']) ? $datos['email'] : 'No disponible') . '</td>
                <td>' . (!empty($datos['direccion']) ? $datos['direccion'] : 'No disponible') . '</td>
                <td>' . (!empty($datos['telefonos']) ? $datos['telefonos'] : 'No disponible') . '</td>
                <td>' . (!empty($datos['representante']) ? $datos['representante'] : 'No disponible') . '</td>
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
$html2pdf->output();
