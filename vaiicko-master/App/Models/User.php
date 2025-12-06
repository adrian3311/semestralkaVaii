<?php

namespace App\Models;

use Framework\Core\IIdentity;
use Framework\Core\Model;

/**
 * Class User
 *
 * Simple User model representing an application user / identity.
 * This model provides basic getters/setters and two helper methods used by
 * the authentication layer: `findByUsernameOrEmail` and `verifyPassword`.
 *
 * Notes:
 * - Passwords should be stored as secure hashes (e.g. produced by password_hash()).
 * - `verifyPassword` supports legacy plaintext values as a fallback but it's
 *   recommended to migrate stored passwords to modern hashes.
 *
 * @package App\Models
 */
class User extends Model implements IIdentity
{
    protected ?int $id = null;
    protected string $username = '';
    protected string $email = '';
    protected ?string $password = null;

    /**
     * User constructor.
     *
     * @param string $username Optional username to initialize the model with.
     * @param string $email Optional email to initialize the model with.
     */
    public function __construct(string $username = '', string $email = '') {
        $this->username = $username;
        $this->email = $email;
    }

    /**
     * Get the primary key identifier.
     *
     * @return int|null The user id or null if not persisted.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the primary key identifier.
     *
     * @param int|null $id The id value or null.
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the username.
     *
     * @return string The username (may be empty string for new models).
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set the username.
     *
     * @param string $username The username to set.
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Get the email address.
     *
     * @return string The user's email.
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the email address.
     *
     * @param string $email The email to set.
     * @return void
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Get stored password value (hash or legacy plaintext).
     *
     * Note: For security don't expose this in APIs. Used internally for verification.
     *
     * @return string|null The stored password/hash or null.
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * Set stored password value.
     *
     * The caller is responsible for hashing passwords before calling this
     * method (e.g. password_hash()). This method accepts null to clear the value.
     *
     * @param string|null $password Hashed password or null.
     * @return void
     */
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * Get display name for the identity.
     * Implements IIdentity::getName().
     *
     * @return string The display name (username).
     */
    public function getName(): string
    {
        return $this->username;
    }

    // ------------------- New helper methods -------------------
    /**
     * Find a user by username or email (case-insensitive).
     *
     * Performs a case-insensitive lookup against the `username` and `email`
     * columns and returns the first matching User instance or null.
     *
     * @param string $identifier Username or email to search for.
     * @return self|null The hydrated User model or null if no match found.
     */
    public static function findByUsernameOrEmail(string $identifier): ?self
    {
        $id = trim($identifier);
        if ($id === '') {
            return null;
        }
        $idLower = mb_strtolower($id);
        $where = "(LOWER(`username`) = ? OR LOWER(`email`) = ? )";

        $rows = self::getAll($where, [$idLower, $idLower], null, 1, 0);

        return $rows[0] ?? null;
    }

    /**
     * Verify a plain text password against the stored password value.
     *
     * Preferred behavior is to use `password_verify()` when the stored value is
     * a modern PHP hash. For backward compatibility this method falls back to a
     * constant-time comparison (hash_equals) if the stored value does not look
     * like a PHP password hash. Applications should migrate stored passwords
     * to `password_hash()` as soon as possible.
     *
     * @param string $plain Plain text password provided by the user.
     * @return bool True when the password matches, false otherwise.
     */
    public function verifyPassword(string $plain): bool
    {
        if ($this->password === null) {
            return false;
        }
        // prefer password_verify for modern hashes
        if (@password_verify($plain, $this->password)) {
            return true;
        }
        // fallback for legacy plaintext or non-standard storage
        $looksLikeHash = (bool)preg_match('/^\$2[ayb]\$|^\$argon2/', $this->password);
        if (!$looksLikeHash && hash_equals((string)$this->password, $plain)) {
            return true;
        }
        return false;
    }
}
