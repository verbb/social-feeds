<?php
namespace verbb\socialfeed\models;

use craft\base\Model;

class Settings extends Model
{
    // Properties
    // =========================================================================

    public string $pluginName = 'Social Feed';
    public bool $hasCpSection = true;
    public bool $enableCache = true;
    public mixed $cacheDuration = 'PT6H';
    public int $postsLimit = 50;

}