<?php
/**
 * Browse quizzes by category.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle       = 'Quizzes';
$pageDescription = 'Browse all available quizzes by category on ' . SITE_NAME . '.';
$canonicalPath   = '/quizzes.php';

$db = getDB();

// Get selected category filter
$categoryFilter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Fetch all categories that have active quizzes
$categories = $db->query(
    'SELECT c.category_id, c.category_name,
            (SELECT COUNT(*) FROM quizzes WHERE category_id = c.category_id AND status = "active") AS quiz_count
     FROM categories c
     HAVING quiz_count > 0
     ORDER BY c.category_name'
)->fetchAll();

// Fetch quizzes (optionally filtered)
if ($categoryFilter > 0) {
    $stmt = $db->prepare(
        'SELECT q.quiz_id, q.title, q.description, c.category_name,
                (SELECT COUNT(*) FROM questions WHERE quiz_id = q.quiz_id) AS question_count
         FROM quizzes q
         JOIN categories c ON q.category_id = c.category_id
         WHERE q.status = "active" AND q.category_id = :cat
         ORDER BY q.title'
    );
    $stmt->execute([':cat' => $categoryFilter]);
} else {
    $stmt = $db->query(
        'SELECT q.quiz_id, q.title, q.description, c.category_name,
                (SELECT COUNT(*) FROM questions WHERE quiz_id = q.quiz_id) AS question_count
         FROM quizzes q
         JOIN categories c ON q.category_id = c.category_id
         WHERE q.status = "active"
         ORDER BY q.title'
    );
}
$quizzes = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Quizzes</h1>
        <p>Choose a quiz to test your knowledge. Filter by category or browse all.</p>
    </div>

    <?php if (!empty($categories)): ?>
        <div class="admin-nav" role="navigation" aria-label="Category filter">
            <a href="<?= e(BASE_URL) ?>/quizzes.php"
               class="<?= $categoryFilter === 0 ? 'active' : '' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
                <a href="<?= e(BASE_URL) ?>/quizzes.php?category=<?= (int)$cat['category_id'] ?>"
                   class="<?= $categoryFilter === (int)$cat['category_id'] ? 'active' : '' ?>">
                    <?= e($cat['category_name']) ?> (<?= (int)$cat['quiz_count'] ?>)
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($quizzes)): ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
            <p>No quizzes found in this category.</p>
            <a href="<?= e(BASE_URL) ?>/quizzes.php" class="btn btn-outline">View all quizzes</a>
        </div>
    <?php else: ?>
        <div class="quiz-grid">
            <?php foreach ($quizzes as $quiz): ?>
                <a href="<?= e(BASE_URL) ?>/quiz.php?id=<?= (int)$quiz['quiz_id'] ?>" class="quiz-card-link">
                    <div class="card">
                        <div class="card-meta"><?= e($quiz['category_name']) ?></div>
                        <h3 class="card-title"><?= e($quiz['title']) ?></h3>
                        <p class="card-desc"><?= e(truncate($quiz['description'])) ?></p>
                        <div class="quiz-card-footer">
                            <span class="text-sm text-muted"><?= (int)$quiz['question_count'] ?> questions</span>
                            <span class="btn btn-sm btn-outline">Start quiz</span>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div style="padding-bottom: var(--space-12);"></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
