<?php
namespace verbb\socialfeed\gql\interfaces;

use verbb\socialfeed\gql\types\generators\PostMediaGenerator;
use verbb\socialfeed\models\PostMedia;

use Craft;
use craft\gql\base\InterfaceType as BaseInterfaceType;
use craft\gql\GqlEntityRegistry;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class PostMediaInterface extends BaseInterfaceType
{
    // Static Methods
    // =========================================================================

    public static function getTypeGenerator(): string
    {
        return PostMediaGenerator::class;
    }

    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by all post medias.',
            'resolveType' => function(PostMedia $value) {
                return GqlEntityRegistry::getEntity(PostMediaGenerator::getName());
            },
        ]));

        PostMediaGenerator::generateTypes();

        return $type;
    }

    public static function getName(): string
    {
        return 'PostMediaInterface';
    }

    public static function getFieldDefinitions(): array
    {
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => Type::string(),
                'description' => 'The post media’s ID.',
            ],
            'title' => [
                'name' => 'title',
                'type' => Type::string(),
                'description' => 'The post media’s title.',
            ],
            'type' => [
                'name' => 'type',
                'type' => Type::string(),
                'description' => 'The post media’s type.',
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'description' => 'The post media’s url.',
            ],
            'width' => [
                'name' => 'width',
                'type' => Type::int(),
                'description' => 'The post media’s width.',
            ],
            'height' => [
                'name' => 'height',
                'type' => Type::int(),
                'description' => 'The post media’s height.',
            ],
        ];

        return Craft::$app->getGql()->prepareFieldDefinitions($fields, self::getName());
    }
}
