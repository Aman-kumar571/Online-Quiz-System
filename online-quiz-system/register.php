<?php
/**
 * User registration page.
 * Creates a new user account with hashed password.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/csrf.php';

if (isLoggedIn()) {
    redirect('/');
}

$errors = [];
$db = getDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken()) {
        $errors[] = 'Invalid form submission. Please try again.';
    } else {
        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $consent  = isset($_POST['consent']);

        // Validation
        if ($name === '') {
            $errors[] = 'Name is required.';
        } elseif (mb_strlen($name) > 100) {
            $errors[] = 'Name must be 100 characters or fewer.';
        }

        if ($email === '') {
            $errors[] = 'Email is required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address.';
        }

        if ($password === '') {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        }

        if ($password !== $confirm) {
            $errors[] = 'Passwords do not match.';
        }

        if (!$consent) {
            $errors[] = 'You must agree to the Privacy Policy and Terms and Conditions.';
        }

        // Check for duplicate email
        if (empty($errors)) {
            $stmt = $db->prepare('SELECT user_id FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            if ($stmt->fetch()) {
                $errors[] = 'An account with this email already exists.';
            }
        }

        // Create account
        if (empty($errors)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare(
                'INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :hash, "user")'
            );
            $stmt->execute([
                ':name'  => $name,
                ':email' => $email,
                ':hash'  => $hash,
            ]);

            setFlash('success', 'Account created successfully. You can now log in.');
            redirect('/login.php');
        }
    }
}

$pageTitle       = 'Register';
$pageDescription = 'Create a free ' . SITE_NAME . ' account to take quizzes and track your scores.';
$canonicalPath   = '/register.php';

require __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card card">
        <h1>Create an account</h1>
        <p class="auth-subtitle">Register to take quizzes and track your progress.</p>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" role="alert">
                <?php foreach ($errors as $err): ?>
                    <p style="margin:0;"><?= e($err) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= e(BASE_URL) ?>/register.php" novalidate>
            <?= csrfField() ?>

            <div class="form-group">
                <label for="name">Full name</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="<?= e($_POST['name'] ?? '') ?>"
                       required autocomplete="name" autofocus>
            </div>

            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= e($_POST['email'] ?? '') ?>"
                       required autocomplete="email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       required autocomplete="new-password" minlength="8">
                <p class="form-help">At least 8 characters.</p>
            </div>

            <div class="form-group">
                <label for="password_confirm">Confirm password</label>
                <input type="password" id="password_confirm" name="password_confirm" class="form-control"
                       required autocomplete="new-password">
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" id="consent" name="consent" required>
                    <label for="consent">
                        I agree to the <a href="<?= e(BASE_URL) ?>/privacy.php" target="_blank">Privacy Policy</a>
                        and <a href="<?= e(BASE_URL) ?>/terms.php" target="_blank">Terms and Conditions</a>.
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block btn-lg">Create account</button>
        </form>

        <p class="auth-footer">
            Already have an account? <a href="<?= e(BASE_URL) ?>/login.php">Log in</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
