<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Class Review
 *
 * Simple model representing a review or comment left by a user.
 *
 * Fields:
 * - id (int|null): primary key
 * - username (string): name of the user who left the review
 * - text (string|null): review text/content
 * - rating (int|null): numeric rating in the range 1..5 (null means no rating)
 *
 * Notes:
 * - Views should escape review text when rendering to prevent XSS.
 * - Rating is normalized on setter to ensure it remains within 1..5.
 */
class Review extends Model
{
    protected ?int $id = null;
    protected string $username = '';
    protected ?string $text = null;
    // 1..5 star rating
    protected ?int $rating = 1;

    /**
     * Get the review id.
     *
     * @return int|null The primary key or null for unsaved instances.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the review id.
     *
     * Generally the id is managed by the persistence layer; accept null for
     * new instances.
     *
     * @param int|null $id
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Return the username of the author of this review.
     *
     * @return string The username (never null)
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Set the username for this review.
     *
     * @param string $username Username of the review author
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Get the review text/content.
     *
     * @return string|null The review content or null if none provided.
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Set the review text/content.
     *
     * Note: The view layer is responsible for escaping output to avoid XSS.
     *
     * @param string|null $text
     * @return void
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * Get the numeric rating for this review.
     *
     * @return int|null Rating between 1 and 5 inclusive, or null for no rating.
     */
    public function getRating(): ?int
    {
        return $this->rating;
    }

    /**
     * Set the numeric rating for this review.
     *
     * The setter normalizes the value:
     * - null is allowed and means "no rating";
     * - numeric values are cast to int and clamped to the 1..5 range.
     *
     * @param int|null $rating
     * @return void
     */
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
