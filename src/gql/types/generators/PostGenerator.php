<?php
namespace verbb\socialfeeds\gql\types\generators;

use verbb\socialfeeds\gql\interfaces\PostInterface;
use verbb\socialfeeds\gql\types\PostType;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;

class PostGenerator implements GeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $gqlTypes = [];

        $typeName = self::getName();
        $postFields = PostInterface::getFieldDefinitions();

        $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new PostType([
            'name' => $typeName,
            'fields' => function() use ($postFields) {
                return $postFields;
            },
        ]));

        return $gqlTypes;
    }

    public static function getName($context = null): string
    {
        return 'PostType';
    }
}
