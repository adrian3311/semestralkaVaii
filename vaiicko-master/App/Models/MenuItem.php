<?php

namespace App\Models;

use Framework\Core\Model;

/**
 * Class MenuItem
 *
 * Represents a single entry on the site's menu.
 * This model stores basic content with an optional picture path. The model
 * is a simple data container with getters and setters for persistence via the
 * framework's Model base class.
 *
 * Properties:
 * @property int|null $id      Primary key identifier
 * @property string|null $title  Human-readable title of the menu item
 * @property string|null $text   Description or content for the menu item
 * @property string|null $picture Relative path to a picture (e.g. 'images/foo.jpg')
 */
class MenuItem extends Model
{

    protected ?int $id = null;
    protected ?string $title = null;
    protected ?string $text = null;
    protected ?string $picture = null;


    /**
     * Get the ID of the menu item.
     *
     * @return int|null The primary key or null for a new, unsaved item.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set the ID of the menu item.
     *
     * Normally this is managed by the database / ORM. Accepts null when the
     * item is not yet persisted.
     *
     * @param int|null $id The primary key value or null.
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the title of the menu item.
     *
     * @return string|null The title or null if not set.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Set the title of the menu item.
     *
     * @param string|null $title The title to set (trimmed and validated by caller).
     * @return void
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get the descriptive text for the menu item.
     *
     * @return string|null The item's description or null.
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * Set the descriptive text for the menu item.
     *
     * @param string|null $text The description or content (HTML should be escaped by the view).
     * @return void
     */
    public function setText(?string $text): void
    {
        $this->text = $text;
    }

    /**
     * Get the picture path for this item.
     *
     * The path is expected to be relative to the public directory (for example
     * "images/photo.jpg"). The view layer should use the LinkGenerator to build
     * a full asset URL if necessary.
     *
     * @return string|null Relative picture path or null if none is set.
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * Set the picture path for this item.
     *
     * @param string|null $picture Relative path to the picture file (e.g. 'images/foo.jpg').
     * @return void
     */
    public function setPicture(?string $picture): void
    {
        $this->picture = $picture;
    }

}