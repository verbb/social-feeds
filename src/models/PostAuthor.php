<?php
namespace verbb\socialfeed\models;

use craft\base\Model;

class PostAuthor extends Model
{
    // Properties
    // =========================================================================

    public ?string $id = null;
    public ?string $username = null;
    public ?string $name = null;
    public ?string $url = null;
    public ?string $photo = null;

}