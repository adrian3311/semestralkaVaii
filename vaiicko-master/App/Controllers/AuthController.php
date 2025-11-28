<?php

namespace App\Controllers;

use App\Configuration;
use App\Models\User;
use Exception;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Responses\ViewResponse;

/**
 * Class AuthController
 *
 * This controller handles authentication actions such as login, logout, and redirection to the login page. It manages
 * user sessions and interactions with the authentication system.
 *
 * @package App\Controllers
 */
class AuthController extends BaseController
{
    /**
     * Redirects to the login page.
     *
     * This action serves as the default landing point for the authentication section of the application, directing
     * users to the login URL specified in the configuration.
     *
     * @return Response The response object for the redirection to the login page.
     */
    public function index(Request $request): Response
    {
        return $this->redirect(Configuration::LOGIN_URL);
    }

    /**
     * Authenticates a user and processes the login request.
     *
     * This action handles user login attempts. If the login form is submitted, it attempts to authenticate the user
     * with the provided credentials. Upon successful login, the user is redirected to the admin dashboard.
     * If authentication fails, an error message is displayed on the login page.
     *
     * @return Response The response object which can either redirect on success or render the login view with
     *                  an error message on failure.
     * @throws Exception If the parameter for the URL generator is invalid throws an exception.
     */
    public function login(Request $request): Response
    {
        $logged = null;
        if ($request->hasValue('submit')) {
            $logged = $this->app->getAuthenticator()->login($request->value('username'), $request->value('password'));
            if ($logged) {
                return $this->redirect($this->url("admin.index"));
            }
        }

        // Show message if redirected here after registration
        $message = null;
        if ($request->value('registered')) {
            $message = 'Registration successful — please log in.';
        } else {
            $message = $logged === false ? 'Bad username or password' : null;
        }
        return $this->html(compact("message"));
    }

    /**
     * Logs out the current user.
     *
     * This action terminates the user's session and redirects them to a view. It effectively clears any authentication
     * tokens or session data associated with the user.
     *
     * @return ViewResponse The response object that renders the logout view.
     */
    public function logout(Request $request): Response
    {
        $this->app->getAuthenticator()->logout();
        return $this->html();
    }

    /**
     * Register new user (simple implementation).
     */
    public function register(Request $request): Response
    {
        $message = null;
        $old = ['username' => '', 'email' => ''];
        if ($request->hasValue('submit')) {
            $username = trim((string)$request->value('username'));
            $email = trim((string)$request->value('email'));
            $password = (string)$request->value('password');
            $passwordConfirm = (string)$request->value('password_confirm');

            // keep entered values for re-display (do NOT include passwords)
            $old['username'] = $username;
            $old['email'] = $email;

            // Basic validation
            if ($username === '' || $email === '' || $password === '') {
                $message = 'All fields are required.';
                return $this->html(compact('message', 'old'));
            }
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $message = 'Invalid email address.';
                return $this->html(compact('message', 'old'));
            }
            if (strlen($password) < 6) {
                $message = 'Password must be at least 6 characters.';
                return $this->html(compact('message', 'old'));
            }
            if ($password !== $passwordConfirm) {
                $message = 'Passwords do not match.';
                return $this->html(compact('message', 'old'));
            }

            // Check uniqueness
            try {
                $exists = User::findByUsernameOrEmail($username) ?? User::findByUsernameOrEmail($email);
                if ($exists !== null) {
                    $message = 'Username or email already in use.';
                    return $this->html(compact('message', 'old'));
                }
            } catch (\Throwable $e) {
                // ignore and allow attempt to create (but warn)
            }

            // Create user
            try {
                $user = new User();
                $user->setUsername($username);
                $user->setEmail($email);
                // Hash password
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $user->setPassword($hash);
                $user->save();

                // Redirect to login with success flag
                return $this->redirect(Configuration::LOGIN_URL . '&registered=1');
            } catch (Exception $e) {
                $message = 'Registration failed: ' . $e->getMessage();
                return $this->html(compact('message', 'old'));
            }
        }

        return $this->html(compact('message', 'old'));
    }
}
