<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once __DIR__ . '/../vendor/autoload.php';

define('MAIL_HOST', 'smtp.gmail.com');
define('MAIL_USERNAME', 'amirunoel8@gmail.com');
define('MAIL_PASSWORD', 'imyqmkfogxrpkzdv');
define('MAIL_PORT', 587);
define('MAIL_FROM', 'amirunoel8@gmail.com');
define('MAIL_FROM_NAME', 'Sri Lanka Election System');
// NOTE: For Gmail, you must enable 2FA and use an App Password, not your normal Gmail password. 