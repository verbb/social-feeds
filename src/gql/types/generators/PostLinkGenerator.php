<?php
namespace verbb\socialfeeds\gql\types\generators;

use verbb\socialfeeds\gql\interfaces\PostLinkInterface;
use verbb\socialfeeds\gql\types\PostLinkType;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;

class PostLinkGenerator implements GeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $gqlTypes = [];

        $typeName = self::getName();
        $fieldFields = PostLinkInterface::getFieldDefinitions();

        $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new PostLinkType([
            'name' => $typeName,
            'fields' => function() use ($fieldFields) {
                return $fieldFields;
            },
        ]));

        return $gqlTypes;
    }

    public static function getName($context = null): string
    {
        return 'PostLinkType';
    }
}
