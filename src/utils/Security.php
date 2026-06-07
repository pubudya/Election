<?php
require_once __DIR__ . '/../../src/utils/Mailer.php';

class Security {
    private $salt;
    public function __construct() {
        $this->salt = 'default_salt'; // Hardcoded for guaranteed match
        error_log('Security salt: ' . $this->salt);
    }
    public function hashPassword($password) {
        return hash('sha256', $password . $this->salt);
    }
    public function verifyHash($password, $hash) {
        return hash_equals($hash, $this->hashPassword($password));
    }
    public function isBruteForce($nicOrUser, $ip) {
        // Check failed_logins in last 15 min
        $db = new DatabaseModel();
        $stmt = $db->prepare('SELECT COUNT(*) AS attempts FROM failed_logins WHERE (nic = ? OR username = ? OR ip_address = ?) AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)');
        $stmt->execute([$nicOrUser, $nicOrUser, $ip]);
        $result = $stmt->fetch();
        return $result['attempts'] > 5;
    }
    public function logFailedLogin($nicOrUser, $ip, $role) {
        $db = new DatabaseModel();
        $stmt = $db->prepare('INSERT INTO failed_logins (nic, username, ip_address, user_agent) VALUES (?, ?, ?, ?)');
        $stmt->execute([$role === 'voter' || $role === 'officer' ? $nicOrUser : null, $role === 'admin' ? $nicOrUser : null, $ip, $_SERVER['HTTP_USER_AGENT'] ?? '']);
    }
    public function generateOTP($nic, $purpose, $email = null) {
        $db = new DatabaseModel();
        // Check for recent unused OTP
        $stmt = $db->prepare('SELECT otp_code FROM otp_logs WHERE nic = ? AND purpose = ? AND is_used = FALSE AND expires_at > NOW() ORDER BY generated_at DESC LIMIT 1');
        $stmt->execute([$nic, $purpose]);
        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            return $row['otp_code'];
        }
        $otp = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $stmt = $db->prepare('INSERT INTO otp_logs (nic, otp_code, expires_at, purpose, ip_address) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 10 MINUTE), ?, ?)');
        $stmt->execute([$nic, $otp, $purpose, $_SERVER['REMOTE_ADDR']]);
        if ($email) {
            Mailer::sendMail($email, 'Your Election System OTP', "Your OTP code is: <strong>$otp</strong><br>Valid for 10 minutes.");
        }
        return $otp;
    }
    public function logEvent($action, $userId, $ip, $status, $details) {
        $db = new DatabaseModel();
        $stmt = $db->prepare('INSERT INTO system_logs (user_type, user_id, action, ip_address, user_agent, status, details) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $userType = $this->determineUserType($userId);
        $stmt->execute([$userType, $userId, $action, $ip, $_SERVER['HTTP_USER_AGENT'] ?? '', $status, $details]);
    }
    private function determineUserType($userId) {
        // Simple logic: if null, unknown; else voter
        if (!$userId) return 'unknown';
        // Could be improved to check DB for user type
        return 'voter';
    }
} 