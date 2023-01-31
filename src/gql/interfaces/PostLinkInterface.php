<?php
namespace verbb\socialfeed\gql\interfaces;

use verbb\socialfeed\gql\types\generators\PostLinkGenerator;
use verbb\socialfeed\models\PostLink;

use Craft;
use craft\gql\base\InterfaceType as BaseInterfaceType;
use craft\gql\GqlEntityRegistry;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class PostLinkInterface extends BaseInterfaceType
{
    // Static Methods
    // =========================================================================

    public static function getTypeGenerator(): string
    {
        return PostLinkGenerator::class;
    }

    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by all post links.',
            'resolveType' => function(PostLink $value) {
                return GqlEntityRegistry::getEntity(PostLinkGenerator::getName());
            },
        ]));

        PostLinkGenerator::generateTypes();

        return $type;
    }

    public static function getName(): string
    {
        return 'PostLinkInterface';
    }

    public static function getFieldDefinitions(): array
    {
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => Type::string(),
                'description' => 'The post link’s ID.',
            ],
            'title' => [
                'name' => 'title',
                'type' => Type::string(),
                'description' => 'The post link’s title.',
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'description' => 'The post link’s url.',
            ],
        ];

        return Craft::$app->getGql()->prepareFieldDefinitions($fields, self::getName());
    }
}
