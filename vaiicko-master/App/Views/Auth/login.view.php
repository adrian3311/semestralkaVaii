<?php

/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');
?>

<?php try { $homeUrl = $link->url('home.index'); } catch (\Throwable $_) { $homeUrl = '/'; } ?>
<!-- fixed top-left button -->
<a href="<?= $homeUrl ?>" class="btn btn-outline-secondary btn-sm" style="position:fixed;left:12px;top:12px;z-index:2000;">Späť na hlavnú stránku</a>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            <div class="card my-5 shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <img src="<?= $link->asset('images/logo.png') ?>" alt="logo" style="height:64px;" class="mb-2">
                        <h3 class="mb-0">Prihlásenie</h3>
                        <p class="text-muted small">Zadajte svoje prihlasovacie údaje</p>
                    </div>

                    <?php if (!empty($message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?= htmlspecialchars($message) ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="<?= $link->url('login') ?>" class="needs-validation" novalidate>
                        <div class="form-floating mb-3">
                            <input name="username" type="text" id="username" class="form-control" placeholder="Používateľské meno" required autofocus>
                            <label for="username">Používateľské meno alebo email</label>
                            <div class="invalid-feedback">Zadajte používateľské meno alebo email.</div>
                        </div>

                        <div class="form-floating mb-3">
                            <input name="password" type="password" id="password" class="form-control" placeholder="Heslo" required>
                            <label for="password">Heslo</label>
                            <div class="invalid-feedback">Zadajte heslo.</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="remember" name="remember">
                                <label class="form-check-label small" for="remember">Zapamätať si ma</label>
                            </div>
                            <a href="#" class="small">Zabudnuté heslo?</a>
                        </div>

                        <div class="d-grid mb-2">
                            <button class="btn btn-primary btn-lg" type="submit" name="submit">Prihlásiť sa</button>
                        </div>

                        <?php
                            try { $registerUrl = $link->url('register'); } catch (\Throwable $_) { $registerUrl = '?c=auth&a=register'; }
                        ?>
                        <div class="text-center">
                            <a href="<?= $registerUrl ?>" class="btn btn-link">Nemáš účet? Zaregistruj sa</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Bootstrap client-side validation
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
</script>
