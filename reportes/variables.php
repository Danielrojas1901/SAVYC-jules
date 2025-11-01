<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

chdir(__DIR__ . '/..');
require_once "./vendor/autoload.php";
require_once 'config/config.php';
use Modelo\Gasto;
use Modelo\General;

use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$gasto = new Gasto();
$general = new General(); 

$fechaInicio = $_GET['fechaInicio'] ?? null;
$fechaFin = $_GET['fechaFin'] ?? null;
$fechas = $_GET['fechas'] ?? 'false';

// Obtener datos de gastos variables
if ($fechas === 'true' && !empty($fechaInicio) && !empty($fechaFin)) {
    // Aquí deberías implementar un método para obtener gastos variables por fecha
    // Por ahora usaremos el método existente
    $datos = $gasto->consultarGastoV();
} else {
    $datos = $gasto->consultarGastoV();
}

if (isset($datos)) {
    $fechaHoraGeneracion = date('d-m-Y H:i:s');
    
    // Obtener información de la empresa desde la base de datos
    $infoEmpresa = $general->mostrar();
    $empresaData = !empty($infoEmpresa) ? $infoEmpresa[0] : [];
    
    // Configuración de rutas y datos de empresa
    $logoPath = !empty($empresaData['logo']) ? __DIR__.'/../'.$empresaData['logo'] : false;
    
    $empresaInfo = [
        'nombre' => $empresaData['nombre'] ?? 'Nombre de Empresa',
        'rif' => $empresaData['rif'] ?? 'RIF no definido',
        'direccion' => $empresaData['direccion'] ?? 'Dirección no definida',
        'telefono' => $empresaData['telefono'] ?? 'Teléfono no definido',
        'email' => $empresaData['email'] ?? 'Email no definido',
        'logo' => $empresaData['logo'] ?? null
    ];
    
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
            background-color:rgb(0, 102, 219);
            color: white;
        }
        .fecha-generacion {
            text-align: right;
            margin-top: 10px;
            font-size: 12px;
        }
        .info-empresa {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .info-empresa td {
            border: none;
            text-align: left;
        }
        .total-row {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
    </style>
    
    <page backtop="7mm" backbottom="10mm">
        <table class="info-empresa">
            <tr>
                <td style="width:150px; border:none;">';
                
    if ($logoPath && file_exists($logoPath)) {
        $html .= '<img src="' . $logoPath . '" style="width:110px; height:auto;">';
    }
    
    $html .= '</td>
                <td style="text-align:center; font-size:12px; width:360px; border:none;">
                    <strong style="color:red; font-size:15px;">' . htmlspecialchars($empresaInfo['nombre']) . '</strong><br>
                    <b>Rif: ' . htmlspecialchars($empresaInfo['rif']) . '</b><br>
                    <b>Domicilio Fiscal: </b> ' . htmlspecialchars($empresaInfo['direccion']) . '.<br>
                    Barquisimeto Lara - <b>Teléfono: </b>' . htmlspecialchars($empresaInfo['telefono']) . '<br>
                    <b>Email: </b>' . htmlspecialchars($empresaInfo['email']) . '
                </td>
            </tr>
        </table>

        <br>
        <hr style="border=0.5px;">
        <br>
        <p class="fecha-generacion">Fecha de creación: ' . $fechaHoraGeneracion . '</p>
        <h1 style="text-align:center;">Reporte de Gastos Variables</h1>';
        
    // Mostrar rango de fechas si se filtró por fechas
    if ($fechas === 'true' && !empty($fechaInicio) && !empty($fechaFin)) {
        $html .= '<p style="text-align:center; font-size:14px;">Período: ' . htmlspecialchars($fechaInicio) . ' al ' . htmlspecialchars($fechaFin) . '</p>';
    }
    
    $html .= '
        <table id="t">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Descripción</th>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th>Monto</th>
                    <th>Fecha Creación</th>
                    <th>Último Pago</th>
                    <th>Monto Pagado</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>';
            
    $totalGastos = 0;
    $totalPagado = 0;
    $contador = 1;
    
    foreach ($datos as $dato) {
        // Calcular totales
        $totalGastos += floatval($dato['monto']);
        $totalPagado += floatval($dato['monto_ultimo_pago'] ?? 0);
        
        // Formatear estados
        $estado = '';
        if ($dato['status'] == 1) {
            $estado = 'Activo';
        } elseif ($dato['status'] == 2) {
            $estado = 'Inactivo';
        } elseif ($dato['status'] == 3) {
            $estado = 'Pagado';
        }
        
        $html .= '
                <tr>
                    <td>' . $contador . '</td>
                    <td>' . htmlspecialchars($dato['descripcion']) . '</td>
                    <td>' . htmlspecialchars($dato['categoria_nombre']) . '</td>
                    <td>' . htmlspecialchars($dato['nombre']) . '</td>
                    <td class="text-right">' . number_format($dato['monto'], 2, ',', '.') . '</td>
                    <td>' . htmlspecialchars($dato['fechac']) . '</td>
                    <td>' . (isset($dato['fecha']) && $dato['fecha'] != 'Sin fecha' ? htmlspecialchars($dato['fecha']) : 'Sin pagos') . '</td>
                    <td class="text-right">' . (isset($dato['monto_ultimo_pago']) ? number_format($dato['monto_ultimo_pago'], 2, ',', '.') : '0,00') . '</td>
                    <td>' . $estado . '</td>
                </tr>';
        $contador++;
    }
    
    // Agregar fila de totales
    $html .= '
                <tr class="total-row">
                    <td colspan="4"><strong>TOTALES</strong></td>
                    <td class="text-right"><strong>' . number_format($totalGastos, 2, ',', '.') . '</strong></td>
                    <td colspan="2"></td>
                    <td class="text-right"><strong>' . number_format($totalPagado, 2, ',', '.') . '</strong></td>
                    <td></td>
                </tr>';
    
    $html .= '
            </tbody>
        </table>
        <page_footer>
            <div style="text-align: center; font-size: 10px;">
                <p>' . htmlspecialchars($empresaInfo['telefono']) . '  |  ' . 
                  htmlspecialchars($empresaInfo['direccion']) . '  |  ' . 
                  htmlspecialchars($empresaInfo['email']) . '</p>
            </div>
        </page_footer>
    </page>';
    
    $html2pdf->writeHTML($html);
    $html2pdf->output('reporte-gastos-variables.pdf');
} else {
    echo "No hay datos disponibles para generar el reporte.";
}