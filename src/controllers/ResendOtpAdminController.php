<?php
session_start();
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../middleware/CSRF.php';
require_once __DIR__ . '/../utils/Security.php';

$db = new DatabaseModel();
$csrf = new CSRF();
$security = new Security();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!$csrf->validateToken($csrf_token)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
    if (!isset($_SESSION['pending_admin_id'])) {
        $_SESSION['otp_error'] = 'Session expired. Please login again.';
        header('Location: ../../public/login.php');
        exit;
    }
    $adminId = (string)($_SESSION['pending_admin_id']);
    $stmt = $db->prepare('SELECT email FROM admins WHERE admin_id = ?');
    $stmt->execute([(int)$adminId]);
    $admin = $stmt->fetch();
    if (!$admin || empty($admin['email'])) {
        $_SESSION['otp_error'] = 'Could not find admin email.';
        header('Location: ../../public/admin/verify_otp.php');
        exit;
    }
    $otp = $security->generateOTP($adminId, 'admin_login', $admin['email']);
    $_SESSION['pending_admin_otp'] = $otp;
    $_SESSION['otp_success'] = 'A new OTP has been sent to your email.';
    header('Location: ../../public/admin/verify_otp.php');
    exit;
} else {
    http_response_code(405);
    die('Method not allowed');
}


