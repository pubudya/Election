<?php

namespace Utils;

class SessionManager
{
    private const SESSION_TIMEOUT = 3600; // 1 hour
    private const REMEMBER_ME_TIMEOUT = 604800; // 7 days
    
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // Regenerate session ID periodically for security
        if (!isset($_SESSION['last_regeneration']) || 
            (time() - $_SESSION['last_regeneration']) > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
    
    /**
     * Start user session after successful authentication
     */
    public function startUserSession($userId, $userType, $userData, $rememberMe = false)
    {
        // Clear any existing session data
        $this->clearSession();
        
        // Set session timeout
        $timeout = $rememberMe ? self::REMEMBER_ME_TIMEOUT : self::SESSION_TIMEOUT;
        
        // Store user data in session
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_type'] = $userType;
        $_SESSION['user_data'] = $userData;
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['session_timeout'] = $timeout;
        $_SESSION['is_authenticated'] = true;
        
        // Set remember me cookie if requested
        if ($rememberMe) {
            $this->setRememberMeCookie($userId, $userType);
        }
        
        // Log successful login
        $this->logSessionActivity('login', $userId, $userType);
    }
    
    /**
     * Check if user is authenticated and session is valid
     */
    public function isAuthenticated()
    {
        if (!isset($_SESSION['is_authenticated']) || !$_SESSION['is_authenticated']) {
            return false;
        }
        
        // Check session timeout
        if (isset($_SESSION['last_activity']) && 
            (time() - $_SESSION['last_activity']) > $_SESSION['session_timeout']) {
            $this->destroySession();
            return false;
        }
        
        // Update last activity
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Check if user is of specific type
     */
    public function isUserType($userType)
    {
        return $this->isAuthenticated() && 
               isset($_SESSION['user_type']) && 
               $_SESSION['user_type'] === $userType;
    }
    
    /**
     * Get current user data
     */
    public function getUserData()
    {
        return $this->isAuthenticated() ? $_SESSION['user_data'] : null;
    }
    
    /**
     * Get current user ID
     */
    public function getUserId()
    {
        return $this->isAuthenticated() ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Get current user type
     */
    public function getUserType()
    {
        return $this->isAuthenticated() ? $_SESSION['user_type'] : null;
    }
    
    /**
     * Destroy user session
     */
    public function destroySession()
    {
        // Log logout
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
            $this->logSessionActivity('logout', $_SESSION['user_id'], $_SESSION['user_type']);
        }
        
        // Clear session data
        $this->clearSession();
        
        // Destroy session
        session_destroy();
        
        // Clear remember me cookie
        $this->clearRememberMeCookie();
        
        // Clear all cookies
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                setcookie($name, '', time() - 3600, '/');
            }
        }
    }
    
    /**
     * Clear session data
     */
    private function clearSession()
    {
        $_SESSION = array();
    }
    
    /**
     * Set remember me cookie
     */
    private function setRememberMeCookie($userId, $userType)
    {
        $token = bin2hex(random_bytes(32));
        $expiry = time() + self::REMEMBER_ME_TIMEOUT;
        
        // Store token in database (you'll need to implement this)
        // For now, we'll store it in session
        $_SESSION['remember_token'] = $token;
        
        setcookie(
            'remember_token',
            $token,
            $expiry,
            '/',
            '',
            true, // secure
            true  // httponly
        );
    }
    
    /**
     * Clear remember me cookie
     */
    private function clearRememberMeCookie()
    {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    /**
     * Log session activity
     */
    private function logSessionActivity($action, $userId, $userType)
    {
        // You can implement logging to database here
        // For now, we'll just log to error log
        error_log("Session Activity: User ID: $userId, Type: $userType, Action: $action, Time: " . date('Y-m-d H:i:s'));
    }
    
    /**
     * Extend session timeout
     */
    public function extendSession()
    {
        if ($this->isAuthenticated()) {
            $_SESSION['last_activity'] = time();
            return true;
        }
        return false;
    }
    
    /**
     * Check if session is about to expire
     */
    public function isSessionExpiringSoon($minutes = 5)
    {
        if (!$this->isAuthenticated()) {
            return false;
        }
        
        $timeLeft = $_SESSION['session_timeout'] - (time() - $_SESSION['last_activity']);
        return $timeLeft <= ($minutes * 60);
    }
    
    /**
     * Get session status information
     */
    public function getSessionInfo()
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        return [
            'user_id' => $_SESSION['user_id'],
            'user_type' => $_SESSION['user_type'],
            'login_time' => $_SESSION['login_time'],
            'last_activity' => $_SESSION['last_activity'],
            'timeout' => $_SESSION['session_timeout'],
            'time_left' => $_SESSION['session_timeout'] - (time() - $_SESSION['last_activity'])
        ];
    }
}
