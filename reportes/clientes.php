<?php
session_start();
chdir(__DIR__ . '/..');

require_once 'vendor/autoload.php';
require_once 'config/config.php';
use Modelo\Clientes;

use Spipu\Html2Pdf\Html2Pdf;

$obj = new Clientes();
$listado = $obj->consultar();

if (isset($listado)) { //&& $listado != []
    // Inicializa HTML2PDF
    $html2pdf = new Html2Pdf('P', 'LETTER', 'es'); 
    $fechaHoraGeneracion = date('d-m-Y');
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
        background-color: #8C57FF;
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
    <p><i>  Fecha de generación:'.$fechaHoraGeneracion.'</i> </p>
    <br>
    <hr style="border=0.5px;">
    <br>
    <h1 style="text-align:center;">Listado de Clientes</h1>
        <table id="t">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Cédula</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Direccion</th>
                </tr>
            </thead>
            <tbody>';
    foreach ($listado as $cliente) {
            $html .= '
                <tr>
                    <td>' . $cliente["cod_cliente"] . '</td>
                    <td>' . $cliente["nombre"] . '</td>
                    <td>' . $cliente["apellido"] . '</td>
                    <td>' . $cliente["cedula_rif"] . '</td>
                    <td>' . (!empty($cliente['telefono']) ? $cliente['telefono'] : 'No disponible') . '</td>
                    <td>' .  (!empty($cliente['email']) ? $cliente['email'] : 'No disponible')  . '</td>
                    <td>' . (!empty($cliente['direccion']) ? $cliente['direccion'] : 'No disponible') . '</td>
                </tr>';
    }
    $html .= '
            </tbody>
        </table>
        <!-- Pie de página -->
            <page_footer>
                <div style="text-align: center;">
                    <p>' . $_SESSION["telefono"] . '  |  ' . $_SESSION["direccion"] . '  |  ' . $_SESSION["email"] . '</p>
                    </div>
            </page_footer>
</page>';
    // Escribe el HTML en el PDF
    $html2pdf->writeHTML($html);

    // Muestra el PDF
    $html2pdf->output('reporte-clientes.pdf');
}
