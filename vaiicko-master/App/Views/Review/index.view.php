<?php

/**
 * Reviews index view
 *
 * Purpose:
 * - Render a list of reviews with their rating and text.
 * - Provide an "Add review" action for authenticated users.
 * - Show Edit/Delete controls to administrators or the original review author.
 *
 * Variables expected in this template:
 * - \App\Models\Review[] $reviews   Array of Review models to render
 * - \Framework\Support\LinkGenerator $link   URL/asset helper
 * - \Framework\Support\View $view    View helper / layout selector
 * - \Framework\Auth\AppUser $user    Current user (for permission checks)
 * - string|null $message              Optional status or error message
 */

/** @var \App\Models\Review[] $reviews */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
/** @var \Framework\Auth\AppUser $user */

$view->setLayout('root');
// ensure $user exists for static analysis/runtime fallback
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }
// is the current user an admin (project convention: username === 'admin')
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
// is the user logged in at all?
$isLoggedIn = false; try { $isLoggedIn = $user->isLoggedIn(); } catch (\Throwable $e) { $isLoggedIn = false; }
// current user name for owner checks
$currentUser = null;
try {
    $currentUser = $user->getName();
    if ($currentUser === null && $user->isLoggedIn()) {
        $currentUser = $user->getUsername();
    }
} catch (\Throwable $e) {
    $currentUser = null;
}
// ensure we have a sort variable (controller provides it when available)
// $sort controls the ordering of reviews on the server side (handled by ReviewController::index):
// - 'new' => newest first (ORDER BY `id` DESC)
// - 'old' => oldest first  (ORDER BY `id` ASC)
// The view receives $sort from the controller and the toggle button below flips between those values.
$sort = $sort ?? 'new';
?>

<div class="container mt-4">
    <!-- Header: Add review button (for logged-in users) and page title -->
    <div class="d-flex align-items-center mb-3">
        <?php if ($isLoggedIn): ?>
            <!-- Add review: visible only for logged-in users; links to the add form -->
            <a href="<?= $link->url('review.add') ?>" class="btn btn-warning me-3">Add review</a>
        <?php endif; ?>
        <h2 class="m-0">Reviews</h2>
        <!-- Sort toggle: align right -->
        <div class="ms-auto">
            <?php
                // Determine target sort value: if current is 'old' then clicking should switch to 'new', and vice versa.
                $toggleTo = ($sort === 'old') ? 'new' : 'old';
                // Label shown on the button describes what will happen when clicked (i.e. the ordering that will be applied).
                $label = ($toggleTo === 'old') ? 'Oldest first' : 'Newest first';
                // Build URL with the new sort parameter. The fourth argument 'true' tells LinkGenerator to append current
                // GET parameters so we don't lose other query values when toggling sort.
                $sortUrl = $link->url(['sort' => $toggleTo], [], false, true);
            ?>
            <!-- Clicking this link reloads the page with ?sort=old or ?sort=new and the controller will return reviews in the requested order. -->
            <a href="<?= $sortUrl ?>" class="btn btn-sm btn-outline-secondary">Sort: <?= htmlspecialchars($label) ?></a>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <!-- Show a message (e.g. error or info) and provide a Log in action -->
        <div class="alert alert-warning"><?= htmlspecialchars($message) ?></div>
        <p><a href="<?= \App\Configuration::LOGIN_URL ?>" class="btn btn-primary">Log in</a></p>
    <?php else: ?>

     <?php if (empty($reviews)): ?>
         <!-- Empty state when there are no reviews -->
         <div class="alert alert-info">No reviews yet.</div>
     <?php else: ?>
         <!-- Reviews list: each review is rendered as a list-group item -->
         <div class="list-group">
             <?php $idx = 0; foreach ($reviews as $r): $idx++; ?>
                 <div class="list-group-item">
                     <div class="d-flex w-100 justify-content-between">
                         <!-- Review author -->
                         <h5 class="mb-1"><?= htmlspecialchars($r->getUsername()) ?></h5>
                         <!-- Sequential index / small id shown for reference. $idx is 1-based loop counter used only for display. -->
                         <small class="text-muted">#<?= $idx ?></small>
                     </div>

                     <!-- Review text (preserve line breaks) -->
                     <p class="mb-1"><?= nl2br(htmlspecialchars((string)$r->getText())) ?></p>

                     <!-- Bottom row: rating display on the left, controls on the right -->
                     <div class="mt-2 d-flex gap-2 justify-content-between align-items-center">
                         <!-- Rating display: render up to 5 stars and a numeric label -->
                         <div class="rating d-flex align-items-center" aria-label="Rating">
                             <?php $rt = (int)($r->getRating() ?? 0); ?>
                             <?php for ($s=1;$s<=5;$s++): ?>
                                 <?php if ($s <= $rt): ?>
                                     <!-- filled star for each point in rating -->
                                     <span class="text-warning star">★</span>
                                 <?php else: ?>
                                     <!-- empty star for remaining -->
                                     <span class="text-muted star">☆</span>
                                 <?php endif; ?>
                             <?php endfor; ?>
                             <?php if ($rt > 0): ?>
                                 <!-- Numeric representation like "4/5"; (no rating) if zero/null -->
                                 <span class="rating-label"><?= htmlspecialchars($rt . '/5') ?></span>
                             <?php else: ?>
                                 <span class="rating-label">(no rating)</span>
                             <?php endif; ?>
                         </div>

                         <!-- Action buttons: Edit/Delete visible for admins or the review owner -->
                         <div class="d-flex gap-2 justify-content-end">
                         <?php
                             // Permission check: allow modification if current user is admin or the original author
                             $canModify = $isAdmin || ($isLoggedIn && ($r->getUsername() === $currentUser));
                             if ($canModify):
                         ?>
                             <!-- Edit link -> edit form for this review -->
                             <a class="btn btn-sm btn-outline-primary" href="<?= $link->url('review.edit', ['id' => $r->getId()]) ?>">Edit</a>
                             <!-- Delete link -> confirmation page to delete review -->
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
