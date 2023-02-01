<?php
namespace verbb\socialfeeds\records;

use craft\db\ActiveRecord;

class Post extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%socialfeeds_posts}}';
    }
}
