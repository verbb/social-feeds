<?php
namespace verbb\socialfeed\gql\interfaces;

use verbb\socialfeed\gql\arguments\SourceArguments;
use verbb\socialfeed\gql\types\generators\FeedGenerator;
use verbb\socialfeed\gql\resolvers\SourceResolver;

use Craft;
use craft\gql\base\InterfaceType as BaseInterfaceType;
use craft\gql\GqlEntityRegistry;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class FeedInterface extends BaseInterfaceType
{
    // Static Methods
    // =========================================================================

    public static function getTypeGenerator(): string
    {
        return FeedGenerator::class;
    }

    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by all feeds.',
            'resolveType' => function($value) {
                return GqlEntityRegistry::getEntity(FeedGenerator::getName());
            },
        ]));

        FeedGenerator::generateTypes();

        return $type;
    }

    public static function getName(): string
    {
        return 'FeedInterface';
    }

    public static function getFieldDefinitions(): array
    {
        return Craft::$app->getGql()->prepareFieldDefinitions(array_merge(parent::getFieldDefinitions(), [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'The feed’s name.',
            ],
            'handle' => [
                'name' => 'handle',
                'type' => Type::string(),
                'description' => 'The feed’s handle.',
            ],
            'enabled' => [
                'name' => 'enabled',
                'type' => Type::boolean(),
                'description' => 'Whether the feed is enabled.',
            ],
            'sources' => [
                'name' => 'sources',
                'args' => SourceArguments::getArguments(),
                'type' => Type::listOf(SourceInterface::getType()),
                'resolve' => SourceResolver::class . '::resolve',
                'description' => 'The feed’s sources.',
            ],
        ]), self::getName());
    }
}
