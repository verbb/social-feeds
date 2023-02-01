<?php
namespace verbb\socialfeeds\events;

use verbb\socialfeeds\models\Post;

use yii\base\Event;

class PostEvent extends Event
{
    // Properties
    // =========================================================================

    public Post $post;
    public bool $isNew = false;

}
