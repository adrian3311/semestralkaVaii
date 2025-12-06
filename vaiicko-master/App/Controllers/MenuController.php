<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\MenuItem;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class MenuController
 *
 * Controller responsible for displaying and managing menu items.
 *
 * Public actions include listing menu items, showing the menu page, and
 * CRUD operations (add, edit, delete). Add/Edit/Delete actions are restricted
 * to administrators (defined here as a logged-in user with username 'admin').
 *
 * Notes:
 * - File uploads (image) are handled in the add/edit actions and stored under
 *   `public/images/` by default. Uploaded filenames are sanitized and prefixed
 *   with a timestamp to avoid collisions.
 * - Deletion removes the database record first, then attempts to remove the
 *   corresponding picture from disk only if the DB deletion succeeded.
 */
class MenuController extends BaseController {

    /**
     * Authorization check for controller actions.
     *
     * This method is invoked by the framework before executing an action. It
     * returns true for permitted actions and false otherwise. By default all
     * actions are allowed, but `add`, `edit`, and `delete` are restricted to
     * an administrator user (a logged-in user whose username equals 'admin').
     *
     * @param Request $request The current HTTP request (contains session/user info).
     * @param string $action The name of the action being authorized (e.g. 'add').
     * @return bool True if the action is permitted, false otherwise.
     */
    public function authorize(Request $request, string $action): bool
    {
        // restrict add, edit, delete actions to admin users only
        if (in_array($action, ['add', 'edit', 'delete'])) {
            // consider admin to be a logged-in user with username 'admin'
            try {
                return $this->user->isLoggedIn() && ($this->user->getUsername() === 'admin');
            } catch (\Throwable $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * Display the menu index (list of menu items).
     *
     * Fetches all menu items from the database and renders them in the view.
     * On database error an HttpException with code 500 is thrown.
     *
     * @param Request $request The current HTTP request.
     * @return Response HTML response rendering the list of menu items.
     * @throws HttpException If the underlying DB query fails.
     */
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

    /**
     * Render the menu page.
     *
     * A simple action that renders the menu page (view). It does not perform
     * data mutations.
     *
     * @param Request $request The current HTTP request.
     * @return Response HTML response for the menu page.
     */
    public function menu(Request $request): Response {
        return $this->html();
    }

    /**
     * Add a new menu item.
     *
     * GET: Renders the add form.
     * POST: Processes submitted form data and creates a new MenuItem record.
     *
     * Behavior details:
     * - Reads properties from the request and populates the MenuItem model via
     *   `setFromRequest()`.
     * - Handles an optional uploaded file under the `picture` field: sanitizes
     *   the filename, stores the file into `public/images/` and saves a web path
     *   (e.g. 'images/123_photo.jpg') into the model.
     * - On success redirects to the menu index; on failure re-renders the form
     *   with an error message and the partially populated model for re-display.
     *
     * Security/validation notes:
     * - Uploaded file handling is basic; in production add checks for file type,
     *   size limits and virus scanning.
     * - The method currently trusts `setFromRequest()` to perform necessary
     *   sanitization/validation — consider adding explicit server-side
     *   validation for required fields.
     *
     * @param Request $request The current HTTP request (GET or POST with files).
     * @return Response Redirect on success or HTML form view on GET / on error.
     */
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

    /**
     * Edit an existing menu item.
     *
     * GET: Renders the edit form for the specified item id (query param `id`).
     * POST: Updates the model from request data and persists changes.
     *
     * Behavior details:
     * - If an uploaded `picture` is present and valid, the file is stored and
     *   the model's picture path is updated.
     * - On success redirects to the menu index; on error re-renders the form
     *   with an error message.
     *
     * Error conditions:
     * - If the requested menu item id does not exist, a 404 HttpException is thrown.
     *
     * @param Request $request The current HTTP request (contains `id` when GET/POST).
     * @return Response Redirect on success or HTML edit form view.
     * @throws HttpException If the specified menu item does not exist.
     */
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
