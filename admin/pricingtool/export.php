<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$DB_HOST = 'localhost';
$DB_NAME = 'sellmyphonedubai';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->query("
        SELECT m.id AS model_id,
               m.model_name AS model_name,
               p.base,
               p.condition_flawless, p.condition_good, p.condition_fair,
               p.acc_charger, p.acc_earbuds, p.acc_box, p.acc_warranty
        FROM model_pricing p
        JOIN models m ON m.id = p.model_id
        ORDER BY m.brand, m.model_name
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


    $storageStmt = $pdo->query("
        SELECT model_id, label, price_delta
        FROM model_storage_options
        ORDER BY model_id, sort_order
    ");
    $storageByModel = [];
    foreach ($storageStmt->fetchAll(PDO::FETCH_ASSOC) as $s) {
        $storageByModel[$s['model_id']][] = $s;
    }

    $maxStorage = 0;
    foreach ($storageByModel as $list) {
        $maxStorage = max($maxStorage, count($list));
    }
    $maxStorage = max($maxStorage, 1); // always show at least one pair for consistency with import.php

} catch (Exception $e) {
    http_response_code(500);
    die('Export failed: ' . htmlspecialchars($e->getMessage()));
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Model Pricing');

$headers = ['Model Name', 'Base Price'];
for ($i = 1; $i <= $maxStorage; $i++) {
    $headers[] = "Storage $i Label";
    $headers[] = "Storage $i Delta";
}
$headers = array_merge($headers, ['Flawless', 'Good', 'Fair', 'Charger', 'Earbuds', 'Box', 'Warranty']);

$sheet->fromArray($headers, null, 'A1');

$lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
$headerRange = "A1:{$lastColLetter}1";
$sheet->getStyle($headerRange)->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
$sheet->getStyle($headerRange)->getFill()
    ->setFillType(Fill::FILL_SOLID)
    ->getStartColor()->setRGB('1F4E78');

$r = 2;
foreach ($rows as $row) {
    $line = [
        $row['model_name'],
        $row['base'],
    ];

    $options = $storageByModel[$row['model_id']] ?? [];
    for ($i = 0; $i < $maxStorage; $i++) {
        $line[] = $options[$i]['label'] ?? '';
        $line[] = $options[$i]['price_delta'] ?? '';
    }

    $line = array_merge($line, [
        $row['condition_flawless'],
        $row['condition_good'],
        $row['condition_fair'],
        $row['acc_charger'],
        $row['acc_earbuds'],
        $row['acc_box'],
        $row['acc_warranty'],
    ]);

    $sheet->fromArray($line, null, "A$r");
    $r++;
}

foreach (range(1, count($headers)) as $colIdx) {
    $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
    $sheet->getColumnDimension($colLetter)->setAutoSize(true);
}
$sheet->freezePane('A2');

$filename = 'model_pricing_export_' . date('Y-m-d_His') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;