<?php
session_start();
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../middleware/CSRF.php';
require_once __DIR__ . '/../utils/Security.php';
require_once __DIR__ . '/../utils/Mailer.php';
require_once __DIR__ . '/../models/Voter.php';

use PDOException;

$db = new DatabaseModel();
$csrf = new CSRF();
$security = new Security();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!$csrf->validateToken($csrf_token)) {
        header('Location: ../../public/officer/index.php?error=' . urlencode('Invalid CSRF token. Please refresh the page and try again.'));
        exit;
    }
    $action = $_POST['action'] ?? '';
    
    // Check email uniqueness for registration form
    if ($action === 'check_email' && isset($_POST['email'])) {
        $email = trim($_POST['email']);
        if (empty($email)) {
            echo 'invalid_email';
            exit;
        }
        
        // Check if email already exists
        $stmt = $db->prepare('SELECT voter_id FROM voters WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            echo 'already_registered';
        } else {
            echo 'available';
        }
        exit;
    }
    // Officer approval
    if ($action === 'approve_voter' && isset($_POST['voter_id'])) {
        $voter_id = intval($_POST['voter_id']);
        $officer_id = $_SESSION['officer_id'] ?? null;
        if (!$officer_id) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('Unauthorized.'));
            exit;
        }
        // Enforce GN-division restriction: officer can only approve voters in their division
        $officerStmt = $db->prepare('SELECT division FROM grama_niladhari WHERE officer_id = ?');
        $officerStmt->execute([$officer_id]);
        $officerDivision = $officerStmt->fetch()['division'] ?? '';
        $voterStmt = $db->prepare('SELECT division FROM voters WHERE voter_id = ?');
        $voterStmt->execute([$voter_id]);
        $voterDivision = $voterStmt->fetch()['division'] ?? null;
        if (!$voterDivision || $voterDivision !== $officerDivision) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('You can only approve voters from your GN Division.'));
            exit;
        }
        $stmt = $db->prepare('UPDATE voters SET is_approved = TRUE WHERE voter_id = ?');
        $stmt->execute([$voter_id]);
        // Log approval
        $security->logEvent('voter_approved', $officer_id, $_SERVER['REMOTE_ADDR'], 'success', "Approved voter_id: $voter_id");
        header('Location: ../../public/officer/index.php?success=' . urlencode('Voter approved.'));
        exit;
    }
    if ($action === 'deny_voter' && isset($_POST['voter_id'])) {
        $voter_id = intval($_POST['voter_id']);
        $officer_id = $_SESSION['officer_id'] ?? null;
        if (!$officer_id) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('Unauthorized.'));
            exit;
        }
        // Only allow denying voters within officer's GN division
        $officerStmt = $db->prepare('SELECT division FROM grama_niladhari WHERE officer_id = ?');
        $officerStmt->execute([$officer_id]);
        $officerDivision = $officerStmt->fetch()['division'] ?? '';
        $voterStmt = $db->prepare('SELECT division FROM voters WHERE voter_id = ?');
        $voterStmt->execute([$voter_id]);
        $voterDivision = $voterStmt->fetch()['division'] ?? null;
        if (!$voterDivision || $voterDivision !== $officerDivision) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('You can only deny voters from your GN Division.'));
            exit;
        }
        // Optionally, you can delete or mark as denied. Here, we delete:
        $stmt = $db->prepare('DELETE FROM voters WHERE voter_id = ?');
        $stmt->execute([$voter_id]);
        // Log denial
        $security->logEvent('voter_denied', $officer_id, $_SERVER['REMOTE_ADDR'], 'success', "Denied voter_id: $voter_id");
        header('Location: ../../public/officer/index.php?success=' . urlencode('Voter denied and removed.'));
        exit;
    }
    // Edit voter
    if ($action === 'edit_voter' && isset($_POST['voter_id'])) {
        $voter_id = intval($_POST['voter_id']);
        $fullName = trim($_POST['full_name'] ?? '');
        $nic = trim($_POST['nic'] ?? '');
        $dob = $_POST['dob'] ?? '';
        $address = trim($_POST['address'] ?? '');
        $division = trim($_POST['division'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        // Validate required fields
        if (!$nic || !$fullName || !$dob || !$address || !$division || !$district || !$province) {
            header('Location: ../../public/officer/register_voter.php?voter_id=' . $voter_id . '&error=' . urlencode('All required fields must be filled.'));
            exit;
        }
        $stmt = $db->prepare('UPDATE voters SET full_name=?, nic=?, date_of_birth=?, address=?, division=?, district=?, province=?, email=?, phone=? WHERE voter_id=?');
        $stmt->execute([$fullName, $nic, $dob, $address, $division, $district, $province, $email, $phone, $voter_id]);
        $security->logEvent('voter_edited', $_SESSION['officer_id'] ?? null, $_SERVER['REMOTE_ADDR'], 'success', "Edited voter_id: $voter_id");
        header('Location: ../../public/officer/index.php?success=' . urlencode('Voter updated.'));
        exit;
    }
    // Delete voter
    if ($action === 'delete_voter' && isset($_POST['voter_id'])) {
        $voter_id = intval($_POST['voter_id']);
        // Restrict delete to officer's GN division as well
        $officer_id = $_SESSION['officer_id'] ?? null;
        if ($officer_id) {
            $officerStmt = $db->prepare('SELECT division FROM grama_niladhari WHERE officer_id = ?');
            $officerStmt->execute([$officer_id]);
            $officerDivision = $officerStmt->fetch()['division'] ?? '';
            $voterStmt = $db->prepare('SELECT division FROM voters WHERE voter_id = ?');
            $voterStmt->execute([$voter_id]);
            $voterDivision = $voterStmt->fetch()['division'] ?? null;
            if (!$voterDivision || $voterDivision !== $officerDivision) {
                header('Location: ../../public/officer/index.php?error=' . urlencode('You can only manage voters from your GN Division.'));
                exit;
            }
        }
        $stmt = $db->prepare('DELETE FROM voters WHERE voter_id = ?');
        $stmt->execute([$voter_id]);
        $security->logEvent('voter_deleted', $_SESSION['officer_id'] ?? null, $_SERVER['REMOTE_ADDR'], 'success', "Deleted voter_id: $voter_id");
        header('Location: ../../public/officer/index.php?success=' . urlencode('Voter deleted.'));
        exit;
    }
    // GN Officer voter registration
    if ($action === 'register_voter_by_gn') {
        $officer_id = $_SESSION['officer_id'] ?? null;
        if (!$officer_id) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('Unauthorized. Please log in again.'));
            exit;
        }
        
        $nic = trim($_POST['nic'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $dob = $_POST['dob'] ?? '';
        $address = trim($_POST['address'] ?? '');
        $division = trim($_POST['division'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        
        // Validate required fields
        if (!$nic || !$fullName || !$dob || !$address || !$division || !$district || !$province || !$email || !$phone) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('All required fields must be filled.'));
            exit;
        }
        
        if (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $nic)) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('Invalid NIC format.'));
            exit;
        }
        
        $dobDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;
        if ($age < 18) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('Voter must be at least 18 years old to register.'));
            exit;
        }
        
        // Check NIC uniqueness
        $stmt = $db->prepare('SELECT voter_id FROM voters WHERE nic = ?');
        $stmt->execute([$nic]);
        if ($stmt->rowCount() > 0) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('Voter with this NIC already registered.'));
            exit;
        }
        
        // Check email uniqueness
        $stmt = $db->prepare('SELECT voter_id FROM voters WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('This email address is already registered.'));
            exit;
        }
        
        // Generate PIN
        $pin = random_int(100000, 999999);
        // Hash PIN
        $pinHash = $security->hashPassword($pin);
        
        // Insert voter (approved by default since GN officer is registering)
        $stmt = $db->prepare('INSERT INTO voters (nic, full_name, date_of_birth, address, division, district, province, email, phone, pin_hash, is_registered, is_approved) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE, TRUE)');
        $stmt->execute([$nic, $fullName, $dob, $address, $division, $district, $province, $email, $phone, $pinHash]);
        
        // Send PIN to email
        $subject = 'Your Voter Registration PIN';
        $body = "<p>Dear {$fullName},</p><p>You have been registered as a voter by your GN Officer.</p><p>Your registration PIN is: <b>{$pin}</b></p><p>This PIN will be required for login. Please keep it safe.</p>";
        \Mailer::sendMail($email, $subject, $body);
        
        // Log registration
        $security->logEvent('voter_registration_by_gn', $officer_id, $_SERVER['REMOTE_ADDR'], 'success', "Registered voter with NIC: {$nic} by GN Officer");
        
        header('Location: ../../public/officer/index.php?success=' . urlencode('Voter registered successfully. PIN sent to email.'));
        exit;
    }
    
    // Voter self-registration logic (step=register)
    $step = $_POST['step'] ?? '';
    if ($step === 'register' && (!isset($_POST['action']) || $_POST['action'] === '')) {
        $nic = trim($_POST['nic'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $dob = $_POST['dob'] ?? '';
        $address = trim($_POST['address'] ?? '');
        $division = trim($_POST['division'] ?? '');
        $district = trim($_POST['district'] ?? '');
        $province = trim($_POST['province'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $ip = $_SERVER['REMOTE_ADDR'];
        // Validate required fields
        if (!$nic || !$fullName || !$dob || !$address || !$division || !$district || !$province || !$email) {
            header('Location: ../../public/register.php?error=' . urlencode('All required fields must be filled.'));
            exit;
        }
        if (!preg_match('/^([0-9]{9}[vVxX]|[0-9]{12})$/', $nic)) {
            header('Location: ../../public/register.php?error=' . urlencode('Invalid NIC format.'));
            exit;
        }
        $dobDate = new DateTime($dob);
        $today = new DateTime();
        $age = $today->diff($dobDate)->y;
        if ($age < 18) {
            header('Location: ../../public/register.php?error=' . urlencode('You must be at least 18 years old to register.'));
            exit;
        }
        // Check NIC uniqueness
        $stmt = $db->prepare('SELECT voter_id FROM voters WHERE nic = ?');
        $stmt->execute([$nic]);
        if ($stmt->rowCount() > 0) {
            header('Location: ../../public/register.php?error=' . urlencode('Voter with this NIC already registered.'));
            exit;
        }
        
        // Check email uniqueness
        $stmt = $db->prepare('SELECT voter_id FROM voters WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            header('Location: ../../public/register.php?error=' . urlencode('This email address is already registered.'));
            exit;
        }
        // Generate PIN
        $pin = random_int(100000, 999999);
        // Hash PIN
        $pinHash = $security->hashPassword($pin);
        // Insert voter
        $stmt = $db->prepare('INSERT INTO voters (nic, full_name, date_of_birth, address, division, district, province, email, phone, pin_hash, is_registered) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, TRUE)');
        $stmt->execute([$nic, $fullName, $dob, $address, $division, $district, $province, $email, $phone, $pinHash]);
        // Send PIN to email
        $subject = 'Your Voter Registration PIN';
        $body = "<p>Your registration PIN is: <b>$pin</b></p><p>This PIN will be required for login. Please keep it safe.</p>";
        \Mailer::sendMail($email, $subject, $body);
        // Log registration
        $security->logEvent('voter_registration', null, $_SERVER['REMOTE_ADDR'], 'success', "Registered voter with NIC: {$nic}");
        header('Location: ../../public/login.php?registered=1');
        exit;
    }
    // Delete voter (officer action)
    if ($action === 'delete_voter' && isset($_POST['voter_id'])) {
        $voter_id = intval($_POST['voter_id']);
        $officer_id = $_SESSION['officer_id'] ?? null;
        if (!$officer_id) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('Unauthorized.'));
            exit;
        }
        // Only allow deleting voters within officer's GN division
        $officerStmt = $db->prepare('SELECT division FROM grama_niladhari WHERE officer_id = ?');
        $officerStmt->execute([$officer_id]);
        $officerDivision = $officerStmt->fetch()['division'] ?? '';
        $voterStmt = $db->prepare('SELECT division FROM voters WHERE voter_id = ?');
        $voterStmt->execute([$voter_id]);
        $voterDivision = $voterStmt->fetch()['division'] ?? null;
        if (!$voterDivision || $voterDivision !== $officerDivision) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('You can only delete voters from your GN Division.'));
            exit;
        }
        // Delete the voter
        $stmt = $db->prepare('DELETE FROM voters WHERE voter_id = ?');
        $stmt->execute([$voter_id]);
        // Log deletion
        $security->logEvent('voter_deleted', $officer_id, $_SERVER['REMOTE_ADDR'], 'success', "Deleted voter_id: $voter_id");
        header('Location: ../../public/officer/index.php?success=' . urlencode('Voter deleted successfully.'));
        exit;
    }
    
    // Resend PIN to voter (officer action)
    if ($action === 'resend_pin' && isset($_POST['voter_id'])) {
        $voter_id = intval($_POST['voter_id']);
        $officer_id = $_SESSION['officer_id'] ?? null;
        if (!$officer_id) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('Unauthorized.'));
            exit;
        }
        // Fetch voter info
        $stmt = $db->prepare('SELECT email, full_name FROM voters WHERE voter_id = ?');
        $stmt->execute([$voter_id]);
        $voter = $stmt->fetch();
        if (!$voter || empty($voter['email'])) {
            header('Location: ../../public/officer/index.php?error=' . urlencode('Voter not found or no email.'));
            exit;
        }
        // Generate new PIN
        $newPin = random_int(100000, 999999);
        $pinHash = $security->hashPassword($newPin);
        \Voter::updatePin($voter_id, $pinHash);
        // Send email
        $subject = 'Your Voter PIN has been reset';
        $body = "<p>Dear {$voter['full_name']},</p><p>Your new PIN is: <b>{$newPin}</b></p><p>Please keep it secure. This PIN is required for login.</p>";
        $mailResult = \Mailer::sendMail($voter['email'], $subject, $body);
        // Log the action
        $security->logEvent('voter_pin_resent', $officer_id, $_SERVER['REMOTE_ADDR'], $mailResult ? 'success' : 'failure', "Resent PIN for voter_id: $voter_id");
        if ($mailResult) {
            header('Location: ../../public/officer/index.php?success=' . urlencode('PIN resent successfully.'));
        } else {
            header('Location: ../../public/officer/index.php?error=' . urlencode('Failed to send PIN email.'));
        }
        exit;
    }
} else {
    http_response_code(405);
    die('Method not allowed');
}
// Debug: Log POST and SESSION data for troubleshooting
error_log('POST: ' . print_r($_POST, true));
error_log('SESSION: ' . print_r($_SESSION, true)); 