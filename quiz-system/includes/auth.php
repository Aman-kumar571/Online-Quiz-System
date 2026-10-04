<?php
// Session initialization and authentication helpers.

require_once __DIR__ . '/config.php';

// Start session with hardened settings
function init_session(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,
        'httponly'  => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// Check if the current visitor is logged in
function is_logged_in(): bool {
    return isset($_SESSION['user_id']);
}

// Check if the current user has the admin role
function is_admin(): bool {
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

// Get the current user's ID, or null
function current_user_id(): ?int {
    return $_SESSION['user_id'] ?? null;
}

// Get the current user's name
function current_user_name(): ?string {
    return $_SESSION['user_name'] ?? null;
}

// Require authentication. Redirects to login if not logged in.
function require_login(): void {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in to continue.';
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

// Require admin role. Sends 403 if not admin.
function require_admin(): void {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Please log in to continue.';
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
    if (!is_admin()) {
        http_response_code(403);
        include __DIR__ . '/../403.php';
        exit;
    }
}

// Log the user in: store session data and regenerate ID
function login_user(array $user): void {
    session_regenerate_id(true);
    $_SESSION['user_id']   = (int) $user['user_id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['role']      = $user['role'];
}

// Log the user out: destroy session completely
function logout_user(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}

// Rate limiting: count recent failed login attempts for an email and IP
function count_login_attempts(PDO $pdo, string $email, string $ip): int {
    $stmt = $pdo->prepare(
        'SELECT COUNT(*) FROM login_attempts
         WHERE email = :email AND ip_address = :ip
         AND attempted_at > DATE_SUB(NOW(), INTERVAL :minutes MINUTE)'
    );
    $stmt->execute([
        ':email'   => $email,
        ':ip'      => $ip,
        ':minutes' => LOGIN_LOCKOUT_MINUTES,
    ]);
    return (int) $stmt->fetchColumn();
}

// Record a failed login attempt
function record_login_attempt(PDO $pdo, string $email, string $ip): void {
    $stmt = $pdo->prepare(
        'INSERT INTO login_attempts (email, ip_address) VALUES (:email, :ip)'
    );
    $stmt->execute([':email' => $email, ':ip' => $ip]);
}

// Clean up old login attempt records (optional maintenance)
function purge_old_login_attempts(PDO $pdo): void {
    $pdo->exec(
        'DELETE FROM login_attempts WHERE attempted_at < DATE_SUB(NOW(), INTERVAL 1 HOUR)'
    );
}
