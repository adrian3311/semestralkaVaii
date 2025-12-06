<?php

/**
 * Edit menu item view
 *
 * Purpose:
 * - Render a form for administrators to edit an existing menu item.
 * - Show optional status/error message and current image preview if available.
 *
 * Variables expected in this template:
 * - \App\Models\MenuItem $menu  The menu item to edit
 * - string|null $message         Optional status or error message
 * - \Framework\Support\LinkGenerator $link  Helper to build URLs/assets
 * - \Framework\Support\View $view    View helper (used to select layout)
 * - \Framework\Auth\AppUser $user  Current user (used to detect admin rights)
 */

/** @var \App\Models\MenuItem $menu */
/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
/** @var \Framework\Auth\AppUser $user */

// Use the main site layout
$view->setLayout('root');
// Ensure $user exists for static analysis and runtime fallback
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }
// determine admin state (project convention: username === 'admin')
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }

?>

<div class="container mt-4">
    <!-- Page heading: edited to English -->
    <h2>Edit menu item</h2>

    <!-- Optional message block: shows errors or status messages from controller -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Authorization check: only admins can edit menu items -->
    <?php if (!$isAdmin): ?>
        <div class="alert alert-warning">You must be logged in as an administrator to edit items.</div>
    <?php else: ?>

    <!-- Edit form
         - POST method
         - multipart/form-data to accept file upload for picture
         - action points to the menu.edit route with the item id
         - fields: title, description (text), picture (optional)
    -->
    <form method="post" enctype="multipart/form-data" action="<?= $link->url('menu.edit', ['id' => $menu->getId()]) ?>">
        <!-- Title field -->
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required value="<?= htmlspecialchars($menu->getTitle() ?? '') ?>">
        </div>

        <!-- Description field -->
        <div class="mb-3">
            <label for="text" class="form-label">Description</label>
            <textarea name="text" id="text" class="form-control" rows="6"><?= htmlspecialchars($menu->getText() ?? '') ?></textarea>
        </div>

        <!-- Picture upload: optional; show current preview if exists -->
        <div class="mb-3">
            <label for="picture" class="form-label">Image (optional)</label>
            <input type="file" name="picture" id="picture" class="form-control">
            <?php if (!empty($menu->getPicture())): ?>
                <!-- Current image preview shown when editing -->
                <div class="mt-2"><img src="<?= htmlspecialchars($menu->getPicture()) ?>" alt="current" style="max-width:200px"></div>
            <?php endif; ?>
        </div>

        <!-- Action buttons: Save and Cancel (return to menu index) -->
        <div class="mb-3 d-flex gap-2">
            <button type="submit" name="submit" class="btn btn-primary">Save</button>
            <a href="<?= $link->url('menu.index') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
    <?php endif; ?>
</div>
