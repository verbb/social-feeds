<?php
namespace verbb\socialfeed\events;

use verbb\socialfeed\models\Post;

use yii\base\Event;

class PostEvent extends Event
{
    // Properties
    // =========================================================================

    public Post $post;
    public bool $isNew = false;

}
