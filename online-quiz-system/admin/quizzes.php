<?php
/**
 * Admin: list and delete quizzes.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

requireAdmin();

$db = getDB();

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!validateCsrfToken()) {
        setFlash('error', 'Invalid form submission.');
    } else {
        $deleteId = (int)$_POST['delete_id'];
        // CASCADE will remove questions and results
        $stmt = $db->prepare('DELETE FROM quizzes WHERE quiz_id = :id');
        $stmt->execute([':id' => $deleteId]);
        setFlash('success', 'Quiz deleted (including its questions and results).');
    }
    redirect('/admin/quizzes.php');
}

// Fetch all quizzes
$quizzes = $db->query(
    'SELECT q.quiz_id, q.title, q.status, q.created_at, c.category_name,
            (SELECT COUNT(*) FROM questions WHERE quiz_id = q.quiz_id) AS question_count,
            (SELECT COUNT(*) FROM results WHERE quiz_id = q.quiz_id) AS attempt_count
     FROM quizzes q
     JOIN categories c ON q.category_id = c.category_id
     ORDER BY q.created_at DESC'
)->fetchAll();

$pageTitle     = 'Manage Quizzes';
$canonicalPath = '/admin/quizzes.php';

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <div class="admin-header">
            <h1>Quizzes</h1>
            <a href="<?= e(BASE_URL) ?>/admin/quiz-edit.php" class="btn btn-primary">Add quiz</a>
        </div>
    </div>

    <?php require __DIR__ . '/admin-nav.php'; ?>

    <?php if (empty($quizzes)): ?>
        <div class="empty-state">
            <p>No quizzes yet.</p>
            <a href="<?= e(BASE_URL) ?>/admin/quiz-edit.php" class="btn btn-primary">Add your first quiz</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Questions</th>
                        <th>Attempts</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($quizzes as $q): ?>
                        <tr>
                            <td><?= (int)$q['quiz_id'] ?></td>
                            <td><strong><?= e($q['title']) ?></strong></td>
                            <td><?= e($q['category_name']) ?></td>
                            <td>
                                <span class="badge badge-<?= $q['status'] === 'active' ? 'active' : 'inactive' ?>">
                                    <?= e($q['status']) ?>
                                </span>
                            </td>
                            <td><?= (int)$q['question_count'] ?></td>
                            <td><?= (int)$q['attempt_count'] ?></td>
                            <td><?= e(formatDate($q['created_at'])) ?></td>
                            <td>
                                <div class="actions">
                                    <a href="<?= e(BASE_URL) ?>/admin/quiz-edit.php?id=<?= (int)$q['quiz_id'] ?>"
                                       class="btn btn-sm btn-outline">Edit</a>
                                    <a href="<?= e(BASE_URL) ?>/admin/questions.php?quiz_id=<?= (int)$q['quiz_id'] ?>"
                                       class="btn btn-sm btn-outline">Questions</a>
                                    <form method="post" style="display:inline;"
                                          onsubmit="return confirm('Delete this quiz and all its questions and results?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="delete_id" value="<?= (int)$q['quiz_id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div style="padding-bottom: var(--space-12);"></div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
