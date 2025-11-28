<?php

/** @var string $contentHTML */
/** @var \Framework\Auth\AppUser $user */
/** @var \Framework\Support\LinkGenerator $link */
?>
<!DOCTYPE html>
<html lang="sk">
<head>
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
    <link rel="stylesheet" href="<?= $link->asset('css/styl.css') ?>">
    <link rel="stylesheet" href="<?= $link->asset('css/images.css') ?>">
    <script src="<?= $link->asset('js/script.js') ?>"></script>
    <style>
        /* Reset page margins to avoid gaps */
        html, body { width: 100%; height: 100%; margin: 0; padding: 0; }
        /* Make body a column flex container so footer can be pushed to bottom when content is short */
        body { display: flex; flex-direction: column; min-height: 100vh; }
        /* Footer full-width background while keeping content centered inside the inner .container */
        .site-footer {
            /* full-bleed: extend background to viewport edges */
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            background: #212529;
            color: #fff;
            box-sizing: border-box;
            left: 0;
        }
        /* Make the main content area expand to fill remaining space */
        .container-fluid.mt-3 { flex: 1 0 auto; }
        /* Navbar center username styling */
        .navbar { position: relative; }
        .navbar-center {
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            pointer-events: none; /* don't block clicks on navbar */
            color: #000;
            font-weight: 500;
            white-space: nowrap;
        }
        /* on dark navbar variations you might want .navbar-center { color: #fff; } */
        .site-footer a { color: #f8f9fa; text-decoration: none; }
    </style>
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
        <div class="mt-2 mt-md-0" style="font-size:0.95rem;">
            <a href="#" class="me-3">Facebook</a>
            <a href="#" class="me-3">Instagram</a>
            <a href="<?= $link->url('home.contact') ?>">Contact</a>
        </div>
    </div>
</footer>
</body>
</html>
