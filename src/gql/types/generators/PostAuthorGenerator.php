<?php
namespace verbb\socialfeed\gql\types\generators;

use verbb\socialfeed\gql\interfaces\PostAuthorInterface;
use verbb\socialfeed\gql\types\PostAuthorType;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;

class PostAuthorGenerator implements GeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $gqlTypes = [];

        $typeName = self::getName();
        $fieldFields = PostAuthorInterface::getFieldDefinitions();

        $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new PostAuthorType([
            'name' => $typeName,
            'fields' => function() use ($fieldFields) {
                return $fieldFields;
            },
        ]));

        return $gqlTypes;
    }

    public static function getName($context = null): string
    {
        return 'PostAuthorType';
    }
}
