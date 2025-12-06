<?php

/**
 * Add review view
 *
 * Purpose:
 * - Render a form for authenticated users to add a review.
 * - Show an optional message, display the logged-in user's name, allow text and rating input,
 *   and provide a live rating preview that can be clicked to set the rating.
 *
 * Template variables (provided by the controller / framework):
 * - $view: Framework\Support\View (view helper / layout selector)
 * - $link: Framework\Support\LinkGenerator (URL/asset helper)
 * - $review: optional Review model used when editing (null when adding)
 * - $user: Framework\Auth\AppUser representing the current user
 * - $message: optional status or error message to display
 */

use Framework\Auth\AppUser;
use Framework\Support\LinkGenerator;
use Framework\Support\View as ViewHelper;

// Ensure helper variables exist for static analysis and safe runtime fallback
if (!isset($view)) {
    if (class_exists(ViewHelper::class)) {
        // Try to create a real View helper if available
        try {
            $layoutSlot = null;
            $view = new ViewHelper($layoutSlot);
        } catch (\Throwable $e) {
            $view = new class { public function setLayout($name) { /* no-op fallback */ } };
        }
    } else {
        $view = new class { public function setLayout($name) { /* no-op fallback */ } };
    }
}

if (!isset($link)) {
    if (class_exists(LinkGenerator::class)) {
        // Can't reliably construct LinkGenerator without App/Router in this template; use a small shim that delegates to url building if needed
        $link = new class {
            public function url($name, $params = []) { return '?c=review&a=index'; }
        };
    } else {
        $link = new class { public function url($name, $params = []) { return '?c=review&a=index'; } };
    }
}

if (!isset($review)) {
    $review = null; // null-safe usage in template (we use null-safe operator)
}

// Ensure $user is available for the view (runtime provides it)
if (!isset($user)) {
    if (class_exists(AppUser::class)) {
        try {
            $user = new AppUser();
        } catch (\Throwable $e) {
            $user = new class { public function isLoggedIn(){return false;} public function getUsername(){return null;} public function getName(){return null;} };
        }
    } else {
        $user = new class { public function isLoggedIn(){return false;} public function getUsername(){return null;} public function getName(){return null;} };
    }
}

$isAdmin = false;
try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
$isLoggedIn = false; try { $isLoggedIn = $user->isLoggedIn(); } catch (\Throwable $e) { $isLoggedIn = false; }

$displayName = null;
try {
    $displayName = $user->getName();
    if ($displayName === null && $user->isLoggedIn()) {
        $displayName = $user->getUsername();
    }
} catch (\Throwable $e) {
    $displayName = null;
}

$view->setLayout('root');
?>

<div class="container mt-4">
    <!-- Page title -->
    <h2>Add review</h2>

    <!-- Optional message: shown when controller passes $message -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Require login: show a friendly notice if the user is not authenticated -->
    <?php if (!$isLoggedIn): ?>
        <div class="alert alert-warning">You must be logged in to add a review.</div>
    <?php else: ?>
        <!-- Review form: POST to current route (controller handles saving) -->
        <form method="post">
            <!-- Display the current user's name as plain text -->
            <div class="mb-3">
                <label class="form-label">Name</label>
                <div class="form-control-plaintext"><?= htmlspecialchars($displayName ?? '') ?></div>
            </div>

            <!-- Text area for the review body -->
            <div class="mb-3">
                <label for="text" class="form-label">Review</label>
                <textarea name="text" id="text" class="form-control" rows="5"><?= htmlspecialchars($review?->getText() ?? '') ?></textarea>
            </div>

            <!-- Rating selector: allows choosing 1..5 or no rating -->
            <div class="mb-3">
                <label for="rating" class="form-label">Rating</label>
                <select name="rating" id="rating" class="form-select" aria-label="Rating">
                    <option value="">(no rating)</option>
                    <?php for ($i=1;$i<=5;$i++): $sel = ($review?->getRating() ?? '') == $i ? 'selected' : ''; ?>
                        <option value="<?= $i ?>" <?= $sel ?>><?= str_repeat('★',$i) ?> <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Live rating preview: shows stars and allows clicking to set the rating -->
            <div class="mb-3">
                <label class="form-label">Rating preview</label>
                <div id="rating-preview" class="fs-4"><?php $rt = $review?->getRating() ?? 0; for ($s=1;$s<=5;$s++): echo $s <= $rt ? '<span class="text-warning">★</span>' : '<span class="text-muted">☆</span>'; endfor; ?></div>
            </div>

            <!-- Action buttons: Save and Cancel -->
            <div class="d-flex gap-2">
                <button type="submit" name="submit" class="btn btn-primary">Save</button>
                <a href="<?= $link->url('review.index') ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<!--
    Rating preview script
    - Renders 5 stars according to the select value
    - Clicking a star sets the select value and updates the preview
    - Keeps the UI in sync with the select element
-->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('rating');
    const preview = document.getElementById('rating-preview');
    if (!select || !preview) return;

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

    // Clicking a star sets the select value and re-renders
    preview.addEventListener('click', function (e) {
        const t = e.target;
        if (!t || !t.classList.contains('star')) return;
        const v = t.dataset.value;
        select.value = v;
        render(v);
    });

    select.addEventListener('change', function (e) { render(e.target.value); });

    // initial render
    render(select.value);
});
</script>
