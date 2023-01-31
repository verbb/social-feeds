<?php
namespace verbb\socialfeed\gql\types;

use verbb\socialfeed\gql\interfaces\FeedInterface;

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
