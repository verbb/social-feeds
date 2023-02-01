<?php
namespace verbb\socialfeeds\gql\types;

use verbb\socialfeeds\gql\interfaces\FeedInterface;

use craft\gql\base\ObjectType;

class FeedType extends ObjectType
{
    // Public Methods
    // =========================================================================

    public function __construct(array $config)
    {
        $config['interfaces'] = [
            FeedInterface::getType(),
        ];

        parent::__construct($config);
    }
}
