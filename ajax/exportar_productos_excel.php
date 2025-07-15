<?php
include("is_logged.php"); 
require_once("../config/db.php");
require_once("../config/conexion.php");

require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$headers = ['codigo', 'nombre', 'categoria', 'precio', 'stock'];
$sheet->fromArray($headers, NULL, 'A1');

$query = "
    SELECT 
        p.codigo_producto AS codigo, 
        p.nombre_producto AS nombre, 
        IFNULL(c.nombre_categoria, 'sin categoría') AS categoria, 
        p.precio_producto_cons_final AS precio, 
        p.stock
    FROM products p
    LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
";

$result = mysqli_query($con, $query);

$rowNumber = 2;
while ($row = mysqli_fetch_assoc($result)) {
    $sheet->setCellValue('A' . $rowNumber, $row['codigo']);
    $sheet->setCellValue('B' . $rowNumber, $row['nombre']);
    $sheet->setCellValue('C' . $rowNumber, $row['categoria']);
    $sheet->setCellValue('D' . $rowNumber, number_format($row['precio'], 2, '.', ''));
    $sheet->setCellValue('E' . $rowNumber, $row['stock']);
    $rowNumber++;
}

foreach (range('A', 'F') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="productos.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
