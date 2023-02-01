<?php
namespace verbb\socialfeeds\events;

use verbb\socialfeeds\base\SourceInterface;

use yii\base\Event;

class SourceEvent extends Event
{
    // Properties
    // =========================================================================

    public SourceInterface $source;
    public bool $isNew = false;

}
