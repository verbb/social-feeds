<?php
namespace verbb\socialfeeds\gql\types;

use verbb\socialfeeds\gql\interfaces\PostInterface;

use craft\gql\base\ObjectType;

class PostType extends ObjectType
{
    // Public Methods
    // =========================================================================

    public function __construct(array $config)
    {
        $config['interfaces'] = [
            PostInterface::getType(),
        ];

        parent::__construct($config);
    }
}
