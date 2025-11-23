<?php

namespace App\Controllers;

use Framework\View;
// Ak máš nejaký Base Controller, ktorý dedíš, nezabudni ho importovať/dediť

class MenuController
{
    /**
     * Zobrazí hlavnú stránku s ponukou.
     */
    public function indexAction()
    {
        // TOTO JE DOČASNÁ TESTOVACIA LOGIKA BEZ DB
        $testItems = [
            'Káva' => [
                ['name' => 'Espresso', 'description' => 'Krátke a silné', 'price' => 1.90],
            ],
            'Zákusky' => [
                ['name' => 'Cheesecake', 'description' => 'Domáca klasika', 'price' => 3.50],
            ],
        ];

        // RENDERUJEME VIEW (šablónu) 'menu/index.view.php'
        View::render('Menu/index', [
            'title' => 'Naša Ponuka',
            'menuItems' => $testItems
        ]);
    }
}