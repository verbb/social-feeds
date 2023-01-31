<?php
namespace verbb\socialfeed\gql\types;

use verbb\socialfeed\gql\interfaces\PostInterface;

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
