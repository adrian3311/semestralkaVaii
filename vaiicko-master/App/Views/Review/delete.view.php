<?php

/**
 * Delete confirmation view for a Review
 *
 * Purpose:
 * - Render a confirmation prompt for deleting a review.
 * - Only allow deletion for administrators or the review owner.
 * - Show optional error/status messages passed from the controller.
 *
 * Template variables expected:
 * - \App\Models\Review $review
 * - string|null $message
 * - \Framework\Support\LinkGenerator $link
 * - \Framework\Support\View $view
 * - \Framework\Auth\AppUser $user
 */

/** @var \App\Models\Review $review */
/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
/** @var \Framework\Auth\AppUser $user */

// Set the layout for this view (use main site layout)
$view->setLayout('root');

// Ensure $user exists (framework provides it at runtime); create a safe fallback to avoid template errors
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }

// Compute whether current user is admin (project convention: username === 'admin')
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
// Compute whether a user is logged in
$isLoggedIn = false; try { $isLoggedIn = $user->isLoggedIn(); } catch (\Throwable $e) { $isLoggedIn = false; }

// Compute the current user's display name safely for owner checks
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
    <!-- Page heading: English title -->
    <h2>Confirm deletion</h2>

    <!-- Show any message provided by the controller (error or status) -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!--
        Permission check:
        - Allow deletion only for administrators OR the original review author (owner).
        - If the current user is neither, show a warning and do not display the form.
    -->
    <?php if (!($isAdmin || ($isLoggedIn && ($review->getUsername() === $currentUser)))): ?>
        <div class="alert alert-warning">You do not have permission to delete this review.</div>
    <?php else: ?>

     <div class="card p-3">
         <!-- Confirmation prompt showing which review / author will be deleted -->
         <p>Are you sure you want to delete the review by <strong><?= htmlspecialchars($review->getUsername()) ?></strong>?</p>

         <!-- Confirmation form: POSTs back to the delete action with the review id -->
         <form method="post" action="<?= $link->url('review.delete', ['id' => $review->getId()]) ?>">
             <!-- Primary destructive action -->
             <button type="submit" name="confirm" class="btn btn-danger">Yes, delete</button>
             <!-- Secondary action: cancel and return to reviews list -->
             <a href="<?= $link->url('review.index') ?>" class="btn btn-secondary">Cancel</a>
         </form>
     </div>
    <?php endif; ?>
</div>
