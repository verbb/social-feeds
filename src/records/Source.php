<?php
namespace verbb\socialfeeds\records;

use craft\db\ActiveRecord;

class Source extends ActiveRecord
{
    // Public Methods
    // =========================================================================

    public static function tableName(): string
    {
        return '{{%socialfeeds_sources}}';
    }
}
