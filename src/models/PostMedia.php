<?php
namespace verbb\socialfeeds\models;

use craft\base\Model;

class PostMedia extends Model
{
    // Constants
    // =========================================================================

    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';


    // Properties
    // =========================================================================

    public ?string $id = null;
    public ?string $title = null;
    public ?string $type = null;
    public ?string $url = null;
    public ?int $width = null;
    public ?int $height = null;

}