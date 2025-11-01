<?php
session_status() == PHP_SESSION_NONE ? session_start() : null;
require_once "./vendor/autoload.php";
use Modelo\Venta;
use Spipu\Html2Pdf\Html2Pdf;

$html2pdf = new Html2Pdf('P', 'LETTER', 'es');
$obj=new Venta();

$dventa=$obj->factura($_POST['cod_venta']);

$html = '
<page backtop="7mm" backbottom="10mm" style=" justify-content: center;">
    <table style="width: 100%; border-collapse: collapse; align-item: center;">
        <tr>
            <td style="width:150px;">
                <img src="' . $_SESSION["logo"] . '" style="width:110px; height:auto;">
            </td>
            <td style="text-align:center; font-size:12px; width:360px;">
                <strong  style="color:red; font-size:15px;">'.$_SESSION["n_empresa"].'</strong><br>
                <b>Rif:  '.$_SESSION["rif"].'</b><br>
                <b>Domicilio Fiscal: </b> '.$_SESSION["direccion"].'.<br>
                Barquisimeto Lara - <b>Teléfono: </b>'.$_SESSION["telefono"].'<br>
                <b>Email: </b>'.$_SESSION["email"].'
            </td>
            <td style="text-align:center; color:red; width:200px; right=10px">
                <strong style="font-size:20px;">NRO. VENTA</strong><br>
                <span style="font-size:15px;">000'.$_POST["cod_venta"].'</span>
            </td>
        </tr>
    </table>

    <table style="width: 95%; font-size:10px; margin-top: 10px; border-collapse: collapse;">
        <tr>
            <td style="border: 1px solid #666; padding: 5px; background-color: #f9f9f9; width: 35%;"><b>Cliente:</b>  '.$_POST["cliente"].'</td>
            <td style="border: 1px solid #666; padding: 5px; background-color: #f9f9f9; width: 35%;"><b>C.I/Rif: </b> '.$_POST["cedula"].'</td>
            <td style="border: 1px solid #666; padding: 5px; background-color: #f9f9f9; text-align:right; width: 30%;"><b>Fecha:</b> '.$_POST["fecha"].'</td>
        </tr>
        <tr>
            <td style="border: 1px solid #666; padding: 5px; background-color: #f9f9f9; width: 35%;"><b>Telefono:  </b>'.(!empty($_POST["telefono"]) ? $_POST["telefono"] : "").'</td>
            <td colspan="2" style="border: 1px solid #666; padding: 5px; background-color: #f9f9f9; width: 65%;"><b>Direccion:  </b>'.(!empty($_POST["direccion"]) ? $_POST["direccion"] : "").'</td>
        </tr>
    </table>

    <table style="width: 95%; font-size:10px; margin-top: 10px; border-collapse: collapse;">
        <tr>
            <th style="border: 1px solid #666; text-align:center; padding: 5px; background-color: #dedede; width: 10%;">Codigo</th>
            <th style="border: 1px solid #666; text-align:center; padding: 5px; background-color: #dedede; width: 34%;">Producto</th>
            <th style="border: 1px solid #666; text-align:center; padding: 5px; background-color: #dedede; width: 10%;">Cantidad</th>
            <th style="border: 1px solid #666; text-align:center; padding: 5px; background-color: #dedede; width: 7%;">Unidad</th>
            <th style="border: 1px solid #666; text-align:center; padding: 5px; background-color: #dedede; width: 17%;">Valor Unit.</th>
            <th style="border: 1px solid #666; text-align:center; padding: 5px; background-color: #dedede; width: 5%;">Iva</th>
            <th style="border: 1px solid #666; text-align:center; padding: 5px; background-color: #dedede; width: 17%;">Valor Total</th>
        </tr>';
$subtotal=0;
$excento=0;
$base=0;

foreach ($dventa as $detalle) {
    $html .= '
        <tr>
            <td style="border: 1px solid #666; padding: 5px; text-align:center;">' . $detalle['cod_presentacion'] . '</td>
            <td style="border: 1px solid #666; padding: 5px; text-align:left;">' . $detalle['producto_nombre']." ".($detalle['marca'] ?? '')." ".$detalle["presentacion"] . '</td>
            <td style="border: 1px solid #666; padding: 5px; text-align:center;">' . $detalle['cantidad'] . '</td>
            <td style="border: 1px solid #666; padding: 5px; text-align:center;">' . $detalle['tipo_medida'] . '</td>
            <td style="border: 1px solid #666; padding: 5px; text-align:center;">' . number_format(($detalle['importe']/$detalle['cantidad']), 2). ' Bs</td>
            <td style="border: 1px solid #666; padding: 5px; text-align:center;">' . ($detalle['excento'] == 1 ? "E" : "G") . '</td>
            <td style="border: 1px solid #666; padding: 5px; text-align:center;">' . $detalle['importe'] . ' Bs</td>
        </tr>';
	$subtotal+=$detalle["importe"];
	if($detalle["excento"]==1){
		$excento+=$detalle["importe"];
	}
	if($detalle["excento"]==2){
		$base+=$detalle["importe"];
	}
}

$html .= '
    </table>

    <table style="width: 95%; font-size:10px; margin-top: 10px; border-collapse: collapse;">
        <tr>
            <td style="width: 70%; border-right: 1px solid #666;"></td>
            <td style="border: 1px solid #666; padding: 5px; background-color: #f9f9f9; text-align:right; width: 10%;"><b>Subtotal:</b></td>
            <td style="border: 1px solid #666; padding: 5px; text-align:center; width: 20%;">'.number_format($subtotal,2).' Bs</td>
        </tr>
        <tr>
            <td style="width: 70%; border-right: 1px solid #666;"></td>
            <td style="border: 1px solid #666; padding: 5px; background-color: #f9f9f9; text-align:right; width: 10%;"><b>Excento:</b></td>
            <td style="border: 1px solid #666; padding: 5px; text-align:center; width: 20%;">'.number_format($excento,2).' Bs</td>
        </tr>
        <tr>
            <td style="width: 70%; border-right: 1px solid #666;"></td>
            <td style="border: 1px solid #666; padding: 5px; background-color: #f9f9f9; text-align:right; width: 10%;"><b>Base imponible:</b></td>
            <td style="border: 1px solid #666; padding: 5px; text-align:center; width: 20%;">'.number_format($base,2).' Bs</td>
        </tr>
        <tr>
            <td style="width: 70%; border-right: 1px solid #666;"></td>
            <td style="border: 1px solid #666; padding: 5px; background-color: #f9f9f9; text-align:right; width: 10%;"><b>Iva(16%):</b></td>
            <td style="border: 1px solid #666; padding: 5px; text-align:center; width: 20%;">'.number_format(($base*0.16),2).' Bs</td>
        </tr>
        <tr>
            <td style="width: 70%; border-right: 1px solid #666;"></td>
            <td style="border: 1px solid #666; padding: 5px; background-color: #f9f9f9; text-align:right; width: 10%;"><b>Total:</b></td>
            <td style="border: 1px solid #666; padding: 5px; text-align:center; width: 20%;">'.$_POST["total"].' Bs</td>
        </tr>
    </table>

    <page_footer>
        <div style="text-align: center; font-size:12px; margin-top: 10px;">
            <p>' . $_SESSION["telefono"] . ' | ' . $_SESSION["direccion"] . ' | ' . $_SESSION["email"] . '</p>
        </div>
    </page_footer>
</page>';

$html2pdf->writeHTML($html);
//para rellenar con 0 el codigo de venta
$html2pdf->output('Venta' . str_pad($_POST['cod_venta'], 3, '0', STR_PAD_LEFT) . '.pdf');

