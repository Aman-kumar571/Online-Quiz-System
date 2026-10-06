<?php
/**
 * Custom 403 error page.
 */

require_once __DIR__ . '/includes/helpers.php';

$pageTitle       = 'Access Forbidden';
$pageDescription = 'You do not have permission to access this page.';

http_response_code(403);
require __DIR__ . '/includes/header.php';
?>

<div class="container error-page">
    <div class="error-code">403</div>
    <h1>Access forbidden</h1>
    <p class="text-muted" style="margin-bottom: var(--space-8);">You do not have permission to access this resource.</p>
    <div class="hero-actions">
        <a href="<?= e(BASE_URL) ?>/" class="btn btn-primary">Go to home page</a>
        <a href="<?= e(BASE_URL) ?>/login.php" class="btn btn-outline">Log in</a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
