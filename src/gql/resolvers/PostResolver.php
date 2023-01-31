<?php
namespace verbb\socialfeed\gql\resolvers;

use craft\gql\base\Resolver;

use GraphQL\Type\Definition\ResolveInfo;

class PostResolver extends Resolver
{
    // Static Methods
    // =========================================================================

    public static function resolve($source, array $arguments, $context, ResolveInfo $resolveInfo): mixed
    {
        return $source->getPosts($arguments);
    }

    public static function resolveOne($source, array $arguments, $context, ResolveInfo $resolveInfo)
    {
        return $source->getPosts($arguments)[0] ?? null;
    }
}
