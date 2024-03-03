<?php
namespace verbb\socialfeeds\utilities;

use Craft;
use craft\base\Utility;

class CacheUtility extends Utility
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('social-feeds', 'Social Feeds');
    }

    public static function id(): string
    {
        return 'social-feeds-cache';
    }

    public static function icon(): ?string
    {
        return '@verbb/socialfeeds/icon-mask.svg';
    }

    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('social-feeds/_utility');
    }
}
