<?php
namespace verbb\socialfeeds\gql\types;

use verbb\socialfeeds\gql\interfaces\SocialFeedsInterface;

use craft\gql\base\ObjectType;

class SocialFeedsType extends ObjectType
{
    // Public Methods
    // =========================================================================

    public function __construct(array $config)
    {
        $config['interfaces'] = [
            SocialFeedsInterface::getType(),
        ];

        parent::__construct($config);
    }
}
