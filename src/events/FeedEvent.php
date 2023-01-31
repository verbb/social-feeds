<?php
namespace verbb\socialfeed\events;

use verbb\socialfeed\models\Feed;

use yii\base\Event;

class FeedEvent extends Event
{
    // Properties
    // =========================================================================

    public Feed $feed;
    public bool $isNew = false;

}
