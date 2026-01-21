<?php

/**
 * Login view
 *
 * This view renders the login form and handles client-side validation. It expects:
 * - $message: optional error or info message to show to the user
 * - $link: LinkGenerator to build URLs and asset links
 * - $view: view helper (used to set layout)
 */

/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

// Use the auth layout (centered card) for this page
$view->setLayout('auth');
?>

<?php try { $homeUrl = $link->url('home.index'); } catch (\Throwable $_) { $homeUrl = '/'; } ?>
<!-- fixed top-left button: quick link back to the public home page -->
<a href="<?= $homeUrl ?>" class="btn btn-outline-secondary btn-sm" style="position:fixed;left:12px;top:12px;z-index:2000;">Back to home</a>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            <div class="card my-5 shadow-sm">
                <div class="card-body p-4">
                    <!-- Header: logo + page title + brief description -->
                    <div class="text-center mb-3">
                        <img src="<?= $link->asset('images/logo.png') ?>" alt="logo" style="height:64px;" class="mb-2">
                        <h3 class="mb-0">Login</h3>
                        <p class="text-muted small">Enter your credentials</p>
                    </div>

                    <!-- Optional message block (e.g. login error or 'registered' notice) -->
                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Login form: uses POST and client-side bootstrap validation -->
                    <form method="post" action="<?= $link->url('login') ?>" class="needs-validation" novalidate>
                        <!-- Username or email field (floating label) -->
                        <div class="form-floating mb-3">
                            <input name="username" type="text" id="username" class="form-control" placeholder="Username or email" required autofocus>
                            <label for="username">Username or email</label>
                            <div class="invalid-feedback">Please enter your username or email.</div>
                        </div>

                        <!-- Password field (floating label) -->
                        <div class="form-floating mb-3">
                            <input name="password" type="password" id="password" class="form-control" placeholder="Password" required>
                            <label for="password">Password</label>
                            <div class="invalid-feedback">Please enter your password.</div>
                        </div>

                        <!-- Remember and forgot password row -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                                <label class="form-check-label small" for="remember">Remember me</label>
                            </div>
                            <a href="#" class="small">Forgot password?</a>
                        </div>

                        <!-- Submit button -->
                        <div class="d-grid mb-2">
                            <button class="btn btn-primary btn-lg" type="submit" name="submit">Sign in</button>
                        </div>

                        <?php
                            // Build register URL with fallback
                            try { $registerUrl = $link->url('register'); } catch (\Throwable $_) { $registerUrl = '?c=auth&a=register'; }
                        ?>

                        <!-- Small footer: link to registration for users who don't have an account -->
                        <div class="text-center">
                            <a href="<?= $registerUrl ?>" class="btn btn-link">Don't have an account? Register</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    /**
    // Bootstrap client-side validation - prevents form submit when invalid and shows validation feedback
    (function () {
        'use strict'
        var forms = document.querySelectorAll('.needs-validation')
        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false)
        })
    })()
    */
</script>
