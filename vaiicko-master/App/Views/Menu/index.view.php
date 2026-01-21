<?php

/**
 * Menu index view
 *
 * Purpose:
 * - Render a grid/list of menu items (images, descriptions, and controls).
 * - Show admin controls (Add/Edit/Delete) only to administrator users.
 *
 * Provided variables:
 * - \Framework\Support\LinkGenerator $link  Helper for building URLs and asset paths
 * - array $formErrors  Optional validation errors (not used here but provided)
 * - \App\Models\MenuItem[] $menu  Array of MenuItem models to render
 * - \Framework\Auth\AppUser $user  Current authenticated user object
 */

/** @var Framework\Support\LinkGenerator $link */
/** @var array $formErrors */
/** @var \App\Models\MenuItem[] $menu */
/** @var \Framework\Auth\AppUser $user */

// Ensure $user is defined (framework provides it at runtime). This avoids static analysis warnings.
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }

// Determine admin state for view controls (project convention: username === 'admin')
$isAdmin = false;
try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }

use App\Configuration;

?>

<div class="container mt-4">
    <div class="row mb-4">
        <div class="col d-flex align-items-left">
            <?php if ($isAdmin): ?>
                <!-- Admin-only: link to add a new menu item (label restored to 'Add item') -->
                <a href="<?= $link->url('menu.add') ?>" class="btn btn-warning">Add item</a>
            <?php endif; ?>
            <!-- Title/text displayed to the right of the button (visible to everyone) -->
            <h2 class="m-0 ms-3">Menu</h2>
        </div>
    </div>

    <!-- Render the menu items in a responsive grid -->
    <div class="row justify-content-center">
        <?php foreach ($menu as $item): ?>
            <div id="menu-item-<?= $item->getId() ?>" class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex mb-4">
                <!-- Single menu card using Bootstrap card with white background to match drinks -->
                <div class="card h-100 w-100">
                    <?php if (!empty($item->getPicture())): ?>
                        <img src="<?= htmlspecialchars($item->getPicture()) ?>" class="card-img-top" alt="<?= htmlspecialchars($item->getTitle()) ?>">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($item->getTitle()) ?></h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($item->getText())) ?></p>
                        <div class="mt-auto d-flex gap-2 justify-content-end align-items-center">
                            <?php if ($user->isLoggedIn() && $isAdmin): ?>
                                <a class="btn btn-sm btn-outline-primary ms-2 ajax-edit" href="<?= $link->url('menu.edit', ['id' => $item->getId()]) ?>" data-id="<?= $item->getId() ?>">Edit</a>
                                <a class="btn btn-sm btn-outline-danger ms-2 ajax-delete" href="<?= $link->url('menu.delete', ['id' => $item->getId()]) ?>" data-id="<?= $item->getId() ?>">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>