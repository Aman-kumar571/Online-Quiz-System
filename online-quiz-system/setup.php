<?php
/**
 * Setup script: configure initial administrator account and generate favicons.
 * Delete this file after initial setup.
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/helpers.php';

$message = '';
$messageType = '';

// Check and generate favicons if GD is available and files do not exist
function generateFaviconPng(int $size, string $filename): bool
{
    $target = __DIR__ . '/' . $filename;
    if (file_exists($target)) {
        return true;
    }
    if (!function_exists('imagecreatetruecolor')) {
        return false;
    }

    $img = imagecreatetruecolor($size, $size);
    imagesavealpha($img, true);

    $blue  = imagecolorallocate($img, 26, 86, 219);
    $white = imagecolorallocate($img, 255, 255, 255);

    imagefilledrectangle($img, 0, 0, $size - 1, $size - 1, $blue);

    $fontSize = (int)($size * 0.55);
    $fontPath = null;
    $possibleFonts = [
        'C:/Windows/Fonts/arial.ttf',
        'C:/Windows/Fonts/arialbd.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
    ];

    foreach ($possibleFonts as $f) {
        if (file_exists($f)) {
            $fontPath = $f;
            break;
        }
    }

    if ($fontPath && function_exists('imagettftext')) {
        $bbox = imagettfbbox($fontSize, 0, $fontPath, 'Q');
        $textWidth  = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];
        $x = (int)(($size - $textWidth) / 2) - $bbox[0];
        $y = (int)(($size + $textHeight) / 2) - $bbox[1] - (int)($textHeight * 0.1);
        imagettftext($img, $fontSize, 0, $x, $y, $white, $fontPath, 'Q');
    } else {
        $builtinFont = 5;
        $fw = imagefontwidth($builtinFont);
        $fh = imagefontheight($builtinFont);
        $x = (int)(($size - $fw) / 2);
        $y = (int)(($size - $fh) / 2);
        imagestring($img, $builtinFont, $x, $y, 'Q', $white);
    }

    $res = imagepng($img, $target);
    imagedestroy($img);
    return $res;
}

generateFaviconPng(32, 'favicon-32.png');
generateFaviconPng(180, 'favicon-180.png');

$dbConnected = false;
$existingAdminCount = 0;
$dbError = '';

try {
    require_once __DIR__ . '/includes/db.php';
    $db = getDB();
    $dbConnected = true;

    // Ensure login_attempts table exists
    $db->exec(
        'CREATE TABLE IF NOT EXISTS login_attempts (
            attempt_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(45) NOT NULL,
            email VARCHAR(255) NOT NULL,
            attempt_time DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_ip_time (ip_address, attempt_time),
            INDEX idx_email_time (email, attempt_time)
        ) ENGINE=InnoDB'
    );

    $stmt = $db->query('SELECT COUNT(*) FROM users WHERE role = "admin"');
    $existingAdminCount = (int)$stmt->fetchColumn();
} catch (Exception $e) {
    $dbError = $e->getMessage();
}

// Handle admin creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $dbConnected) {
    $name     = trim($_POST['admin_name'] ?? '');
    $email    = trim($_POST['admin_email'] ?? '');
    $password = $_POST['admin_password'] ?? '';
    $confirm  = $_POST['admin_password_confirm'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $message = 'All fields are required.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please provide a valid email address.';
        $messageType = 'error';
    } elseif (strlen($password) < 8) {
        $message = 'Password must be at least 8 characters long.';
        $messageType = 'error';
    } elseif ($password !== $confirm) {
        $message = 'Passwords do not match.';
        $messageType = 'error';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Check if this email exists
        $stmt = $db->prepare('SELECT user_id, role FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $existing = $stmt->fetch();

        if ($existing) {
            $stmt = $db->prepare(
                'UPDATE users SET name = :name, password_hash = :hash, role = "admin" WHERE email = :email'
            );
            $stmt->execute([':name' => $name, ':hash' => $hash, ':email' => $email]);
            $message = 'Existing account updated to Administrator with your chosen password.';
            $messageType = 'success';
        } else {
            $stmt = $db->prepare(
                'INSERT INTO users (name, email, password_hash, role) VALUES (:name, :email, :hash, "admin")'
            );
            $stmt->execute([':name' => $name, ':email' => $email, ':hash' => $hash]);
            $message = 'Administrator account created successfully with your chosen password.';
            $messageType = 'success';
        }

        $existingAdminCount = (int)$db->query('SELECT COUNT(*) FROM users WHERE role = "admin"')->fetchColumn();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup | <?= e(SITE_NAME) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= e(BASE_URL) ?>/favicon.svg">
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container header-inner">
            <span class="site-logo"><span><?= e(SITE_NAME) ?> Setup</span></span>
            <a href="<?= e(BASE_URL) ?>/" class="btn btn-sm btn-outline">Go to site</a>
        </div>
    </header>

    <main class="container" style="max-width: 680px; padding-top: var(--space-8); padding-bottom: var(--space-12);">
        <h1>Initial Setup</h1>
        <p class="text-muted" style="margin-bottom: var(--space-6);">
            Configure your administrator account. No default passwords are provided; choose your credentials below.
        </p>

        <?php if ($message): ?>
            <div class="alert alert-<?= e($messageType) ?>" role="alert">
                <?= e($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!$dbConnected): ?>
            <div class="alert alert-error" role="alert">
                <strong>Database connection failed:</strong> <?= e($dbError) ?><br>
                Please verify that MySQL is running and your database credentials in <code>includes/config.php</code> are correct.
            </div>
        <?php else: ?>
            <div class="card" style="margin-bottom: var(--space-8);">
                <h2 style="font-size: var(--text-xl); margin-bottom: var(--space-4);">
                    <?= $existingAdminCount > 0 ? 'Create or Update Administrator' : 'Create Administrator Account' ?>
                </h2>

                <?php if ($existingAdminCount > 0): ?>
                    <p class="text-sm text-muted" style="margin-bottom: var(--space-4);">
                        There is currently <?= $existingAdminCount ?> administrator account registered. You may create another or reset credentials below.
                    </p>
                <?php endif; ?>

                <form method="post" action="setup.php" novalidate>
                    <div class="form-group">
                        <label for="admin_name">Full Name</label>
                        <input type="text" id="admin_name" name="admin_name" class="form-control"
                               value="<?= e($_POST['admin_name'] ?? 'Administrator') ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="admin_email">Email Address</label>
                        <input type="email" id="admin_email" name="admin_email" class="form-control"
                               value="<?= e($_POST['admin_email'] ?? '') ?>" required placeholder="admin@example.com">
                    </div>

                    <div class="form-group">
                        <label for="admin_password">Password</label>
                        <input type="password" id="admin_password" name="admin_password" class="form-control"
                               required minlength="8" autocomplete="new-password">
                        <p class="form-help">Must be at least 8 characters.</p>
                    </div>

                    <div class="form-group">
                        <label for="admin_password_confirm">Confirm Password</label>
                        <input type="password" id="admin_password_confirm" name="admin_password_confirm" class="form-control"
                               required minlength="8" autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">Set up administrator</button>
                </form>
            </div>
        <?php endif; ?>

        <div class="card">
            <h2 style="font-size: var(--text-xl); margin-bottom: var(--space-4);">Next Steps</h2>
            <ol style="list-style: decimal; padding-left: var(--space-5); line-height: 1.8;">
                <li>Log in at <a href="<?= e(BASE_URL) ?>/login.php"><?= e(BASE_URL) ?>/login.php</a> using your chosen credentials.</li>
                <li>Access the admin dashboard at <a href="<?= e(BASE_URL) ?>/admin/"><?= e(BASE_URL) ?>/admin/</a>.</li>
                <li><strong>Delete setup.php</strong> before deploying to production.</li>
            </ol>
        </div>
    </main>
</body>
</html>
