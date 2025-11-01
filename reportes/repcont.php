<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL & ~E_DEPRECATED); 
chdir(__DIR__ . '/..');
require_once "./vendor/autoload.php";
require_once 'config/config.php';
use Modelo\Movimientos;
use Spipu\Html2Pdf\Html2Pdf;

$fecha_inicio = $_GET['fecha_inicio1'] ?? date('Y-01-01');
$fecha_fin    = $_GET['fecha_fin1']    ?? date('Y-12-31');
$codigo_cuenta_filtro = $_GET['codigo_cuenta'] ?? '';
$fechaActual = date("d/m/Y"); 
$obj = new Movimientos();
$resultados = $obj->reporte1($fecha_inicio, $fecha_fin, $codigo_cuenta_filtro);

$html = '
<style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            color: #333;
        }

        /* Contenedor principal */
        .page-container {
            width: 100%;
            padding: 0;
            position: relative;
        }

        /* Membrete */
        .membrete {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .membrete td {
            border: none;
            padding: 0;
            vertical-align: top;
        }

        .logo-section {
            width: 35%;
            text-align: left;
        }

        .logo-section img {
            max-width: 100px;
            height: auto;
        }

        .company-info {
            width: 65%;
            text-align: right;
            padding-left: 20px;
        }

        .company-info h3 {
            font-size: 14px;
            margin-bottom: 5px;
            color: #333;
            font-weight: bold;
        }

        .company-info p {
            font-size: 11px;
            margin: 2px 0;
            color: #666;
            line-height: 1.2;
        }

        /* Información del reporte */
        .report-info {
            text-align: right;
            font-size: 10px;
            margin: 8px 0;
            color: #666;
        }

        .report-info div {
            margin: 2px 0;
        }

        .report-title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 15px 0 10px 0;
            color: #333;
            text-transform: uppercase;
            border-bottom: 2px solid #5271ff;
            padding-bottom: 4px;
            clear: both;           
            display: block; 

        }

        /* Sección de cuenta */
        .account-section {
            margin-top: 20px;         
            margin-bottom: 15px;
            clear: both;
        }

        .account-title {
            font-size: 13px;
            font-weight: bold;
            margin: 20px 0 15px 0;
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #5271ff;
            color: #333;
            clear: both;         
            display: block;
        }

        /* Tabla de movimientos */
        .movements-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }

        .movements-table th,
        .movements-table td {
            border: 1px solid #ddd;
            padding: 6px 5px;
            text-align: left;
        }

        .movements-table th {
            background-color: #5271ff;
            color: white;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            padding: 8px 5px;
        }

        .movements-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .movements-table tbody tr {
            border-bottom: 1px solid #eee;
        }

        /* Alineaciones */
        .text-center { 
            text-align: center !important; 
        }
        
        .text-right { 
            text-align: right !important; 
        }
        
        .text-left { 
            text-align: left !important; 
        }

        /* Footer de tabla */
        .movements-table tfoot td {
            background-color: #f5f5f5;
            font-weight: bold;
            border-top: 2px solid #5271ff;
            padding: 8px 5px;
            font-size: 10px;
        }

        /* Totales generales */
        .summary-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .summary-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            text-align: center;
        }


        /* Footer */
        .footer-info {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 9px;
            color: #666;
            padding: 10px;
            border-top: 1px solid #ddd;
        }

        /* Ajustes para PDF */
        @page {
            margin: 50mm 15mm 25mm 15mm;
        }
        .main-content {
            margin-top: 20px;
            padding-top: 10px;
        }
        pagebreak {
            page-break-before: always;
        }

        .total-final {
            background-color: #f5f5f5;
            font-weight: bold;
            border-top: 2px solid #5271ff;
        }

