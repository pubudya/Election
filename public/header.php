<?php
if (!isset($activePage)) $activePage = '';
session_start();
// Language switching logic
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
    exit;
}
$lang = $_SESSION['lang'] ?? 'en';
$langFile = __DIR__ . '/../lang/' . $lang . '.php';
if (!file_exists($langFile)) $langFile = __DIR__ . '/../lang/en.php';
$tr = include $langFile;
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tr['presidential_election'] ?></title>
    <link rel="icon" href="../assets/images/election-logo.jpg">
    <link rel="stylesheet" href="../assets/css/sri-lanka-theme.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Custom styles for security messages */
        .alert-container {
            animation: slideInDown 0.5s ease-out;
        }
        
        .alert {
            transition: all 0.3s ease;
            border-radius: 12px !important;
        }
        
        .alert:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15) !important;
        }
        
        .main-card[style*="border: 3px solid #dc3545"] {
            transition: all 0.3s ease;
            animation: pulse 2s infinite;
        }
        
        .main-card[style*="border: 3px solid #dc3545"]:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3) !important;
        }
        
        @keyframes slideInDown {
            from {
                transform: translateY(-100%);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        @keyframes pulse {
            0% { box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2); }
            50% { box-shadow: 0 4px 20px rgba(220, 53, 69, 0.4); }
            100% { box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2); }
        }
    </style>
</head>
<body>
<?php if (!isset($hideFlagBar) || !$hideFlagBar): ?>
<div class="flag-bar">
    <div class="maroon"></div>
    <div class="gold"></div>
    <div class="green"></div>
    <div class="orange"></div>
</div>
<?php endif; ?>
<header class="secure-header text-center py-4 mb-3">
    <img src="../assets/images/election-logo.jpg" alt="Sri Lanka Emblem" class="emblem">
    <h1 class="mb-1"><?= $tr['presidential_election'] ?></h1>
    <?php if (!isset($hideOfficialPortal) || !$hideOfficialPortal): ?>
    <h2 class="mb-0" style="font-size:1.3rem; color:var(--sl-gold);"><?= $tr['official_portal'] ?></h2>
    <?php endif; ?>
</header>
<?php if (!isset($hideNav) || !$hideNav): ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Sri Lanka Election</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?php if($activePage=='home') echo 'active'; ?>" href="index.php"><?= $tr['homepage'] ?></a></li>
                <li class="nav-item"><a class="nav-link <?php if($activePage=='how') echo 'active'; ?>" href="how-election-works.php"><?= $tr['how_election_works'] ?></a></li>
                <li class="nav-item"><a class="nav-link <?php if($activePage=='after') echo 'active'; ?>" href="after-election.php"><?= $tr['after_election'] ?></a></li>
                <li class="nav-item"><a class="nav-link <?php if($activePage=='why') echo 'active'; ?>" href="why-vote.php"><?= $tr['why_vote'] ?></a></li>
                <li class="nav-item"><a class="nav-link <?php if($activePage=='results') echo 'active'; ?>" href="election-results.php"><?= $tr['election_results'] ?></a></li>
                <li class="nav-item"><a class="nav-link <?php if($activePage=='guidance') echo 'active'; ?>" href="guidance.php"><?= $tr['guidance'] ?></a></li>
                <li class="nav-item"><a class="nav-link <?php if($activePage=='candidates') echo 'active'; ?>" href="candidates.php"><?= $tr['candidates'] ?></a></li>
            </ul>
            <div class="d-flex align-items-center">
                <a href="register.php" class="btn btn-danger me-2"><?= $tr['register'] ?></a>
                <a href="login.php" class="btn btn-danger me-2"><?= $tr['login'] ?></a>
                <div class="btn-group" role="group" aria-label="Language selection">
                    <a href="?lang=si" class="btn btn-outline-secondary lang-btn<?php if($lang=='si') echo ' active'; ?>">සිංහල</a>
                    <a href="?lang=ta" class="btn btn-outline-secondary lang-btn<?php if($lang=='ta') echo ' active'; ?>">தமிழ்</a>
                    <a href="?lang=en" class="btn btn-outline-secondary lang-btn<?php if($lang=='en') echo ' active'; ?>">English</a>
                </div>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?> 