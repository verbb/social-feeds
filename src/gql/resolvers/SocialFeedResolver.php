<?php
namespace verbb\socialfeed\gql\resolvers;

use craft\gql\base\Resolver;

use GraphQL\Type\Definition\ResolveInfo;

class SocialFeedResolver extends Resolver
{
    // Static Methods
    // =========================================================================

    public static function resolve($source, array $arguments, $context, ResolveInfo $resolveInfo): mixed
    {
        return [];
    }
}
