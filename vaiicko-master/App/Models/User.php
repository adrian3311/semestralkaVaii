<?php

namespace App\Models;

use Framework\Core\IIdentity;
use Framework\Core\Model;

/**
 * Simple User value object representing an authenticated user.
 */
class User extends Model implements IIdentity
{
    protected ?int $id = null;
    protected string $username = '';
    protected string $email = '';
    protected ?string $password = null;

    public function __construct(string $username = '', string $email = '') {
        $this->username = $username;
        $this->email = $email;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    public function getName(): string
    {
        return $this->username;
    }

    // ------------------- New helper methods -------------------
    /**
     * Find a user by username or email (case-insensitive). Returns a hydrated User or null.
     */
    public static function findByUsernameOrEmail(string $identifier): ?self
    {
        $id = trim($identifier);
        if ($id === '') {
            return null;
        }
        $idLower = mb_strtolower($id);
        $where = "(LOWER(`username`) = ? OR LOWER(`email`) = ?)";

        $rows = self::getAll($where, [$idLower, $idLower], null, 1, 0);

        return $rows[0] ?? null;
    }

    /**
     * Verify given plain password against this user's stored password.
     * Uses password_verify when stored hash looks like a PHP hash, otherwise falls back to direct equality
     * (compatibility mode; migrate to password_hash recommended).
     */
    public function verifyPassword(string $plain): bool
    {
        if ($this->password === null) {
            return false;
        }
        // prefer password_verify
        if (password_verify($plain, $this->password)) {
            return true;
        }
        // fallback for legacy plaintext storage
        $looksLikeHash = (bool)preg_match('/^\$2[ayb]\$|^\$argon2/', $this->password);
        if (!$looksLikeHash && hash_equals((string)$this->password, $plain)) {
            return true;
        }
        return false;
    }
}
