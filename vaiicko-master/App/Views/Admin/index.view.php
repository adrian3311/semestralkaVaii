<?php

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
?>

<?php
// Determine admin status in a safe way (project convention: username === 'admin')
$isAdmin = false;
try {
    $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin');
} catch (\Throwable $_) {
    $isAdmin = false;
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card my-5 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= $link->asset('images/logo.png') ?>" alt="logo" style="height:48px;">
                        <div>
                            <h4 class="mb-0">Vitajte, <span class="badge bg-primary text-white"><?= htmlspecialchars($user->getName() ?? $user->getUsername() ?? 'User') ?></span></h4>
                            <small class="text-muted">Admin rozhranie</small>
                        </div>
                    </div>

                    <?php if ($isAdmin): ?>

                        <p class="mb-3">Táto sekcia je prístupná iba administrátorom. Použite ovládacie prvky nižšie na správu obsahu stránky.</p>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6>Správa menu</h6>
                                        <p class="small text-muted mb-2">Pridávajte, upravujte alebo odstraňujte položky menu.</p>
                                        <a href="<?= $link->url('menu.index') ?>" class="btn btn-sm btn-outline-primary">Otvoriť menu</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6>Správa recenzií</h6>
                                        <p class="small text-muted mb-2">Prezerajte a moderujte recenzie používateľov.</p>
                                        <a href="<?= $link->url('review.index') ?>" class="btn btn-sm btn-outline-primary">Otvoriť recenzie</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-end">
                            <a href="<?= $link->url('auth.logout') ?>" class="btn btn-outline-danger">Odhlásiť sa</a>
                        </div>

                    <?php else: ?>

                        <div class="p-3 mb-3 rounded-3" style="background:linear-gradient(90deg,#eef7ff,#f8fbff);">
                            <h5 class="mb-1">Vitajte!</h5>
                            <p class="mb-0 text-muted">Teraz môžete pridávať recenzie, prehliadať menu a prezerať recenzie ostatných používateľov.</p>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6>Zobraziť menu</h6>
                                        <p class="small text-muted mb-2">Prezerajte dostupné položky menu.</p>
                                        <a href="<?= $link->url('menu.index') ?>" class="btn btn-sm btn-outline-secondary">Zobraziť menu</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6>Pridať recenziu</h6>
                                        <p class="small text-muted mb-2">Zanechajte svoju recenziu pre ostatných návštevníkov.</p>
                                        <a href="<?= $link->url('review.add') ?>" class="btn btn-sm btn-success">Pridať recenziu</a>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6>Prehliadať recenzie</h6>
                                        <p class="small text-muted mb-2">Prezerajte recenzie zanechané inými používateľmi.</p>
                                        <a href="<?= $link->url('review.index') ?>" class="btn btn-sm btn-outline-secondary">Zobraziť recenzie</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="<?= $link->url('home.index') ?>" class="btn btn-secondary">Späť na stránku</a>
                            <?php if ($user->isLoggedIn()): ?>
                                <a href="<?= $link->url('auth.logout') ?>" class="btn btn-outline-danger">Odhlásiť sa</a>
                            <?php endif; ?>
                        </div>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>