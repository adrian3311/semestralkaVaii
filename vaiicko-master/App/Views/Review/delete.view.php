<?php

/** @var \App\Models\Review $review */
/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
/** @var \Framework\Auth\AppUser $user */

$view->setLayout('root');
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
$isLoggedIn = false; try { $isLoggedIn = $user->isLoggedIn(); } catch (\Throwable $e) { $isLoggedIn = false; }
// compute current user safely
$currentUser = null;
try {
    $currentUser = $user->getName();
    if ($currentUser === null && $user->isLoggedIn()) {
        $currentUser = $user->getUsername();
    }
} catch (\Throwable $e) {
    $currentUser = null;
}
?>
<div class="container mt-4">
    <h2>Potvrdiť zmazanie</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!($isAdmin || ($isLoggedIn && ($review->getUsername() === $currentUser)))): ?>
        <div class="alert alert-warning">Nemáte oprávnenie zmazať túto recenziu.</div>
    <?php else: ?>
+
+     <div class="card p-3">
+         <p>Skutočne chcete zmazať recenziu od <strong><?= htmlspecialchars($review->getUsername()) ?></strong> ?</p>
+         <form method="post" action="<?= $link->url('review.delete', ['id' => $review->getId()]) ?>">
+             <button type="submit" name="confirm" class="btn btn-danger">Áno, zmazať</button>
+             <a href="<?= $link->url('review.index') ?>" class="btn btn-secondary">Zrušiť</a>
+         </form>
+     </div>
+    <?php endif; ?>
 </div>
