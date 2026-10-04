<?php
/**
 * Custom 404 error page.
 */

require_once __DIR__ . '/includes/helpers.php';

$pageTitle       = 'Page Not Found';
$pageDescription = 'The page you are looking for could not be found.';

http_response_code(404);
require __DIR__ . '/includes/header.php';
?>

<div class="container error-page">
    <div class="error-code">404</div>
    <h1>Page not found</h1>
    <p class="text-muted" style="margin-bottom: var(--space-8);">The page you are looking for does not exist or has been moved.</p>
    <div class="hero-actions">
        <a href="<?= e(BASE_URL) ?>/" class="btn btn-primary">Go to home page</a>
        <a href="<?= e(BASE_URL) ?>/quizzes.php" class="btn btn-outline">Browse quizzes</a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
