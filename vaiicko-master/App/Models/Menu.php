<?php

namespace App\Models;

// Predpokladáme, že tvoj Framework má nejakú základnú triedu Model,
// ktorá poskytuje prístup k DB pripojeniu.
use Framework\Model;

class MenuItemModel extends Model
{
    /**
     * Názov tabuľky v databáze, s ktorou model pracuje.
     * Predpokladáme, že tabuľku si pomenoval "menu_items".
     */
    protected $table = 'menu_items';

    /**
     * Vracia všetky dostupné položky z menu usporiadané podľa kategórie.
     * @return array Zoskupené položky (napr. ['Káva' => [...], 'Zákusky' => [...]])
     */
    public function getAllGroupedByCategory()
    {
        // SQL dotaz na načítanie dostupných položiek
        $sql = "SELECT id, name, description, price, category FROM {$this->table} WHERE is_available = TRUE ORDER BY category, name";

        // Predpokladáme, že metóda 'fetchAll' zabezpečí komunikáciu s DB
        // a vráti pole výsledkov.
        $items = $this->db->fetchAll($sql);

        // Zoskupenie položiek podľa kategórie
        $grouped = [];
        foreach ($items as $item) {
            $grouped[$item['category']][] = $item;
        }

        return $grouped;
    }

    /**
     * Príklad metódy pre načítanie jednej položky podľa ID.
     * public function getById($id)
     * {
     * return $this->db->fetchOne("SELECT * FROM {$this->table} WHERE id = ?", [$id]);
     * }
     */
}