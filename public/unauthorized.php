<?php
require_once '../vendor/autoload.php';

use Utils\SessionManager;

$sessionManager = new SessionManager();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - Election System</title>
    <link rel="stylesheet" href="/assets/css/sri-lanka-theme.css">
    <style>
        .unauthorized-container {
            text-align: center;
            padding: 50px 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        .error-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        .error-title {
            color: #dc3545;
            font-size: 2.5em;
            margin-bottom: 20px;
        }
        .error-message {
            color: #6c757d;
            font-size: 1.2em;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        .action-buttons {
            margin-top: 30px;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 0 10px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
    <div class="unauthorized-container">
        <div class="error-icon">🚫</div>
        <h1 class="error-title">Access Denied</h1>
        <p class="error-message">
            Sorry, you don't have permission to access this resource. 
            This could be because:
        </p>
        <ul style="text-align: left; display: inline-block; color: #6c757d;">
            <li>You're not logged in</li>
            <li>Your session has expired</li>
            <li>You don't have the required permissions</li>
            <li>You're trying to access a restricted area</li>
        </ul>
        
        <div class="action-buttons">
            <?php if ($sessionManager->isAuthenticated()): ?>
                <a href="/<?php echo $sessionManager->getUserType(); ?>/" class="btn btn-primary">
                    Go to Dashboard
                </a>
            <?php else: ?>
                <a href="/login.php" class="btn btn-primary">
                    Login
                </a>
            <?php endif; ?>
            
            <a href="/" class="btn btn-secondary">
                Go Home
            </a>
        </div>
        
        <div style="margin-top: 40px; padding: 20px; background-color: #f8f9fa; border-radius: 5px;">
            <p style="margin: 0; color: #6c757d; font-size: 0.9em;">
                If you believe this is an error, please contact the system administrator.
            </p>
        </div>
    </div>
</body>
</html>
