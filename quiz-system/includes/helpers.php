<?php
// Shared helper functions: CSRF protection, output escaping, flash messages, redirects.

// Escape a string for safe HTML output
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// Generate a CSRF token and store it in the session
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Output a hidden CSRF input field
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

// Validate the submitted CSRF token against the session
function verify_csrf(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

// Generate a one-time attempt token for a specific quiz
function generate_attempt_token(int $quiz_id): string {
    $token = bin2hex(random_bytes(32));
    $_SESSION['attempt_token_' . $quiz_id] = $token;
    return $token;
}

// Validate and consume the one-time attempt token
function consume_attempt_token(int $quiz_id, string $submitted_token): bool {
    $key = 'attempt_token_' . $quiz_id;
    $stored = $_SESSION[$key] ?? '';
    if ($stored === '' || !hash_equals($stored, $submitted_token)) {
        return false;
    }
    unset($_SESSION[$key]);
    return true;
}

// Set a flash message (shown once on the next page load)
function set_flash(string $type, string $message): void {
    $_SESSION['flash_' . $type] = $message;
}

// Retrieve and clear a flash message
function get_flash(string $type): ?string {
    $key = 'flash_' . $type;
    $msg = $_SESSION[$key] ?? null;
    unset($_SESSION[$key]);
    return $msg;
}

// Render flash messages as HTML
function render_flashes(): string {
    $html = '';
    foreach (['success', 'error', 'info'] as $type) {
        $msg = get_flash($type);
        if ($msg) {
            $html .= '<div class="alert alert-' . $type . '" role="alert">'
                   . e($msg)
                   . '</div>';
        }
    }
    return $html;
}

// Redirect to a URL and exit
function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

// Get the client IP address (supports common proxy headers)
function get_client_ip(): string {
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $parts = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($parts[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// Validate that a string is a non-empty trimmed value
function is_filled(string $value): bool {
    return trim($value) !== '';
}

// Validate an email address
function is_valid_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
