<?php
/**
 * Admin: list and delete categories.
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

        // Check if category has quizzes
        $stmt = $db->prepare('SELECT COUNT(*) FROM quizzes WHERE category_id = :id');
        $stmt->execute([':id' => $deleteId]);
        $quizCount = (int)$stmt->fetchColumn();

        if ($quizCount > 0) {
            setFlash('error', 'Cannot delete a category that has quizzes. Remove the quizzes first.');
        } else {
            $stmt = $db->prepare('DELETE FROM categories WHERE category_id = :id');
            $stmt->execute([':id' => $deleteId]);
            setFlash('success', 'Category deleted.');
        }
    }
    redirect('/admin/categories.php');
}

// Fetch all categories with quiz count
$categories = $db->query(
    'SELECT c.category_id, c.category_name,
            (SELECT COUNT(*) FROM quizzes WHERE category_id = c.category_id) AS quiz_count
     FROM categories c
     ORDER BY c.category_name'
)->fetchAll();

$pageTitle     = 'Manage Categories';
$canonicalPath = '/admin/categories.php';

require __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <div class="page-header">
        <div class="admin-header">
            <h1>Categories</h1>
            <a href="<?= e(BASE_URL) ?>/admin/category-edit.php" class="btn btn-primary">Add category</a>
        </div>
    </div>

    <?php require __DIR__ . '/admin-nav.php'; ?>

    <?php if (empty($categories)): ?>
        <div class="empty-state">
            <p>No categories yet.</p>
            <a href="<?= e(BASE_URL) ?>/admin/category-edit.php" class="btn btn-primary">Add your first category</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Quizzes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= (int)$cat['category_id'] ?></td>
                            <td><strong><?= e($cat['category_name']) ?></strong></td>
                            <td><?= (int)$cat['quiz_count'] ?></td>
                            <td>
                                <div class="actions">
                                    <a href="<?= e(BASE_URL) ?>/admin/category-edit.php?id=<?= (int)$cat['category_id'] ?>"
                                       class="btn btn-sm btn-outline">Edit</a>
                                    <?php if ((int)$cat['quiz_count'] === 0): ?>
                                        <form method="post" style="display:inline;"
                                              onsubmit="return confirm('Delete this category?');">
                                            <?= csrfField() ?>
                                            <input type="hidden" name="delete_id" value="<?= (int)$cat['category_id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    <?php endif; ?>
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
