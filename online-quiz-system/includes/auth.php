<?php
/**
 * Authentication, session management, and rate limiting.
 */

require_once __DIR__ . '/config.php';

function initSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

    session_set_cookie_params([
        'lifetime' => SESSION_LIFETIME,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    session_start();
}

// Ensure session is initialized whenever auth is loaded
initSession();

function isLoggedIn(): bool
{
    return !empty($_SESSION['user_id']);
}

function isAdmin(): bool
{
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireAdmin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }

    if (!isAdmin()) {
        http_response_code(403);
        header('Location: ' . BASE_URL . '/403.php');
        exit;
    }
}

function loginUser(array $user): void
{
    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int)$user['user_id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];
}

function logoutUser(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function currentUserId(): ?int
{
    return $_SESSION['user_id'] ?? null;
}

function currentUserName(): string
{
    return $_SESSION['name'] ?? '';
}

function currentUserRole(): string
{
    return $_SESSION['role'] ?? '';
}

/**
 * Get client IP address safely
 */
function getClientIp(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Check if login attempts are currently rate limited
 * Allows at most 5 failed attempts per 15 minutes per IP or email
 */
function isLoginRateLimited(string $email, ?PDO $db = null): bool
{
    $maxAttempts = 5;
    $windowSeconds = 900; // 15 minutes
    $cutoff = date('Y-m-d H:i:s', time() - $windowSeconds);
    $ip = getClientIp();

    if ($db !== null) {
        try {
            $stmt = $db->prepare(
                'SELECT COUNT(*) FROM login_attempts
                 WHERE (ip_address = :ip OR email = :email)
                   AND attempt_time >= :cutoff'
            );
            $stmt->execute([
                ':ip'     => $ip,
                ':email'  => $email,
                ':cutoff' => $cutoff,
            ]);
            $count = (int)$stmt->fetchColumn();
            return $count >= $maxAttempts;
        } catch (Exception $e) {
            // If table does not exist yet, fallback to session tracking
        }
    }

    // Session fallback
    $sessionAttempts = $_SESSION['login_failed_attempts'] ?? [];
    $validAttempts = array_filter($sessionAttempts, function ($timestamp) use ($windowSeconds) {
        return (time() - $timestamp) < $windowSeconds;
    });
    return count($validAttempts) >= $maxAttempts;
}

/**
 * Record a failed login attempt
 */
function recordFailedLogin(string $email, ?PDO $db = null): void
{
    $ip = getClientIp();
    $now = date('Y-m-d H:i:s');

    if ($db !== null) {
        try {
            $stmt = $db->prepare(
                'INSERT INTO login_attempts (ip_address, email, attempt_time)
                 VALUES (:ip, :email, :time)'
            );
            $stmt->execute([':ip' => $ip, ':email' => $email, ':time' => $now]);
        } catch (Exception $e) {
            // Ignore if table not present
        }
    }

    if (!isset($_SESSION['login_failed_attempts'])) {
        $_SESSION['login_failed_attempts'] = [];
    }
    $_SESSION['login_failed_attempts'][] = time();
}

/**
 * Clear failed login attempts upon successful login
 */
function clearLoginAttempts(string $email, ?PDO $db = null): void
{
    $ip = getClientIp();

    if ($db !== null) {
        try {
            $stmt = $db->prepare(
                'DELETE FROM login_attempts WHERE ip_address = :ip OR email = :email'
            );
            $stmt->execute([':ip' => $ip, ':email' => $email]);
        } catch (Exception $e) {
            // Ignore
        }
    }

    unset($_SESSION['login_failed_attempts']);
}
