<?php
namespace verbb\socialfeed\helpers;

use craft\helpers\Gql as GqlHelper;

class Gql extends GqlHelper
{
    // Public Methods
    // =========================================================================

    public static function canQuerySocialFeed(): bool
    {
        $allowedEntities = self::extractAllowedEntitiesFromSchema();

        return isset($allowedEntities['socialFeed']);
    }
}