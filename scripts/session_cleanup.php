#!/usr/bin/env php
<?php
/**
 * Session Cleanup Cron Job Script
 * Run this script periodically to clean up expired sessions
 * 
 * Usage:
 * php session_cleanup.php [--force] [--verbose] [--dry-run]
 * 
 * Cron job example (every hour):
 * 0 * * * * /usr/bin/php /path/to/scripts/session_cleanup.php
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Parse command line arguments
$options = getopt('', ['force', 'verbose', 'dry-run', 'help']);

if (isset($options['help'])) {
    echo "Session Cleanup Script\n";
    echo "Usage: php session_cleanup.php [--force] [--verbose] [--dry-run]\n\n";
    echo "Options:\n";
    echo "  --force     Force cleanup even if not due\n";
    echo "  --verbose   Show detailed output\n";
    echo "  --dry-run   Show what would be cleaned without actually cleaning\n";
    echo "  --help      Show this help message\n";
    exit(0);
}

$verbose = isset($options['verbose']);
$dryRun = isset($options['dry-run']);
$force = isset($options['force']);

// Load configuration
$configPath = __DIR__ . '/../config/session_config.php';
if (!file_exists($configPath)) {
    die("Error: Session configuration file not found at $configPath\n");
}

require_once $configPath;

// Load autoloader
$autoloadPath = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die("Error: Autoloader not found at $autoloadPath\n");
}

require_once $autoloadPath;

use Utils\SessionCleanup;

echo "Session Cleanup Script Started at " . date('Y-m-d H:i:s') . "\n";
echo "================================================\n\n";

try {
    // Initialize session cleanup utility
    $cleanup = new SessionCleanup();
    
    // Check if cleanup is due
    if (!$force) {
        $lastCleanupFile = __DIR__ . '/../tmp/last_session_cleanup.txt';
        $lastCleanup = 0;
        
        if (file_exists($lastCleanupFile)) {
            $lastCleanup = (int) file_get_contents($lastCleanupFile);
        }
        
        $timeSinceLastCleanup = time() - $lastCleanup;
        
        if ($timeSinceLastCleanup < SESSION_CLEANUP_INTERVAL) {
            $nextCleanup = SESSION_CLEANUP_INTERVAL - $timeSinceLastCleanup;
            echo "Cleanup not due yet. Next cleanup in " . gmdate('H:i:s', $nextCleanup) . "\n";
            exit(0);
        }
    }
    
    // Get current session statistics
    if ($verbose) {
        echo "Current Session Statistics:\n";
        echo "---------------------------\n";
        $stats = $cleanup->getSessionStats();
        foreach ($stats as $key => $value) {
            echo ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
        }
        echo "\n";
    }
    
    // Run health check
    if ($verbose) {
        echo "Running Health Check:\n";
        echo "--------------------\n";
        $health = $cleanup->healthCheck();
        echo "Status: " . ($health['healthy'] ? 'Healthy' : 'Issues Found') . "\n";
        
        if (!empty($health['issues'])) {
            echo "Issues:\n";
            foreach ($health['issues'] as $issue) {
                echo "  - $issue\n";
            }
        }
        echo "\n";
    }
    
    if ($dryRun) {
        echo "DRY RUN MODE - No actual cleanup will be performed\n";
        echo "================================================\n\n";
        
        // Simulate cleanup process
        echo "Would clean up expired sessions...\n";
        echo "Would clean up expired tokens...\n";
        echo "Would update cleanup timestamp...\n";
        
    } else {
        echo "Starting Session Cleanup...\n";
        echo "==========================\n\n";
        
        // Perform cleanup
        $startTime = microtime(true);
        $result = $cleanup->cleanupExpiredSessions();
        $endTime = microtime(true);
        
        if ($result) {
            echo "Cleanup completed successfully!\n";
            echo "Time taken: " . round(($endTime - $startTime) * 1000, 2) . "ms\n";
            
            // Update last cleanup timestamp
            $lastCleanupFile = __DIR__ . '/../tmp/last_session_cleanup.txt';
            $tmpDir = dirname($lastCleanupFile);
            
            if (!is_dir($tmpDir)) {
                mkdir($tmpDir, 0755, true);
            }
            
            file_put_contents($lastCleanupFile, time());
            
        } else {
            echo "Cleanup failed!\n";
            exit(1);
        }
    }
    
    // Show final statistics
    if ($verbose) {
        echo "\nFinal Session Statistics:\n";
        echo "-------------------------\n";
        $finalStats = $cleanup->getSessionStats();
        foreach ($finalStats as $key => $value) {
            echo ucfirst(str_replace('_', ' ', $key)) . ": $value\n";
        }
    }
    
    echo "\nSession Cleanup Script Completed at " . date('Y-m-d H:i:s') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

// Function to format bytes
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

// Function to format time
function formatTime($seconds) {
    if ($seconds < 60) {
        return $seconds . 's';
    } elseif ($seconds < 3600) {
        return round($seconds / 60, 1) . 'm';
    } else {
        return round($seconds / 3600, 1) . 'h';
    }
}
