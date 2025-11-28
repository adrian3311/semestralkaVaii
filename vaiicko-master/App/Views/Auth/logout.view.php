<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('auth');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            <div class="card my-5 text-center shadow-sm">
                <div class="card-body p-4">
                    <div class="mb-3">
                        <span style="font-size:48px;line-height:1;">👋</span>
                    </div>
                    <h3 class="mb-2">Boli ste odhlásený</h3>
                    <p class="text-muted">Úspešne ste sa odhlásili z účtu. Ak sa chcete prihlásiť znova, použite tlačidlo nižšie.</p>

                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="<?= App\Configuration::LOGIN_URL ?>" class="btn btn-primary">Prihlásiť sa</a>
                        <a href="<?= $link->url('home.index') ?>" class="btn btn-outline-secondary">Späť na hlavnú</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
