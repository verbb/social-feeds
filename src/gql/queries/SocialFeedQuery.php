<?php
namespace verbb\socialfeed\gql\queries;

use verbb\socialfeed\gql\arguments\SocialFeedArguments;
use verbb\socialfeed\gql\interfaces\SocialFeedInterface;
use verbb\socialfeed\gql\resolvers\SocialFeedResolver;
use verbb\socialfeed\helpers\Gql as GqlHelper;

use craft\gql\base\Query;

class SocialFeedQuery extends Query
{
    // Static Methods
    // =========================================================================

    public static function getQueries($checkToken = true): array
    {
        if ($checkToken && !GqlHelper::canQuerySocialFeed()) {
            return [];
        }

        return [
            'socialFeed' => [
                'type' => SocialFeedInterface::getType(),
                'args' => SocialFeedArguments::getArguments(),
                'resolve' => SocialFeedResolver::class . '::resolve',
                'description' => 'This query is used to query for Social Feed content.'
            ],
        ];
    }
}
