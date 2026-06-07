<?php
header('Content-Type: application/json');
require_once '../../vendor/autoload.php';

use Utils\SessionManager;

try {
    $sessionManager = new SessionManager();
    
    if ($sessionManager->isAuthenticated()) {
        $sessionInfo = $sessionManager->getSessionInfo();
        
        echo json_encode([
            'authenticated' => true,
            'user_id' => $sessionInfo['user_id'],
            'user_type' => $sessionInfo['user_type'],
            'login_time' => $sessionInfo['login_time'],
            'last_activity' => $sessionInfo['last_activity'],
            'timeout' => $sessionInfo['timeout'],
            'time_left' => $sessionInfo['time_left'],
            'is_expiring_soon' => $sessionManager->isSessionExpiringSoon()
        ]);
    } else {
        echo json_encode([
            'authenticated' => false,
            'message' => 'User not authenticated'
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Internal server error',
        'debug' => DEBUG ? $e->getMessage() : null
    ]);
}
