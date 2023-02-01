<?php
namespace verbb\socialfeeds\gql\queries;

use verbb\socialfeeds\gql\arguments\SocialFeedsArguments;
use verbb\socialfeeds\gql\interfaces\SocialFeedsInterface;
use verbb\socialfeeds\gql\resolvers\SocialFeedsResolver;
use verbb\socialfeeds\helpers\Gql as GqlHelper;

use craft\gql\base\Query;

class SocialFeedsQuery extends Query
{
    // Static Methods
    // =========================================================================

    public static function getQueries($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canQuerySocialFeeds()) {
            return [];
        }

        return [
            'socialFeeds' => [
                'type' => SocialFeedsInterface::getType(),
                'args' => SocialFeedsArguments::getArguments(),
                'resolve' => SocialFeedsResolver::class . '::resolve',
                'description' => 'This query is used to query for Social Feeds content.'
            ],
        ];
    }
}
