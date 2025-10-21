<?php
// download_prices.php
session_start();
require 'vendor/autoload.php'; // Cargar la biblioteca

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// --- ¡NUEVO BLOQUEO DE SEGURIDAD! ---
// Ahora solo los admins que iniciaron sesión pueden descargar esto.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    die("Acceso denegado. Esta función es solo para administradores.");
}
// --- FIN DEL BLOQUEO ---


// 1. Cargar el JSON
if (!file_exists('tests.json')) {
    die("No se ha cargado ningún archivo de precios todavía.");
}
$tests = json_decode(file_get_contents('tests.json'), true);

// 2. Crear un nuevo objeto Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Lista de Precios Florlab');

// 3. Poner los encabezados
$sheet->setCellValue('A1', 'Nombre');
$sheet->setCellValue('B1', 'Palabras Clave (separadas por coma)');
$sheet->setCellValue('C1', 'Precio (USD)');
$sheet->getStyle('A1:C1')->getFont()->setBold(true);

// 4. Llenar los datos
$row = 2;
foreach ($tests as $test) {
    $sheet->setCellValue('A' . $row, $test['name']);
    $sheet->setCellValue('B' . $row, implode(', ', $test['keywords']));
    $sheet->setCellValue('C' . $row, $test['price_usd']);
    $sheet->getStyle('C' . $row)
          ->getNumberFormat()
          ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
    $row++;
}

// 5. Auto-ajustar columnas
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);

// 6. Enviar el archivo al navegador
$filename = 'plantilla_precios_florlab.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>