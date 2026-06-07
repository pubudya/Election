<?php

namespace Utils;

class SessionCleanup
{
    private $sessionManager;
    private $db;
    
    public function __construct()
    {
        $this->sessionManager = new SessionManager();
        $this->db = new \Models\Database();
    }
    
    /**
     * Clean up expired sessions
     */
    public function cleanupExpiredSessions()
    {
        try {
            // Clean up file-based sessions
            $this->cleanupFileSessions();
            
            // Clean up database sessions if using database storage
            if (defined('SESSION_DB_CLEANUP_ENABLED') && SESSION_DB_CLEANUP_ENABLED) {
                $this->cleanupDatabaseSessions();
            }
            
            // Clean up remember me tokens
            $this->cleanupExpiredTokens();
            
            // Log cleanup activity
            $this->logCleanupActivity();
            
            return true;
        } catch (Exception $e) {
            error_log("Session cleanup error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clean up file-based sessions
     */
    private function cleanupFileSessions()
    {
        $sessionPath = session_save_path() ?: '/tmp';
        
        if (!is_dir($sessionPath)) {
            return;
        }
        
        $files = glob($sessionPath . '/sess_*');
        $currentTime = time();
        $cleanedCount = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $fileTime = filemtime($file);
                
                // Remove files older than max lifetime
                if (($currentTime - $fileTime) > SESSION_MAX_LIFETIME) {
                    if (unlink($file)) {
                        $cleanedCount++;
                    }
                }
            }
        }
        
        if (SESSION_LOGGING_ENABLED) {
            error_log("Cleaned up $cleanedCount expired session files");
        }
    }
    
    /**
     * Clean up database sessions
     */
    private function cleanupDatabaseSessions()
    {
        try {
            $table = SESSION_DB_TABLE;
            $maxLifetime = SESSION_MAX_LIFETIME;
            
            $sql = "DELETE FROM $table WHERE last_activity < (UNIX_TIMESTAMP() - $maxLifetime)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $cleanedCount = $stmt->rowCount();
            
            if (SESSION_LOGGING_ENABLED) {
                error_log("Cleaned up $cleanedCount expired database sessions");
            }
        } catch (Exception $e) {
            error_log("Database session cleanup error: " . $e->getMessage());
        }
    }
    
    /**
     * Clean up expired remember me tokens
     */
    private function cleanupExpiredTokens()
    {
        try {
            $sql = "DELETE FROM remember_tokens WHERE expires_at < NOW()";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            $cleanedCount = $stmt->rowCount();
            
            if (SESSION_LOGGING_ENABLED) {
                error_log("Cleaned up $cleanedCount expired remember me tokens");
            }
        } catch (Exception $e) {
            error_log("Token cleanup error: " . $e->getMessage());
        }
    }
    
    /**
     * Log cleanup activity
     */
    private function logCleanupActivity()
    {
        if (SESSION_LOGGING_ENABLED) {
            $logMessage = sprintf(
                "Session cleanup completed at %s - Files: %s, Database: %s, Tokens: %s",
                date('Y-m-d H:i:s'),
                $this->getFileSessionCount(),
                $this->getDatabaseSessionCount(),
                $this->getTokenCount()
            );
            
            error_log($logMessage);
        }
    }
    
    /**
     * Get current file session count
     */
    private function getFileSessionCount()
    {
        $sessionPath = session_save_path() ?: '/tmp';
        if (!is_dir($sessionPath)) {
            return 0;
        }
        
        $files = glob($sessionPath . '/sess_*');
        return count($files);
    }
    
    /**
     * Get current database session count
     */
    private function getDatabaseSessionCount()
    {
        try {
            if (!defined('SESSION_DB_CLEANUP_ENABLED') || !SESSION_DB_CLEANUP_ENABLED) {
                return 0;
            }
            
            $table = SESSION_DB_TABLE;
            $sql = "SELECT COUNT(*) as count FROM $table";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get current token count
     */
    private function getTokenCount()
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM remember_tokens";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch();
            
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Force cleanup of specific user sessions
     */
    public function cleanupUserSessions($userId, $userType = null)
    {
        try {
            // Clean up file sessions for specific user
            $this->cleanupUserFileSessions($userId);
            
            // Clean up database sessions for specific user
            if (defined('SESSION_DB_CLEANUP_ENABLED') && SESSION_DB_CLEANUP_ENABLED) {
                $this->cleanupUserDatabaseSessions($userId, $userType);
            }
            
            // Clean up remember me tokens for specific user
            $this->cleanupUserTokens($userId);
            
            return true;
        } catch (Exception $e) {
            error_log("User session cleanup error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Clean up file sessions for specific user
     */
    private function cleanupUserFileSessions($userId)
    {
        $sessionPath = session_save_path() ?: '/tmp';
        
        if (!is_dir($sessionPath)) {
            return;
        }
        
        $files = glob($sessionPath . '/sess_*');
        $cleanedCount = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $sessionData = file_get_contents($file);
                
                // Check if session contains user ID
                if (strpos($sessionData, "user_id|s:" . strlen($userId) . ":\"$userId\"") !== false) {
                    if (unlink($file)) {
                        $cleanedCount++;
                    }
                }
            }
        }
        
        if (SESSION_LOGGING_ENABLED) {
            error_log("Cleaned up $cleanedCount file sessions for user $userId");
        }
    }
    
    /**
     * Clean up database sessions for specific user
     */
    private function cleanupUserDatabaseSessions($userId, $userType = null)
    {
        try {
            $table = SESSION_DB_TABLE;
            $sql = "DELETE FROM $table WHERE user_id = ?";
            $params = [$userId];
            
            if ($userType) {
                $sql .= " AND user_type = ?";
                $params[] = $userType;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            $cleanedCount = $stmt->rowCount();
            
            if (SESSION_LOGGING_ENABLED) {
                error_log("Cleaned up $cleanedCount database sessions for user $userId");
            }
        } catch (Exception $e) {
            error_log("User database session cleanup error: " . $e->getMessage());
        }
    }
    
    /**
     * Clean up tokens for specific user
     */
    private function cleanupUserTokens($userId)
    {
        try {
            $sql = "DELETE FROM remember_tokens WHERE user_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
            
            $cleanedCount = $stmt->rowCount();
            
            if (SESSION_LOGGING_ENABLED) {
                error_log("Cleaned up $cleanedCount tokens for user $userId");
            }
        } catch (Exception $e) {
            error_log("User token cleanup error: " . $e->getMessage());
        }
    }
    
    /**
     * Get session statistics
     */
    public function getSessionStats()
    {
        return [
            'file_sessions' => $this->getFileSessionCount(),
            'database_sessions' => $this->getDatabaseSessionCount(),
            'tokens' => $this->getTokenCount(),
            'total_sessions' => $this->getFileSessionCount() + $this->getDatabaseSessionCount(),
            'last_cleanup' => $this->getLastCleanupTime()
        ];
    }
    
    /**
     * Get last cleanup time
     */
    private function getLastCleanupTime()
    {
        // This could be stored in a database or file
        // For now, return current time
        return date('Y-m-d H:i:s');
    }
    
    /**
     * Run health check on sessions
     */
    public function healthCheck()
    {
        $stats = $this->getSessionStats();
        $issues = [];
        
        // Check for too many sessions
        if ($stats['total_sessions'] > 10000) {
            $issues[] = 'Too many active sessions: ' . $stats['total_sessions'];
        }
        
        // Check for orphaned sessions
        if ($stats['file_sessions'] > 0 && !is_writable(session_save_path())) {
            $issues[] = 'Session directory not writable';
        }
        
        return [
            'healthy' => empty($issues),
            'issues' => $issues,
            'stats' => $stats
        ];
    }
}
