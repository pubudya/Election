<?php
session_start();
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../middleware/CSRF.php';
require_once __DIR__ . '/../utils/Security.php';
require_once __DIR__ . '/../utils/Mailer.php';

$db = new DatabaseModel();
$csrf = new CSRF();
$security = new Security();

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Log the logout event
    if (isset($_SESSION['voter_id'])) {
        $security->logEvent('voter_logout', $_SESSION['voter_id'], $_SERVER['REMOTE_ADDR'], 'success', 'User logged out');
    } elseif (isset($_SESSION['officer_id'])) {
        $security->logEvent('officer_logout', $_SESSION['officer_id'], $_SERVER['REMOTE_ADDR'], 'success', 'User logged out');
    } elseif (isset($_SESSION['admin_id'])) {
        $security->logEvent('admin_logout', $_SESSION['admin_id'], $_SERVER['REMOTE_ADDR'], 'success', 'User logged out');
    }
    
    // Destroy session
    session_destroy();
    header('Location: ../../public/login.php?success=' . urlencode('You have been logged out successfully.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'];

    // CSRF check
    if (!$csrf->validateToken($csrf_token)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }

    // Extract credentials based on role
    $nic_username = '';
    $pin_password = '';
    
    if ($role === 'voter') {
        $nic_username = trim($_POST['nic_username'] ?? '');
        $pin_password = $_POST['pin_password'] ?? '';
    } elseif ($role === 'officer') {
        $nic_username = trim($_POST['officer_nic'] ?? '');
        $pin_password = $_POST['officer_password'] ?? '';
    } elseif ($role === 'admin') {
        $nic_username = trim($_POST['admin_username'] ?? '');
        $pin_password = $_POST['admin_password'] ?? '';
    }

    // Validate that credentials are provided
    if (empty($nic_username) || empty($pin_password)) {
        header('Location: ../../public/login.php?error=' . urlencode('Please provide all required credentials.'));
        exit;
    }

    // Brute force protection
    if ($security->isBruteForce($nic_username, $ip)) {
        header('Location: ../../public/login.php?error=' . urlencode('Account temporarily locked due to too many failed attempts.'));
        exit;
    }

    if ($role === 'voter') {
        // Voter login: NIC + PIN
        try {
            // First, check if voter exists and get their data
            $stmt = $db->prepare('SELECT voter_id, nic, pin_hash, email, is_registered, has_voted, full_name, is_approved FROM voters WHERE nic = ?');
            $stmt->execute([$nic_username]);
            $voter = $stmt->fetch();
            
            // Debug logging
            error_log('Login attempt - NIC: ' . $nic_username);
            error_log('Voter found: ' . ($voter ? 'YES' : 'NO'));
            if ($voter) {
                error_log('PIN hash in DB: ' . $voter['pin_hash']);
                error_log('Input PIN: ' . $pin_password);
                error_log('Hashed input PIN: ' . $security->hashPassword($pin_password));
            }
            
            if (!$voter) {
                $security->logFailedLogin($nic_username, $ip, 'voter');
                header('Location: ../../public/login.php?error_type=invalid_nic&nic=' . urlencode($nic_username));
                exit;
            }
            
            // Check registration status BEFORE verifying PIN to surface proper indicator
            if (!$voter['is_registered']) {
                header('Location: ../../public/login.php?error_type=not_registered&nic=' . urlencode($nic_username));
                exit;
            }
            // Check approval status BEFORE verifying PIN so users see pending-approval message
            if (!isset($voter['is_approved']) || !$voter['is_approved']) {
                header('Location: ../../public/login.php?error_type=pending_approval&nic=' . urlencode($nic_username));
                exit;
            }
            
            // Verify PIN after basic status checks
            if (!$security->verifyHash($pin_password, $voter['pin_hash'])) {
                $security->logFailedLogin($nic_username, $ip, 'voter');
                header('Location: ../../public/login.php?error_type=invalid_pin&nic=' . urlencode($nic_username));
                exit;
            }
            // Check if already voted in the CURRENT active election only
            $activeElection = $db->query("SELECT config_id FROM election_config WHERE is_active = TRUE AND start_date <= NOW() AND end_date >= NOW() LIMIT 1")->fetch();
            if ($activeElection) {
                $stmt = $db->prepare('SELECT vote_id FROM votes WHERE voter_id = ? AND election_id = ?');
                $stmt->execute([$voter['voter_id'], $activeElection['config_id']]);
                if ($stmt->rowCount() > 0) {
                    header('Location: ../../public/login.php?error_type=already_voted&nic=' . urlencode($nic_username));
                    exit;
                }
            }
            
            // Generate OTP and send to email
            $otp = $security->generateOTP($nic_username, 'voter_login', $voter['email']);
            
            // Store session data for OTP verification
            $_SESSION['pending_voter_id'] = $voter['voter_id'];
            $_SESSION['pending_nic'] = $nic_username;
            $_SESSION['pending_otp'] = $otp;
            $_SESSION['pending_voter_name'] = $voter['full_name'];
            
            // Log successful login attempt
            $security->logEvent('voter_login_attempt', $voter['voter_id'], $ip, 'success', 'PIN verified, OTP sent');
            
            // Redirect to OTP verification page
            header('Location: ../../public/voter/verify_otp.php');
            exit;
            
        } catch (Exception $e) {
            error_log('Login error: ' . $e->getMessage());
            header('Location: ../../public/login.php?error_type=system_error&nic=' . urlencode($nic_username));
            exit;
        }
    } elseif ($role === 'officer') {
        // Officer login: NIC + PIN with OTP second factor
        $stmt = $db->prepare('SELECT officer_id, password_hash, is_active, email, full_name FROM grama_niladhari WHERE nic = ?');
        $stmt->execute([$nic_username]);
        $officer = $stmt->fetch();
        
        if (!$officer) {
            $security->logFailedLogin($nic_username, $ip, 'officer');
            header('Location: ../../public/login.php?error_type=invalid_nic&nic=' . urlencode($nic_username));
            exit;
        }
        
        if (!$security->verifyHash($pin_password, $officer['password_hash'])) {
            $security->logFailedLogin($nic_username, $ip, 'officer');
            header('Location: ../../public/login.php?error_type=invalid_pin&nic=' . urlencode($nic_username));
            exit;
        }
        
        if (!$officer['is_active']) {
            header('Location: ../../public/login.php?error_type=inactive_account&nic=' . urlencode($nic_username));
            exit;
        }
        
        // Generate OTP and send to officer email
        $otp = $security->generateOTP($nic_username, 'officer_login', $officer['email'] ?? null);
        // Store pending session for OTP verification
        $_SESSION['pending_officer_id'] = $officer['officer_id'];
        $_SESSION['pending_officer_nic'] = $nic_username;
        $_SESSION['pending_officer_name'] = $officer['full_name'] ?? '';
        $_SESSION['pending_officer_otp'] = $otp;
        // Log and redirect to OTP verify page
        $security->logEvent('officer_login_attempt', $officer['officer_id'], $ip, 'success', 'PIN verified, OTP sent');
        header('Location: ../../public/officer/verify_otp.php');
        exit;
    } elseif ($role === 'admin') {
        // Admin login: username + password + OTP
        $stmt = $db->prepare('SELECT admin_id, password_hash, account_locked, email, full_name FROM admins WHERE username = ?');
        $stmt->execute([$nic_username]);
        $admin = $stmt->fetch();
        error_log('Input username: ' . $nic_username);
        error_log('Input password: ' . $pin_password);
        error_log('Hash in DB: ' . ($admin['password_hash'] ?? 'NULL'));
        error_log('Hash of input (with salt): ' . $security->hashPassword($pin_password));
        
        if (!$admin) {
            header('Location: ../../public/login.php?error_type=invalid_username&username=' . urlencode($nic_username));
            exit;
        }
        
        if (!$security->verifyHash($pin_password, $admin['password_hash'])) {
            header('Location: ../../public/login.php?error_type=invalid_password&username=' . urlencode($nic_username));
            exit;
        }
        
        if ($admin['account_locked']) {
            header('Location: ../../public/login.php?error_type=account_locked&username=' . urlencode($nic_username));
            exit;
        }

        // Generate OTP and send to admin email
        $otp = $security->generateOTP((string)$admin['admin_id'], 'admin_login', $admin['email'] ?? null);
        // Store pending session for OTP verification
        $_SESSION['pending_admin_id'] = $admin['admin_id'];
        $_SESSION['pending_admin_username'] = $nic_username;
        $_SESSION['pending_admin_name'] = $admin['full_name'] ?? '';
        $_SESSION['pending_admin_email'] = $admin['email'] ?? '';
        $_SESSION['pending_admin_otp'] = $otp;
        // Log and redirect to OTP verify page
        $security->logEvent('admin_login_attempt', $admin['admin_id'], $ip, 'success', 'Password verified, OTP sent');
        header('Location: ../../public/admin/verify_otp.php');
        exit;
    } else {
        die('Invalid role selected.');
    }
} else {
    http_response_code(405);
    die('Method not allowed');
} 