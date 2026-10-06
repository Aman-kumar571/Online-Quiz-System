<?php
/**
 * Result page: shows score immediately after quiz submission.
 * Only the result owner can view their result.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

requireLogin();

$resultId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($resultId <= 0) {
    redirect('/my-attempts.php');
}

$db   = getDB();
$stmt = $db->prepare(
    'SELECT r.*, q.title AS quiz_title, c.category_name
     FROM results r
     JOIN quizzes q ON r.quiz_id = q.quiz_id
     JOIN categories c ON q.category_id = c.category_id
     WHERE r.result_id = :id AND r.user_id = :uid'
);
$stmt->execute([':id' => $resultId, ':uid' => currentUserId()]);
$result = $stmt->fetch();

if (!$result) {
    setFlash('error', 'Result not found.');
    redirect('/my-attempts.php');
}

$percentage = $result['total_questions'] > 0
    ? round(($result['score'] / $result['total_questions']) * 100)
    : 0;

$pageTitle       = 'Result: ' . $result['quiz_title'];
$pageDescription = 'You scored ' . $result['score'] . ' out of ' . $result['total_questions'] . '.';
$canonicalPath   = '/result.php?id=' . $resultId;

require __DIR__ . '/includes/header.php';
?>

<div class="container page-section">
    <div class="result-card card">
        <div class="card-meta"><?= e($result['category_name']) ?></div>
        <h1 style="margin-bottom: var(--space-6);"><?= e($result['quiz_title']) ?></h1>

        <div class="result-score"><?= (int)$result['score'] ?>/<?= (int)$result['total_questions'] ?></div>
        <p class="result-detail">
            You answered <?= (int)$result['score'] ?> out of <?= (int)$result['total_questions'] ?> questions correctly.
        </p>
        <p class="result-percentage"><?= $percentage ?>%</p>
        <p class="text-sm text-muted" style="margin-bottom: var(--space-6);">
            Attempted on <?= e(formatDate($result['attempt_date'])) ?>
        </p>

        <div class="result-actions">
            <a href="<?= e(BASE_URL) ?>/quiz.php?id=<?= (int)$result['quiz_id'] ?>" class="btn btn-primary">Try again</a>
            <a href="<?= e(BASE_URL) ?>/my-attempts.php" class="btn btn-outline">View all attempts</a>
            <a href="<?= e(BASE_URL) ?>/quizzes.php" class="btn btn-outline">Browse quizzes</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
