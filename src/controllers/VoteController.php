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
    if (!isset($_SESSION['voter_id'])) {
        http_response_code(401);
        die('Invalid voting session.');
    }
    $voterId = $_SESSION['voter_id'];
    // Normalize preferences: convert empty strings or "0" to NULL; cast valid ids to int
    $firstPref = isset($_POST['firstPref']) && $_POST['firstPref'] !== '' && $_POST['firstPref'] !== '0' ? (int)$_POST['firstPref'] : null;
    $secondPref = isset($_POST['secondPref']) && $_POST['secondPref'] !== '' && $_POST['secondPref'] !== '0' ? (int)$_POST['secondPref'] : null;
    $thirdPref = isset($_POST['thirdPref']) && $_POST['thirdPref'] !== '' && $_POST['thirdPref'] !== '0' ? (int)$_POST['thirdPref'] : null;
    $ip = $_SERVER['REMOTE_ADDR'];
    $device = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $sessionId = session_id();
    // Check election is active
    $election = $db->query("SELECT * FROM election_config WHERE is_active = TRUE AND start_date <= NOW() AND end_date >= NOW() LIMIT 1")->fetch();
    if (!$election) {
        header('Location: ../../public/voter/index.php?error=' . urlencode('No active election at this time.'));
        exit;
    }
    $electionId = $election['config_id'];
    // Check if already voted in this election
    $stmt = $db->prepare('SELECT vote_id FROM votes WHERE voter_id = ? AND election_id = ?');
    $stmt->execute([$voterId, $electionId]);
    if ($stmt->rowCount() > 0) {
        die('You have already voted in this election.');
    }
    if (!$firstPref) {
        header('Location: ../../public/voting.php?error=' . urlencode('You must select at least a 1st preference.'));
        exit;
    }
    // Validate candidate IDs (only those provided)
    $candidateIds = [];
    if (!is_null($firstPref)) $candidateIds[] = $firstPref;
    if (!is_null($secondPref)) $candidateIds[] = $secondPref;
    if (!is_null($thirdPref)) $candidateIds[] = $thirdPref;
    $placeholders = implode(',', array_fill(0, count($candidateIds), '?'));
    $stmt = $db->prepare("SELECT COUNT(*) AS valid FROM candidates WHERE candidate_id IN ($placeholders) AND is_active = TRUE");
    $stmt->execute($candidateIds);
    $result = $stmt->fetch();
    if ($result['valid'] != count($candidateIds)) {
        die('Invalid candidate selection.');
    }
    // Insert vote with election_id
    $stmt = $db->prepare('INSERT INTO votes (voter_id, first_preference, second_preference, third_preference, ip_address, device_info, session_id, election_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$voterId, $firstPref, $secondPref, $thirdPref, $ip, $device, $sessionId, $electionId]);
    // Mark voter as voted
    $db->prepare('UPDATE voters SET has_voted = TRUE WHERE voter_id = ?')->execute([$voterId]);
    // Log vote
    $security->logEvent('vote_submitted', $voterId, $ip, 'success', 'Vote recorded successfully');
    unset($_SESSION['voting_session']);
    $_SESSION['has_voted'] = true;
    header('Location: ../../public/voter/index.php?success=' . urlencode('Your vote has been recorded successfully.'));
    exit;
} else {
    http_response_code(405);
    die('Method not allowed');
} 