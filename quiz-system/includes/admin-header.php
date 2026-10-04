<?php
// Admin page header. Expects $page_title to be set.
// This file is included AFTER require_admin() has already been called.

if (!isset($page_title))       $page_title = 'Admin';
if (!isset($page_description)) $page_description = 'Admin area for managing quizzes, categories, questions, and results.';

$canonical_url = BASE_URL . $_SERVER['REQUEST_URI'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?> | Admin | <?= e(SITE_NAME) ?></title>
    <meta name="description" content="<?= e($page_description) ?>">
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon -->
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/favicon.svg" type="image/svg+xml">
    <link rel="icon" href="<?= BASE_URL ?>/assets/img/favicon-32.png" sizes="32x32" type="image/png">
    <link rel="apple-touch-icon" href="<?= BASE_URL ?>/assets/img/apple-touch-icon.png">

    <!-- Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="admin-body">
    <a href="#main" class="skip-link">Skip to main content</a>

    <header class="site-header admin-header">
        <div class="container header-inner">
            <a href="<?= BASE_URL ?>/admin/" class="site-logo" aria-label="Admin dashboard">
                <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                    <rect x="2" y="2" width="24" height="24" rx="4" stroke="currentColor" stroke-width="2"/>
                    <path d="M9 10l3 3-3 3M14 16h5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span><?= e(SITE_NAME) ?> <small>Admin</small></span>
            </a>

            <button class="nav-toggle" aria-label="Open navigation" aria-expanded="false" aria-controls="admin-nav">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>

            <nav id="admin-nav" class="main-nav" aria-label="Admin navigation">
                <ul class="nav-list">
                    <li><a href="<?= BASE_URL ?>/admin/">Dashboard</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/categories.php">Categories</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/quizzes.php">Quizzes</a></li>
                    <li><a href="<?= BASE_URL ?>/admin/results.php">Results</a></li>
                    <li><a href="<?= BASE_URL ?>/">View Site</a></li>
                    <li class="nav-user">
                        <form method="post" action="<?= BASE_URL ?>/logout.php" class="logout-form">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-sm btn-outline">Log out</button>
                        </form>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main id="main" class="main-content">
        <div class="container">
            <?= render_flashes() ?>
