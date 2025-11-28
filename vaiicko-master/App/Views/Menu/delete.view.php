<?php

/** @var \App\Models\MenuItem $menu */
/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

$view->setLayout('root');
// Ensure $user exists and compute admin state
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
?>
<div class="container mt-4">
    <h2>Potvrdiť zmazanie</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!$isAdmin): ?>
        <div class="alert alert-warning">Nemáte oprávnenie na zmazanie tejto položky.</div>
    <?php else: ?>

     <div class="card p-3">
         <p>Skutočne chcete zmazať položku: <strong><?= htmlspecialchars($menu->getTitle()) ?></strong> ?</p>
         <form method="post" action="<?= $link->url('menu.delete', ['id' => $menu->getId()]) ?>">
             <button type="submit" name="confirm" class="btn btn-danger">Áno, zmazať</button>
             <a href="<?= $link->url('menu.index') ?>" class="btn btn-secondary">Zrušiť</a>
         </form>
     </div>
    <?php endif; ?>
 </div>
