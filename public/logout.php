<?php
require_once '../vendor/autoload.php';

use Utils\SessionManager;

// Initialize session manager
$sessionManager = new SessionManager();

// Get user type before destroying session for proper redirect
$userType = null;
if (isset($_SESSION['user_type'])) {
    $userType = $_SESSION['user_type'];
}

// Destroy the session
$sessionManager->destroySession();

// Redirect based on user type
switch ($userType) {
    case 'admin':
        header('Location: /admin/login.php?logout=success');
        break;
    case 'officer':
        header('Location: /officer/login.php?logout=success');
        break;
    case 'voter':
        header('Location: /login.php?logout=success');
        break;
    default:
        header('Location: /login.php?logout=success');
        break;
}

exit();
