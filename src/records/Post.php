<?php
namespace verbb\socialfeed\records;

use craft\db\ActiveRecord;

class Post extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%socialfeed_posts}}';
    }
}
