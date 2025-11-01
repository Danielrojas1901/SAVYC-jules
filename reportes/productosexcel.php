<?php
//LISTO 03-06-2025
session_start();

chdir(__DIR__ . '/..');
require_once "./vendor/autoload.php";
require_once 'config/config.php';
use Modelo\Productos;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing; // Importar la clase Drawing
use PhpOffice\PhpSpreadsheet\Style\Color; // Importar la clase Color
use PhpOffice\PhpSpreadsheet\Style\Style; // Importar la clase Style
// Objetos
$obj = new Productos();
// Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$dato = $obj->getmostrar();

// Agregar el logo HACERLO CON VARIABLES DE SESION
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
$sheet->getStyle('A1:I5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('A1:I5')->getFill()->getStartColor()->setARGB('5271ff'); // Color de fondo

// Ajustar el ancho de las columnas
$sheet->getColumnDimension('A')->setWidth(10);
$sheet->getColumnDimension('B')->setWidth(30);
$sheet->getColumnDimension('C')->setWidth(20);
$sheet->getColumnDimension('D')->setWidth(15);
$sheet->getColumnDimension('E')->setWidth(10);
$sheet->getColumnDimension('F')->setWidth(15);
$sheet->getColumnDimension('G')->setWidth(15);
$sheet->getColumnDimension('H')->setWidth(30);
$sheet->getColumnDimension('I')->setWidth(30);


// Dejar una fila en blanco antes de los encabezados de la tabla
$row = 7;

// Establecer los encabezados de la tabla
$sheet->setCellValue('A' . $row, 'Código');
$sheet->setCellValue('B' . $row, 'Nombre');
$sheet->setCellValue('C' . $row, 'Marca');
$sheet->setCellValue('D' . $row, 'Presentación');
$sheet->setCellValue('E' . $row, 'Categoría');
$sheet->setCellValue('F' . $row, 'Costo');
$sheet->setCellValue('G' . $row, 'IVA');
$sheet->setCellValue('H' . $row, 'Precio de venta');
$sheet->setCellValue('I' . $row, 'Stock');


// Establecer formato para el encabezado de la tabla
$sheet->getStyle('A' . $row . ':I' . $row)->getFont()->setBold(true);
$sheet->getStyle('A' . $row . ':I' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Establecer color de fondo para el encabezado de la tabla de productos
$sheet->getStyle('A' . $row . ':I' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('A' . $row . ':I' . $row)->getFill()->getStartColor()->setARGB('D9EAD3'); // Color de fondo verde claro

// Aplicar bordes al encabezado de la tabla de productos
// Aplicar bordes al encabezado de la tabla de productos
$sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => '000000'], // Color de los bordes
        ],
    ],
]);

$comp = 0;
$ncomp = 0;
$pend = 0;
$npend = 0;
$pp = 0;
$npp = 0;
$anu = 0;
// Llenar los datos de los productos
// Llenar los datos de los productos
$row++; // Pasar a la siguiente fila
foreach ($dato as $producto) {
    $precioVenta = ($producto["porcen_venta"] / 100 + 1) * $producto["costo"];
    
    $sheet->setCellValue('A' . $row, $producto["cod_producto"]);
    $sheet->setCellValue('B' . $row, $producto["nombre"]);
    $sheet->setCellValue('C' . $row, ($producto["marca"]) ? $producto["marca"] : 'No disponible');
    $sheet->setCellValue('D' . $row, ($producto["presentacion"]) ? $producto["presentacion"] : 'No disponible');
    $sheet->setCellValue('E' . $row, $producto["cat_nombre"]);
    $sheet->setCellValue('F' . $row, $producto["costo"]);
    $sheet->setCellValue('G' . $row, ($producto["excento"] == 1 ? 'E' : 'G'));
    $sheet->setCellValue('H' . $row, $precioVenta);
    $sheet->setCellValue('I' . $row, $producto['stock_total']); // Aquí puedes agregar el stock real si lo tienes
    //$sheet->setCellValue('J' . $row, 'Detalle'); // Aquí puedes agregar el detalle real si lo tienes

   // Aplicar sombreado de fondo a la fila de datos (más claro)
   $sheet->getStyle('A' . $row . ':I' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
   $sheet->getStyle('A' . $row . ':I' . $row)->getFill()->getStartColor()->setARGB('F4F6F9'); // Color de fondo gris claro

   // Aplicar bordes a las filas de datos
   $sheet->getStyle('A' . $row . ':I' . $row)->applyFromArray([
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => ['argb' => '000000'], // Color de los bordes
           ],
       ],
   ]);
    $row++;
}

$sheet->getStyle('H' . 7 . ':I' . 7)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('H' . 7 . ':I' . 7)->getFill()->getStartColor()->setARGB('D9EAD3');

$sheet->getStyle('H' . 8 . ':I' . 8)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
$sheet->getStyle('H' . 8 . ':I' . 8)->getFill()->getStartColor()->setARGB('F0F0F0');

$sheet->setCellValue('H' . 8, $comp);
$sheet->setCellValue('I' . 8, $pp);

// Establecer el nombre del archivo
$filename = 'producto.xlsx';

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