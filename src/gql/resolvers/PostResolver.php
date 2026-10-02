<?php
namespace verbb\socialfeeds\gql\resolvers;

use craft\gql\base\Resolver;

use GraphQL\Error\UserError;
use GraphQL\Type\Definition\ResolveInfo;

class PostResolver extends Resolver
{
    // Static Methods
    // =========================================================================

    public static function resolve($source, array $arguments, $context, ResolveInfo $resolveInfo): mixed
    {
        return $source->getPosts(self::_normalizeArguments($arguments));
    }

    public static function resolveOne($source, array $arguments, $context, ResolveInfo $resolveInfo)
    {
        return $source->getPosts(self::_normalizeArguments($arguments))[0] ?? null;
    }

    private static function _normalizeArguments(array $arguments): array
    {
        $limit = $arguments['limit'] ?? self::DEFAULT_LIMIT;
        $offset = $arguments['offset'] ?? 0;

        if ($limit < 0 || $limit > self::MAX_LIMIT) {
            throw new UserError('`limit` must be between 0 and 100.');
        }

        if ($offset < 0) {
            throw new UserError('`offset` must be zero or greater.');
        }

        $arguments['limit'] = $limit;
        $arguments['offset'] = $offset;

        return $arguments;
    }


    // Constants
    // =========================================================================

    private const DEFAULT_LIMIT = 50;
    private const MAX_LIMIT = 100;
}
