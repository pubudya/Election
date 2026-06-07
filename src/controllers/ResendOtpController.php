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
    if (!isset($_SESSION['pending_nic'])) {
        $_SESSION['otp_error'] = 'Session expired. Please login again.';
        header('Location: ../../public/login.php');
        exit;
    }
    $nic = $_SESSION['pending_nic'];
    // Fetch voter email
    $stmt = $db->prepare('SELECT email FROM voters WHERE nic = ?');
    $stmt->execute([$nic]);
    $voter = $stmt->fetch();
    if (!$voter || !$voter['email']) {
        $_SESSION['otp_error'] = 'Could not find voter email.';
        header('Location: ../../public/voter/verify_otp.php');
        exit;
    }
    // Generate and send OTP
    $otp = $security->generateOTP($nic, 'voter_login', $voter['email']);
    $_SESSION['pending_otp'] = $otp;
    $_SESSION['otp_success'] = 'A new OTP has been sent to your email.';
    header('Location: ../../public/voter/verify_otp.php');
    exit;
} else {
    http_response_code(405);
    die('Method not allowed');
} 