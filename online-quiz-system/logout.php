<?php
/**
 * Logout: destroys session and redirects to home.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';

initSession();
logoutUser();

header('Location: ' . BASE_URL . '/');
exit;
