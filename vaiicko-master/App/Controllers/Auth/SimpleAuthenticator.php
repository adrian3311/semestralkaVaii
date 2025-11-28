<?php

namespace App\Controllers\Auth;

use App\Models\User;
use Framework\Auth\SessionAuthenticator;
use Framework\Core\App;
use Framework\Core\IIdentity;

class SimpleAuthenticator extends SessionAuthenticator
{
    protected App $app;

    public function __construct(App $app)
    {
        $this->app = $app;
        parent::__construct($app);
    }

    public function login(string $username, string $password): bool
    {
        if ($username === '' && $password === '') {
            $req = $this->app->getRequest();
            $username = (string)$req->value('username');
            $password = (string)$req->value('password');
        }
        return parent::login($username, $password);
    }

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
