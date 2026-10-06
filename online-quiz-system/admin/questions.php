<?php
/**
 * Admin: list and delete questions for a specific quiz.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

requireAdmin();

$db = getDB();

$quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (!validateCsrfToken()) {
        setFlash('error', 'Invalid form submission.');
    } else {
        $deleteId = (int)$_POST['delete_id'];
        $stmt = $db->prepare('DELETE FROM questions WHERE question_id = :id');
        $stmt->execute([':id' => $deleteId]);
        setFlash('success', 'Question deleted.');
    }
    redirect('/admin/questions.php?quiz_id=' . $quizId);
}

// If no quiz selected, show quiz picker
if ($quizId <= 0) {
    $quizzes = $db->query(
        'SELECT q.quiz_id, q.title, c.category_name,
                (SELECT COUNT(*) FROM questions WHERE quiz_id = q.quiz_id) AS question_count
         FROM quizzes q
         JOIN categories c ON q.category_id = c.category_id
         ORDER BY q.title'
    )->fetchAll();

    $pageTitle     = 'Manage Questions';
    $canonicalPath = '/admin/questions.php';

    require __DIR__ . '/../includes/header.php';
    ?>

    <div class="container">
        <div class="page-header">
            <h1>Questions</h1>
            <p>Select a quiz to manage its questions.</p>
        </div>

        <?php require __DIR__ . '/admin-nav.php'; ?>

        <?php if (empty($quizzes)): ?>
            <div class="empty-state">
                <p>No quizzes exist yet. Create a quiz first.</p>
                <a href="<?= e(BASE_URL) ?>/admin/quiz-edit.php" class="btn btn-primary">Add quiz</a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Quiz</th>
                            <th>Category</th>
                            <th>Questions</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($quizzes as $q): ?>
                            <tr>
                                <td><strong><?= e($q['title']) ?></strong></td>
                                <td><?= e($q['category_name']) ?></td>
                                <td><?= (int)$q['question_count'] ?></td>
                                <td>
                                    <a href="<?= e(BASE_URL) ?>/admin/questions.php?quiz_id=<?= (int)$q['quiz_id'] ?>"
                                       class="btn btn-sm btn-outline">Manage</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div style="padding-bottom: var(--space-12);"></div>

    <?php
    require __DIR__ . '/../includes/footer.php';
    exit;
}

// Fetch quiz info
$stmt = $db->prepare('SELECT q.*, c.category_name FROM quizzes q JOIN categories c ON q.category_id = c.category_id WHERE q.quiz_id = :id');
$stmt->execute([':id' => $quizId]);
$quiz = $stmt->fetch();

if (!$quiz) {
    setFlash('error', 'Quiz not found.');
    redirect('/admin/questions.php');
}

// Fetch questions
$stmt = $db->prepare('SELECT * FROM questions WHERE quiz_id = :id ORDER BY question_id');
$stmt->execute([':id' => $quizId]);
$questions = $stmt->fetchAll();

$pageTitle     = 'Questions: ' . $quiz['title'];
$canonicalPath = '/admin/questions.php?quiz_id=' . $quizId;

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <div class="admin-header">
            <div>
                <h1>Questions: <?= e($quiz['title']) ?></h1>
                <p class="text-muted text-sm"><?= e($quiz['category_name']) ?> / <?= count($questions) ?> questions</p>
            </div>
            <a href="<?= e(BASE_URL) ?>/admin/question-edit.php?quiz_id=<?= (int)$quizId ?>" class="btn btn-primary">Add question</a>
        </div>
    </div>

    <?php require __DIR__ . '/admin-nav.php'; ?>

    <?php if (empty($questions)): ?>
        <div class="empty-state">
            <p>No questions in this quiz yet.</p>
            <a href="<?= e(BASE_URL) ?>/admin/question-edit.php?quiz_id=<?= (int)$quizId ?>" class="btn btn-primary">Add first question</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Question</th>
                        <th>Correct</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($questions as $i => $q): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e(truncate($q['question_text'], 80)) ?></td>
                            <td><span class="badge badge-active"><?= e($q['correct_option']) ?></span></td>
                            <td>
                                <div class="actions">
                                    <a href="<?= e(BASE_URL) ?>/admin/question-edit.php?id=<?= (int)$q['question_id'] ?>&quiz_id=<?= (int)$quizId ?>"
                                       class="btn btn-sm btn-outline">Edit</a>
                                    <form method="post" style="display:inline;"
                                          onsubmit="return confirm('Delete this question?');">
                                        <?= csrfField() ?>
                                        <input type="hidden" name="delete_id" value="<?= (int)$q['question_id'] ?>">
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

    <div style="margin-top: var(--space-4);">
        <a href="<?= e(BASE_URL) ?>/admin/questions.php" class="btn btn-outline">Back to quiz list</a>
    </div>
</div>

<div style="padding-bottom: var(--space-12);"></div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
