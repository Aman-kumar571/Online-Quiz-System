<?php
/**
 * Shared admin navigation partial.
 * Included at the top of every admin page.
 */
?>
<nav class="admin-nav" aria-label="Admin navigation">
    <a href="<?= e(BASE_URL) ?>/admin/" class="<?= isActivePage('index.php') && strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false ? 'active' : '' ?>">Dashboard</a>
    <a href="<?= e(BASE_URL) ?>/admin/categories.php" class="<?= isActivePage('categories.php') || isActivePage('category-edit.php') ? 'active' : '' ?>">Categories</a>
    <a href="<?= e(BASE_URL) ?>/admin/quizzes.php" class="<?= isActivePage('quizzes.php') || isActivePage('quiz-edit.php') ? 'active' : '' ?>">Quizzes</a>
    <a href="<?= e(BASE_URL) ?>/admin/questions.php" class="<?= isActivePage('questions.php') || isActivePage('question-edit.php') ? 'active' : '' ?>">Questions</a>
    <a href="<?= e(BASE_URL) ?>/admin/results.php" class="<?= isActivePage('results.php') ? 'active' : '' ?>">Results</a>
</nav>
