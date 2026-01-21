<?php

/** @var string $contentHTML */
/** @var \Framework\Auth\AppUser $user */
/** @var \Framework\Support\LinkGenerator $link */
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= App\Configuration::APP_NAME ?></title>
    <!-- Favicons (use public/images/logo.png) -->
    <?php $fav = $link->asset('images/logo.png') . '?v=1'; ?>
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $fav ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $fav ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $fav ?>">
    <link rel="shortcut icon" href="<?= $fav ?>">
    <link rel="manifest" href="<?= $link->asset('favicons/site.webmanifest') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="<?= $link->asset('css/images.css') ?>">
    <link rel="stylesheet" href="<?= $link->asset('css/theme.css') ?>">
    <link rel="stylesheet" href="<?= $link->asset('css/inlined-styles.css') ?>">
    <script src="<?= $link->asset('js/script.js') ?>"></script>
    <script src="<?= $link->asset('js/back-to-top.js') ?>"></script>
</head>
<body>
<nav class="navbar navbar-expand-sm bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $link->url('home.index') ?>">
            <img id="logo" src='images/logo.png' title="Arch Cafe" alt="Cafe Logo">
        </a>
        <ul class="navbar-nav me-auto">
            <li class="nav-item">
                <a class="nav-link" href="<?= $link->url('home.contact') ?>">Contact</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $link->url('menu.index') ?>">Menu</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $link->url('drink.index') ?>">Drinks</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?= $link->url('review.index') ?>">Reviews</a>
            </li>
        </ul>

        <?php if ($user->isLoggedIn()) { ?>
            <!-- Centered username (visible on md and up to avoid overlap on small screens) -->
            <div class="navbar-center d-none d-md-block">Logged in: <b><?= htmlspecialchars($user->getName()) ?></b></div>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= $link->url('auth.logout') ?>">Log out</a>
                </li>
            </ul>
        <?php } else { ?>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= App\Configuration::LOGIN_URL ?>">Log in</a>
                </li>
            </ul>
        <?php } ?>
    </div>
</nav>
<div class="container-fluid mt-3">
    <div class="web-content">
        <?= $contentHTML ?>
    </div>
</div>

<!-- Site footer placed here so background covers full viewport width -->
<footer class="site-footer py-2 mt-2">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-center" style="padding-top:6px;padding-bottom:6px;">
        <div style="font-size:0.95rem;">&copy; <?= date('Y') ?> <?= htmlspecialchars(App\Configuration::APP_NAME) ?> — All rights reserved.</div>
        <div style="font-size:0.95rem;">
            <!-- Open external social links in a new tab and use rel for security -->
            <a href="https://www.facebook.com/p/Arch-Cafe-at-Kresen-Kernow-100091795280068" class="me-3" target="_blank" rel="noopener noreferrer">Facebook</a>
            <a href="https://www.instagram.com/michal.liba" class="me-3" target="_blank" rel="noopener noreferrer">Instagram</a>
            <!-- Internal contact link stays on same tab -->
            <a href="<?= $link->url('home.contact') ?>">Contact</a>
        </div>
    </div>
</footer>
</body>
</html>
