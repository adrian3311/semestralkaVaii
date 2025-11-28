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
    <h2>Upraviť recenziu</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <?php if (!($isAdmin || ($isLoggedIn && ($review->getUsername() === $currentUser)))): ?>
        <div class="alert alert-warning">Nemáte oprávnenie upravovať túto recenziu.</div>
    <?php else: ?>
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Meno</label>
                <div class="form-control-plaintext"><?= htmlspecialchars($review->getUsername()) ?></div>
            </div>

            <div class="mb-3">
                <label for="text" class="form-label">Recenzia</label>
                <textarea name="text" id="text" class="form-control" rows="5"><?= htmlspecialchars($review->getText() ?? '') ?></textarea>
            </div>
            <div class="mb-3">
                <label for="rating" class="form-label">Hodnotenie</label>
                <select name="rating" id="rating" class="form-select">
                    <option value="">(bez hodnotenia)</option>
                    <?php for ($i=1;$i<=5;$i++): $sel = ($review->getRating() ?? '') == $i ? 'selected' : ''; ?>
                        <option value="<?= $i ?>" <?= $sel ?>><?= str_repeat('★',$i) ?> <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" name="submit" class="btn btn-primary">Uložiť</button>
                <a href="<?= $link->url('review.index') ?>" class="btn btn-secondary">Zrušiť</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const select = document.getElementById('rating');
    const preview = document.getElementById('rating-preview');
    if (!select) return;
    // if preview element is missing, create it after the rating select
    if (!preview) {
        const wrapper = select.parentElement;
        const div = document.createElement('div');
        div.id = 'rating-preview';
        div.className = 'fs-4';
        wrapper.parentElement.insertBefore(div, wrapper.nextSibling);
    }
    const pr = document.getElementById('rating-preview');

    function render(n) {
        n = parseInt(n) || 0;
        pr.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const span = document.createElement('span');
            span.className = (i <= n) ? 'text-warning star' : 'text-muted star';
            span.style.cursor = 'pointer';
            span.textContent = (i <= n) ? '★' : '☆';
            span.dataset.value = i;
            pr.appendChild(span);
        }
    }

    pr.addEventListener('click', function (e) {
        const t = e.target;
        if (!t || !t.classList.contains('star')) return;
        const v = t.dataset.value;
        select.value = v;
        render(v);
    });

    select.addEventListener('change', function (e) { render(e.target.value); });

    // initial render using select value or review rating
    render(select.value);
});
</script>
