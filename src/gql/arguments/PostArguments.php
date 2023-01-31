<?php
namespace verbb\socialfeed\gql\arguments;

use craft\gql\base\Arguments;

use GraphQL\Type\Definition\Type;

class PostArguments extends Arguments
{
    // Static Methods
    // =========================================================================

    public static function getArguments(): array
    {
        return [
            'limit' => [
                'name' => 'limit',
                'type' => Type::int(),
                'description' => 'Sets the limit for paginated results.',
            ],
            'offset' => [
                'name' => 'offset',
                'type' => Type::int(),
                'description' => 'Sets the offset for paginated results.',
            ],
        ];
    }
}
