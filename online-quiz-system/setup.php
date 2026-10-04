<?php
/**
 * One-time setup script: generates PNG favicons and the proper admin password hash.
 * Run once via browser: http://localhost/online-quiz-system/setup.php
 * Delete this file after setup.
 */

// Generate PNG favicons from the SVG concept
function createFaviconPng(int $size, string $filename): void
{
    $img = imagecreatetruecolor($size, $size);
    imagesavealpha($img, true);

    // Background: deep blue
    $blue  = imagecolorallocate($img, 26, 86, 219);
    $white = imagecolorallocate($img, 255, 255, 255);

    // Fill with blue
    imagefilledrectangle($img, 0, 0, $size - 1, $size - 1, $blue);

    // Rounded corners (approximate by drawing circles at corners)
    $radius = (int)($size * 0.15);
    $transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
    imagecolortransparent($img, $transparent);

    // Draw "Q" letter centered
    $fontSize = (int)($size * 0.55);
    $fontPath = null;

    // Try to find a system font
    $possibleFonts = [
        'C:/Windows/Fonts/arial.ttf',
        'C:/Windows/Fonts/arialbd.ttf',
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
    ];

    foreach ($possibleFonts as $font) {
        if (file_exists($font)) {
            $fontPath = $font;
            break;
        }
    }

    if ($fontPath) {
        $bbox = imagettfbbox($fontSize, 0, $fontPath, 'Q');
        $textWidth  = $bbox[2] - $bbox[0];
        $textHeight = $bbox[1] - $bbox[7];
        $x = (int)(($size - $textWidth) / 2) - $bbox[0];
        $y = (int)(($size + $textHeight) / 2) - $bbox[1] - (int)($textHeight * 0.1);
        imagettftext($img, $fontSize, 0, $x, $y, $white, $fontPath, 'Q');
    } else {
        // Fallback: use built-in font
        $builtinFont = 5; // Largest built-in font
        $fw = imagefontwidth($builtinFont);
        $fh = imagefontheight($builtinFont);
        $x = (int)(($size - $fw) / 2);
        $y = (int)(($size - $fh) / 2);
        imagestring($img, $builtinFont, $x, $y, 'Q', $white);
    }

    imagepng($img, __DIR__ . '/' . $filename);
    imagedestroy($img);
}

echo "<h1>QuizPoint Setup</h1>";

// Generate favicons
echo "<h2>Generating Favicons</h2>";
if (function_exists('imagecreatetruecolor')) {
    createFaviconPng(32, 'favicon-32.png');
    echo "<p>Created favicon-32.png</p>";

    createFaviconPng(180, 'favicon-180.png');
    echo "<p>Created favicon-180.png</p>";
} else {
    echo "<p style='color:red;'>GD library not available. Please enable php_gd in php.ini.</p>";
}

// Generate proper admin password hash
echo "<h2>Admin Password Hash</h2>";
$password = 'Admin@1234';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "<p>Password: <code>{$password}</code></p>";
echo "<p>Hash: <code>{$hash}</code></p>";
echo "<p>Copy the hash above and update it in <code>sql/seed.sql</code>, or run this SQL:</p>";
echo "<pre>UPDATE users SET password_hash = '{$hash}' WHERE email = 'admin@quizsystem.local';</pre>";

// Try to update directly if database is available
echo "<h2>Direct Database Update</h2>";
try {
    require_once __DIR__ . '/includes/db.php';
    $db = getDB();

    // Check if admin exists
    $stmt = $db->prepare('SELECT user_id FROM users WHERE email = :email');
    $stmt->execute([':email' => 'admin@quizsystem.local']);
    $admin = $stmt->fetch();

    if ($admin) {
        $stmt = $db->prepare('UPDATE users SET password_hash = :hash WHERE email = :email');
        $stmt->execute([':hash' => $hash, ':email' => 'admin@quizsystem.local']);
        echo "<p style='color:green;'>Admin password updated directly in database.</p>";
    } else {
        echo "<p>Admin user not found. Import seed.sql first, then run this script again.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:orange;'>Could not connect to database: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Import schema.sql and seed.sql first, then run this script again to fix the admin password.</p>";
}

echo "<hr>";
echo "<p><strong>Delete this file (setup.php) after you are done.</strong></p>";
echo '<p><a href="' . (defined('BASE_URL') ? BASE_URL : '.') . '/">Go to home page</a></p>';
