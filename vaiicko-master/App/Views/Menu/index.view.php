<?php

/** @var Framework\Support\LinkGenerator $link */
/** @var array $formErrors */
/** @var \App\Models\MenuItem[] $menu */
/** @var \Framework\Auth\AppUser $user */

// Ensure $user is defined (provided by framework in runtime). This avoids static analysis undefined variable warnings.
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }

use App\Configuration;

?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <?php if ($user->isLoggedIn()): ?>
                <a href="<?= $link->url('menu.add') ?>" class="btn btn-success">Pridať položku</a>
            <?php endif; ?>
        </div>
    </div>
    <div class="row justify-content-center">
        <?php foreach ($menu as $item): ?>
            <div class="col-3 d-flex gap-4 flex-column">
                <div class="border post d-flex flex-column">
                    <div>
                        <img src="<?= htmlspecialchars($item->getPicture()) ?>" class="img-fluid" alt="Menu image">
                    </div>
                    <div class="m-2">
                        <?= $item->getText() ?>
                    </div>
                    <div class="m-2 d-flex gap-2 justify-content-end">
                        <span>Title: <?= $item->getTitle() ?></span>
                        <span class="flex-grow-1"></span>
                        <?php if ($user->isLoggedIn()): ?>
                            <a class="btn btn-sm btn-outline-primary ms-2" href="<?= $link->url('menu.edit', ['id' => $item->getId()]) ?>">Edit</a>
                            <a class="btn btn-sm btn-outline-danger ms-2" href="<?= $link->url('menu.delete', ['id' => $item->getId()]) ?>">Delete</a>
                         <?php endif; ?>
                     </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>