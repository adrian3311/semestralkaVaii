<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\MenuItem;
use App\Models\Post;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\Response;


class MenuController extends BaseController {
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->app->getAuth()->isLogged()) return false;
        if (in_array($action, ['edit', 'save', 'delete'])) {
            $id = (int)$request->value('id');
            $menu = Menu::getOne($id);
            if (is_null($menu)) {
                return true;
            }
            if ($menu->getAuthor() != $this->app->getAuth()->getUser()->getName()) {
                return false;
            }
        }
        return true;
    }

    public function index(Request $request): Response
    {
        try {
            return $this->html(
                [
                    'menu' => MenuItem::getAll()
                ]
            );
        } catch (Exception $e) {
            throw new HttpException(500, "DB Chyba: " . $e->getMessage());
        }
    }

    public function menu(Request $request): Response {
        return $this->html();
    }
    public function add(Request $request): Response
    {
        return $this->html();
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->value('id');
        $menu = MenuItem::getOne($id);

        if (is_null($menu)) {
            throw new HttpException(404);
        }
        return $this->html(compact('menu'));
    }
}