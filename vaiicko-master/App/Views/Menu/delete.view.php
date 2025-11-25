<?php

/** @var \App\Models\MenuItem $menu */
/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('root');
?>
<div class="container mt-4">
    <h2>Potvrdiť zmazanie</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="card p-3">
        <p>Skutočne chcete zmazať položku: <strong><?= htmlspecialchars($menu->getTitle()) ?></strong> ?</p>
        <form method="post" action="<?= $link->url('menu.delete', ['id' => $menu->getId()]) ?>">
            <button type="submit" name="confirm" class="btn btn-danger">Áno, zmazať</button>
            <a href="<?= $link->url('menu.index') ?>" class="btn btn-secondary">Zrušiť</a>
        </form>
    </div>
</div>

