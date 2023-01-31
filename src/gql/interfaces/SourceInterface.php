<?php
namespace verbb\socialfeed\gql\interfaces;

use verbb\socialfeed\gql\arguments\PostArguments;
use verbb\socialfeed\gql\types\generators\SourceGenerator;
use verbb\socialfeed\gql\resolvers\PostResolver;

use Craft;
use craft\gql\base\InterfaceType as BaseInterfaceType;
use craft\gql\GqlEntityRegistry;
use craft\gql\types\DateTime;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class SourceInterface extends BaseInterfaceType
{
    // Static Methods
    // =========================================================================

    public static function getTypeGenerator(): string
    {
        return SourceGenerator::class;
    }

    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by all sources.',
            'resolveType' => function($value) {
                return GqlEntityRegistry::getEntity(SourceGenerator::getName());
            },
        ]));

        SourceGenerator::generateTypes();

        return $type;
    }

    public static function getName(): string
    {
        return 'SourceInterface';
    }

    public static function getFieldDefinitions(): array
    {
        return Craft::$app->getGql()->prepareFieldDefinitions(array_merge(parent::getFieldDefinitions(), [
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'The source’s name.',
            ],
            'handle' => [
                'name' => 'handle',
                'type' => Type::string(),
                'description' => 'The source’s handle.',
            ],
            'enabled' => [
                'name' => 'enabled',
                'type' => Type::boolean(),
                'description' => 'Whether the source is enabled.',
            ],
            'dateLastFetch' => [
                'name' => 'dateLastFetch',
                'type' => DateTime::getType(),
                'description' => 'The source’s last fetch of data as a date.',
            ],
            'providerName' => [
                'name' => 'providerName',
                'type' => Type::string(),
                'description' => 'The source’s provider name.',
            ],
            'providerHandle' => [
                'name' => 'providerHandle',
                'type' => Type::string(),
                'description' => 'The source’s provider handle.',
            ],
            'primaryColor' => [
                'name' => 'primaryColor',
                'type' => Type::string(),
                'description' => 'The source’s provider primary brand color.',
            ],
            'icon' => [
                'name' => 'icon',
                'type' => Type::string(),
                'description' => 'The source’s provider SVG icon.',
            ],
            'isConnected' => [
                'name' => 'isConnected',
                'type' => Type::boolean(),
                'description' => 'Whether the source is connected.',
            ],
            'posts' => [
                'name' => 'posts',
                'args' => PostArguments::getArguments(),
                'type' => Type::listOf(PostInterface::getType()),
                'resolve' => PostResolver::class . '::resolve',
                'description' => 'The source’s posts.',
            ],
        ]), self::getName());
    }
}
