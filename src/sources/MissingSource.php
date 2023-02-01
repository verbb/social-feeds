<?php
namespace verbb\socialfeeds\sources;

use verbb\socialfeeds\base\Source;

use Craft;
use craft\base\MissingComponentInterface;
use craft\base\MissingComponentTrait;

use yii\base\NotSupportedException;

class MissingSource extends Source implements MissingComponentInterface
{
    // Traits
    // =========================================================================

    use MissingComponentTrait;


    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return Craft::t('social-feeds', 'Missing Source');
    }

    public static function getOAuthProviderClass(): string
    {
        throw new NotSupportedException('getOAuthProviderClass() is not implemented.');
    }


    // Properties
    // =========================================================================

    public static string $providerHandle = 'missingSource';


    // Public Methods
    // =========================================================================

    public function fetchPosts(): ?array
    {
        throw new NotSupportedException('fetchPosts() is not implemented.');
    }
}
