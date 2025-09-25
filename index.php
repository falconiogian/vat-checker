<?php
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/CsvImporter.php';
require_once __DIR__ . '/classes/VatValidator.php';

$db = new Database();
$message = null;
$summary = null;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
    $uploadDir = __DIR__ . '/uploads/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES['csv_file']['name']);
    $targetPath = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $targetPath)) {
        $importer = new CsvImporter($db);
        try {
            $summary = $importer->import($targetPath);
            $message = "CSV file processed successfully.";
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Failed to upload file.";
    }
}

// Fetch categorized results
$validRows = $db->getVatNumbersByStatus('valid');
$correctedRows = $db->getVatNumbersByStatus('corrected');
$invalidRows = $db->getVatNumbersByStatus('invalid');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VAT Checker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1 class="mb-4">Italian VAT Number Checker</h1>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if ($summary): ?>
        <div class="alert alert-success">
            <strong>Summary:</strong><br>
            Processed rows: <?= $summary['processed'] ?><br>
            ✅ Valid: <?= $summary['valid'] ?><br>
            🛠 Corrected: <?= $summary['corrected'] ?><br>
            ❌ Invalid: <?= $summary['invalid'] ?>
        </div>
    <?php endif; ?>

    <!-- Upload form -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Upload CSV File</h5>
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="file" name="csv_file" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Upload & Process</button>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="row">
        <div class="col-md-4">
            <h3>✅ Valid VAT Numbers</h3>
            <table class="table table-sm table-bordered table-striped">
                <thead>
                <tr><th>Original</th><th>Notes</th></tr>
                </thead>
                <tbody>
                <?php foreach ($validRows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['original_input']) ?></td>
                        <td><?= htmlspecialchars($row['notes']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-4">
            <h3>🛠 Corrected VAT Numbers</h3>
            <table class="table table-sm table-bordered table-striped">
                <thead>
                <tr><th>Original</th><th>Corrected</th><th>Notes</th></tr>
                </thead>
                <tbody>
                <?php foreach ($correctedRows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['original_input']) ?></td>
                        <td><?= htmlspecialchars($row['corrected_value']) ?></td>
                        <td><?= htmlspecialchars($row['notes']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-4">
            <h3>❌ Invalid VAT Numbers</h3>
            <table class="table table-sm table-bordered table-striped">
                <thead>
                <tr><th>Original</th><th>Notes</th></tr>
                </thead>
                <tbody>
                <?php foreach ($invalidRows as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['original_input']) ?></td>
                        <td><?= htmlspecialchars($row['notes']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <hr>
    <a href="check.php" class="btn btn-secondary">Check a Single VAT Number</a>
</div>

</body>
</html>
