<?php
/**
 * Helper functions used across the application.
 */

// Escape output for safe HTML rendering
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

// Redirect and stop execution
function redirect(string $path): void
{
    header('Location: ' . BASE_URL . $path);
    exit;
}

// Set a flash message that persists for one page load
function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message,
    ];
}

// Retrieve and clear the flash message
function getFlash(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Format a datetime string for display
function formatDate(string $datetime): string
{
    $dt = new DateTime($datetime);
    return $dt->format('j M Y, g:i A');
}

// Truncate text to a maximum length
function truncate(string $text, int $maxLength = 120): string
{
    if (mb_strlen($text) <= $maxLength) {
        return $text;
    }
    return mb_substr($text, 0, $maxLength) . '...';
}

// Get current page URL path for active nav highlighting
function isActivePage(string $page): string
{
    $current = basename($_SERVER['SCRIPT_NAME']);
    return $current === $page ? 'active' : '';
}
