<?php
namespace verbb\socialfeeds\migrations;

use Craft;
use craft\db\Migration;
use craft\helpers\MigrationHelper;

class m230303_000000_cachekey extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        MigrationHelper::dropAllForeignKeysOnTable('{{%socialfeeds_posts}}');
        MigrationHelper::dropAllForeignKeysToTable('{{%socialfeeds_posts}}');
        MigrationHelper::dropAllUniqueIndexesOnTable('{{%socialfeeds_posts}}');
        MigrationHelper::dropAllIndexesOnTable('{{%socialfeeds_posts}}');

        $this->createIndex(null, '{{%socialfeeds_posts}}', ['sourceId'], false);
        $this->createIndex(null, '{{%socialfeeds_posts}}', ['cacheKey'], false);
        $this->createIndex(null, '{{%socialfeeds_posts}}', ['postId'], false);
        $this->createIndex(null, '{{%socialfeeds_posts}}', ['datePosted'], false);

        return true;
    }

    public function safeDown(): bool
    {
        echo "m230303_000000_cachekey cannot be reverted.\n";
        return false;
    }
}