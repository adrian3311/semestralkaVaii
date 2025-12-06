<?php

/**
 * Admin dashboard view
 *
 * Purpose:
 * - Show a compact admin panel for users with administrative privileges.
 * - If the current user is not an admin, show a friendly panel with actions
 *   available to regular users (view menu, add review, browse reviews).
 *
 * Variables available in this template:
 * - $link: Framework\Support\LinkGenerator used to create URLs and asset paths
 * - $user: Framework\Auth\AppUser representing the current user (may be guest)
 */

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
?>

<?php
// Determine admin status in a safe way (project convention: username === 'admin')
// We wrap this in a try/catch because $user may be missing or throw when not logged in.
$isAdmin = false;
try {
    // isLoggedIn() returns whether the user has an active session
    // getUsername() returns their login name; here 'admin' is treated as the administrator
    $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin');
} catch (\Throwable $_) {
    // If anything goes wrong, treat the user as non-admin (safe default)
    $isAdmin = false;
}
?>

<!-- The main container for the admin page -->
<div class="container">
    <!-- center the card in the viewport -->
    <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card my-5 shadow-sm">
                <div class="card-body">
                    <!-- Header area: logo + welcome message -->
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <!-- logo image (served from public assets) -->
                        <img src="<?= $link->asset('images/logo.png') ?>" alt="logo" style="height:48px;">
                        <div>
                            <!-- greet the user by name if available -->
                            <h4 class="mb-0">Welcome, <span class="badge bg-primary text-white"><?= htmlspecialchars($user->getName() ?? $user->getUsername() ?? 'User') ?></span></h4>
                            <small class="text-muted">Admin interface</small>
                        </div>
                    </div>

                    <!-- Conditional: admin-only controls -->
                    <?php if ($isAdmin): ?>

                        <!-- Short description for admins -->
                        <p class="mb-3">This section is accessible to administrators only. Use the controls below to manage site content.</p>

                        <!-- Admin cards: quick links to manage menu and reviews -->
                        <div class="row g-3">
                            <!-- Card: Manage menu items -->
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6>Manage menu</h6>
                                        <p class="small text-muted mb-2">Add, edit, or remove menu items.</p>
                                        <!-- Link to the menu management index -->
                                        <a href="<?= $link->url('menu.index') ?>" class="btn btn-sm btn-outline-primary">Open menu</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Card: Manage reviews -->
                            <div class="col-12 col-md-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-body">
                                        <h6>Manage reviews</h6>
                                        <p class="small text-muted mb-2">Review and moderate user-submitted reviews.</p>
                                        <!-- Link to the reviews management index -->
                                        <a href="<?= $link->url('review.index') ?>" class="btn btn-sm btn-outline-primary">Open reviews</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Admin logout button (aligned to the right) -->
                        <div class="mt-4 text-end">
                            <a href="<?= $link->url('auth.logout') ?>" class="btn btn-outline-danger">Log out</a>
                        </div>

                    <?php else: ?>

                        <!-- Non-admin / regular user view: a friendly welcome and actions -->
                        <div class="p-3 mb-3 rounded-3" style="background:linear-gradient(90deg,#eef7ff,#f8fbff);">
                            <h5 class="mb-1">Welcome!</h5>
                            <p class="mb-0 text-muted">You can now add reviews, browse the menu, and read reviews left by other visitors.</p>
                        </div>

                        <!-- Regular user cards: view menu, add review, browse reviews -->
                        <div class="row g-3 mb-3">
                            <!-- Card: View menu (public page) -->
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6>View menu</h6>
                                        <p class="small text-muted mb-2">Browse available menu items.</p>
                                        <a href="<?= $link->url('menu.index') ?>" class="btn btn-sm btn-outline-secondary">View menu</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Card: Add review (visible only to logged-in users; otherwise link can redirect to login) -->
                            <div class="col-12 col-md-6">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6>Add review</h6>
                                        <p class="small text-muted mb-2">Leave your review for other visitors.</p>
                                        <a href="<?= $link->url('review.add') ?>" class="btn btn-sm btn-success">Add review</a>
                                    </div>
                                </div>
                            </div>

                            <!-- Card: Browse reviews -->
                            <div class="col-12">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <h6>Browse reviews</h6>
                                        <p class="small text-muted mb-2">Read reviews left by other users.</p>
                                        <a href="<?= $link->url('review.index') ?>" class="btn btn-sm btn-outline-secondary">View reviews</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Utility buttons for regular users: back to site and logout (if logged in) -->
                        <div class="d-flex gap-2">
                            <a href="<?= $link->url('home.index') ?>" class="btn btn-secondary">Back to site</a>
                            <?php if ($user->isLoggedIn()): ?>
                                <!-- Logout only shown when the user is logged in -->
                                <a href="<?= $link->url('auth.logout') ?>" class="btn btn-outline-danger">Log out</a>
                            <?php endif; ?>
                        </div>

                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>

