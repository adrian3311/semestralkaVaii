<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\Review;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class ReviewController extends BaseController
{
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

    public function index(Request $request): Response
    {
        // allow anyone (including anonymous) to view the reviews list
        try {
            return $this->html(['reviews' => Review::getAll()]);
         } catch (Exception $e) {
             throw new HttpException(500, 'DB Chyba: ' . $e->getMessage());
         }
     }

    public function menu(Request $request): Response {
        return $this->html();
    }

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
                    $logsDir = __DIR__ . '/../../App/logs/';
                    if (!is_dir($logsDir)) { @mkdir($logsDir, 0755, true); }
                    $msg = '[' . date('Y-m-d H:i:s') . "] Failed to delete review id={$review->getId()} - " . $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
                    @file_put_contents($logsDir . 'delete_errors.log', $msg, FILE_APPEND | LOCK_EX);
                } catch (\Throwable $ignore) {}

                $message = 'Chyba pri mazani: ' . $e->getMessage();
                return $this->html(compact('review', 'message'));
            }
        }

        return $this->html(compact('review'));
    }
}
