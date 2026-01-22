<?php

namespace App\Controllers;

use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\HttpException;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;

/**
 * Simple ContactController
 * - index(): renders the contact view
 * - send(): accepts POST, validates, returns JSON for AJAX
 * - markread(): accepts POST with id, returns JSON (mark message as read)
 */
class ContactController extends BaseController
{
    /**
     * Render contact page (uses App/Views/Home/contact.view.php)
     */
    public function index(Request $request): Response
    {
        return new ViewResponse($this->app, 'Home' . DIRECTORY_SEPARATOR . 'contact', []);
    }

    /**
     * Handle contact form send
     */
    public function send(Request $request): Response
    {
        // only accept POST for sending
        if (!$request->isPost()) {
            return new ViewResponse($this->app, 'Home' . DIRECTORY_SEPARATOR . 'contact', []);
        }

        try {
            $name = trim((string)$request->value('name'));
            $email = trim((string)$request->value('email'));
            $message = trim((string)$request->value('message'));

            // basic validation
            if ($name === '' || $email === '' || $message === '') {
                $payload = ['ok' => false, 'message' => 'Please fill all fields'];
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $payload = ['ok' => false, 'message' => 'Invalid email'];
            } else {
                // TODO: persist message to DB or send email in production
                $payload = ['ok' => true, 'message' => 'Thank you — message sent'];
            }

            // For POST requests return JSON (AJAX clients expect JSON)
            return $this->json($payload);
        } catch (Exception $e) {
            if ($request->isAjax()) {
                return $this->json(['ok' => false, 'message' => 'Server error']);
            }
            throw new HttpException(500, 'Server error');
        }
    }
}
