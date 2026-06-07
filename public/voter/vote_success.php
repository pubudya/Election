<?php
require_once __DIR__ . '/../../src/middleware/CSRF.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Success - Sri Lankan Election System</title>
    <link rel="icon" href="../assets/images/sri-lanka-flag.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/sri-lanka-theme.css">
</head>
<body>
    <div class="flag-bar">
        <div class="maroon"></div>
        <div class="gold"></div>
        <div class="green"></div>
        <div class="orange"></div>
    </div>
    <div class="container mt-5">
        <div class="secure-container text-center">
            <div class="secure-header mb-4">
                <img src="../assets/images/sri-lanka-flag.png" alt="Sri Lanka Flag" class="logo mb-2">
                <h2>Vote Submitted Successfully!</h2>
            </div>
            <div class="alert alert-success">
                <p>Your vote has been recorded securely. Thank you for participating in the Sri Lankan Presidential Election.</p>
            </div>
            <div class="d-grid gap-2 col-6 mx-auto mt-4">
                <a href="../../results.php" class="btn btn-warning">View Results</a>
                <a href="../../login.php" class="btn btn-primary">Logout</a>
            </div>
        </div>
    </div>
</body>
</html> 