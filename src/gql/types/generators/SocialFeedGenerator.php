<?php
namespace verbb\socialfeeds\gql\types\generators;

use verbb\socialfeeds\gql\arguments\SocialFeedsArguments;
use verbb\socialfeeds\gql\interfaces\SocialFeedsInterface;
use verbb\socialfeeds\gql\types\SocialFeedsType;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;

class SocialFeedsGenerator implements GeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $gqlTypes = [];

        $typeName = self::getName();
        $socialFeedsFields = SocialFeedsInterface::getFieldDefinitions();
        $socialFeedsArgs = SocialFeedsArguments::getArguments();
        
        $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new SocialFeedsType([
            'name' => $typeName,
            'args' => function() use ($socialFeedsArgs) {
                return $socialFeedsArgs;
            },
            'fields' => function() use ($socialFeedsFields) {
                return $socialFeedsFields;
            },
        ]));

        return $gqlTypes;
    }

    public static function getName($context = null): string
    {
        return 'SocialFeedsType';
    }
}
