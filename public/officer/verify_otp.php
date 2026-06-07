<?php 
session_start();
require_once __DIR__ . '/../../src/middleware/CSRF.php';
$csrf = new CSRF();
$token = $csrf->generateToken();
$error = $_SESSION['otp_error'] ?? ($_GET['error'] ?? '');
$success = $_SESSION['otp_success'] ?? ($_GET['success'] ?? '');
unset($_SESSION['otp_error'], $_SESSION['otp_success']);
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
    <title><?= htmlspecialchars($tr['otp_verification'] ?? 'OTP Verification') ?></title>
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
        <div class="secure-container">
            <div class="secure-header text-center mb-4">
                <img src="../../assets/images/election-logo.jpg" alt="Sri Lanka Emblem" class="logo mb-2">
                <h2><?= $tr['otp_verification'] ?? 'OTP Verification' ?></h2>
                <p><?= $tr['enter_otp_instruction'] ?? 'Enter the 6-digit OTP sent to your email.' ?></p>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success text-center"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <form method="POST" action="../../src/controllers/VerifyOtpOfficerController.php" class="needs-validation" novalidate>
                <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                <div class="mb-3">
                    <label for="otp_code" class="form-label"><?= $tr['otp_code'] ?? 'OTP Code' ?></label>
                    <input type="text" class="form-control" id="otp_code" name="otp_code" pattern="[0-9]{6}" maxlength="6" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary"><?= $tr['verify_otp'] ?? 'Verify OTP' ?></button>
                </div>
            </form>
            <div class="text-center mt-3">
                <form method="POST" action="../../src/controllers/ResendOtpOfficerController.php">
                    <input type="hidden" name="csrf_token" value="<?php echo $token; ?>">
                    <button type="submit" class="btn btn-link"><?= $tr['resend_otp'] ?? 'Resend OTP' ?></button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


