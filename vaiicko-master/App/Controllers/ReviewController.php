<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\Review;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

/**
 * Class ReviewController
 *
 * Handles listing and CRUD operations for reviews.
 * Public pages (index) are viewable by anyone. Adding a review requires the
 * user to be logged in. Editing and deleting are restricted to administrators
 * or the original review author (owner).
 */
class ReviewController extends BaseController
{
    /**
     * Authorization check for review actions.
     *
     * Rules:
     * - 'add' requires an authenticated user.
     * - 'edit' and 'delete' are allowed for admin (username == 'admin') or the
     *   original author of the review.
     * - all other actions are permitted.
     *
     * @param Request $request Current HTTP request (used to read query values).
     * @param string $action Action name being authorized (e.g. 'add', 'edit').
     * @return bool True if the current user may perform the action, false otherwise.
     */
    public function authorize(Request $request, string $action): bool
    {
        // allow any logged-in user to add reviews; edit/delete remain admin-only or owner-only
        if ($action === 'add') {
            return $this->user->isLoggedIn();
        }
        if (in_array($action, ['edit', 'delete'])) {
            // if admin, allow
            try {
                if ($this->user->isLoggedIn() && ($this->user->getUsername() === 'admin')) {
                    return true;
                }
            } catch (\Throwable $e) {
                // continue to owner check
            }

            // owner check: allow if logged-in user is the one who created the review
            try {
                if (!$this->user->isLoggedIn()) {
                    return false;
                }
                $id = (int)$request->value('id');
                if ($id <= 0) {
                    return false;
                }
                $review = Review::getOne($id);
                if ($review === null) {
                    return false;
                }
                $owner = $review->getUsername();
                $current = $this->user->getName() ?? $this->user->getUsername();
                return $current !== null && $owner === $current;
            } catch (\Throwable $e) {
                return false;
            }
        }
        return true;
    }

    /**
     * Display a list of reviews.
     *
     * This action fetches all reviews and renders the index view. It is
     * accessible to anonymous users. Database errors are translated into a
     * 500 HttpException so the framework can handle the response.
     *
     * @param Request $request The current HTTP request.
     * @return Response HTML response rendering the reviews list.
     * @throws HttpException On underlying database error.
     */
    public function index(Request $request): Response
    {
        // allow anyone (including anonymous) to view the reviews list
        try {
            // support sort parameter: 'new' (default) or 'old'
            $sort = strtolower((string)($request->value('sort') ?? 'new'));
            if ($sort === 'old') {
                $orderBy = '`id` ASC'; // oldest first by primary key (insertion order)
            } else {
                $orderBy = '`id` DESC'; // newest first by primary key
                $sort = 'new';
            }
            return $this->html(['reviews' => Review::getAll(null, [], $orderBy), 'sort' => $sort]);
         } catch (Exception $e) {
             throw new HttpException(500, 'DB Chyba: ' . $e->getMessage());
         }
     }

    /**
     * Render a simple menu view.
     *
     * This action renders the menu page and does not perform data changes.
     *
     * @param Request $request The current HTTP request.
     * @return Response HTML response for the menu page.
     */
    public function menu(Request $request): Response {
        return $this->html();
    }

    /**
     * Add a new review.
     *
     * GET: renders the add form.
     * POST: validates and saves a new review. The method sets the username from
     * the currently logged-in user and ensures rating is an integer if provided.
     * On success the user is redirected to the review index.
     *
     * Security and behavior notes:
     * - Only authenticated users may add reviews (redirects to login when not).
     * - Server-side validation should be applied in the model (not shown here).
     *
     * @param Request $request The current HTTP request (GET or POST payload).
     * @return Response Redirect on success or the add form view on GET/error.
     */
    public function add(Request $request): Response
    {
        // require login
        if (!$this->user->isLoggedIn()) {
            return $this->redirect(Configuration::LOGIN_URL);
        }

        if ($request->isPost() && $request->hasValue('submit')) {
            try {
                $review = new Review();
                // populate text from request
                $review->setFromRequest($request);
                // rating from request (optional) - ensure integer 1..5
                $ratingVal = $request->value('rating');
                if ($ratingVal !== null) {
                    $review->setRating((int)$ratingVal);
                }
                // set username from logged-in user
                $review->setUsername($this->user->getName());
                $review->save();
                 return $this->redirect($this->url('review.index'));
             } catch (Exception $e) {
                $message = 'Chyba pri ukladaní: ' . $e->getMessage();
                return $this->html(compact('message'));
             }
         }

        $review = new Review();
        return $this->html(compact('review'));
    }

    /**
     * Edit an existing review.
     *
     * GET: renders the edit form for a given review id.
     * POST: updates the review and persists changes.
     *
     * Permission: only admin or the review owner may edit.
     *
     * @param Request $request The current HTTP request (should contain 'id').
     * @return Response Redirect on success or the edit form view.
     * @throws HttpException If the review does not exist (404).
     */
    public function edit(Request $request): Response
    {
        $id = (int)$request->value('id');
        $review = Review::getOne($id);
        if (is_null($review)) {
            throw new HttpException(404);
        }

        if ($request->isPost() && $request->hasValue('submit')) {
            try {
                $review->setFromRequest($request);
                $ratingVal = $request->value('rating');
                if ($ratingVal !== null) {
                    $review->setRating((int)$ratingVal);
                }
                $review->save();
                 return $this->redirect($this->url('review.index'));
             } catch (Exception $e) {
                $message = 'Chyba pri ukladaní: ' . $e->getMessage();
                return $this->html(compact('review', 'message'));
             }
         }

        return $this->html(compact('review'));
    }

    /**
     * Delete a review (with confirmation flow).
     *
     * GET: shows a confirmation view for deletion.
     * POST with 'confirm' value: attempts to delete the review record and
     * redirects to the reviews list on success. Errors during deletion are
     * logged and an error message is displayed.
     *
     * Permission: only admin or the review owner may delete.
     *
     * @param Request $request The current HTTP request (should contain 'id').
     * @return Response Redirect on successful deletion or confirmation view.
     * @throws HttpException If the review does not exist (404).
     */
    public function delete(Request $request): Response
    {
        $id = (int)$request->value('id');
        $review = Review::getOne($id);
        if (is_null($review)) {
            throw new HttpException(404);
        }

        if ($request->isPost() && $request->hasValue('confirm')) {
            try {
                $review->delete();
                return $this->redirect($this->url('review.index'));
            } catch (Exception $e) {
                try {
                    /*$logsDir = __DIR__ . '/../../App/logs/';
                    if (!is_dir($logsDir)) { @mkdir($logsDir, 0755, true); }
                    $msg = '[' . date('Y-m-d H:i:s') . "] Failed to delete review id={$review->getId()} - " . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
                    @file_put_contents($logsDir . 'delete_errors.log', $msg, FILE_APPEND | LOCK_EX);*/
                } catch (\Throwable $ignore) {}

                $message = 'Chyba pri mazani: ' . $e->getMessage();
                return $this->html(compact('review', 'message'));
            }
        }

        return $this->html(compact('review'));
    }
}
