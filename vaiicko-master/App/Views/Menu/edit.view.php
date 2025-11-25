<?php

/** @var \App\Models\MenuItem $menu */
/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
/** @var \Framework\Auth\AppUser $user */

$view->setLayout('root');
// Ensure $user exists for static analysis and runtime fallback
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }

?>

<div class="container mt-4">
    <h2>Upraviť položku menu</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!$user->isLoggedIn()): ?>
        <div class="alert alert-warning">Musíte byť prihlásený ako administrátor, aby ste mohli upraviť položku.</div>
    <?php else: ?>

    <form method="post" enctype="multipart/form-data" action="<?= $link->url('menu.edit', ['id' => $menu->getId()]) ?>">
        <div class="mb-3">
            <label for="title" class="form-label">Názov</label>
            <input type="text" name="title" id="title" class="form-control" required value="<?= htmlspecialchars($menu->getTitle() ?? '') ?>">
        </div>
        <div class="mb-3">
            <label for="text" class="form-label">Text</label>
            <textarea name="text" id="text" class="form-control" rows="6"><?= htmlspecialchars($menu->getText() ?? '') ?></textarea>
        </div>
        <div class="mb-3">
            <label for="picture" class="form-label">Obrázok (voliteľné)</label>
            <input type="file" name="picture" id="picture" class="form-control">
            <?php if (!empty($menu->getPicture())): ?>
                <div class="mt-2"><img src="<?= htmlspecialchars($menu->getPicture()) ?>" alt="current" style="max-width:200px"></div>
            <?php endif; ?>
        </div>
        <div class="mb-3 d-flex gap-2">
            <button type="submit" name="submit" class="btn btn-primary">Uložiť</button>
            <a href="<?= $link->url('menu.index') ?>" class="btn btn-secondary">Zrušiť</a>
        </div>
    </form>
    <?php endif; ?>
</div>
