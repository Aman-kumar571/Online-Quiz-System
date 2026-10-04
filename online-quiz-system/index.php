<?php
/**
 * Home page: shows headline, available quizzes with real counts.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle       = 'Home';
$pageDescription = 'Practice multiple-choice quizzes on HTML, SQL, C programming, and more. Free, no sign-up required to browse.';
$canonicalPath   = '/';

$db = getDB();

// Fetch active quizzes with category name and question count
$stmt = $db->query(
    'SELECT q.quiz_id, q.title, q.description, c.category_name,
            (SELECT COUNT(*) FROM questions WHERE quiz_id = q.quiz_id) AS question_count
     FROM quizzes q
     JOIN categories c ON q.category_id = c.category_id
     WHERE q.status = "active"
     ORDER BY q.created_at DESC'
);
$quizzes = $stmt->fetchAll();

// Real stats from database
$totalQuizzes   = $db->query('SELECT COUNT(*) FROM quizzes WHERE status = "active"')->fetchColumn();
$totalQuestions = $db->query('SELECT COUNT(*) FROM questions q JOIN quizzes qz ON q.quiz_id = qz.quiz_id WHERE qz.status = "active"')->fetchColumn();
$totalUsers     = $db->query('SELECT COUNT(*) FROM users WHERE role = "user"')->fetchColumn();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <h1>Multiple-choice quizzes for computer science students</h1>
        <p><?= e(SITE_TAGLINE) ?>. Pick a topic, answer the questions, and see your score right away.</p>
        <div class="hero-actions">
            <a href="<?= e(BASE_URL) ?>/quizzes.php" class="btn btn-primary btn-lg">Browse quizzes</a>
            <?php if (!isLoggedIn()): ?>
                <a href="<?= e(BASE_URL) ?>/register.php" class="btn btn-outline btn-lg">Create an account</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php if ((int)$totalQuizzes > 0 || (int)$totalUsers > 0): ?>
<section class="page-section">
    <div class="container">
        <div class="stat-cards">
            <div class="card stat-card">
                <div class="stat-value"><?= (int)$totalQuizzes ?></div>
                <div class="stat-label">Active quizzes</div>
            </div>
            <div class="card stat-card">
                <div class="stat-value"><?= (int)$totalQuestions ?></div>
                <div class="stat-label">Questions</div>
            </div>
            <div class="card stat-card">
                <div class="stat-value"><?= (int)$totalUsers ?></div>
                <div class="stat-label">Registered users</div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="page-section">
    <div class="container">
        <h2>Available quizzes</h2>
        <?php if (empty($quizzes)): ?>
            <div class="empty-state">
                <p>No quizzes are available yet. Check back soon.</p>
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
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
