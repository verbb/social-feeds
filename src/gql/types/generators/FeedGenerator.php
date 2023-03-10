<?php
namespace verbb\socialfeeds\gql\types\generators;

use verbb\socialfeeds\gql\interfaces\FeedInterface;
use verbb\socialfeeds\gql\types\FeedType;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;

class FeedGenerator implements GeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $gqlTypes = [];

        $typeName = self::getName();
        $feedFields = FeedInterface::getFieldDefinitions();

        $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new FeedType([
            'name' => $typeName,
            'fields' => function() use ($feedFields) {
                return $feedFields;
            },
        ]));

        return $gqlTypes;
    }

    public static function getName($context = null): string
    {
        return 'FeedType';
    }
}
