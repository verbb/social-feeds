<?php
namespace verbb\socialfeed\gql\arguments;

use craft\gql\base\Arguments;

use GraphQL\Type\Definition\Type;

class FeedArguments extends Arguments
{
    // Static Methods
    // =========================================================================

    public static function getArguments(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::listOf(Type::int()),
                'description' => 'Narrows the query results based on the feeds’s ID.',
            ],
            'handle' => [
                'name' => 'handle',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the feeds’s handle.',
            ],
            'uid' => [
                'name' => 'uid',
                'type' => Type::listOf(Type::string()),
                'description' => 'Narrows the query results based on the feeds’s UID.',
            ],
            'limit' => [
                'name' => 'limit',
                'type' => Type::int(),
                'description' => 'Sets the limit for paginated results.',
            ],
        ];
    }
}
