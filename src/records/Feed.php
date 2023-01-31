<?php
namespace verbb\socialfeed\records;

use craft\db\ActiveRecord;

class Feed extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%socialfeed_feeds}}';
    }
}
