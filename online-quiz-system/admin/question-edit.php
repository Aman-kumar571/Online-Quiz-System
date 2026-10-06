<?php
/**
 * Admin: create or edit a question.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

requireAdmin();

$db = getDB();

$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$quizId = isset($_GET['quiz_id']) ? (int)$_GET['quiz_id'] : 0;
$isEdit = $editId > 0;
$question = null;
$errors   = [];

if ($isEdit) {
    $stmt = $db->prepare('SELECT * FROM questions WHERE question_id = :id');
    $stmt->execute([':id' => $editId]);
    $question = $stmt->fetch();
    if (!$question) {
        setFlash('error', 'Question not found.');
        redirect('/admin/questions.php' . ($quizId > 0 ? '?quiz_id=' . $quizId : ''));
    }
    if ($quizId <= 0) {
        $quizId = (int)$question['quiz_id'];
    }
}

if ($quizId <= 0) {
    setFlash('error', 'Please select a quiz first.');
    redirect('/admin/questions.php');
}

// Verify quiz exists
$stmt = $db->prepare('SELECT title FROM quizzes WHERE quiz_id = :id');
$stmt->execute([':id' => $quizId]);
$quiz = $stmt->fetch();
if (!$quiz) {
    setFlash('error', 'Quiz not found.');
    redirect('/admin/questions.php');
}

$pageTitle = ($isEdit ? 'Edit' : 'Add') . ' Question';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $questionText   = trim($_POST['question_text'] ?? '');
        $optionA        = trim($_POST['option_a'] ?? '');
        $optionB        = trim($_POST['option_b'] ?? '');
        $optionC        = trim($_POST['option_c'] ?? '');
        $optionD        = trim($_POST['option_d'] ?? '');
        $correctOption  = $_POST['correct_option'] ?? '';

        if ($questionText === '') {
            $errors[] = 'Question text is required.';
        }
        if ($optionA === '' || $optionB === '' || $optionC === '' || $optionD === '') {
            $errors[] = 'All four options are required.';
        }
        if (!in_array($correctOption, ['A', 'B', 'C', 'D'], true)) {
            $errors[] = 'Please select the correct answer.';
        }

        if (empty($errors)) {
            if ($isEdit) {
                $stmt = $db->prepare(
                    'UPDATE questions SET question_text = :qt, option_a = :a, option_b = :b,
                     option_c = :c, option_d = :d, correct_option = :co
                     WHERE question_id = :id'
                );
                $stmt->execute([
                    ':qt' => $questionText,
                    ':a'  => $optionA,
                    ':b'  => $optionB,
                    ':c'  => $optionC,
                    ':d'  => $optionD,
                    ':co' => $correctOption,
                    ':id' => $editId,
                ]);
                setFlash('success', 'Question updated.');
            } else {
                $stmt = $db->prepare(
                    'INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_option)
                     VALUES (:qid, :qt, :a, :b, :c, :d, :co)'
                );
                $stmt->execute([
                    ':qid' => $quizId,
                    ':qt'  => $questionText,
                    ':a'   => $optionA,
                    ':b'   => $optionB,
                    ':c'   => $optionC,
                    ':d'   => $optionD,
                    ':co'  => $correctOption,
                ]);
                setFlash('success', 'Question added.');
            }
            redirect('/admin/questions.php?quiz_id=' . $quizId);
        }
    }
}

// Current form values
$curText = $_POST['question_text'] ?? ($question['question_text'] ?? '');
$curA    = $_POST['option_a']      ?? ($question['option_a'] ?? '');
$curB    = $_POST['option_b']      ?? ($question['option_b'] ?? '');
$curC    = $_POST['option_c']      ?? ($question['option_c'] ?? '');
$curD    = $_POST['option_d']      ?? ($question['option_d'] ?? '');
$curCo   = $_POST['correct_option'] ?? ($question['correct_option'] ?? '');
$canonicalPath = '/admin/question-edit.php?' . ($isEdit ? 'id=' . $editId . '&' : '') . 'quiz_id=' . $quizId;

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <h1><?= e($pageTitle) ?></h1>
        <p class="text-muted text-sm">Quiz: <?= e($quiz['title']) ?></p>
    </div>

    <?php require __DIR__ . '/admin-nav.php'; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error" role="alert">
            <?php foreach ($errors as $err): ?>
                <p style="margin:0;"><?= e($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card" style="max-width: 720px;">
        <form method="post" action="<?= e(BASE_URL) ?>/admin/question-edit.php?<?= $isEdit ? 'id=' . (int)$editId . '&' : '' ?>quiz_id=<?= (int)$quizId ?>">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="question_text">Question text</label>
                <textarea id="question_text" name="question_text" class="form-control" rows="3"
                          required autofocus><?= e($curText) ?></textarea>
            </div>

            <div class="form-group">
                <label for="option_a">Option A</label>
                <input type="text" id="option_a" name="option_a" class="form-control"
                       value="<?= e($curA) ?>" required maxlength="500">
            </div>

            <div class="form-group">
                <label for="option_b">Option B</label>
                <input type="text" id="option_b" name="option_b" class="form-control"
                       value="<?= e($curB) ?>" required maxlength="500">
            </div>

            <div class="form-group">
                <label for="option_c">Option C</label>
                <input type="text" id="option_c" name="option_c" class="form-control"
                       value="<?= e($curC) ?>" required maxlength="500">
            </div>

            <div class="form-group">
                <label for="option_d">Option D</label>
                <input type="text" id="option_d" name="option_d" class="form-control"
                       value="<?= e($curD) ?>" required maxlength="500">
            </div>

            <div class="form-group">
                <label for="correct_option">Correct answer</label>
                <select id="correct_option" name="correct_option" class="form-control" required>
                    <option value="">Select correct option</option>
                    <option value="A" <?= $curCo === 'A' ? 'selected' : '' ?>>A</option>
                    <option value="B" <?= $curCo === 'B' ? 'selected' : '' ?>>B</option>
                    <option value="C" <?= $curCo === 'C' ? 'selected' : '' ?>>C</option>
                    <option value="D" <?= $curCo === 'D' ? 'selected' : '' ?>>D</option>
                </select>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Add' ?> question</button>
                <a href="<?= e(BASE_URL) ?>/admin/questions.php?quiz_id=<?= (int)$quizId ?>" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<div style="padding-bottom: var(--space-12);"></div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
