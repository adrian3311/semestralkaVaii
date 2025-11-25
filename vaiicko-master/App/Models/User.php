<?php

namespace App\Models;

use Framework\Core\IIdentity;

/**
 * Simple User value object representing an authenticated user.
 */
class User implements IIdentity
{
    public function __construct(
        public ?int $id = null,
        public string $username = '',
        public string $email = ''
    ) {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }


    public function getName(): string
    {
        return $this->username;
    }
}
