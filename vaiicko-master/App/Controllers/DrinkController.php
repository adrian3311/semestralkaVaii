<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\Drink;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Controller responsible for CRUD operations for Drink entities.
 *
 * Behavior:
 * - index(): lists all drinks (public)
 * - add(): creates a new drink (admin-only)
 * - edit(): updates an existing drink (admin-only)
 * - delete(): deletes a drink (admin-only)
 *
 * Image uploads are handled in add/edit and stored under public/images/; the
 * database stores the relative path (e.g. 'images/foo.jpg'). Authorization
 * follows the convention that the user with username 'admin' is the site
 * administrator.
 */
class DrinkController extends BaseController
{
    /**
     * Authorization rules for Drink actions.
     *
     * - 'add', 'edit', 'delete' require the user to be an administrator
     *   (username === 'admin'). Other actions are allowed for everyone.
     */
    public function authorize(Request $request, string $action): bool
    {
        // restrict add, edit and delete to administrators (username 'admin')
        if (in_array($action, ['add', 'edit', 'delete'])) {
            try {
                return $this->user->isLoggedIn() && ($this->user->getUsername() === 'admin');
            } catch (\Throwable $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * Show list of drinks.
     */
    public function index(Request $request): Response
    {
        try {
            return $this->html(['drinks' => Drink::getAll()]);
        } catch (Exception $e) {
            throw new HttpException(500, 'DB Chyba: ' . $e->getMessage());
        }
    }

    /**
     * Add new drink.
     * GET: show form. POST: validate, handle optional picture upload and save.
     */
    public function add(Request $request): Response
    {
        if ($request->isPost() && $request->hasValue('submit')) {
            try {
                $drink = new Drink();
                // populate fields (expects 'title' and 'text' from the form)
                $drink->setFromRequest($request);

                $file = $request->file('picture');
                if ($file !== null && $file->isOk()) {
                    $uploadsDir = realpath(__DIR__ . '/../../public') . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                    if ($uploadsDir === false) {
                        $uploadsDir = __DIR__ . '/../../public/images/';
                    }
                    if (!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0755, true); }
                    $safeName = preg_replace('/[^a-z0-9._-]/i', '_', $file->getName());
                    $filename = time() . '_' . $safeName;
                    $stored = $file->store($uploadsDir . $filename);
                    if ($stored) {
                        $drink->setPicture('images/' . $filename);
                    }
                }

                $drink->save();
                return $this->redirect($this->url('drink.index'));
            } catch (Exception $e) {
                $message = 'Chyba pri ukladaní: ' . $e->getMessage();
                return $this->html(compact('message', 'drink'));
            }
        }

        $drink = new Drink();
        return $this->html(compact('drink'));
    }

    /**
     * Edit existing drink.
     */
    public function edit(Request $request): Response
    {
        $id = (int)$request->value('id');
        $drink = Drink::getOne($id);
        if (is_null($drink)) { throw new HttpException(404); }

        if ($request->isPost() && $request->hasValue('submit')) {
            try {
                // expecting 'title' and 'text' from the form
                $drink->setFromRequest($request);

                $file = $request->file('picture');
                if ($file !== null && $file->isOk()) {
                    $uploadsDir = realpath(__DIR__ . '/../../public') . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                    if ($uploadsDir === false) {
                        $uploadsDir = __DIR__ . '/../../public/images/';
                    }
                    if (!is_dir($uploadsDir)) { @mkdir($uploadsDir, 0755, true); }
                    $safeName = preg_replace('/[^a-z0-9._-]/i', '_', $file->getName());
                    $filename = time() . '_' . $safeName;
                    $stored = $file->store($uploadsDir . $filename);
                    if ($stored) {
                        $drink->setPicture('images/' . $filename);
                    }
                }

                $drink->save();
                return $this->redirect($this->url('drink.index'));
            } catch (Exception $e) {
                $message = 'Chyba pri ukladaní: ' . $e->getMessage();
                return $this->html(compact('drink', 'message'));
            }
        }

        return $this->html(compact('drink'));
    }

    /**
     * Delete a drink (confirmation flow).
     */
    public function delete(Request $request): Response
    {
        $id = (int)$request->value('id');
        $drink = Drink::getOne($id);
        if (is_null($drink)) { throw new HttpException(404); }

        if ($request->isPost() && $request->hasValue('confirm')) {
            try {
                $drink->delete();

                $picture = $drink->getPicture();
                if (!empty($picture)) {
                    $publicDir = realpath(__DIR__ . '/../../public');
                    if ($publicDir !== false) {
                        $filePath = $publicDir . DIRECTORY_SEPARATOR . ltrim($picture, '/\\');
                        if (is_file($filePath)) { @unlink($filePath); }
                    }
                }

                return $this->redirect($this->url('drink.index'));
            } catch (Exception $e) {
                try {
                    $logsDir = __DIR__ . '/../../App/logs/';
                    if (!is_dir($logsDir)) { @mkdir($logsDir, 0755, true); }
                    $msg = '[' . date('Y-m-d H:i:s') . "] Failed to delete drink id={$drink->getId()} - " . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
                    @file_put_contents($logsDir . 'delete_errors.log', $msg, FILE_APPEND | LOCK_EX);
                } catch (\Throwable $ignore) {}

                $message = 'Chyba pri mazani: ' . $e->getMessage();
                return $this->html(compact('drink', 'message'));
            }
        }

        return $this->html(compact('drink'));
    }
}
