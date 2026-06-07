<?php
require_once __DIR__ . '/../../src/middleware/CSRF.php';
$csrf = new CSRF();
$token = $csrf->generateToken();
if (!isset($_SESSION['pending_voter'])) {
    echo '<div class="alert alert-danger">Session expired. Please register again.</div>';
    exit;
}
$email = $_SESSION['pending_voter']['email'];
// Load selected language
$lang = $_SESSION['lang'] ?? 'en';
$langFile = __DIR__ . '/../../lang/' . $lang . '.php';
if (!file_exists($langFile)) { $langFile = __DIR__ . '/../../lang/en.php'; }
$tr = include $langFile;
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($tr['verify_registration_pin_title'] ?? 'Verify Registration PIN') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/sri-lanka-theme.css">
</head>
<body>
    <div class="container mt-5">
        <div class="secure-container">
            <div class="secure-header text-center mb-4">
                <img src="../../assets/images/election-logo.jpg" alt="Sri Lanka Emblem" class="logo mb-2">
                <h2><?= htmlspecialchars($tr['verify_registration_pin_title'] ?? 'Verify Registration PIN') ?></h2>
                <p><?= htmlspecialchars($tr['pin_sent_to_email'] ?? 'A 6-digit PIN has been sent to your email:') ?> <b><?php echo htmlspecialchars($email); ?></b></p>
            </div>
            <form method="POST" action="../../src/controllers/VoterController.php" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                <input type="hidden" name="step" value="verify_pin">
                <div class="mb-3">
                    <label for="pin" class="form-label"><?= htmlspecialchars($tr['enter_pin'] ?? 'Enter PIN') ?></label>
                    <input type="text" class="form-control" id="pin" name="pin" maxlength="6" required>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="../../public/register.php" class="btn btn-secondary"><?= htmlspecialchars($tr['cancel'] ?? 'Cancel') ?></a>
                    <button type="submit" class="btn btn-success"><?= htmlspecialchars($tr['verify_pin'] ?? 'Verify PIN') ?></button>
                </div>
            </form>
        </div>
    </div>
    <footer class="secure-footer mt-5 py-3">
        <img src="../../assets/images/election-logo.jpg" alt="Election Logo" class="logo mb-2">
        <div>© 2025 Democratic Socialist Republic of Sri Lanka - Presidential Election. <?= htmlspecialchars($tr['all_rights_reserved'] ?? 'All Rights Reserved.') ?></div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 