</style>
<page backtop="50mm" backbottom="15mm" backleft="10mm" backright="10mm">
    <page_header>
        <table class="membrete">
            <tr>
                <td class="logo-section">
                    <img src="'.realpath(__DIR__.'/../vista/dist/img/logo_generico.png').'" alt="Logo">
                </td>
                <td class="company-info">
                    <h3>'. $_SESSION["n_empresa"] .'</h3>
                    <p>'.$_SESSION["rif"].'</p>
                    <p>Teléfono: '.$_SESSION["telefono"].'</p>
                    <p>Email: '.$_SESSION["email"].'</p>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="header-details-cell">
                    <div style="text-align:right; margin-bottom: 2px; font-size:10px; color:#666;"><i>Fecha de generación: '.date('d/m/Y').'</i></div>
                    <div style="text-align:right; font-size:10px; color:#666;">Rango del reporte: '.$fecha_inicio.' al '.$fecha_fin.'</div>
                    <h1 class="report-title">Reporte General</h1>
                </td>
            </tr>
        </table>
    </page_header>';

    $cuentasAgrupadas = [];
    foreach ($resultados as $row) {
        $idCuenta = $row['CodCuenta']; 
        if (!isset($cuentasAgrupadas[$idCuenta])) {
            $cuentasAgrupadas[$idCuenta] = [
                'codigo' => $row['CodigoCuenta'],
                'nombre' => $row['NombreCuenta'],
                'movimientos' => []
            ];
        }
        $cuentasAgrupadas[$idCuenta]['movimientos'][] = $row;
    }
    /*echo '<pre>';
    var_dump($cuentasAgrupadas);
    echo '</pre>';
    exit;*/
    // Generar sección por cada cuenta
    $first = true;
    foreach ($cuentasAgrupadas as $cuenta) {
        $codigo = $cuenta['codigo'];
        $nombreCuenta = $cuenta['nombre'];
        $movimientos = $cuenta['movimientos'];
        if (!$first) {
            $html .= '<div style=" height:1px; page-break-before: always;"></div>';
        }
        $html .= '<div class="account-section">';
        $first = false;
        
    
        $html .= '
            
                <h3 class="account-title">Cuenta: ' . $codigo . ' - ' . $nombreCuenta . '</h3>
                <table class="movements-table">
                    <thead>
                        <tr>
                            <th style="width: 12%;">Fecha</th>
                            <th style="width: 8%;">No. Asiento</th>
                            <th style="width: 42%;">Descripción</th>
                            <th style="width: 13%;">Debe</th>
                            <th style="width: 13%;">Haber</th>
                            <th style="width: 12%;">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>';
    
        foreach ($movimientos as $mov) {
            $html .= '
                        <tr>
                            <td class="text-center">' . date('d/m/Y', strtotime($mov['FechaAsiento'])) . '</td>
                            <td class="text-center">' . $mov['NumeroAsiento'] . '</td>
                            <td>' . htmlspecialchars($mov['DescripcionAsiento']) . '</td>
                            <td class="text-right">' . number_format($mov['Debe'], 2, ',', '.') . '</td>
                            <td class="text-right">' . number_format($mov['Haber'], 2, ',', '.') . '</td>
                            <td class="text-right">' . number_format($mov['SaldoAcumulado'], 2, ',', '.') . '</td>
                        </tr>';
        }
    
        $ultimoSaldo = end($movimientos)['SaldoAcumulado'];
    
        $html .= '
                    <tr class="total-final">
                        <td colspan="5" class="text-right"><strong>SALDO FINAL ' . strtoupper($nombreCuenta) . ':</strong></td>
                        <td class="text-right"><strong>' . number_format($ultimoSaldo, 2, ',', '.') . '</strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>';
    }
// Cuenta 2: Bancos
$html .= '
    <page_footer>
        <div class="footer-info">
            Página [[page_cu]] de [[page_nb]]<br>
            '.$_SESSION["telefono"].' | '.$_SESSION["direccion"].' | '.$_SESSION["email"].'
        </div>
    </page_footer>
</page>';
// --- 6. Generar el PDF ---


    // Configuración de Html2Pdf: Orientación, Tamaño, Idioma, Unicode, Margenes
    // Usamos 'LETTER' y backtop='30mm' para dejar espacio para la cabecera dinámica.
    // Los márgenes [izquierdo, superior, derecho, inferior]
    $html2pdf = new Html2Pdf('P', 'LETTER', 'es', true, 'UTF-8', [10, 7, 10, 10]); // Ajustado backtop
    $html2pdf->setDefaultFont('Arial'); // Asegúrate de que esta fuente esté disponible o sea genérica.
    $html2pdf->writeHTML($html);
    $html2pdf->output('LibroMayor_' . $fecha_inicio . '_' . $fecha_fin . '.pdf');
    $html2pdf->clean();

?>