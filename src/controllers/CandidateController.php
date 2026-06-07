<?php
session_start();
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../middleware/CSRF.php';
$csrf = new CSRF();
$db = new DatabaseModel();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!$csrf->validateToken($csrf_token)) {
        header('Location: ../../public/admin/index.php?error=' . urlencode('Invalid CSRF token. Please refresh and try again.'));
        exit;
    }
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'add_candidate':
            $required = ['full_name','nic','party','candidate_number','province','is_active'];
            foreach ($required as $field) {
                if (empty($_POST[$field])) {
                    header('Location: ../../public/admin/index.php?error=Missing+field');
                    exit;
                }
            }
            $full_name = trim($_POST['full_name']);
            $nic = trim($_POST['nic']);
            $party = trim($_POST['party']);
            $party_symbol = trim($_POST['party_symbol'] ?? '');
            $candidate_number = intval($_POST['candidate_number']);
            $province = trim($_POST['province']);
            $is_active = intval($_POST['is_active']);
            $photo_path = null;
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photo_path = '../../assets/images/candidate_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
            }
            $stmt = $db->getConnection()->prepare("INSERT INTO candidates (nic, full_name, party, party_symbol, candidate_number, province, photo_path, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$nic, $full_name, $party, $party_symbol, $candidate_number, $province, $photo_path, $is_active]);
                header('Location: ../../public/admin/index.php?success=Candidate+added');
                exit;
            } catch (PDOException $e) {
                header('Location: ../../public/admin/index.php?error=NIC+or+Candidate+Number+already+exists');
                exit;
            }
            break;
        case 'edit_candidate':
            $required = ['candidate_id','full_name','nic','party','candidate_number','province','is_active'];
            foreach ($required as $field) {
                if (!isset($_POST[$field]) || $_POST[$field] === '') {
                    header('Location: ../../public/admin/index.php?error=Missing+field');
                    exit;
                }
            }
            $candidate_id = intval($_POST['candidate_id']);
            $full_name = trim($_POST['full_name']);
            $nic = trim($_POST['nic']);
            $party = trim($_POST['party']);
            $party_symbol = trim($_POST['party_symbol'] ?? '');
            $candidate_number = intval($_POST['candidate_number']);
            $province = trim($_POST['province']);
            $is_active = intval($_POST['is_active']);
            $update_fields = "nic=?, full_name=?, party=?, party_symbol=?, candidate_number=?, province=?, is_active=?";
            $params = [$nic, $full_name, $party, $party_symbol, $candidate_number, $province, $is_active];
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $photo_path = '../../assets/images/candidate_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
                $update_fields .= ", photo_path=?";
                $params[] = $photo_path;
            }
            $params[] = $candidate_id;
            $sql = "UPDATE candidates SET $update_fields WHERE candidate_id=?";
            $stmt = $db->getConnection()->prepare($sql);
            try {
                $stmt->execute($params);
                header('Location: ../../public/admin/index.php?success=Candidate+updated');
                exit;
            } catch (PDOException $e) {
                header('Location: ../../public/admin/index.php?error=Update+failed');
                exit;
            }
            break;
        case 'delete_candidate':
            $candidate_id = intval($_POST['candidate_id'] ?? 0);
            if ($candidate_id > 0) {
                $stmt = $db->getConnection()->prepare("DELETE FROM candidates WHERE candidate_id=?");
                $stmt->execute([$candidate_id]);
                header('Location: ../../public/admin/index.php?success=Candidate+deleted');
                exit;
            } else {
                header('Location: ../../public/admin/index.php?error=Invalid+candidate+ID');
                exit;
            }
            break;
        default:
            header('Location: ../../public/admin/index.php?error=' . urlencode('Unknown action.'));
            exit;
    }
} else {
    http_response_code(405);
    die('Method not allowed');
} 