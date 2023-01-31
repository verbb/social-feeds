<?php
namespace verbb\socialfeed\gql\types;

use verbb\socialfeed\gql\interfaces\SocialFeedInterface;

use craft\gql\base\ObjectType;

class SocialFeedType extends ObjectType
{
    // Public Methods
    // =========================================================================

    public function __construct(array $config)
    {
        $config['interfaces'] = [
            SocialFeedInterface::getType(),
        ];

        parent::__construct($config);
    }
}
