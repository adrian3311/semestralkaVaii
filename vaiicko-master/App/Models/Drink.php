<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Class Drink
 *
 * Model representing a drink/menu entry stored in the `drinks` table (or
 * a table with equivalent columns). The expected DB columns are:
 * - id (int) PRIMARY KEY
 * - title (varchar)  -> mapped to $title
 * - text (text)      -> mapped to $text
 * - picture (varchar)-> mapped to $picture (path relative to public/)
 *
 * The base Model class handles mapping between DB columns and model
 * properties using the project's naming conventions.
 */
class Drink extends Model
{
    /** @var int|null Primary key */
    protected ?int $id = null;

    /** @var string|null Title/short name of the drink */
    protected ?string $title = null;

    /** @var string|null Longer description or text for the drink */
    protected ?string $text = null;

    /** @var string|null Relative path to an image under public/ (e.g. 'images/foo.jpg') */
    protected ?string $picture = null;

    /** Get the ID */
    public function getId(): ?int
    {
        return $this->id;
    }

    /** Set the ID (usually managed by the DB) */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /** Get the title */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /** Set the title */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /** Get the text/description */
    public function getText(): ?string
    {
        return $this->text;
    }

    /** Set the text/description */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /** Get picture path */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /** Set picture path (relative to public/) */
    public function setPicture(?string $picture): void
    {
        $this->picture = $picture;
    }
}
