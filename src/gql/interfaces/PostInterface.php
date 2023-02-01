<?php
namespace verbb\socialfeeds\gql\interfaces;

use verbb\socialfeeds\gql\types\generators\PostGenerator;

use Craft;
use craft\gql\base\InterfaceType as BaseInterfaceType;
use craft\gql\GqlEntityRegistry;
use craft\gql\types\DateTime;
use craft\helpers\Json;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class PostInterface extends BaseInterfaceType
{
    // Static Methods
    // =========================================================================

    public static function getTypeGenerator(): string
    {
        return PostGenerator::class;
    }

    public static function getType($fields = null): Type
    {
        if ($type = GqlEntityRegistry::getEntity(self::getName())) {
            return $type;
        }

        $type = GqlEntityRegistry::createEntity(self::getName(), new InterfaceType([
            'name' => static::getName(),
            'fields' => self::class . '::getFieldDefinitions',
            'description' => 'This is the interface implemented by all posts.',
            'resolveType' => function($value) {
                return GqlEntityRegistry::getEntity(PostGenerator::getName());
            },
        ]));

        PostGenerator::generateTypes();

        return $type;
    }

    public static function getName(): string
    {
        return 'PostInterface';
    }

    public static function getFieldDefinitions(): array
    {
        return Craft::$app->getGql()->prepareFieldDefinitions(array_merge(parent::getFieldDefinitions(), [
            'title' => [
                'name' => 'title',
                'type' => Type::string(),
                'description' => 'The post’s title.',
            ],
            'text' => [
                'name' => 'text',
                'type' => Type::string(),
                'description' => 'The post’s text.',
            ],
            'url' => [
                'name' => 'url',
                'type' => Type::string(),
                'description' => 'The post’s url.',
            ],
            'sourceId' => [
                'name' => 'sourceId',
                'type' => Type::int(),
                'description' => 'The post’s source ID.',
            ],
            'sourceHandle' => [
                'name' => 'sourceHandle',
                'type' => Type::string(),
                'description' => 'The post’s source handle.',
            ],
            'sourceType' => [
                'name' => 'sourceType',
                'type' => Type::string(),
                'description' => 'The post’s source type.',
            ],
            'postType' => [
                'name' => 'postType',
                'type' => Type::string(),
                'description' => 'The post’s type.',
            ],
            'likes' => [
                'name' => 'likes',
                'type' => Type::int(),
                'description' => 'The post’s number of likes.',
            ],
            'shares' => [
                'name' => 'shares',
                'type' => Type::int(),
                'description' => 'The post’s number of shares.',
            ],
            'replies' => [
                'name' => 'replies',
                'type' => Type::int(),
                'description' => 'The post’s number of replies.',
            ],
            'dateCreated' => [
                'name' => 'dateCreated',
                'type' => DateTime::getType(),
                'description' => 'The post’s created date.',
            ],
            'dateUpdated' => [
                'name' => 'dateUpdated',
                'type' => DateTime::getType(),
                'description' => 'The post’s updated date.',
            ],
            'author' => [
                'name' => 'author',
                'type' => PostAuthorInterface::getType(),
                'description' => 'The post’s author.',
            ],
            'tags' => [
                'name' => 'tags',
                'type' => Type::string(),
                'description' => 'The post’s tags as a JSON string.',
                'resolve' => function($post) {
                    return Json::encode($post->tags);
                },
            ],
            'links' => [
                'name' => 'links',
                'type' => Type::listOf(PostLinkInterface::getType()),
                'description' => 'The post’s links.',
            ],
            'images' => [
                'name' => 'images',
                'type' => Type::listOf(PostMediaInterface::getType()),
                'description' => 'The post’s images.',
            ],
            'videos' => [
                'name' => 'videos',
                'type' => Type::listOf(PostMediaInterface::getType()),
                'description' => 'The post’s videos.',
            ],
            'data' => [
                'name' => 'data',
                'type' => Type::string(),
                'description' => 'The post’s raw data as a JSON string.',
                'resolve' => function($post) {
                    return Json::encode($post->data);
                },
            ],
            'meta' => [
                'name' => 'meta',
                'type' => Type::string(),
                'description' => 'The post’s meta data as a JSON string.',
                'resolve' => function($post) {
                    return Json::encode($post->meta);
                },
            ],
        ]), self::getName());
    }
}
