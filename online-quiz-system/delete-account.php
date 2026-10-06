<?php
/**
 * Account deletion page.
 * Allows users to permanently delete their account and associated quiz attempts.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

requireLogin();

$errors = [];
$db = getDB();
$userId = currentUserId();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $password = $_POST['password'] ?? '';
        $confirm = isset($_POST['confirm_delete']);

        if ($password === '') {
            $errors[] = 'Please enter your current password to confirm deletion.';
        }

        if (!$confirm) {
            $errors[] = 'You must acknowledge that this action cannot be undone.';
        }

        if (empty($errors)) {
            $stmt = $db->prepare('SELECT password_hash FROM users WHERE user_id = :uid LIMIT 1');
            $stmt->execute([':uid' => $userId]);
            $user = $stmt->fetch();

            if (!$user || !password_verify($password, $user['password_hash'])) {
                $errors[] = 'The password you entered is incorrect.';
            } else {
                // Delete user (cascade automatically removes quiz attempt results)
                $stmt = $db->prepare('DELETE FROM users WHERE user_id = :uid');
                $stmt->execute([':uid' => $userId]);

                logoutUser();
                initSession();
                setFlash('success', 'Your account and all associated quiz history have been permanently deleted.');
                redirect('/');
            }
        }
    }
}

$pageTitle       = 'Delete Account';
$pageDescription = 'Permanently delete your account and quiz history from ' . SITE_NAME . '.';
$canonicalPath   = '/delete-account.php';

require __DIR__ . '/includes/header.php';
?>

<div class="container" style="max-width: 580px; padding-top: var(--space-8); padding-bottom: var(--space-12);">
    <div class="page-header">
        <h1>Delete account</h1>
        <p class="text-muted">Permanently remove your account and all associated data.</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error" role="alert">
            <?php foreach ($errors as $err): ?>
                <p style="margin:0;"><?= e($err) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="alert alert-warning" style="margin-bottom: var(--space-6);">
            <strong>Warning:</strong> Deleting your account will permanently erase your user profile and all quiz attempt history. This action cannot be reversed.
        </div>

        <form method="post" action="<?= e(BASE_URL) ?>/delete-account.php" novalidate>
            <?= csrfField() ?>

            <div class="form-group">
                <label for="password">Confirm your password</label>
                <input type="password" id="password" name="password" class="form-control"
                       required autocomplete="current-password" autofocus>
                <p class="form-help">Enter your password to verify your identity.</p>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" id="confirm_delete" name="confirm_delete" required>
                    <label for="confirm_delete">
                        I understand that my account and all my quiz records will be permanently deleted.
                    </label>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-danger btn-lg">Permanently delete my account</button>
                <a href="<?= e(BASE_URL) ?>/my-attempts.php" class="btn btn-outline btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
