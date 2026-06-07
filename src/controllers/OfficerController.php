<?php
session_start();
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../middleware/CSRF.php';
require_once __DIR__ . '/../utils/Security.php';
require_once __DIR__ . '/../utils/Mailer.php';

$db = new DatabaseModel();
$csrf = new CSRF();
$security = new Security();

// Example stub for handling officer actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!$csrf->validateToken($csrf_token)) {
        http_response_code(403);
        header('Location: ../../public/admin/add_officer.php?error=' . urlencode('Invalid CSRF token. Please try again.'));
        exit;
    }
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'register_voter':
            // Validate and insert voter (with officer context)
            // ...
            break;
        case 'update_voter':
            // Validate and update voter info
            // ...
            break;
        case 'add_officer':
            // Validate input (no password expected; PIN will be generated and emailed)
            $required = ['full_name','nic','division','district','province','email','phone'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    header('Location: ../../public/admin/index.php?error=Missing+field');
                    exit;
                }
            }
            $full_name = trim($_POST['full_name']);
            $nic = trim($_POST['nic']);
            $division = trim($_POST['division']);
            $district = trim($_POST['district']);
            $province = trim($_POST['province']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            // Generate a 6-digit PIN and hash it using existing security scheme
            $pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $password_hash = $security->hashPassword($pin);
            // Insert into DB
            $stmt = $db->getConnection()->prepare("INSERT INTO grama_niladhari (full_name, nic, division, district, province, email, phone, password_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$full_name, $nic, $division, $district, $province, $email, $phone, $password_hash]);
                // Email the PIN to the officer
                $subject = 'Your GN Officer Login PIN';
                $body = "<p>Dear " . htmlspecialchars($full_name) . ",</p><p>Your GN Officer login PIN is: <strong>" . $pin . "</strong></p><p>Use this PIN to log in along with your NIC. For security, you will be prompted for a one-time OTP on each login.</p>";
                \Mailer::sendMail($email, $subject, $body);
                header('Location: ../../public/admin/index.php?success=Officer+added+and+PIN+sent+via+email');
                exit;
            } catch (PDOException $e) {
                header('Location: ../../public/admin/index.php?error=NIC+or+Email+already+exists');
                exit;
            }
            break;
        case 'edit_officer':
            $required = ['officer_id','full_name','nic','division','district','province','email','phone','is_active'];
            foreach ($required as $field) {
                if (!isset($_POST[$field]) || $_POST[$field] === '') {
                    header('Location: ../../public/admin/index.php?error=Missing+field');
                    exit;
                }
            }
            $officer_id = intval($_POST['officer_id']);
            $full_name = trim($_POST['full_name']);
            $nic = trim($_POST['nic']);
            $division = trim($_POST['division']);
            $district = trim($_POST['district']);
            $province = trim($_POST['province']);
            $email = trim($_POST['email']);
            $phone = trim($_POST['phone']);
            $is_active = intval($_POST['is_active']);
            $password = $_POST['password'] ?? '';
            $update_fields = "full_name=?, nic=?, division=?, district=?, province=?, email=?, phone=?, is_active=?";
            $params = [$full_name, $nic, $division, $district, $province, $email, $phone, $is_active];
            if (!empty($password)) {
                $salt = 'default_salt';
                $password_hash = hash('sha256', $password . $salt);
                $update_fields .= ", password_hash=?";
                $params[] = $password_hash;
            }
            $params[] = $officer_id;
            $sql = "UPDATE grama_niladhari SET $update_fields WHERE officer_id=?";
            $stmt = $db->getConnection()->prepare($sql);
            try {
                $stmt->execute($params);
                header('Location: ../../public/admin/index.php?success=Officer+updated');
                exit;
            } catch (PDOException $e) {
                header('Location: ../../public/admin/index.php?error=Update+failed');
                exit;
            }
            break;
        default:
            die('Unknown action');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $type = $_GET['type'] ?? '';
    switch ($type) {
        case 'voters':
            // Fetch and return voters for this officer as JSON
            // ...
            break;
        default:
            die('Unknown type');
    }
} else {
    http_response_code(405);
    die('Method not allowed');
} 