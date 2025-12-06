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

<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <?php if ($isAdmin): ?>
                <!-- Admin-only: link to add a new menu item -->
                <a href="<?= $link->url('menu.add') ?>" class="btn btn-warning">Add item</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Render the menu items in a responsive grid -->
    <div class="row justify-content-center">
        <?php foreach ($menu as $item): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex mb-4">
                <!-- Single menu card: image, text and admin controls -->
                <div class="border post d-flex flex-column w-100">
                    <div>
                        <!-- Item image (use htmlspecialchars to avoid XSS) -->
                        <img src="<?= htmlspecialchars($item->getPicture()) ?>" class="img-fluid" alt="Menu image">
                    </div>
                    <div class="m-2">
                        <!-- Item description/text -->
                        <?= $item->getText() ?>
                    </div>
                    <div class="m-2 d-flex gap-2 justify-content-end align-items-center">
                        <!-- Title is shown; spacer used previously to push buttons to right -->
                        <span class="me-auto">Title: <?= htmlspecialchars($item->getTitle()) ?></span>

                        <!-- Controls: visible only to logged-in admin users -->
                        <?php if ($user->isLoggedIn() && $isAdmin): ?>
                            <a class="btn btn-sm btn-outline-primary ms-2" href="<?= $link->url('menu.edit', ['id' => $item->getId()]) ?>">Edit</a>
                            <a class="btn btn-sm btn-outline-danger ms-2" href="<?= $link->url('menu.delete', ['id' => $item->getId()]) ?>">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>