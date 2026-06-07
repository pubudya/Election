<?php
session_start();
require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../middleware/CSRF.php';
require_once __DIR__ . '/../utils/Security.php';

$db = new DatabaseModel();
$csrf = new CSRF();
$security = new Security();

// Example stub for handling admin actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!$csrf->validateToken($csrf_token)) {
        http_response_code(403);
        die('Invalid CSRF token');
    }
    $action = $_POST['action'] ?? '';
    switch ($action) {
        case 'add_candidate':
            // Validate and insert candidate
            // ...
            break;
        case 'edit_candidate':
            // Validate and update candidate
            // ...
            break;
        case 'delete_candidate':
            // Delete candidate
            // ...
            break;
        case 'add_officer':
            // Validate and insert officer
            // ...
            break;
        case 'edit_officer':
            // Validate and update officer
            // ...
            break;
        case 'delete_officer':
            $officerId = $_POST['officer_id'] ?? null;
            if ($officerId) {
                $db->prepare('DELETE FROM grama_niladhari WHERE officer_id = ?')->execute([$officerId]);
                header('Location: ../../public/admin/index.php?success=' . urlencode('GN Officer deleted successfully'));
            } else {
                header('Location: ../../public/admin/index.php?error=' . urlencode('Officer ID missing.'));
            }
            exit;
        case 'resend_officer_pin':
            $officerId = $_POST['officer_id'] ?? null;
            if (!$officerId) {
                header('Location: ../../public/admin/index.php?error=' . urlencode('Officer ID missing.'));
                exit;
            }
            // Fetch officer email and name
            $stmt = $db->prepare('SELECT full_name, email, nic FROM grama_niladhari WHERE officer_id = ?');
            $stmt->execute([$officerId]);
            $o = $stmt->fetch();
            if (!$o) {
                header('Location: ../../public/admin/index.php?error=' . urlencode('Officer not found.'));
                exit;
            }
            // Generate a fresh PIN and update stored hash
            $security = new Security();
            $newPin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            $newHash = $security->hashPassword($newPin);
            $upd = $db->prepare('UPDATE grama_niladhari SET password_hash = ? WHERE officer_id = ?');
            $upd->execute([$newHash, $officerId]);
            // Email the PIN
            require_once __DIR__ . '/../utils/Mailer.php';
            $subject = 'Your GN Officer PIN has been reset';
            $body = "<p>Dear " . htmlspecialchars($o['full_name']) . ",</p><p>Your new GN Officer login PIN is: <strong>" . $newPin . "</strong></p><p>NIC: " . htmlspecialchars($o['nic']) . "</p>";
            \Mailer::sendMail($o['email'], $subject, $body);
            header('Location: ../../public/admin/index.php?success=' . urlencode('PIN resent to officer email.'));
            exit;
        case 'launch_election':
            // Deactivate all other elections
            $db->query('UPDATE election_config SET is_active = 0');
            // Insert new active election
            $electionName = $_POST['election_name'] ?? 'Presidential Election';
            $startDate = $_POST['start_date'] ?? null;
            $endDate = $_POST['end_date'] ?? null;
            if (!$startDate || !$endDate) {
                die('Start and end date required');
            }
            $stmt = $db->prepare('INSERT INTO election_config (election_name, start_date, end_date, is_active, created_by) VALUES (?, ?, ?, 1, ?)');
            $stmt->execute([$electionName, $startDate, $endDate, $_SESSION['admin_id'] ?? null]);
            header('Location: ../../public/admin/index.php?success=' . urlencode('Election launched successfully'));
            exit;
        case 'publish_results':
            // Prefer the explicitly selected election
            $electionId = $_POST['election_id'] ?? null;
            if ($electionId) {
                $election = $db->query('SELECT * FROM election_config WHERE config_id = ?', [$electionId])->fetch();
            } else {
                $election = $db->query("SELECT * FROM election_config ORDER BY end_date DESC LIMIT 1")->fetch();
            }
            if ($election) {
                // Check if election has ended or if it's been manually ended
                $endTime = strtotime($election['end_date']);
                $currentTime = time();
                
                // Allow publishing if election has ended OR if it's been manually ended (end_date is in the past)
                if ($endTime > $currentTime && $election['is_active'] == 1) {
                    header('Location: ../../public/admin/index.php?error=' . urlencode('Cannot publish before election has ended.'));
                    exit;
                }
                
                $db->prepare("UPDATE election_config SET results_published = 1 WHERE config_id = ?")->execute([$election['config_id']]);
                header('Location: ../../public/admin/index.php?success=' . urlencode('Results published to home page.'));
            } else {
                header('Location: ../../public/admin/index.php?error=' . urlencode('No election found to publish results for.'));
            }
            exit;
        case 'cancel_election':
            $electionId = $_POST['election_id'] ?? null;
            if ($electionId) {
                $db->prepare('UPDATE election_config SET is_active = 0 WHERE config_id = ?')->execute([$electionId]);
                header('Location: ../../public/admin/index.php?success=' . urlencode('Election canceled successfully'));
            } else {
                header('Location: ../../public/admin/index.php?error=' . urlencode('Election ID missing.'));
            }
            exit;
        case 'end_now':
            $electionId = $_POST['election_id'] ?? null;
            if ($electionId) {
                // Force end: set end_date to NOW and deactivate
                $db->prepare('UPDATE election_config SET end_date = NOW(), is_active = 0 WHERE config_id = ?')->execute([$electionId]);
                header('Location: ../../public/admin/index.php?success=' . urlencode('Election ended immediately. You can now view results.'));
            } else {
                header('Location: ../../public/admin/index.php?error=' . urlencode('Election ID missing.'));
            }
            exit;
        case 'delete_election_votes':
            $electionId = $_POST['election_id'] ?? null;
            if ($electionId) {
                // Delete all votes for this election
                $stmt = $db->prepare('SELECT COUNT(*) as count FROM votes WHERE election_id = ?');
                $stmt->execute([$electionId]);
                $voteCount = $stmt->fetch()['count'];
                
                if ($voteCount > 0) {
                    $db->prepare('DELETE FROM votes WHERE election_id = ?')->execute([$electionId]);
                    header('Location: ../../public/admin/index.php?success=' . urlencode('Deleted ' . $voteCount . ' votes for election. You can now delete the election if needed.'));
                } else {
                    header('Location: ../../public/admin/index.php?info=' . urlencode('No votes found for this election.'));
                }
            } else {
                header('Location: ../../public/admin/index.php?error=' . urlencode('Election ID missing.'));
            }
            exit;
        case 'delete_election':
            $electionId = $_POST['election_id'] ?? null;
            if ($electionId) {
                // Check if there are votes for this election
                $stmt = $db->prepare('SELECT COUNT(*) as count FROM votes WHERE election_id = ?');
                $stmt->execute([$electionId]);
                $voteCount = $stmt->fetch()['count'];
                
                if ($voteCount > 0) {
                    header('Location: ../../public/admin/index.php?error=' . urlencode('Cannot delete election: There are ' . $voteCount . ' votes associated with this election. Delete votes first or use cancel instead.'));
                    exit;
                }
                
                $db->prepare('DELETE FROM election_config WHERE config_id = ?')->execute([$electionId]);
                header('Location: ../../public/admin/index.php?success=' . urlencode('Election deleted successfully'));
            } else {
                header('Location: ../../public/admin/index.php?error=' . urlencode('Election ID missing.'));
            }
            exit;
        case 'edit_election':
            $electionId = $_POST['election_id'] ?? null;
            $name = $_POST['election_name'] ?? null;
            $start = $_POST['start_date'] ?? null;
            $end = $_POST['end_date'] ?? null;
            if ($electionId && $name && $start && $end) {
                $db->prepare('UPDATE election_config SET election_name = ?, start_date = ?, end_date = ? WHERE config_id = ?')->execute([$name, $start, $end, $electionId]);
                header('Location: ../../public/admin/index.php?success=' . urlencode('Election updated successfully'));
            } else {
                header('Location: ../../public/admin/index.php?error=' . urlencode('Missing required fields.'));
            }
            exit;
        default:
            die('Unknown action');
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $type = $_GET['type'] ?? '';
    switch ($type) {
        case 'candidates':
            // Fetch and return candidate list as JSON
            // ...
            break;
        case 'officers':
            // Fetch and return officer list as JSON
            // ...
            break;
        default:
            die('Unknown type');
    }
} elseif (isset($_GET['logs']) && $_GET['logs'] == '1') {
    $page = max(1, intval($_GET['page'] ?? 1));
    $perPage = 10;
    $offset = ($page - 1) * $perPage;
    $where = [];
    $params = [];
    if (!empty($_GET['user_type'])) {
        $where[] = 'user_type = ?';
        $params[] = $_GET['user_type'];
    }
    if (!empty($_GET['action'])) {
        $where[] = 'action LIKE ?';
        $params[] = '%' . $_GET['action'] . '%';
    }
    if (!empty($_GET['status'])) {
        $where[] = 'status = ?';
        $params[] = $_GET['status'];
    }
    if (!empty($_GET['date_from'])) {
        $where[] = 'action_timestamp >= ?';
        $params[] = $_GET['date_from'] . ' 00:00:00';
    }
    if (!empty($_GET['date_to'])) {
        $where[] = 'action_timestamp <= ?';
        $params[] = $_GET['date_to'] . ' 23:59:59';
    }
    if (!empty($_GET['search'])) {
        $where[] = '(action LIKE ? OR details LIKE ?)';
        $params[] = '%' . $_GET['search'] . '%';
        $params[] = '%' . $_GET['search'] . '%';
    }
    $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';
    $total = $db->query("SELECT COUNT(*) as cnt FROM system_logs $whereSql", $params)->fetch()['cnt'];
    $logs = $db->query("SELECT action_timestamp, user_type, user_id, action, status, ip_address, details FROM system_logs $whereSql ORDER BY action_timestamp DESC LIMIT ? OFFSET ?", array_merge($params, [$perPage, $offset]))->fetchAll();
    header('Content-Type: application/json');
    echo json_encode([
        'logs' => $logs,
        'page' => $page,
        'totalPages' => ceil($total / $perPage)
    ]);
    exit;
} else {
    http_response_code(405);
    die('Method not allowed');
} 