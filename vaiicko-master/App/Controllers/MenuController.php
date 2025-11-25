<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\MenuItem;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\Response;


class MenuController extends BaseController {

    public function authorize(Request $request, string $action): bool
    {
        // restrict add and edit actions to logged-in users
        if (in_array($action, ['add', 'edit', 'delete'])) {
            return $this->user->isLoggedIn();
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
        // If form submitted (POST), create and save new MenuItem
        if ($request->isPost() && $request->hasValue('submit')) {
            try {
                $menuItem = new MenuItem();
                // populate properties from request; Model::setFromRequest expects property names
                $menuItem->setFromRequest($request);

                // handle optional file upload 'picture'
                $file = $request->file('picture');
                if ($file !== null && $file->isOk()) {
                    // move uploaded file to public images directory (simple approach)
                    $uploadsDir = realpath(__DIR__ . '/../../public') . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                    if ($uploadsDir === false) {
                        // fallback to path construction
                        $uploadsDir = __DIR__ . '/../../public/images/';
                    }
                    if (!is_dir($uploadsDir)) {
                        @mkdir($uploadsDir, 0755, true);
                    }
                    $safeName = preg_replace('/[^a-z0-9._-]/i', '_', $file->getName());
                    $filename = time() . '_' . $safeName;
                    // use store() provided by UploadedFile
                    $stored = $file->store($uploadsDir . $filename);
                    if ($stored) {
                        // store web path relative to public
                        $menuItem->setPicture('images/' . $filename);
                    }
                }

                $menuItem->save();
                return $this->redirect($this->url('menu.index'));
            } catch (Exception $e) {
                // On failure show error message in form
                $message = 'Chyba pri ukladaní: ' . $e->getMessage();
                // return menu back to view to repopulate fields
                return $this->html(compact('message', 'menuItem'));
            }
        }

        // GET -> show add form
        $menuItem = new MenuItem();
        return $this->html(compact('menuItem'));
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->value('id');
        $menu = MenuItem::getOne($id);

        if (is_null($menu)) {
            throw new HttpException(404);
        }

        // If POST, update and save
        if ($request->isPost() && $request->hasValue('submit')) {
            try {
                $menu->setFromRequest($request);

                $file = $request->file('picture');
                if ($file !== null && $file->isOk()) {
                    $uploadsDir = realpath(__DIR__ . '/../../public') . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR;
                    if ($uploadsDir === false) {
                        $uploadsDir = __DIR__ . '/../../public/images/';
                    }
                    if (!is_dir($uploadsDir)) {
                        @mkdir($uploadsDir, 0755, true);
                    }
                    $safeName = preg_replace('/[^a-z0-9._-]/i', '_', $file->getName());
                    $filename = time() . '_' . $safeName;
                    $stored = $file->store($uploadsDir . $filename);
                    if ($stored) {
                        $menu->setPicture('images/' . $filename);
                    }
                }

                $menu->save();
                return $this->redirect($this->url('menu.index'));
            } catch (Exception $e) {
                $message = 'Chyba pri ukladaní: ' . $e->getMessage();
                return $this->html(compact('menu', 'message'));
            }
        }

        return $this->html(compact('menu'));
    }

    /**
     * Delete confirmation and action
     * GET -> show confirmation view
     * POST (confirm) -> perform deletion and redirect
     */
    public function delete(Request $request): Response
    {
        $id = (int)$request->value('id');
        $menu = MenuItem::getOne($id);

        if (is_null($menu)) {
            throw new HttpException(404);
        }

        // If confirmed via POST => delete
        if ($request->isPost() && $request->hasValue('confirm')) {
            try {
                // First delete DB record
                $menu->delete();

                // Only if DB delete succeeded, remove picture file from disk if exists
                $picture = $menu->getPicture();
                if (!empty($picture)) {
                    $publicDir = realpath(__DIR__ . '/../../public');
                    if ($publicDir !== false) {
                        $filePath = $publicDir . DIRECTORY_SEPARATOR . ltrim($picture, '/\\');
                        if (is_file($filePath)) {
                            @unlink($filePath);
                        }
                    }
                }

                return $this->redirect($this->url('menu.index'));
            } catch (Exception $e) {
                // Log exception to file for debugging
                try {
                    $logsDir = __DIR__ . '/../../App/logs/';
                    if (!is_dir($logsDir)) {
                        @mkdir($logsDir, 0755, true);
                    }
                    $logFile = $logsDir . 'delete_errors.log';
                    $logMsg = '[' . date('Y-m-d H:i:s') . "] Failed to delete menu id={$menu->getId()} - " . get_class($e) . ": " . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
                    @file_put_contents($logFile, $logMsg, FILE_APPEND | LOCK_EX);
                } catch (\Throwable $ignore) {
                    // ignore logging errors
                }

                $message = 'Chyba pri mazani: ' . $e->getMessage();
                return $this->html(compact('menu', 'message'));
            }
        }

        // show confirmation form
        return $this->html(compact('menu'));
    }
}

