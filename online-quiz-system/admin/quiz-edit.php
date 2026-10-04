<?php
/**
 * Admin: create or edit a quiz.
 */

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';

$canonicalPath = '/admin/quiz-edit.php';

require __DIR__ . '/../includes/header.php';
requireAdmin();

$db = getDB();

$editId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit = $editId > 0;
$quiz   = null;
$errors = [];

if ($isEdit) {
    $stmt = $db->prepare('SELECT * FROM quizzes WHERE quiz_id = :id');
    $stmt->execute([':id' => $editId]);
    $quiz = $stmt->fetch();
    if (!$quiz) {
        setFlash('error', 'Quiz not found.');
        redirect('/admin/quizzes.php');
    }
}

$pageTitle = $isEdit ? 'Edit Quiz' : 'Add Quiz';

// Fetch categories for the dropdown
$categories = $db->query('SELECT category_id, category_name FROM categories ORDER BY category_name')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $title       = trim($_POST['title'] ?? '');
        $categoryId  = (int)($_POST['category_id'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $status      = $_POST['status'] ?? 'active';

        if ($title === '') {
            $errors[] = 'Title is required.';
        } elseif (mb_strlen($title) > 255) {
            $errors[] = 'Title must be 255 characters or fewer.';
        }

        if ($categoryId <= 0) {
            $errors[] = 'Please select a category.';
        }

        if (!in_array($status, ['active', 'inactive'], true)) {
            $errors[] = 'Invalid status.';
        }

        if (empty($errors)) {
            if ($isEdit) {
                $stmt = $db->prepare(
                    'UPDATE quizzes SET title = :title, category_id = :cat, description = :desc, status = :status
                     WHERE quiz_id = :id'
                );
                $stmt->execute([
                    ':title'  => $title,
                    ':cat'    => $categoryId,
                    ':desc'   => $description,
                    ':status' => $status,
                    ':id'     => $editId,
                ]);
                setFlash('success', 'Quiz updated.');
            } else {
                $stmt = $db->prepare(
                    'INSERT INTO quizzes (title, category_id, description, status)
                     VALUES (:title, :cat, :desc, :status)'
                );
                $stmt->execute([
                    ':title'  => $title,
                    ':cat'    => $categoryId,
                    ':desc'   => $description,
                    ':status' => $status,
                ]);
                setFlash('success', 'Quiz created.');
            }
            redirect('/admin/quizzes.php');
        }
    }
}

// Current form values (POST or existing record)
$currentTitle  = $_POST['title']       ?? ($quiz['title'] ?? '');
$currentCat    = (int)($_POST['category_id'] ?? ($quiz['category_id'] ?? 0));
$currentDesc   = $_POST['description'] ?? ($quiz['description'] ?? '');
$currentStatus = $_POST['status']      ?? ($quiz['status'] ?? 'active');
?>

<div class="container">
    <div class="page-header">
        <h1><?= e($pageTitle) ?></h1>
    </div>

    <?php require __DIR__ . '/admin-nav.php'; ?>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error" role="alert">
            <?php foreach ($errors as $err): ?>
                <p style="margin:0;"><?= e($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card" style="max-width: 640px;">
        <form method="post">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="title">Quiz title</label>
                <input type="text" id="title" name="title" class="form-control"
                       value="<?= e($currentTitle) ?>" required autofocus maxlength="255">
            </div>

            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= (int)$cat['category_id'] ?>"
                            <?= $currentCat === (int)$cat['category_id'] ? 'selected' : '' ?>>
                            <?= e($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" rows="4"><?= e($currentDesc) ?></textarea>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $currentStatus === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Create' ?> quiz</button>
                <a href="<?= e(BASE_URL) ?>/admin/quizzes.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<div style="padding-bottom: var(--space-12);"></div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
