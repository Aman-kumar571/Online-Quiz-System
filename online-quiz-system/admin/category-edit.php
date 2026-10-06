<?php
/**
 * Admin: create or edit a category.
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../includes/csrf.php';

requireAdmin();

$db = getDB();

$editId   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$isEdit   = $editId > 0;
$category = null;
$errors   = [];

if ($isEdit) {
    $stmt = $db->prepare('SELECT * FROM categories WHERE category_id = :id');
    $stmt->execute([':id' => $editId]);
    $category = $stmt->fetch();
    if (!$category) {
        setFlash('error', 'Category not found.');
        redirect('/admin/categories.php');
    }
}

$pageTitle = $isEdit ? 'Edit Category' : 'Add Category';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = 'Invalid form submission.';
    } else {
        $name = trim($_POST['category_name'] ?? '');

        if ($name === '') {
            $errors[] = 'Category name is required.';
        } elseif (mb_strlen($name) > 100) {
            $errors[] = 'Category name must be 100 characters or fewer.';
        }

        // Check for duplicate name
        if (empty($errors)) {
            $stmt = $db->prepare(
                'SELECT category_id FROM categories WHERE category_name = :name AND category_id != :id'
            );
            $stmt->execute([':name' => $name, ':id' => $editId]);
            if ($stmt->fetch()) {
                $errors[] = 'A category with this name already exists.';
            }
        }

        if (empty($errors)) {
            if ($isEdit) {
                $stmt = $db->prepare('UPDATE categories SET category_name = :name WHERE category_id = :id');
                $stmt->execute([':name' => $name, ':id' => $editId]);
                setFlash('success', 'Category updated.');
            } else {
                $stmt = $db->prepare('INSERT INTO categories (category_name) VALUES (:name)');
                $stmt->execute([':name' => $name]);
                setFlash('success', 'Category created.');
            }
            redirect('/admin/categories.php');
        }
    }
}

$currentName   = $_POST['category_name'] ?? ($category['category_name'] ?? '');
$canonicalPath = '/admin/category-edit.php' . ($isEdit ? '?id=' . $editId : '');

require __DIR__ . '/../includes/header.php';
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

    <div class="card" style="max-width: 560px;">
        <form method="post" action="<?= e(BASE_URL) ?>/admin/category-edit.php<?= $isEdit ? '?id=' . (int)$editId : '' ?>">
            <?= csrfField() ?>

            <div class="form-group">
                <label for="category_name">Category name</label>
                <input type="text" id="category_name" name="category_name" class="form-control"
                       value="<?= e($currentName) ?>" required autofocus maxlength="100">
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Create' ?> category</button>
                <a href="<?= e(BASE_URL) ?>/admin/categories.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<div style="padding-bottom: var(--space-12);"></div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
