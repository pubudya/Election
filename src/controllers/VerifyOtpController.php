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
        header('Location: ../../public/voter/verify_otp.php?error=' . urlencode('Invalid CSRF token. Please refresh the page and try again.'));
        exit;
    }
    
    if (!isset($_SESSION['pending_voter_id'], $_SESSION['pending_nic'])) {
        header('Location: ../../public/login.php?error=' . urlencode('Session expired. Please login again.'));
        exit;
    }
    
    $nic = $_SESSION['pending_nic'];
    $otp = $_POST['otp_code'] ?? '';
    $purpose = 'voter_login';
    
    try {
        // Invalidate expired OTPs
        $db->prepare('UPDATE otp_logs SET is_used = TRUE WHERE nic = ? AND expires_at <= NOW()')->execute([$nic]);
        
        // Check for valid OTP
        $stmt = $db->prepare('SELECT otp_id FROM otp_logs WHERE nic = ? AND otp_code = ? AND purpose = ? AND is_used = FALSE AND expires_at > NOW() LIMIT 1');
        $stmt->execute([$nic, $otp, $purpose]);
        
        if ($stmt->rowCount() === 1) {
            // Mark OTP as used
            $db->prepare('UPDATE otp_logs SET is_used = TRUE, used_at = NOW() WHERE nic = ? AND otp_code = ?')->execute([$nic, $otp]);
            
            // Set voter session
            $_SESSION['voter_id'] = $_SESSION['pending_voter_id'];
            $_SESSION['voter_nic'] = $_SESSION['pending_nic'];
            $_SESSION['voter_name'] = $_SESSION['pending_voter_name'] ?? '';
            $_SESSION['login_time'] = time();
            
            // Clear pending session data
            unset($_SESSION['pending_voter_id'], $_SESSION['pending_nic'], $_SESSION['pending_otp'], $_SESSION['pending_voter_name']);
            
            // Log successful login
            $security->logEvent('voter_login_complete', $_SESSION['voter_id'], $_SERVER['REMOTE_ADDR'], 'success', 'OTP verified successfully');
            
            // Redirect to voting page
            header('Location: ../../public/voter/index.php');
            exit;
        } else {
            // Log failed OTP attempt
            $security->logEvent('voter_otp_failed', $_SESSION['pending_voter_id'], $_SERVER['REMOTE_ADDR'], 'failure', 'Invalid OTP provided');
            
            header('Location: ../../public/voter/verify_otp.php?error=' . urlencode('Invalid or expired OTP.'));
            exit;
        }
    } catch (Exception $e) {
        error_log('OTP verification error: ' . $e->getMessage());
        header('Location: ../../public/voter/verify_otp.php?error=' . urlencode('System error. Please try again.'));
        exit;
    }
} else {
    http_response_code(405);
    die('Method not allowed');
} 