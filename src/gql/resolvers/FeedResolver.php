<?php
namespace verbb\socialfeed\gql\resolvers;

use verbb\socialfeed\SocialFeed;

use craft\gql\base\Resolver;

use Exception;

use GraphQL\Type\Definition\ResolveInfo;

class FeedResolver extends Resolver
{
    // Static Methods
    // =========================================================================

    public static function resolve($source, array $arguments, $context, ResolveInfo $resolveInfo): mixed
    {
        return SocialFeed::$plugin->getFeeds()->getAllFeedsByParams($arguments);
    }

    public static function resolveOne($source, array $arguments, $context, ResolveInfo $resolveInfo): mixed
    {
        if (!array_key_exists('id', $arguments) && !array_key_exists('handle', $arguments) && !array_key_exists('uid', $arguments)) {
            throw new Exception('You must provide at least one identifier (`id`, `handle`, `uid`) argument to query a `feed`.');
        }

        return SocialFeed::$plugin->getFeeds()->getFeedByParams($arguments);
    }
}
