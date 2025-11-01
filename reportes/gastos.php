<?php
session_start();
chdir(__DIR__ . '/..');
require_once 'vendor/autoload.php';
<<<<<<< brian
require_once 'config/config.php';
use Modelo\Gasto;
use Modelo\General;
=======
use Modelo\Gasto;
>>>>>>> testing

if (
    empty($_SESSION["logo"]) ||
    empty($_SESSION["n_empresa"]) ||
    empty($_SESSION["rif"]) ||
    empty($_SESSION["telefono"]) ||
    empty($_SESSION["email"]) ||
    empty($_SESSION["direccion"])
) {
    echo "Error: Datos de la empresa incompletos. Por favor, configure los datos de la empresa.";
    exit;
}
//var_dump($_SESSION["rif"]);
use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$objGasto = new Gasto();
<<<<<<< brian
$objGeneral = new General();
=======
>>>>>>> testing
$gastos = $objGasto->repSet(); // Obtener datos de gastos generales
$fechaActual = date("d/m/Y");

if (isset($gastos)) {
    $html = '
    <style>
   #t {
    width: 95%;
    border-collapse: collapse;
    margin: auto;
    border: 2px solid #000; /* Borde externo de la tabla de gastos */
}
#t th, #t td {
    border: 1px solid black;
    padding: 8px;
    font-size: 12px;
}
#t th {
    background-color:rgb(0, 99, 219);
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
    <p><i>Fecha de generación: ' . $fechaActual . '</i></p>
    <h1 style="text-align:center;">Reporte de Gastos Generales</h1>
    <table id="t">
        <thead>
            <tr>
             
                <th>Descripción</th>
                <th>Monto (Bs)</th>
                <th>Fecha Creación</th>
                <th>Último Pago</th>
                <th>Fecha Últ. Pago</th>
                <th>Condición</th>
                <th>Naturaleza</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($gastos as $gasto) {
        // Determinar clase CSS según el status
        $statusClass = '';
        $statusText = '';
        switch ($gasto['status']) {
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

        $html .= '<tr>
            <td>' . $gasto['descripcion'] . '</td>
            <td>' . number_format($gasto['monto'], 2, ',', '.') . ' Bs</td>
            <td>' . date('d/m/Y', strtotime($gasto['fecha_creacion'])) . '</td>
            <td>' . (isset($gasto['monto_total']) ? number_format($gasto['monto_total'], 2, ',', '.') . ' Bs' : 'N/A') . '</td>
            <td>' . (isset($gasto['fecha_pago']) ? date('d/m/Y', strtotime($gasto['fecha_pago'])) : 'N/A') . '</td>
            <td>' . ($gasto['nombre_condicion'] ?? 'N/A') . '</td>
            <td>' . $gasto['nombre_naturaleza'] . '</td>
            <td class="' . $statusClass . '">' . $statusText . '</td>
        </tr>';
    }

    // Total de gastos
    $totalGastos = array_sum(array_column($gastos, 'monto'));

    $html .= '
        </tbody>
    </table>
    <div style="margin-top: 20px; text-align: right;">
        <strong>Total General: ' . number_format($totalGastos, 2, ',', '.') . ' Bs</strong>
    </div>
    <page_footer>
        <div style="text-align: center;">
            <p>' . $_SESSION["telefono"] . '  |  ' . $_SESSION["direccion"] . '  |  ' . $_SESSION["email"] . '</p>
        </div>
    </page_footer>
</page>';

    $html2pdf->writeHTML($html);
    $html2pdf->output('reporte-gastos.pdf');
} else {
    die("No hay datos de gastos para mostrar");
}
