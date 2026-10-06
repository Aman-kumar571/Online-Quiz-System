<?php
/**
 * Admin: view all quiz results.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

requireAdmin();

$db = getDB();

// Optional quiz filter
$filterQuizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

if ($filterQuizId > 0) {
    $stmt = $db->prepare(
        'SELECT r.result_id, r.score, r.total_questions, r.attempt_date,
                u.name AS user_name, u.email AS user_email,
                q.title AS quiz_title
         FROM results r
         JOIN users u ON r.user_id = u.user_id
         JOIN quizzes q ON r.quiz_id = q.quiz_id
         WHERE r.quiz_id = :qid
         ORDER BY r.attempt_date DESC'
    );
    $stmt->execute([':qid' => $filterQuizId]);
} else {
    $stmt = $db->query(
        'SELECT r.result_id, r.score, r.total_questions, r.attempt_date,
                u.name AS user_name, u.email AS user_email,
                q.title AS quiz_title
         FROM results r
         JOIN users u ON r.user_id = u.user_id
         JOIN quizzes q ON r.quiz_id = q.quiz_id
         ORDER BY r.attempt_date DESC'
    );
}
$results = $stmt->fetchAll();

// Get quizzes for filter dropdown
$quizzes = $db->query('SELECT quiz_id, title FROM quizzes ORDER BY title')->fetchAll();

$pageTitle     = 'All Results';
$canonicalPath = '/admin/results.php' . ($filterQuizId > 0 ? '?quiz_id=' . $filterQuizId : '');

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Results</h1>
        <p>All quiz attempts by all users.</p>
    </div>

    <?php require __DIR__ . '/admin-nav.php'; ?>

    <!-- Filter -->
    <form method="get" action="<?= e(BASE_URL) ?>/admin/results.php" style="margin-bottom: var(--space-6); display: flex; gap: var(--space-3); align-items: flex-end; flex-wrap: wrap;">
        <div class="form-group" style="margin-bottom:0;">
            <label for="quiz_filter" class="text-sm">Filter by quiz</label>
            <select id="quiz_filter" name="quiz_id" class="form-control" style="min-width:240px;">
                <option value="0">All quizzes</option>
                <?php foreach ($quizzes as $q): ?>
                    <option value="<?= (int)$q['quiz_id'] ?>"
                        <?= $filterQuizId === (int)$q['quiz_id'] ? 'selected' : '' ?>>
                        <?= e($q['title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-outline">Filter</button>
        <?php if ($filterQuizId > 0): ?>
            <a href="<?= e(BASE_URL) ?>/admin/results.php" class="btn btn-outline">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (empty($results)): ?>
        <div class="empty-state">
            <p>No results found.</p>
        </div>
    <?php else: ?>
        <p class="text-sm text-muted" style="margin-bottom: var(--space-4);"><?= count($results) ?> results found.</p>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Quiz</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $r):
                        $pct = $r['total_questions'] > 0
                            ? round(($r['score'] / $r['total_questions']) * 100)
                            : 0;
                    ?>
                        <tr>
                            <td><?= e($r['user_name']) ?></td>
                            <td class="text-muted"><?= e($r['user_email']) ?></td>
                            <td><?= e($r['quiz_title']) ?></td>
                            <td><?= (int)$r['score'] ?>/<?= (int)$r['total_questions'] ?></td>
                            <td><?= $pct ?>%</td>
                            <td><?= e(formatDate($r['attempt_date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div style="padding-bottom: var(--space-12);"></div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
