<?php
/**
 * Delete confirmation view for drink
 * Variables: $drink, $link, $view, $message
 */
/** @var \Framework\Support\View $view */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \App\Models\Drink $drink */

$view->setLayout('root');
if (!isset($drink)) { $drink = new \App\Models\Drink(); }
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }
// admin check for UX (controller enforces permission)
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
?>
<div class="container mt-4">
    <h2>Delete drink</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <?php if (!$isAdmin): ?>
        <div class="alert alert-warning">You must be an administrator to delete drinks.</div>
    <?php endif; ?>
    <p>Are you sure you want to delete "<?= htmlspecialchars($drink->getTitle() ?? '') ?>"?</p>
    <form method="post">
        <button name="confirm" class="btn btn-danger">Delete</button>
        <a href="<?= $link->url('drink.index') ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>
