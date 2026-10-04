<?php
/**
 * User login page.
 * Validates credentials, regenerates session, redirects by role.
 */

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';

$pageTitle       = 'Log in';
$pageDescription = 'Log in to your ' . SITE_NAME . ' account to take quizzes and track your progress.';
$canonicalPath   = '/login.php';

require __DIR__ . '/includes/header.php';

// If already logged in, redirect
if (isLoggedIn()) {
    redirect('/');
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $errors[] = 'Email and password are required.';
        } else {
            $db   = getDB();
            $stmt = $db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                loginUser($user);

                if ($user['role'] === 'admin') {
                    redirect('/admin/');
                } else {
                    redirect('/');
                }
            } else {
                $errors[] = 'Invalid email or password.';
            }
        }
    }
}
?>

<div class="auth-page">
    <div class="auth-card card">
        <h1>Log in</h1>
        <p class="auth-subtitle">Enter your email and password to continue.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $err): ?>
                    <p style="margin:0;"><?= e($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(BASE_URL) ?>/login.php" novalidate>
            <?= csrfField() ?>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= e($_POST['email'] ?? '') ?>"
                       required autocomplete="email" autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">Log in</button>
        </form>

        <p class="auth-footer">
            Do not have an account? <a href="<?= e(BASE_URL) ?>/register.php">Register here</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
