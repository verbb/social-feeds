<?php
namespace verbb\socialfeeds\events;

use verbb\socialfeeds\models\Feed;

use yii\base\Event;

class FeedEvent extends Event
{
    // Properties
    // =========================================================================

    public Feed $feed;
    public bool $isNew = false;

}
