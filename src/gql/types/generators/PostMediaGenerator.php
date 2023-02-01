<?php
namespace verbb\socialfeeds\gql\types\generators;

use verbb\socialfeeds\gql\interfaces\PostMediaInterface;
use verbb\socialfeeds\gql\types\PostMediaType;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;

class PostMediaGenerator implements GeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $gqlTypes = [];

        $typeName = self::getName();
        $fieldFields = PostMediaInterface::getFieldDefinitions();

        $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new PostMediaType([
            'name' => $typeName,
            'fields' => function() use ($fieldFields) {
                return $fieldFields;
            },
        ]));

        return $gqlTypes;
    }

    public static function getName($context = null): string
    {
        return 'PostMediaType';
    }
}
