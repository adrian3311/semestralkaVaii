<?php
/**
 * Edit drink form
 * Variables: $drink (App\Models\Drink), $link, $view, $message
 */
/** @var \Framework\Support\View $view */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Drink $drink */
/** @var \Framework\Auth\AppUser $user */

$view->setLayout('root');
if (!isset($drink)) { $drink = new \App\Models\Drink(); }
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }
?>
<div class="container mt-4">
    <h2>Edit drink</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php
        // show a notice if current user is not admin. Controller should prevent access, but this helps UX.
        $isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
    ?>
    <?php if (!$isAdmin): ?>
        <div class="alert alert-warning">You must be an administrator to edit drinks.</div>
    <?php endif; ?>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($drink->getTitle() ?? '') ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="text" class="form-control"><?= htmlspecialchars($drink->getText() ?? '') ?></textarea>
        </div>
        <!-- no price field for drinks; using title/text/picture like menuitems -->
        <div class="mb-3">
            <label class="form-label">Picture</label>
            <?php if (!empty($drink->getPicture())): ?>
                <div class="mb-2"><img src="<?= $link->asset($drink->getPicture()) ?>" alt="<?= htmlspecialchars($drink->getTitle() ?? 'drink') ?>" style="max-width:150px"></div>
            <?php endif; ?>
            <input type="file" name="picture" class="form-control">
        </div>
        <button name="submit" class="btn btn-primary">Save</button>
        <a href="<?= $link->url('drink.index') ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
