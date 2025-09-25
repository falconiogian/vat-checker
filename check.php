<?php
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/VatValidator.php';

$db = new Database();
$validator = new VatValidator();

$message = null;
$result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vat_number'])) {
    $input = trim($_POST['vat_number']);
    $result = $validator->validate($input);

    // Save to DB
    $db->insertVatNumber(
        $input,
        $result['status'],
        $result['corrected'],
        $result['notes']
    );

    $message = "VAT number processed.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Single VAT Number</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">
    <h1 class="mb-4">Check Single VAT Number</h1>

    <a href="index.php" class="btn btn-secondary mb-3">← Back to CSV Upload</a>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Input form -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Enter a VAT Number</h5>
            <form method="post">
                <div class="mb-3">
                    <input type="text" name="vat_number" class="form-control" placeholder="e.g. IT12345678901" required>
                </div>
                <button type="submit" class="btn btn-primary">Check</button>
            </form>
        </div>
    </div>

    <!-- Result -->
    <?php if ($result): ?>
        <div class="card">
            <div class="card-header">
                Result
            </div>
            <div class="card-body">
                <p><strong>Original Input:</strong> <?= htmlspecialchars($input) ?></p>
                <p><strong>Status:</strong> 
                    <?php if ($result['status'] === 'valid'): ?>
                        <span class="badge bg-success">Valid</span>
                    <?php elseif ($result['status'] === 'corrected'): ?>
                        <span class="badge bg-warning text-dark">Corrected</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Invalid</span>
                    <?php endif; ?>
                </p>
                <?php if ($result['corrected']): ?>
                    <p><strong>Corrected Value:</strong> <?= htmlspecialchars($result['corrected']) ?></p>
                <?php endif; ?>
                <p><strong>Notes:</strong> <?= htmlspecialchars($result['notes']) ?></p>
            </div>
        </div>
    <?php endif; ?>
</div>

</body>
</html>
