<?php
/**
 * Admin dashboard: shows overview stats from the database.
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$pageTitle       = 'Admin Dashboard';
$pageDescription = 'Admin overview for ' . SITE_NAME;
$canonicalPath   = '/admin/';

require __DIR__ . '/../includes/header.php';
requireAdmin();

$db = getDB();

$totalUsers      = (int)$db->query('SELECT COUNT(*) FROM users WHERE role = "user"')->fetchColumn();
$totalAdmins     = (int)$db->query('SELECT COUNT(*) FROM users WHERE role = "admin"')->fetchColumn();
$totalCategories = (int)$db->query('SELECT COUNT(*) FROM categories')->fetchColumn();
$totalQuizzes    = (int)$db->query('SELECT COUNT(*) FROM quizzes')->fetchColumn();
$activeQuizzes   = (int)$db->query('SELECT COUNT(*) FROM quizzes WHERE status = "active"')->fetchColumn();
$totalQuestions  = (int)$db->query('SELECT COUNT(*) FROM questions')->fetchColumn();
$totalAttempts   = (int)$db->query('SELECT COUNT(*) FROM results')->fetchColumn();

// Recent attempts
$recentAttempts = $db->query(
    'SELECT r.result_id, r.score, r.total_questions, r.attempt_date,
            u.name AS user_name, q.title AS quiz_title
     FROM results r
     JOIN users u ON r.user_id = u.user_id
     JOIN quizzes q ON r.quiz_id = q.quiz_id
     ORDER BY r.attempt_date DESC
     LIMIT 10'
)->fetchAll();
?>

<div class="container">
    <div class="page-header">
        <h1>Admin Dashboard</h1>
    </div>

    <?php require __DIR__ . '/admin-nav.php'; ?>

    <div class="stat-cards">
        <div class="card stat-card">
            <div class="stat-value"><?= $totalUsers ?></div>
            <div class="stat-label">Users</div>
        </div>
        <div class="card stat-card">
            <div class="stat-value"><?= $activeQuizzes ?>/<?= $totalQuizzes ?></div>
            <div class="stat-label">Active / Total quizzes</div>
        </div>
        <div class="card stat-card">
            <div class="stat-value"><?= $totalCategories ?></div>
            <div class="stat-label">Categories</div>
        </div>
        <div class="card stat-card">
            <div class="stat-value"><?= $totalQuestions ?></div>
            <div class="stat-label">Questions</div>
        </div>
        <div class="card stat-card">
            <div class="stat-value"><?= $totalAttempts ?></div>
            <div class="stat-label">Total attempts</div>
        </div>
    </div>

    <h2>Recent Attempts</h2>
    <?php if (empty($recentAttempts)): ?>
        <p class="text-muted">No attempts yet.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Quiz</th>
                        <th>Score</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentAttempts as $a):
                        $pct = $a['total_questions'] > 0
                            ? round(($a['score'] / $a['total_questions']) * 100)
                            : 0;
                    ?>
                        <tr>
                            <td><?= e($a['user_name']) ?></td>
                            <td><?= e($a['quiz_title']) ?></td>
                            <td><?= (int)$a['score'] ?>/<?= (int)$a['total_questions'] ?> (<?= $pct ?>%)</td>
                            <td><?= e(formatDate($a['attempt_date'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div style="padding-bottom: var(--space-12);"></div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
