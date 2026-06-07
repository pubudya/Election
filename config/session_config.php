<?php
/**
 * Session Configuration File
 * Centralized configuration for all session-related settings
 */

// Session timeout settings (in seconds)
define('SESSION_TIMEOUT', 3600);           // 1 hour - normal session
define('REMEMBER_ME_TIMEOUT', 604800);     // 7 days - remember me session
define('SESSION_WARNING_TIME', 300);       // 5 minutes - show warning before expiry
define('SESSION_REGENERATION_TIME', 300);  // 5 minutes - regenerate session ID

// Cookie settings
define('SESSION_COOKIE_NAME', 'PHPSESSID');
define('SESSION_COOKIE_PATH', '/');
define('SESSION_COOKIE_DOMAIN', '');
define('SESSION_COOKIE_SECURE', true);     // HTTPS only
define('SESSION_COOKIE_HTTPONLY', true);   // Prevent XSS
define('SESSION_COOKIE_SAMESITE', 'Strict');

// Remember me settings
define('REMEMBER_COOKIE_NAME', 'remember_token');
define('REMEMBER_COOKIE_PATH', '/');
define('REMEMBER_COOKIE_DOMAIN', '');
define('REMEMBER_COOKIE_SECURE', true);
define('REMEMBER_COOKIE_HTTPONLY', true);

// Session security settings
define('SESSION_USE_STRICT_MODE', true);
define('SESSION_USE_ONLY_COOKIES', true);
define('SESSION_COOKIE_LIFETIME', 0);      // Session cookie (browser close)

// Session storage settings
define('SESSION_SAVE_HANDLER', 'files');   // files, redis, memcached
define('SESSION_SAVE_PATH', '/tmp');       // Path for file-based sessions

// Logging settings
define('SESSION_LOGGING_ENABLED', true);
define('SESSION_LOG_LEVEL', 'INFO');       // DEBUG, INFO, WARNING, ERROR

// User type configurations
$USER_SESSION_CONFIGS = [
    'admin' => [
        'timeout' => SESSION_TIMEOUT,
        'max_concurrent_sessions' => 3,
        'allowed_ips' => [], // Empty means all IPs allowed
        'activity_logging' => true,
        'session_regeneration' => true
    ],
    'officer' => [
        'timeout' => SESSION_TIMEOUT,
        'max_concurrent_sessions' => 2,
        'allowed_ips' => [],
        'activity_logging' => true,
        'session_regeneration' => true
    ],
    'voter' => [
        'timeout' => SESSION_TIMEOUT,
        'max_concurrent_sessions' => 1,
        'allowed_ips' => [],
        'activity_logging' => false,
        'session_regeneration' => false
    ]
];

// Session cleanup settings
define('SESSION_CLEANUP_INTERVAL', 3600);  // Clean up expired sessions every hour
define('SESSION_MAX_LIFETIME', 86400);     // Maximum session lifetime (24 hours)

// CSRF protection settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LENGTH', 32);
define('CSRF_TOKEN_EXPIRY', 3600);        // 1 hour

// Rate limiting settings
define('SESSION_RATE_LIMIT_ENABLED', true);
define('SESSION_MAX_LOGIN_ATTEMPTS', 5);
define('SESSION_LOGIN_TIMEOUT', 900);      // 15 minutes lockout
define('SESSION_MAX_REQUESTS_PER_MINUTE', 60);

// Debug settings
define('SESSION_DEBUG_MODE', false);
define('SESSION_SHOW_TIMEOUT_WARNINGS', true);
define('SESSION_AUTO_EXTEND', true);

// Database session settings (if using database storage)
define('SESSION_DB_TABLE', 'user_sessions');
define('SESSION_DB_CLEANUP_ENABLED', true);

// Mobile/device settings
define('SESSION_MOBILE_TIMEOUT_MULTIPLIER', 0.5);  // Mobile sessions are shorter
define('SESSION_DEVICE_FINGERPRINTING', true);

// API session settings
define('API_SESSION_TIMEOUT', 1800);       // 30 minutes for API sessions
define('API_SESSION_RENEWAL', true);       // Allow API session renewal

// Error handling
define('SESSION_ERROR_REPORTING', true);
define('SESSION_ERROR_LOG_FILE', '/var/log/session_errors.log');

// Backup and recovery
define('SESSION_BACKUP_ENABLED', false);
define('SESSION_BACKUP_INTERVAL', 86400);  // Daily backup

// Performance settings
define('SESSION_GARBAGE_COLLECTION', true);
define('SESSION_GARBAGE_COLLECTION_PROBABILITY', 1);
define('SESSION_GARBAGE_COLLECTION_DIVISOR', 100);

// Monitoring and alerts
define('SESSION_MONITORING_ENABLED', true);
define('SESSION_ALERT_ON_EXPIRY', true);
define('SESSION_ALERT_ON_SUSPICIOUS_ACTIVITY', true);

// Compliance settings
define('SESSION_GDPR_COMPLIANT', true);
define('SESSION_DATA_RETENTION_DAYS', 30);
define('SESSION_PRIVACY_NOTICE', true);

// Custom session handlers
define('SESSION_CUSTOM_HANDLER_ENABLED', false);
define('SESSION_CUSTOM_HANDLER_CLASS', '');

// Session sharing between subdomains
define('SESSION_SHARE_SUBDOMAINS', false);
define('SESSION_SUBDOMAIN_LIST', []);

// Session migration settings
define('SESSION_MIGRATION_ENABLED', false);
define('SESSION_MIGRATION_PATH', '');

// Health check settings
define('SESSION_HEALTH_CHECK_ENABLED', true);
define('SESSION_HEALTH_CHECK_INTERVAL', 300);  // Every 5 minutes

// Export/Import settings
define('SESSION_EXPORT_ENABLED', false);
define('SESSION_IMPORT_ENABLED', false);
define('SESSION_EXPORT_FORMAT', 'json');

// Maintenance mode
define('SESSION_MAINTENANCE_MODE', false);
define('SESSION_MAINTENANCE_MESSAGE', 'System is under maintenance. Please try again later.');

// Load environment-specific overrides
$env = getenv('APP_ENV') ?: 'production';
$envConfigFile = __DIR__ . "/session_config_{$env}.php";

if (file_exists($envConfigFile)) {
    require_once $envConfigFile;
}
