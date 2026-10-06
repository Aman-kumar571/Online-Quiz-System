<?php
/**
 * Quiz attempt page.
 * Shows all questions for a quiz. On POST, scores server-side and redirects to result.
 * Uses POST-Redirect-GET to prevent double submission on refresh.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

initSession();

$db = getDB();

// Validate quiz ID
$quizId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($quizId <= 0) {
    require __DIR__ . '/404.php';
    exit;
}

// Fetch quiz
$stmt = $db->prepare(
    'SELECT q.*, c.category_name FROM quizzes q
     JOIN categories c ON q.category_id = c.category_id
     WHERE q.quiz_id = :id AND q.status = "active"'
);
$stmt->execute([':id' => $quizId]);
$quiz = $stmt->fetch();

if (!$quiz) {
    require __DIR__ . '/404.php';
    exit;
}

// Fetch questions (without exposing correct_option to client)
$stmt = $db->prepare('SELECT question_id, question_text, option_a, option_b, option_c, option_d FROM questions WHERE quiz_id = :id ORDER BY question_id');
$stmt->execute([':id' => $quizId]);
$questions = $stmt->fetchAll();

if (empty($questions)) {
    $pageTitle = $quiz['title'];
    require __DIR__ . '/includes/header.php';
    echo '<div class="container"><div class="page-header"><h1>' . e($quiz['title']) . '</h1></div><div class="alert alert-warning">This quiz has no questions yet. Please check back later.</div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle       = $quiz['title'];
$pageDescription = truncate($quiz['description'], 160);
$canonicalPath   = '/quiz.php?id=' . $quizId;

// Handle submission (before any output)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isLoggedIn()) {
        setFlash('error', 'You must be logged in to submit a quiz.');
        redirect('/login.php');
    }

    if (!validateCsrfToken()) {
        setFlash('error', 'Invalid form submission. Please try again.');
        redirect('/quiz.php?id=' . $quizId);
    }

    // Fetch correct answers from database (server-side scoring)
    $stmt = $db->prepare('SELECT question_id, correct_option FROM questions WHERE quiz_id = :id');
    $stmt->execute([':id' => $quizId]);
    $answers = $stmt->fetchAll();

    $score          = 0;
    $totalQuestions  = count($answers);

    foreach ($answers as $answer) {
        $qId       = $answer['question_id'];
        $submitted = $_POST['q_' . $qId] ?? '';
        $correct   = $answer['correct_option'];

        // Only accept valid options
        if (in_array($submitted, ['A', 'B', 'C', 'D'], true) && $submitted === $correct) {
            $score++;
        }
    }

    // Save result
    $stmt = $db->prepare(
        'INSERT INTO results (user_id, quiz_id, score, total_questions) VALUES (:uid, :qid, :score, :total)'
    );
    $stmt->execute([
        ':uid'   => currentUserId(),
        ':qid'   => $quizId,
        ':score' => $score,
        ':total' => $totalQuestions,
    ]);

    $resultId = $db->lastInsertId();

    // PRG: redirect to result page to prevent double submission
    redirect('/result.php?id=' . $resultId);
}

require __DIR__ . '/includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <div class="card-meta"><?= e($quiz['category_name']) ?></div>
        <h1><?= e($quiz['title']) ?></h1>
        <p><?= e($quiz['description']) ?></p>
        <p class="text-sm text-muted"><?= count($questions) ?> questions</p>
    </div>

    <?php if (!isLoggedIn()): ?>
        <div class="alert alert-info" role="alert">
            You need to <a href="<?= e(BASE_URL) ?>/login.php">log in</a> or
            <a href="<?= e(BASE_URL) ?>/register.php">register</a> before you can submit your answers.
        </div>
    <?php endif; ?>

    <form method="post" action="<?= e(BASE_URL) ?>/quiz.php?id=<?= (int)$quizId ?>" id="quiz-form">
        <?= csrfField() ?>

        <?php foreach ($questions as $index => $q): ?>
            <div class="quiz-question">
                <div class="question-number">Question <?= $index + 1 ?></div>
                <p class="question-text"><?= e($q['question_text']) ?></p>
                <div class="option-group">
                    <?php
                    $options = [
                        'A' => $q['option_a'],
                        'B' => $q['option_b'],
                        'C' => $q['option_c'],
                        'D' => $q['option_d'],
                    ];
                    foreach ($options as $letter => $text):
                    ?>
                        <label class="option-label" for="q<?= (int)$q['question_id'] ?>_<?= $letter ?>">
                            <input type="radio"
                                   id="q<?= (int)$q['question_id'] ?>_<?= $letter ?>"
                                   name="q_<?= (int)$q['question_id'] ?>"
                                   value="<?= $letter ?>">
                            <span class="option-letter"><?= $letter ?></span>
                            <?= e($text) ?>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if (isLoggedIn()): ?>
            <button type="submit" class="btn btn-primary btn-lg">Submit answers</button>
        <?php else: ?>
            <a href="<?= e(BASE_URL) ?>/login.php" class="btn btn-primary btn-lg">Log in to submit</a>
        <?php endif; ?>
    </form>
</div>

<div style="padding-bottom: var(--space-12);"></div>

<?php require __DIR__ . '/includes/footer.php'; ?>
