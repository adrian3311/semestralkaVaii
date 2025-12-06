<?php

/**
 * Add menu item view
 *
 * Renders a form for administrators to add a new menu item. Handles display of
 * optional messages, shows current picture preview when available and restricts
 * the action to admin users.
 *
 * Variables:
 * - string|null $message  Optional status/error message to show to the user
 * - \Framework\Support\LinkGenerator $link  Link helper for building URLs/assets
 * - \Framework\Support\View $view  View helper (used to select layout)
 * - \App\Models\MenuItem $menuItem  Model used to prefill form when editing
 * - \Framework\Auth\AppUser $user  Current user (used to detect admin status)
 */

/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
/** @var \App\Models\MenuItem $menuItem */
/** @var \Framework\Auth\AppUser $user */

// Use the root layout (main site layout)
$view->setLayout('root');

// Ensure $user exists for static analysis/runtime fallback
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }

// Determine admin state: in this project admin is user with username 'admin'
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
?>

<div class="container mt-4">
    <!-- Page heading -->
    <h2>Add menu item</h2>

    <!-- Optional message block: shows errors or status messages -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Admin check: only show form to administrators -->
    <?php if (!$isAdmin): ?>
        <div class="alert alert-warning">You must be logged in as an administrator to add menu items.</div>
    <?php else: ?>

    <!-- Form for creating a menu item
         - method POST
         - enctype multipart/form-data to support file uploads (picture)
         - action uses link generator for the menu.add route
    -->
    <form method="post" enctype="multipart/form-data" action="<?= $link->url('menu.add') ?>">
        <!-- Title field -->
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required value="<?= htmlspecialchars($menuItem?->getTitle() ?? '') ?>">
        </div>

        <!-- Description / Text field -->
        <div class="mb-3">
            <label for="text" class="form-label">Description</label>
            <textarea name="text" id="text" class="form-control" rows="6"><?= htmlspecialchars($menuItem?->getText() ?? '') ?></textarea>
        </div>

        <!-- Picture upload: optional file input and current preview if present -->
        <div class="mb-3">
            <label for="picture" class="form-label">Image (optional)</label>
            <input type="file" name="picture" id="picture" class="form-control">
            <?php if (!empty($menuItem?->getPicture())): ?>
                <!-- Show current image preview when editing an existing item -->
                <div class="mt-2"><img src="<?= htmlspecialchars($menuItem->getPicture()) ?>" alt="current" style="max-width:200px"></div>
            <?php endif; ?>
        </div>

        <!-- Action buttons: Save and Cancel -->
        <div class="mb-3 d-flex gap-2">
            <button type="submit" name="submit" class="btn btn-primary">Save</button>
            <a href="<?= $link->url('menu.index') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
    <?php endif; ?>
</div>
