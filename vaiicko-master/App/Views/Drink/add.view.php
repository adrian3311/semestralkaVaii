<?php
/**
 * Add drink form
 * Variables: $drink (App\Models\Drink), $link, $view, $message
 */
/** @var \Framework\Support\View $view */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Drink $drink */
/** @var \Framework\Auth\AppUser $user */

$view->setLayout('root');
if (!isset($drink)) { $drink = new \App\Models\Drink(); }
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }
// admin check: only admin can add drinks (controller enforces it but show message in UI)
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
?>
<div class="container mt-4">
    <h2>Add drink</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!$isAdmin): ?>
        <div class="alert alert-warning">You must be an administrator to add a drink.</div>
    <?php else: ?>
    <form method="post" enctype="multipart/form-data" action="<?= $link->url('drink.add') ?>">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($drink->getTitle() ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="text" class="form-control"><?= htmlspecialchars($drink->getText() ?? '') ?></textarea>
        </div>
        <!-- no price field: schema uses title/text/picture like menuitems -->
        <div class="mb-3">
            <label class="form-label">Picture</label>
            <input type="file" name="picture" class="form-control">
        </div>
        <button name="submit" class="btn btn-primary">Save</button>
        <a href="<?= $link->url('drink.index') ?>" class="btn btn-secondary">Cancel</a>
    </form>
    <?php endif; ?>
</div>
