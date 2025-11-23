<?php
// 1. Zahrnie hlavnú šablónu (Layout)
// Predpokladáme, že máš metódu, ktorá načíta root.layout.view.php
// Vo tvojom Vaii Frameworku to bude pravdepodobne vyzerať takto:
require_once __DIR__ . '/../Layouts/root.layout.view.php';

// Pre túto ukážku budeme pracovať s premennými $title a $menuItems,
// ktoré by ti sem mali prísť z Controllera.
?>

<div class="container menu-page">
    <header class="text-center my-5">
        <h1><?= htmlspecialchars($title ?? 'Naša Ponuka') ?></h1>
        <p class="lead">Vyberte si z našej čerstvo pripravenej kávy, zákuskov a ľahkého občerstvenia.</p>
    </header>

    <?php
    // Kontrola, či vôbec máme nejaké položky
    if (!empty($menuItems)):

        // 2. Prechádzanie cez zoskupené položky (Káva, Zákusky, Čaj...)
        foreach ($menuItems as $category => $items):
            ?>
            <section class="menu-category mb-5">
                <h2 class="border-bottom pb-2 mb-4"><?= htmlspecialchars($category) ?></h2>

                <div class="row">
                    <?php
                    // 3. Prechádzanie cez jednotlivé položky v kategórii
                    foreach ($items as $item):
                        ?>
                        <div class="col-md-6 mb-3">
                            <div class="menu-item d-flex justify-content-between">
                                <div>
                                    <h3><?= htmlspecialchars($item['name']) ?></h3>
                                    <p class="text-muted"><?= htmlspecialchars($item['description']) ?></p>
                                </div>
                                <div class="price font-weight-bold">
                                    <?= htmlspecialchars(number_format($item['price'], 2, ',', ' ')) ?> €
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php
        endforeach;

    else:
        ?>
        <div class="alert alert-warning text-center">
            Ospravedlňujeme sa, menu sa momentálne pripravuje. Príďte neskôr!
        </div>
    <?php endif; ?>
</div>
