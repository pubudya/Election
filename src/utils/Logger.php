<?php
require_once __DIR__ . '/../models/Database.php';
class Logger {
    public static function log($userType, $userId, $action, $status, $details) {
        $db = new DatabaseModel();
        $stmt = $db->prepare('INSERT INTO system_logs (user_type, user_id, action, ip_address, user_agent, status, details) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $stmt->execute([$userType, $userId, $action, $ip, $agent, $status, $details]);
    }
} 