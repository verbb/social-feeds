<?php
namespace verbb\socialfeed\gql\types\generators;

use verbb\socialfeed\gql\arguments\SocialFeedArguments;
use verbb\socialfeed\gql\interfaces\SocialFeedInterface;
use verbb\socialfeed\gql\types\SocialFeedType;

use craft\gql\base\GeneratorInterface;
use craft\gql\GqlEntityRegistry;

class SocialFeedGenerator implements GeneratorInterface
{
    // Static Methods
    // =========================================================================

    public static function generateTypes(mixed $context = null): array
    {
        $gqlTypes = [];

        $typeName = self::getName();
        $socialFeedFields = SocialFeedInterface::getFieldDefinitions();
        $socialFeedArgs = SocialFeedArguments::getArguments();
        
        $gqlTypes[$typeName] = GqlEntityRegistry::getEntity($typeName) ?: GqlEntityRegistry::createEntity($typeName, new SocialFeedType([
            'name' => $typeName,
            'args' => function() use ($socialFeedArgs) {
                return $socialFeedArgs;
            },
            'fields' => function() use ($socialFeedFields) {
                return $socialFeedFields;
            },
        ]));

        return $gqlTypes;
    }

    public static function getName($context = null): string
    {
        return 'SocialFeedType';
    }
}
