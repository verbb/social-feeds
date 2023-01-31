<?php
namespace verbb\socialfeed\gql\types;

use verbb\socialfeed\gql\interfaces\SourceInterface;

use craft\gql\base\ObjectType;

class SourceType extends ObjectType
{
    // Public Methods
    // =========================================================================

    public function __construct(array $config)
    {
        $config['interfaces'] = [
            SourceInterface::getType(),
        ];

        parent::__construct($config);
    }
}
