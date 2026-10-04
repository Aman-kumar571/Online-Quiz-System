<?php
/**
 * User's past quiz attempts.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle       = 'My Attempts';
$pageDescription = 'View your quiz attempt history and scores.';
$canonicalPath   = '/my-attempts.php';

require __DIR__ . '/includes/header.php';
requireLogin();

$db = getDB();

$stmt = $db->prepare(
    'SELECT r.result_id, r.score, r.total_questions, r.attempt_date,
            q.title AS quiz_title, c.category_name
     FROM results r
     JOIN quizzes q ON r.quiz_id = q.quiz_id
     JOIN categories c ON q.category_id = c.category_id
     WHERE r.user_id = :uid
     ORDER BY r.attempt_date DESC'
);
$stmt->execute([':uid' => currentUserId()]);
$attempts = $stmt->fetchAll();
?>

<div class="container">
    <div class="page-header">
        <h1>My Attempts</h1>
        <p>Your complete quiz history and scores.</p>
    </div>

    <?php if (empty($attempts)): ?>
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
            <p>You have not taken any quizzes yet.</p>
            <a href="<?= e(BASE_URL) ?>/quizzes.php" class="btn btn-primary">Browse quizzes</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Quiz</th>
                        <th>Category</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attempts as $a):
                        $pct = $a['total_questions'] > 0
                            ? round(($a['score'] / $a['total_questions']) * 100)
                            : 0;
                    ?>
                        <tr>
                            <td><strong><?= e($a['quiz_title']) ?></strong></td>
                            <td><?= e($a['category_name']) ?></td>
                            <td><?= (int)$a['score'] ?>/<?= (int)$a['total_questions'] ?></td>
                            <td><?= $pct ?>%</td>
                            <td><?= e(formatDate($a['attempt_date'])) ?></td>
                            <td>
                                <a href="<?= e(BASE_URL) ?>/result.php?id=<?= (int)$a['result_id'] ?>"
                                   class="btn btn-sm btn-outline">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div style="padding-bottom: var(--space-12);"></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
