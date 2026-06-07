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
        header('Location: ../../public/admin/verify_otp.php?error=' . urlencode('Invalid CSRF token.'));
        exit;
    }

    if (!isset($_SESSION['pending_admin_id'])) {
        header('Location: ../../public/login.php?error=' . urlencode('Session expired. Please login again.'));
        exit;
    }

    $adminId = (string)($_SESSION['pending_admin_id']);
    $otp = $_POST['otp_code'] ?? '';
    $purpose = 'admin_login';
    try {
        $db->prepare('UPDATE otp_logs SET is_used = TRUE WHERE nic = ? AND expires_at <= NOW()')->execute([$adminId]);
        $stmt = $db->prepare('SELECT otp_id FROM otp_logs WHERE nic = ? AND otp_code = ? AND purpose = ? AND is_used = FALSE AND expires_at > NOW() LIMIT 1');
        $stmt->execute([$adminId, $otp, $purpose]);
        if ($stmt->rowCount() === 1) {
            $db->prepare('UPDATE otp_logs SET is_used = TRUE, used_at = NOW() WHERE nic = ? AND otp_code = ?')->execute([$adminId, $otp]);
            $_SESSION['admin_id'] = $_SESSION['pending_admin_id'];
            unset($_SESSION['pending_admin_id'], $_SESSION['pending_admin_username'], $_SESSION['pending_admin_name'], $_SESSION['pending_admin_email'], $_SESSION['pending_admin_otp']);
            $security->logEvent('admin_login_complete', $_SESSION['admin_id'], $_SERVER['REMOTE_ADDR'], 'success', 'OTP verified successfully');
            header('Location: ../../public/admin/index.php');
            exit;
        } else {
            $security->logEvent('admin_otp_failed', $_SESSION['pending_admin_id'], $_SERVER['REMOTE_ADDR'], 'failure', 'Invalid OTP provided');
            header('Location: ../../public/admin/verify_otp.php?error=' . urlencode('Invalid or expired OTP.'));
            exit;
        }
    } catch (Exception $e) {
        error_log('Admin OTP verification error: ' . $e->getMessage());
        header('Location: ../../public/admin/verify_otp.php?error=' . urlencode('System error. Please try again.'));
        exit;
    }
} else {
    http_response_code(405);
    die('Method not allowed');
}


