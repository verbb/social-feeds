<?php
namespace verbb\socialfeeds\gql\interfaces;

use verbb\socialfeeds\gql\arguments\FeedArguments;
use verbb\socialfeeds\gql\arguments\PostArguments;
use verbb\socialfeeds\gql\arguments\SourceArguments;
use verbb\socialfeeds\gql\resolvers\FeedResolver;
use verbb\socialfeeds\gql\resolvers\PostResolver;
use verbb\socialfeeds\gql\resolvers\SourceResolver;
use verbb\socialfeeds\gql\types\generators\SocialFeedsGenerator;

use craft\gql\base\InterfaceType as BaseInterfaceType;
use craft\gql\GqlEntityRegistry;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class SocialFeedsInterface extends BaseInterfaceType
{
    // Static Methods
    // =========================================================================

    public static function getTypeGenerator(): string
    {
        return SocialFeedsGenerator::class;
    }

    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::class)) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::class, new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by Social Feeds.',
            'resolveType' => function (array $value) {
                return GqlEntityRegistry::getEntity(SocialFeedsGenerator::getName());
            },
        ]));

        SocialFeedsGenerator::generateTypes();

        return $type;
    }

    public static function getName(): string
    {
        return 'SocialFeedsInterface';
    }

    public static function getFieldDefinitions(): array
    {
        return [
            'feeds' => [
                'name' => 'feeds',
                'args' => FeedArguments::getArguments(),
                'type' => Type::listOf(FeedInterface::getType()),
                'resolve' => FeedResolver::class . '::resolve',
                'description' => 'All Social Feeds feeds.',
            ],
            'feed' => [
                'name' => 'feed',
                'args' => FeedArguments::getArguments(),
                'type' => FeedInterface::getType(),
                'resolve' => FeedResolver::class . '::resolveOne',
                'description' => 'A single Social Feeds feed.',
            ],
            'sources' => [
                'name' => 'sources',
                'args' => SourceArguments::getArguments(),
                'type' => Type::listOf(SourceInterface::getType()),
                'resolve' => SourceResolver::class . '::resolve',
                'description' => 'All Social Feeds sources.',
            ],
            'source' => [
                'name' => 'source',
                'args' => SourceArguments::getArguments(),
                'type' => SourceInterface::getType(),
                'resolve' => SourceResolver::class . '::resolveOne',
                'description' => 'A single Social Feeds source.',
            ],
            'posts' => [
                'name' => 'posts',
                'args' => PostArguments::getArguments(),
                'type' => Type::listOf(PostInterface::getType()),
                'resolve' => PostResolver::class . '::resolve',
                'description' => 'All Social Feeds posts.',
            ],
        ];
    }
}
