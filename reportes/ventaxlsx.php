<?php
session_start();
chdir(__DIR__ . '/..');
require_once "./vendor/autoload.php";
require_once 'config/config.php';
use Modelo\General;
use Modelo\Venta;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing; // Importar la clase Drawing
use PhpOffice\PhpSpreadsheet\Style\Color; // Importar la clase Color
use PhpOffice\PhpSpreadsheet\Style\Style; // Importar la clase Style
// Objetos
$obj=new Venta();
$general=new General();
// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$fechaInicio = $_POST['fechaInicio1'] ?? null;
$fechaFin = $_POST['fechaFin1'] ?? null;
if (!empty($fechaInicio) && !empty($fechaFin)) {
    $dato=$obj->venta_f($fechaInicio, $fechaFin);
} else {
    $dato=$obj->consultar(); // Obtener todos los datos si no hay filtros
}

// Agregar el logo
if (!empty($_SESSION["logo"])) {
    $drawing = new Drawing();
    $drawing->setName('Logo');
    $drawing->setDescription('Logo de la empresa');
    $drawing->setPath($_SESSION["logo"]); // Ruta del logo
    $drawing->setHeight(50); // Altura del logo
    $drawing->setCoordinates('A1'); // Coordenadas donde se insertará el logo
    $drawing->setWorksheet($sheet);
}

// Agregar encabezado de la empresa
$sheet->setCellValue('B1', $_SESSION["n_empresa"]);
$sheet->setCellValue('B2', 'RIF: ' . $_SESSION["rif"]);
$sheet->setCellValue('B3', 'Teléfono: ' . $_SESSION["telefono"]);
$sheet->setCellValue('B4', 'Email: ' . $_SESSION["email"]);
$sheet->setCellValue('B5', 'Dirección: ' . $_SESSION["direccion"]);

// Establecer formato para el encabezado
$sheet->getStyle('B1:O5')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('B1:O5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

// Establecer color de fondo para el encabezado
$sheet->getStyle('A1:J5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('A1:J5')->getFill()->getStartColor()->setARGB('DB6A00'); // Color de fondo

// Ajustar el ancho de las columnas
$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(10);
/*$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setWidth(15);*/
$sheet->getColumnDimension('H')->setWidth(30);
$sheet->getColumnDimension('I')->setWidth(30);
$sheet->getColumnDimension('J')->setWidth(30);

// Dejar una fila en blanco antes de los encabezados de la tabla
$row = 7;

// Establecer los encabezados de la tabla
$sheet->setCellValue('A' . $row, 'Nro. de venta');
$sheet->setCellValue('B' . $row, 'Cliente');
$sheet->setCellValue('C' . $row, 'fecha');
$sheet->setCellValue('D' . $row, 'monto');
$sheet->setCellValue('E' . $row, 'status');
/*$sheet->setCellValue('F' . $row, 'Costo');
$sheet->setCellValue('G' . $row, 'IVA');*/
$sheet->setCellValue('H' . $row, 'Total de ventas completadas');
$sheet->setCellValue('I' . $row, 'Total de ventas con Pagos parciales');
$sheet->setCellValue('J' . $row, 'Total de ventas pendientes');

// Establecer formato para el encabezado de la tabla
$sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
$sheet->getStyle('A' . $row . ':E' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Establecer color de fondo para el encabezado de la tabla de productos
$sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('A' . $row . ':E' . $row)->getFill()->getStartColor()->setARGB('D9EAD3'); // Color de fondo verde claro

// Aplicar bordes al encabezado de la tabla de productos
// Aplicar bordes al encabezado de la tabla de productos
$sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => '000000'], // Color de los bordes
        ],
    ],
]);

$comp=0;
$ncomp=0;
$pend=0;
$npend=0;
$pp=0;
$npp=0;
$anu=0;
// Llenar los datos de los productos
$row++; // Pasar a la siguiente fila
foreach ($dato as $venta) {
    $sheet->setCellValue('A' . $row, $venta["cod_venta"]);
    $sheet->setCellValue('B' . $row, $venta["nombre"]." ".$venta["apellido"]);
    $sheet->setCellValue('C' . $row, date("d-m-y", strtotime($venta["fecha"])));
    $sheet->setCellValue('D' . $row, $venta["total"]." Bs");
    $sheet->setCellValue('E' . $row, $venta["status"]);
    if ($venta['status'] == 3) {
        $comp+=$venta['total'];
        $ncomp+=1;
    } elseif ($venta['status'] == 2) {
        $pp+=$venta['total'];
        $npp+=1;
    } elseif ($venta['status'] == 1) {
        $pend+=$venta['total'];
        $npend+=1;
    }
    /*$sheet->setCellValue('F' . $row, $venta["costo"]);
    $sheet->setCellValue('G' . $row, $venta["excento"]);*/

   // Aplicar sombreado de fondo a la fila de datos (más claro)
   $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
   $sheet->getStyle('A' . $row . ':E' . $row)->getFill()->getStartColor()->setARGB('F0F0F0'); // Color de fondo gris claro

   // Aplicar bordes a las filas de datos
    $sheet->getStyle('A' . $row . ':E' . $row)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
               'color' => ['argb' => '000000'], // Color de los bordes
            ],
        ],
    ]);
    $row++;
}

$sheet->getStyle('H' . 7 . ':J' . 7)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('H' . 7 . ':J' . 7)->getFill()->getStartColor()->setARGB('D9EAD3');

$sheet->getStyle('H' . 8 . ':J' . 8)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('H' . 8 . ':J' . 8)->getFill()->getStartColor()->setARGB('F0F0F0');

$sheet->setCellValue('H' . 8, $comp);
$sheet->setCellValue('I' . 8, $pp); 
$sheet->setCellValue('J' . 8, $pend);

// Establecer el nombre del archivo
$filename = 'ventas.xlsx';

// Crear un escritor para guardar el archivo
$writer = new Xlsx($spreadsheet);

// Forzar la descarga del archivo
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Guardar el archivo en la salida
$writer->save('php://output');
exit;
?>