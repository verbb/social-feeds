<?php
namespace verbb\socialfeeds\gql\interfaces;

use verbb\socialfeeds\gql\types\generators\PostAuthorGenerator;
use verbb\socialfeeds\models\PostAuthor;

use Craft;
use craft\gql\base\InterfaceType as BaseInterfaceType;
use craft\gql\GqlEntityRegistry;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class PostAuthorInterface extends BaseInterfaceType
{
    // Static Methods
    // =========================================================================

    public static function getTypeGenerator(): string
    {
        return PostAuthorGenerator::class;
    }

    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by all post authors.',
            'resolveType' => function(PostAuthor $value) {
                return GqlEntityRegistry::getEntity(PostAuthorGenerator::getName());
            },
        ]));

        PostAuthorGenerator::generateTypes();

        return $type;
    }

    public static function getName(): string
    {
        return 'PostAuthorInterface';
    }

    public static function getFieldDefinitions(): array
    {
        $fields = [
            'id' => [
                'name' => 'id',
                'type' => Type::string(),
                'description' => 'The post author’s ID.',
            ],
            'username' => [
                'name' => 'username',
                'type' => Type::string(),
                'description' => 'The post author’s username.',
            ],
            'name' => [
                'name' => 'name',
                'type' => Type::string(),
                'description' => 'The post author’s name.',
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'description' => 'The post author’s url.',
            ],
            'photo' => [
                'name' => 'photo',
                'type' => Type::string(),
                'description' => 'The post author’s photo.',
            ],
        ];

        return Craft::$app->getGql()->prepareFieldDefinitions($fields, self::getName());
    }
}
