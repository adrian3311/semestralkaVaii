<?php

/**
 * Logout view
 *
 * Purpose:
 * - Show a friendly confirmation page after the user logs out.
 * - Provide quick actions: go to the login page or return to the home page.
 *
 * Available variables:
 * - $link: \Framework\Support\LinkGenerator for building URLs/assets
 * - $view: \Framework\Support\View (layout helper)
 */

/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Support\View $view */

// Use the 'auth' layout (centered card) for consistent auth pages
$view->setLayout('auth');
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6">
            <div class="card my-5 text-center shadow-sm">
                <div class="card-body p-4">
                    <!-- Emoji / visual confirmation -->
                    <div class="mb-3">
                        <span style="font-size:48px;line-height:1;">👋</span>
                    </div>

                    <!-- Main title: confirmation of logout -->
                    <h3 class="mb-2">You have been logged out</h3>

                    <!-- Short explanatory text telling user what happened and next steps -->
                    <p class="text-muted">You have successfully signed out of your account. To sign in again, use the button below.</p>

                    <!-- Action buttons: primary goes to login, secondary returns to the public home -->
                    <div class="d-flex justify-content-center gap-2 mt-3">
                        <a href="<?= App\Configuration::LOGIN_URL ?>" class="btn btn-primary">Sign in</a>
                        <a href="<?= $link->url('home.index') ?>" class="btn btn-outline-secondary">Back to home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
