<?php
namespace verbb\socialfeeds\records;

use craft\db\ActiveRecord;

class Feed extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%socialfeeds_feeds}}';
    }
}
