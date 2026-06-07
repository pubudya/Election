<?php

namespace Middleware;

use Utils\SessionManager;

class SessionAuth
{
    private $sessionManager;
    
    public function __construct()
    {
        $this->sessionManager = new SessionManager();
    }
    
    /**
     * Check if user is authenticated
     */
    public function requireAuth()
    {
        if (!$this->sessionManager->isAuthenticated()) {
            $this->redirectToLogin();
        }
    }
    
    /**
     * Check if user is admin
     */
    public function requireAdmin()
    {
        if (!$this->sessionManager->isUserType('admin')) {
            $this->redirectToUnauthorized();
        }
    }
    
    /**
     * Check if user is officer
     */
    public function requireOfficer()
    {
        if (!$this->sessionManager->isUserType('officer')) {
            $this->redirectToUnauthorized();
        }
    }
    
    /**
     * Check if user is voter
     */
    public function requireVoter()
    {
        if (!$this->sessionManager->isUserType('voter')) {
            $this->redirectToUnauthorized();
        }
    }
    
    /**
     * Check if user is either admin or officer
     */
    public function requireAdminOrOfficer()
    {
        if (!$this->sessionManager->isUserType('admin') && 
            !$this->sessionManager->isUserType('officer')) {
            $this->redirectToUnauthorized();
        }
    }
    
    /**
     * Check if user is either officer or voter
     */
    public function requireOfficerOrVoter()
    {
        if (!$this->sessionManager->isUserType('officer') && 
            !$this->sessionManager->isUserType('voter')) {
            $this->redirectToUnauthorized();
        }
    }
    
    /**
     * Get current user data
     */
    public function getCurrentUser()
    {
        return $this->sessionManager->getUserData();
    }
    
    /**
     * Get current user type
     */
    public function getCurrentUserType()
    {
        return $this->sessionManager->getUserType();
    }
    
    /**
     * Check if session is expiring soon and show warning
     */
    public function checkSessionExpiry()
    {
        if ($this->sessionManager->isSessionExpiringSoon()) {
            $this->showSessionExpiryWarning();
        }
    }
    
    /**
     * Extend session
     */
    public function extendSession()
    {
        return $this->sessionManager->extendSession();
    }
    
    /**
     * Redirect to login page
     */
    private function redirectToLogin()
    {
        // Store intended URL for redirect after login
        if (isset($_SERVER['REQUEST_URI'])) {
            $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
        }
        
        header('Location: /login.php');
        exit();
    }
    
    /**
     * Redirect to unauthorized page
     */
    private function redirectToUnauthorized()
    {
        header('Location: /unauthorized.php');
        exit();
    }
    
    /**
     * Show session expiry warning
     */
    private function showSessionExpiryWarning()
    {
        if (!isset($_SESSION['expiry_warning_shown'])) {
            $_SESSION['expiry_warning_shown'] = true;
            
            // You can implement a JavaScript alert or modal here
            // For now, we'll just set a session variable
            $_SESSION['show_expiry_warning'] = true;
        }
    }
    
    /**
     * Check if user can access specific resource
     */
    public function canAccessResource($resourceType, $resourceId = null)
    {
        $userType = $this->getCurrentUserType();
        $userId = $this->sessionManager->getUserId();
        
        switch ($resourceType) {
            case 'own_profile':
                return $userId == $resourceId;
                
            case 'voter_data':
                return in_array($userType, ['admin', 'officer']);
                
            case 'candidate_data':
                return in_array($userType, ['admin', 'officer']);
                
            case 'election_results':
                return in_array($userType, ['admin', 'officer', 'voter']);
                
            case 'admin_panel':
                return $userType === 'admin';
                
            case 'officer_panel':
                return in_array($userType, ['admin', 'officer']);
                
            default:
                return false;
        }
    }
}
