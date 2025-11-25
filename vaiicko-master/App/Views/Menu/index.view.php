<?php

/** @var Framework\Support\LinkGenerator $link */
/** @var array $formErrors */
/** @var \App\Models\MenuItem[] $menu*/

use App\Configuration;

?>

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <a href="<?= $link->url('post.add') ?>" class="btn btn-success">Pridať príspevok</a>
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
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>