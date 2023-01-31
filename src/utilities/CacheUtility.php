<?php
namespace verbb\socialfeed\utilities;

use Craft;
use craft\base\Utility;

class CacheUtility extends Utility
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('social-feed', 'Social Feed');
    }

    public static function id(): string
    {
        return 'social-feed-cache';
    }

    public static function iconPath(): ?string
    {
        return Craft::getAlias('@vendor/verbb/social-feed/src/icon-mask.svg');
    }

    public static function contentHtml(): string
    {
        return Craft::$app->getView()->renderTemplate('social-feed/_utility');
    }
}
