<?php

namespace App\Controllers\Auth;

use App\Models\User;
use Framework\Auth\SessionAuthenticator;
use Framework\Core\App;
use Framework\Core\IIdentity;

/**
 * SimpleAuthenticator
 *
 * Lightweight authenticator that extends the session-based authenticator and
 * verifies credentials against the application's `users` table via the
 * `App\Models\User` model.
 *
 * Responsibilities:
 * - Collect username/password from the method arguments or from the current
 *   HTTP request when arguments are empty.
 * - Delegate session handling and login flow to the parent `SessionAuthenticator`.
 * - Use `User::findByUsernameOrEmail()` and `User::verifyPassword()` to
 *   authenticate credentials.
 *
 * Notes:
 * - This class adapts the framework authenticator to the app's User model.
 * - Returned identities implement `Framework\Core\IIdentity` (User model).
 */
class SimpleAuthenticator extends SessionAuthenticator
{
    /** @var App Application instance used to access request and services */
    protected App $app;

    /**
     * SimpleAuthenticator constructor.
     *
     * @param App $app The application container (gives access to request, DB, etc.)
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct($app);
    }

    /**
     * Attempt to log in a user.
     *
     * Behavior:
     * - If both `$username` and `$password` arguments are empty strings, this
     *   method will read `username` and `password` from the current HTTP
     *   request (useful when the controller calls login() without parameters).
     * - The actual session creation / login flow is handled by
     *   `SessionAuthenticator::login()` so this method delegates to `parent::login()`.
     *
     * @param string $username Username or email (may be empty to read from request)
     * @param string $password Plaintext password (may be empty to read from request)
     * @return bool True when login succeeded; false otherwise.
     */
    public function login(string $username, string $password): bool
    {
        if ($username === '' && $password === '') {
            $req = $this->app->getRequest();
            $username = (string)$req->value('username');
            $password = (string)$req->value('password');
        }
        return parent::login($username, $password);
    }

    /**
     * Authenticate provided credentials against the application's user store.
     *
     * This method is invoked by the parent authenticator as the concrete
     * credential-checking step. It looks up the user by username or email and
     * verifies the password using the `User` model's verification method.
     *
     * @param string $username Identifier provided by the user (username or email)
     * @param string $password Plaintext password to verify
     * @return IIdentity|null Returns an identity (User) when credentials match,
     *                        or null on failure.
     */
    protected function authenticate(string $username, string $password): ?IIdentity
    {
        $identifier = trim($username);
        if ($identifier === '') {
            return null;
        }

        $user = User::findByUsernameOrEmail($identifier);
        if ($user === null) {
            return null;
        }

        if (!$user->verifyPassword($password)) {
            return null;
        }

        return $user;
    }
}
