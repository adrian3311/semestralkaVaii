<?php
/**
 * Drinks index view
 * Variables: $drinks (array of App\Models\Drink), $link, $view, $user
 */
/** @var \Framework\Support\View $view */
/** @var \Framework\Support\LinkGenerator $link */
/** @var \Framework\Auth\AppUser $user */
/** @var array $drinks */

$view->setLayout('root');
if (!isset($user)) { $user = new \Framework\Auth\AppUser(); }
if (!isset($drinks)) { $drinks = []; }
// admin check: only admin should be allowed to add/edit/delete drinks
$isAdmin = false; try { $isAdmin = $user->isLoggedIn() && ($user->getUsername() === 'admin'); } catch (\Throwable $e) { $isAdmin = false; }
// keep isLoggedIn available for non-admin UI decisions if needed
$isLoggedIn = false; try { $isLoggedIn = $user->isLoggedIn(); } catch (\Throwable $e) { $isLoggedIn = false; }

?>
<div class="container mt-4">
    <div class="d-flex align-items-center mb-3">
        <?php if ($isAdmin): ?>
            <a href="<?= $link->url('drink.add') ?>" class="btn btn-warning me-3">Add drink</a>
        <?php endif; ?>
        <h2 class="m-0">Drinks</h2>
    </div>

    <?php if (empty($drinks)): ?>
        <div class="alert alert-info">No drinks yet.</div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($drinks as $d): ?>
                <div class="col-md-4 mb-3">
                    <div class="card h-100">
                        <?php if (!empty($d->getPicture())): ?>
                            <img src="<?= $link->asset($d->getPicture()) ?>" class="card-img-top" alt="<?= htmlspecialchars($d->getTitle()) ?>">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($d->getTitle()) ?></h5>
                            <?php if (!empty($d->getText())): ?>
                                <p class="card-text"><?= nl2br(htmlspecialchars($d->getText())) ?></p> <!--nl2br: zmení každé nové riadky za <br>  -->
                            <?php endif; ?>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <div class="price"></div>
                                <div>
                                    <?php if ($isAdmin): ?>
                                        <a href="<?= $link->url('drink.edit', ['id' => $d->getId()]) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                        <a href="<?= $link->url('drink.delete', ['id' => $d->getId()]) ?>" class="btn btn-sm btn-outline-danger">Delete</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
