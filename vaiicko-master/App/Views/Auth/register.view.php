<?php

/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');
?>

<?php try { $homeUrl = $link->url('home.index'); } catch (\Throwable $_) { $homeUrl = '/'; } ?>
<!-- fixed top-left button: quick access back to public home -->
<a href="<?= $homeUrl ?>" class="btn btn-outline-secondary btn-sm" style="position:fixed;left:12px;top:12px;z-index:2000;">Back to home</a>

<div class="container">
    <div class="row">
        <div class="col-sm-9 col-md-7 col-lg-5 mx-auto">
            <div class="card card-signin my-5">
                <div class="card-body">
                    <!-- Card title: registration form header -->
                    <h5 class="card-title text-center">Register</h5>

                    <?php if (!empty($message)): ?>
                        <div class="text-center text-danger mb-3"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>

                    <?php
                        // prepare register action URL and login fallback
                        try {
                            $registerAction = $link->url('register');
                        } catch (\Throwable $_) {
                            $registerAction = '?c=auth&a=register';
                        }
                        try {
                            $loginUrl = $link->url('login');
                        } catch (\Throwable $_) {
                            $loginUrl = '?c=auth&a=login';
                        }

                        // ensure $old exists and has keys
                        if (!isset($old) || !is_array($old)) {
                            $old = ['username' => '', 'email' => ''];
                        } else {
                            $old['username'] = $old['username'] ?? '';
                            $old['email'] = $old['email'] ?? '';
                        }
                    ?>

                    <form class="form-signin" method="post" action="<?= $registerAction ?>" novalidate>
                        <div class="form-label-group mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input name="username" type="text" id="username" class="form-control" placeholder="Username" required value="<?= htmlspecialchars($old['username']) ?>">
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input name="email" type="email" id="email" class="form-control" placeholder="email@example.com" required value="<?= htmlspecialchars($old['email']) ?>">
                        </div>

                        <div class="form-label-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input name="password" type="password" id="password" class="form-control" placeholder="Password" required>
                        </div>

                        <!-- Confirm password field -->
                        <div class="form-label-group mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password</label>
                            <input name="password_confirm" type="password" id="password_confirm" class="form-control" placeholder="Confirm Password" required>
                        </div>

                        <!-- Submit button -->
                        <div class="text-center mb-2">
                            <button id="register-btn" class="btn btn-primary" type="submit" name="submit">Register</button>
                        </div>

                        <div class="text-center">
                            <a href="<?= $loginUrl ?>" class="btn btn-link btn-sm">Already have an account? Sign in</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const pwd = document.getElementById('password');
    const pwd2 = document.getElementById('password_confirm');
    const btn = document.getElementById('register-btn');
    function validate() {
        if (!pwd || !pwd2 || !btn) return;
        if (pwd.value === '' || pwd2.value === '') {
            btn.disabled = false; // allow server-side to validate emptiness
            return;
        }
        if (pwd.value !== pwd2.value) {
            btn.disabled = true;
            pwd2.setCustomValidity('Passwords do not match');
        } else {
            btn.disabled = false;
            pwd2.setCustomValidity('');
        }
    }
    if (pwd && pwd2) {
        // attach live validation to both password fields
        pwd.addEventListener('input', validate);
        pwd2.addEventListener('input', validate);
    }
});
</script>
