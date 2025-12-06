<?php

/**
 * Delete confirmation view for a MenuItem
 *
 * Renders a confirmation form for administrators to delete a menu item.
 * Variables expected in this template:
 * - \App\Models\MenuItem $menu  The menu item to be deleted
 * - string|null $message         Optional status or error message to display
 * - \Framework\Support\LinkGenerator $link  Helper for building URLs/assets
 * - \Framework\Support\View $view    View helper (used to set layout)
 * - \Framework\Auth\AppUser $user  Current user (used to check admin rights)
 */

/** @var \App\Models\MenuItem $menu */
/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

// Use the main site layout
$view->setLayout('root');

// Ensure $user exists and compute admin state (project convention: username === 'admin')
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
?>

<div class="container mt-4">
    <!-- Page heading / title -->
    <h2>Confirm deletion</h2>

    <!-- Message block: show any error/status message passed from controller -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Authorization check: only administrators may perform delete -->
    <?php if (!$isAdmin): ?>
        <div class="alert alert-warning">You do not have permission to delete this item.</div>
    <?php else: ?>

     <div class="card p-3">
         <!-- Confirmation prompt: show the item title to confirm deletion -->
         <p>Are you sure you want to delete the item: <strong><?= htmlspecialchars($menu->getTitle()) ?></strong>?</p>

         <!-- Confirmation form: POST request with 'confirm' to perform deletion -->
         <form method="post" action="<?= $link->url('menu.delete', ['id' => $menu->getId()]) ?>">
             <!-- Primary action: delete the item -->
             <button type="submit" name="confirm" class="btn btn-danger">Yes, delete</button>
             <!-- Secondary action: cancel and go back to menu index -->
             <a href="<?= $link->url('menu.index') ?>" class="btn btn-secondary">Cancel</a>
         </form>
     </div>
    <?php endif; ?>
</div>
