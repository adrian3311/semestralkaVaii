<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Class Review
 *
 * Simple model representing a review or comment left by a user.
 */
class Review extends Model
{
    protected ?int $id = null;
    protected string $username = '';
    protected ?string $text = null;
    // 1..5 star rating
    protected ?int $rating = 1;

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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): void
    {
        if ($rating === null) {
            $this->rating = null;
            return;
        }
        $r = (int)$rating;
        if ($r < 1) { $r = 1; }
        if ($r > 5) { $r = 5; }
        $this->rating = $r;
    }
}
