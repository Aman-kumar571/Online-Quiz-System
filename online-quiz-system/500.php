<?php
/**
 * Custom 500 error page.
 */

require_once __DIR__ . '/includes/helpers.php';

$pageTitle       = 'Server Error';
$pageDescription = 'An unexpected server error occurred.';

http_response_code(500);
require __DIR__ . '/includes/header.php';
?>

<div class="container error-page">
    <div class="error-code">500</div>
    <h1>Server error</h1>
    <p class="text-muted" style="margin-bottom: var(--space-8);">An unexpected error occurred on the server. Please try again later.</p>
    <div class="hero-actions">
        <a href="<?= e(BASE_URL) ?>/" class="btn btn-primary">Go to home page</a>
        <a href="<?= e(BASE_URL) ?>/quizzes.php" class="btn btn-outline">Browse quizzes</a>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
