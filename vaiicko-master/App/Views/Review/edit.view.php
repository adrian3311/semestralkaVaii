<?php

/**
 * Edit review view
 *
 * Purpose:
 * - Render a form that allows an administrator or the original author to edit a review.
 * - Show optional status/error messages passed from the controller.
 * - Provide a rating selector and a live star preview (clickable) implemented in JS.
 *
 * Template variables expected:
 * - \App\Models\Review $review  The review model being edited
 * - string|null $message          Optional status or error message
 * - \Framework\Support\LinkGenerator $link  Helper for building URLs/assets
 * - \Framework\Support\View $view    View helper (used to select layout)
 * - \Framework\Auth\AppUser $user  Current user (used to check permissions)
 */

/** @var \App\Models\Review $review */
/** @var string|null $message */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */
/** @var \Framework\Auth\AppUser $user */

// Use the main site layout
$view->setLayout('root');

// Ensure $user exists (framework provides it at runtime); create a safe fallback to avoid template errors
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }

// Compute permission flags
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
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
    <!-- Page heading -->
    <h2>Edit review</h2>

    <!-- Optional message block: displays errors or info messages from the controller -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Permission check:
         - Allow editing only for admins or the original review author.
         - If not permitted, show a warning and do not display the form.
    -->
    <?php if (!($isAdmin || ($isLoggedIn && ($review->getUsername() === $currentUser)))): ?>
        <div class="alert alert-warning">You do not have permission to edit this review.</div>
    <?php else: ?>
        <!-- Edit form: POST to the current route; controller handles saving -->
        <form method="post">
            <!-- Author name (plain text) -->
            <div class="mb-3">
                <label for="username" class="form-label">Name</label>
                <div class="form-control-plaintext"><?= htmlspecialchars($review->getUsername()) ?></div>
            </div>

            <!-- Review text -->
            <div class="mb-3">
                <label for="text" class="form-label">Review</label>
                <textarea name="text" id="text" class="form-control" rows="5"><?= htmlspecialchars($review->getText() ?? '') ?></textarea>
            </div>

            <!-- Rating select -->
            <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <select name="rating" id="rating" class="form-select">
                    <option value="">(no rating)</option>
                    <?php for ($i=1;$i<=5;$i++): $sel = ($review->getRating() ?? '') == $i ? 'selected' : ''; ?>
                        <option value="<?= $i ?>" <?= $sel ?>><?= str_repeat('★',$i) ?> <?= $i ?></option>
                    <?php endfor; ?>
                </select>
                <!-- Note: the JS below creates/updates the #rating-preview element if missing -->
            </div>

            <!-- Form actions -->
            <div class="d-flex gap-2">
                <button type="submit" name="submit" class="btn btn-primary">Save</button>
                <a href="<?= $link->url('review.index') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<!--
    Rating preview script (English comments):
    - Ensures a #rating-preview element exists next to the rating select.
    - Renders 5 stars according to the current select value (filled or empty).
    - Clicking a star sets the select value and updates the preview.
-->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('rating');
    // attempt to find a preview element; if missing, create it right after the select's wrapper
    let preview = document.getElementById('rating-preview');
    if (!select) return;
    if (!preview) {
        const wrapper = select.parentElement;
        const div = document.createElement('div');
        div.id = 'rating-preview';
        div.className = 'fs-4';
        wrapper.parentElement.insertBefore(div, wrapper.nextSibling);
        preview = div;
    }

    function render(n) {
        n = parseInt(n) || 0;
        preview.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const span = document.createElement('span');
            span.className = (i <= n) ? 'text-warning star' : 'text-muted star';
            span.style.cursor = 'pointer';
            span.textContent = (i <= n) ? '★' : '☆';
            span.dataset.value = i;
            preview.appendChild(span);
        }
    }

    // clicking a star sets the select value and re-renders
    preview.addEventListener('click', function (e) {
        const t = e.target;
        if (!t || !t.classList.contains('star')) return;
        const v = t.dataset.value;
        select.value = v;
        render(v);
    });

    // keep preview in sync when select changes
    select.addEventListener('change', function (e) { render(e.target.value); });

    // initial render using select value or review rating
    render(select.value);
});
</script>
