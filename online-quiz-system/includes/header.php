<?php
/**
 * Shared page header.
 * Expected variables: $pageTitle (string), $pageDescription (string, optional)
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/csrf.php';

initSession();

$pageTitle       = $pageTitle ?? SITE_NAME;
$pageDescription = $pageDescription ?? SITE_TAGLINE;
$canonicalPath   = $canonicalPath ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> | <?= e(SITE_NAME) ?></title>
    <meta name="description" content="<?= e($pageDescription) ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e(CANONICAL_URL . $canonicalPath) ?>">
    <meta property="og:site_name" content="<?= e(SITE_NAME) ?>">

    <!-- Canonical -->
    <link rel="canonical" href="<?= e(CANONICAL_URL . $canonicalPath) ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="<?= e(BASE_URL) ?>/favicon.svg">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= e(BASE_URL) ?>/favicon-32.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= e(BASE_URL) ?>/favicon-180.png">
    <link rel="manifest" href="<?= e(BASE_URL) ?>/site.webmanifest">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= e(BASE_URL) ?>/assets/css/style.css">
</head>
<body>
    <a href="#main-content" class="skip-link">Skip to main content</a>

    <header class="site-header" role="banner">
        <div class="container header-inner">
            <a href="<?= e(BASE_URL) ?>/" class="site-logo" aria-label="<?= e(SITE_NAME) ?> home">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect x="2" y="2" width="24" height="24" rx="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M9 10h10M9 14h7M9 18h10" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    <circle cx="21" cy="18" r="2" fill="currentColor"/>
                </svg>
                <span><?= e(SITE_NAME) ?></span>
            </a>

            <button class="nav-toggle" aria-label="Toggle navigation" aria-expanded="false" aria-controls="main-nav">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            <nav id="main-nav" class="main-nav" aria-label="Main navigation">
                <ul>
                    <li><a href="<?= e(BASE_URL) ?>/" class="<?= isActivePage('index.php') ?>">Home</a></li>
                    <li><a href="<?= e(BASE_URL) ?>/quizzes.php" class="<?= isActivePage('quizzes.php') ?>">Quizzes</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="<?= e(BASE_URL) ?>/my-attempts.php" class="<?= isActivePage('my-attempts.php') ?>">My Attempts</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a href="<?= e(BASE_URL) ?>/admin/" class="nav-admin">Admin</a></li>
                        <?php endif; ?>
                        <li class="nav-user">
                            <span class="nav-user-name"><?= e(currentUserName()) ?></span>
                            <a href="<?= e(BASE_URL) ?>/logout.php" class="btn btn-sm btn-outline">Log out</a>
                        </li>
                    <?php else: ?>
                        <li><a href="<?= e(BASE_URL) ?>/login.php" class="btn btn-sm btn-outline <?= isActivePage('login.php') ?>">Log in</a></li>
                        <li><a href="<?= e(BASE_URL) ?>/register.php" class="btn btn-sm btn-primary <?= isActivePage('register.php') ?>">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main-content">
        <?php
        $flash = getFlash();
        if ($flash): ?>
            <div class="container">
                <div class="alert alert-<?= e($flash['type']) ?>" role="alert">
                    <?= e($flash['message']) ?>
                </div>
            </div>
        <?php endif; ?>
