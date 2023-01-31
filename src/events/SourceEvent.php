<?php
namespace verbb\socialfeed\events;

use verbb\socialfeed\base\SourceInterface;

use yii\base\Event;

class SourceEvent extends Event
{
    // Properties
    // =========================================================================

    public SourceInterface $source;
    public bool $isNew = false;

}
