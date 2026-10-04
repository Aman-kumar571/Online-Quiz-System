<?php
// Configuration template for Online Quiz System.
// Copy this file to config.php and fill in your values.

// Timezone
date_default_timezone_set('Asia/Kolkata');

// Base URL: no trailing slash. Set to your production domain.
define('BASE_URL', 'https://yourdomain.com');

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'quiz_system');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');
define('DB_CHARSET', 'utf8mb4');

// Site settings
define('SITE_NAME', 'QuizPoint');
define('CONTACT_EMAIL', 'contact@yourdomain.com');

// Rate limiting
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINUTES', 15);
