<?php
// Configuration file for Online Quiz System.
// Copy config.example.php to config.php and fill in your values.

// Timezone (used by PHP date functions and session handling)
date_default_timezone_set('Asia/Kolkata');

// Base URL: no trailing slash. Update this for production.
define('BASE_URL', 'http://localhost/quiz-system');

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'quiz_system');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Site settings
define('SITE_NAME', 'QuizPoint');
define('CONTACT_EMAIL', 'contact@quizpoint.local');

// Rate limiting: max failed login attempts per email+IP in the window
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_MINUTES', 15);
