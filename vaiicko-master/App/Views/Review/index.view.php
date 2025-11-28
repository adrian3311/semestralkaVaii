<?php

/** @var \App\Models\Review[] $reviews */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
/** @var \Framework\Auth\AppUser $user */

$view->setLayout('root');
// ensure $user for static analysis/runtime fallback
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
$isLoggedIn = false; try { $isLoggedIn = $user->isLoggedIn(); } catch (\Throwable $e) { $isLoggedIn = false; }
// current user name (for owner checks)
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
    <style>
        /* Ensure star colors are visible even if Bootstrap is not loaded */
        .rating .text-warning { color: #f5c518; } /* gold */
        .rating .text-muted  { color: #c0c0c0; } /* light gray */
        .rating .star { font-size: 1.1rem; margin-right: 2px; }
        .rating-label { margin-left: 8px; color: #6c757d; font-size: 0.95rem; }
    </style>
    <div class="d-flex align-items-center mb-3">
        <?php if ($isLoggedIn): ?>
            <a href="<?= $link->url('review.add') ?>" class="btn btn-success me-3">Pridať recenziu</a>
        <?php endif; ?>
        <h2 class="m-0">Recenzie</h2>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-warning"><?= htmlspecialchars($message) ?></div>
        <p><a href="<?= \App\Configuration::LOGIN_URL ?>" class="btn btn-primary">Prihlásiť sa</a></p>
    <?php else: ?>
     <?php if (empty($reviews)): ?>
         <div class="alert alert-info">Žiadne recenzie zatiaľ.</div>
     <?php else: ?>
         <div class="list-group">
             <?php $idx = 0; foreach ($reviews as $r): $idx++; ?>
                 <div class="list-group-item">
                     <div class="d-flex w-100 justify-content-between">
                         <h5 class="mb-1"><?= htmlspecialchars($r->getUsername()) ?></h5>
                         <small class="text-muted">#<?= $idx ?></small>
                     </div>
                     <p class="mb-1"><?= nl2br(htmlspecialchars((string)$r->getText())) ?></p>
                     <div class="mt-2 d-flex gap-2 justify-content-between align-items-center">
                         <div class="rating d-flex align-items-center" aria-label="Hodnotenie">
                             <?php $rt = (int)($r->getRating() ?? 0); ?>
                             <?php for ($s=1;$s<=5;$s++): ?>
                                 <?php if ($s <= $rt): ?>
                                     <span class="text-warning star">★</span>
                                 <?php else: ?>
                                     <span class="text-muted star">☆</span>
                                 <?php endif; ?>
                             <?php endfor; ?>
                             <?php if ($rt > 0): ?>
                                 <span class="rating-label"><?= htmlspecialchars($rt . '/5') ?></span>
                             <?php else: ?>
                                 <span class="rating-label">(bez hodnotenia)</span>
                             <?php endif; ?>
                         </div>
                         <div class="d-flex gap-2 justify-content-end">
                         <?php
                             $canModify = $isAdmin || ($isLoggedIn && ($r->getUsername() === $currentUser));
                             if ($canModify):
                         ?>
                             <a class="btn btn-sm btn-outline-primary" href="<?= $link->url('review.edit', ['id' => $r->getId()]) ?>">Edit</a>
                             <a class="btn btn-sm btn-outline-danger" href="<?= $link->url('review.delete', ['id' => $r->getId()]) ?>">Delete</a>
                         <?php endif; ?>
                         </div>
                     </div>
                 </div>
             <?php endforeach; ?>
         </div>
     <?php endif; ?>
    <?php endif; ?>
</div>
