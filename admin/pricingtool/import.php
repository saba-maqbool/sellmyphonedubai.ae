<?php

require 'vendor/autoload.php'; // Composer autoload (PhpSpreadsheet)

use PhpOffice\PhpSpreadsheet\IOFactory;

$DB_HOST = 'localhost';
$DB_NAME = 'sellmyphonedubai';
$DB_USER = 'root';
$DB_PASS = '';

function db(): PDO {
    global $DB_HOST, $DB_NAME, $DB_USER, $DB_PASS;
    return new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
}

function detectBrand(string $fullName): string {
    $name = trim($fullName);
    if (stripos($name, 'iPhone') === 0) return 'Apple';
    if (stripos($name, 'Samsung') === 0) return 'Samsung';
    $parts = explode(' ', $name, 2);
    return $parts[0] ?? '';
}

function getOrCreateModelId(PDO $pdo, string $fullName): int {
    $fullName = trim($fullName);
    $brand = detectBrand($fullName);

    $stmt = $pdo->prepare("SELECT id FROM models WHERE model_name = :model_name LIMIT 1");
    $stmt->execute(['model_name' => $fullName]);
    $id = $stmt->fetchColumn();
    if ($id) return (int)$id;

    $stmt = $pdo->prepare(
        "INSERT INTO models (brand, model_name, image) VALUES (:brand, :model_name, :image)"
    );
    $stmt->execute(['brand' => $brand, 'model_name' => $fullName, 'image' => '']);
    return (int)$pdo->lastInsertId();
}

