<?php
header('Content-Type: application/json');
require_once '../../vendor/autoload.php';

use Utils\SessionManager;

try {
    $sessionManager = new SessionManager();
    
    if ($sessionManager->isAuthenticated()) {
        $success = $sessionManager->extendSession();
        
        if ($success) {
            $sessionInfo = $sessionManager->getSessionInfo();
            
            echo json_encode([
                'success' => true,
                'message' => 'Session extended successfully',
                'session_info' => $sessionInfo
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to extend session'
            ]);
        }
    } else {
        http_response_code(401);
        echo json_encode([
            'success' => false,
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
