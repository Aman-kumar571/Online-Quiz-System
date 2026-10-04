<?php
// Shared page header: HTML head, navigation bar.
// Expects $page_title (string) and optional $page_description (string) to be set before including.

if (!isset($page_title))       $page_title = SITE_NAME;
if (!isset($page_description)) $page_description = 'Take quizzes on programming, databases, and web development. Test your knowledge and track your progress.';

$canonical_url = BASE_URL . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | <?= e(SITE_NAME) ?></title>
    <meta name="description" content="<?= e($page_description) ?>">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="<?= e($canonical_url) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= e($page_title) ?> | <?= e(SITE_NAME) ?>">
    <meta property="og:description" content="<?= e($page_description) ?>">
    <meta property="og:url" content="<?= e($canonical_url) ?>">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">

    <!-- Favicon -->
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>/assets/img/apple-touch-icon.png">
    <link rel="manifest" href="<?= BASE_URL ?>/site.webmanifest">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
    <a href="#main" class="skip-link">Skip to main content</a>

    <header class="site-header">
        <div class="container header-inner">
            <a href="<?= BASE_URL ?>/" class="site-logo" aria-label="<?= e(SITE_NAME) ?> home">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect x="2" y="2" width="24" height="24" rx="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M9 10l3 3-3 3M14 16h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span><?= e(SITE_NAME) ?></span>
            </a>

            <button class="nav-toggle" aria-label="Open navigation" aria-expanded="false" aria-controls="main-nav">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            <nav id="main-nav" class="main-nav" aria-label="Main navigation">
                <ul class="nav-list">
                    <li><a href="<?= BASE_URL ?>/">Home</a></li>
                    <li><a href="<?= BASE_URL ?>/quizzes.php">Quizzes</a></li>
                    <?php if (is_logged_in()): ?>
                        <li><a href="<?= BASE_URL ?>/my-attempts.php">My Attempts</a></li>
                        <?php if (is_admin()): ?>
                            <li><a href="<?= BASE_URL ?>/admin/">Admin</a></li>
                        <?php endif; ?>
                        <li class="nav-user">
                            <span class="nav-user-name"><?= e(current_user_name()) ?></span>
                            <form method="post" action="<?= BASE_URL ?>/logout.php" class="logout-form">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-outline">Log out</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="<?= BASE_URL ?>/login.php">Log in</a></li>
                        <li><a href="<?= BASE_URL ?>/register.php" class="btn btn-sm btn-primary">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main" class="main-content">
        <div class="container">
            <?= render_flashes() ?>