function upsertPricing(PDO $pdo, array $row): void {
    $stmt = $pdo->prepare("SELECT id FROM model_pricing WHERE model_id = :model_id LIMIT 1");
    $stmt->execute(['model_id' => $row['model_id']]);
    $existingId = $stmt->fetchColumn();

    if ($existingId) {
        $sql = "UPDATE model_pricing SET
                    base = :base,
                    condition_flawless = :condition_flawless,
                    condition_good = :condition_good,
                    condition_fair = :condition_fair,
                    acc_charger = :acc_charger,
                    acc_earbuds = :acc_earbuds,
                    acc_box = :acc_box,
                    acc_warranty = :acc_warranty
                WHERE model_id = :model_id";
    } else {
        $sql = "INSERT INTO model_pricing
                    (model_id, base,
                     condition_flawless, condition_good, condition_fair,
                     acc_charger, acc_earbuds, acc_box, acc_warranty)
                VALUES
                    (:model_id, :base,
                     :condition_flawless, :condition_good, :condition_fair,
                     :acc_charger, :acc_earbuds, :acc_box, :acc_warranty)";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($row);
}

/** Replace all storage options (e.g. 128GB, 256GB, 1TB) for a model. */
function replaceStorageOptions(PDO $pdo, int $modelId, array $options): void {
    $stmt = $pdo->prepare("DELETE FROM model_storage_options WHERE model_id = :model_id");
    $stmt->execute(['model_id' => $modelId]);

    if (empty($options)) return;

    $stmt = $pdo->prepare(
        "INSERT INTO model_storage_options (model_id, label, price_delta, sort_order)
         VALUES (:model_id, :label, :price_delta, :sort_order)"
    );
    $order = 0;
    foreach ($options as $opt) {
        if ($opt['label'] === '') continue;
        $stmt->execute([
            'model_id'    => $modelId,
            'label'       => $opt['label'],
            'price_delta' => $opt['delta'],
            'sort_order'  => $order,
        ]);
        $order++;
    }
}

function numOrNull($v) {
    return is_numeric($v) ? (float)$v : null;
}

function countStoragePairs(array $headerRow): int {
    $flawlessIdx = null;
    foreach ($headerRow as $idx => $label) {
        if (trim((string)$label) === 'Flawless') {
            $flawlessIdx = $idx;
            break;
        }
    }
    if ($flawlessIdx === null) {
        return 2;
    }
    $storageColCount = $flawlessIdx - 2;
    return max(1, (int) floor($storageColCount / 2));
}

$summary = ['updated' => 0, 'skipped' => 0, 'errors' => []];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pricing_file'])) {

    $file = $_FILES['pricing_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $summary['errors'][] = 'File upload failed. Please try again.';
    } else {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['xlsx', 'xls'])) {
            $summary['errors'][] = 'Please upload a .xlsx or .xls file.';
        } else {
            try {
                $spreadsheet = IOFactory::load($file['tmp_name']);
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray(null, true, true, false);

                $pdo = db();

                $storagePairs = countStoragePairs($rows[0] ?? []);
                $flawlessCol = 2 + ($storagePairs * 2); // first column after all storage pairs

                foreach ($rows as $i => $r) {
                    if ($i === 0) continue; // skip header row

                    $modelName = trim((string)($r[0] ?? ''));
                    $base      = $r[1] ?? null;

                    if ($modelName === '' || !is_numeric($base)) {
                        if ($modelName !== '') {
                            $summary['skipped']++;
                            $summary['errors'][] = "Row " . ($i + 1) . " ('$modelName'): invalid or missing base price.";
                        }
                        continue;
                    }

                    $modelId = getOrCreateModelId($pdo, $modelName);

                    $data = [
                        'model_id'            => $modelId,
                        'base'                => (float)$base,
                        'condition_flawless'  => numOrNull($r[$flawlessCol]     ?? null) ?? 0,
                        'condition_good'      => numOrNull($r[$flawlessCol + 1] ?? null) ?? 0,
                        'condition_fair'      => numOrNull($r[$flawlessCol + 2] ?? null) ?? 0,
                        'acc_charger'         => numOrNull($r[$flawlessCol + 3] ?? null) ?? 0,
                        'acc_earbuds'         => numOrNull($r[$flawlessCol + 4] ?? null) ?? 0,
                        'acc_box'             => numOrNull($r[$flawlessCol + 5] ?? null) ?? 0,
                        'acc_warranty'        => numOrNull($r[$flawlessCol + 6] ?? null) ?? 0,
                    ];

                    upsertPricing($pdo, $data);

                    $options = [];
                    for ($p = 0; $p < $storagePairs; $p++) {
                        $labelCol = 2 + ($p * 2);
                        $deltaCol = $labelCol + 1;
                        $options[] = [
                            'label' => trim((string)($r[$labelCol] ?? '')),
                            'delta' => numOrNull($r[$deltaCol] ?? null) ?? 0,
                        ];
                    }
                    replaceStorageOptions($pdo, $modelId, $options);

                    $summary['updated']++;
                }

            } catch (Exception $e) {
                $summary['errors'][] = 'Error processing file: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Import Pricing — SellMyPhoneDubai Admin</title>
    <style>
        body { font-family: -apple-system, Arial, sans-serif; max-width: 640px; margin: 60px auto; color: #1f2937; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        p.sub { color: #6b7280; margin-top: 0; }
        form { border: 1px solid #e5e7eb; border-radius: 10px; padding: 24px; margin-top: 24px; }
        input[type=file] { display: block; margin: 12px 0 20px; }
        button { background: #1f4e78; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-size: 14px; }
        button:hover { background: #163a58; }
        .result { margin-top: 24px; padding: 16px 20px; border-radius: 8px; }
        .result.ok { background: #ecfdf5; border: 1px solid #a7f3d0; }
        .result.warn { background: #fef2f2; border: 1px solid #fecaca; }
        ul { margin: 8px 0 0; padding-left: 20px; font-size: 13px; color: #7f1d1d; }
    </style>
</head>
<body>
    <h1>Import Pricing</h1>
    <p class="sub">Upload the pricing Excel file to update <code>model_pricing</code> and <code>model_storage_options</code>.</p>

    <form method="post" enctype="multipart/form-data">
        <input type="file" name="pricing_file" accept=".xlsx,.xls" required>
        <button type="submit">Upload &amp; Import</button>
    </form>

    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="result <?= empty($summary['errors']) ? 'ok' : 'warn' ?>">
            <strong><?= $summary['updated'] ?> model(s) updated.</strong>
            <?php if ($summary['skipped'] > 0): ?>
                <br><?= $summary['skipped'] ?> row(s) skipped.
            <?php endif; ?>
            <?php if (!empty($summary['errors'])): ?>
                <ul>
                    <?php foreach ($summary['errors'] as $err): ?>
                        <li><?= htmlspecialchars($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <p><a href="export.php">Export current pricing to Excel →</a></p>
</body>
</html>