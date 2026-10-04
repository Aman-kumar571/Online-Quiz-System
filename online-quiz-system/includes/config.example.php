<?php
/**
 * Application configuration.
 * Copy this file to config.php and fill in your values.
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'quiz_system');
define('DB_USER', 'root');
define('DB_PASS', '');          // Set your MySQL password
define('DB_CHARSET', 'utf8mb4');

// Base URL (no trailing slash)
// Change this when deploying to a real domain.
define('BASE_URL', 'http://localhost/online-quiz-system');

// Site identity
define('SITE_NAME', 'QuizPoint');
define('SITE_TAGLINE', 'Practice quizzes for computer science students');
define('CONTACT_EMAIL', 'contact@quizpoint.example');

// Canonical domain (used in sitemap, OG tags)
define('CANONICAL_URL', 'https://quizpoint.example');

// Session configuration
define('SESSION_LIFETIME', 3600); // 1 hour
