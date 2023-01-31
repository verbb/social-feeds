<?php
namespace verbb\socialfeed\migrations;

use craft\db\Migration;

use verbb\auth\Auth;

class Install extends Migration
{
    // Public Methods
    // =========================================================================

    public function safeUp(): bool
    {
        // Ensure that the Auth module kicks off setting up tables
        Auth::$plugin->migrator->up();

        $this->createTables();
        $this->createIndexes();
        $this->addForeignKeys();

        return true;
    }

    public function safeDown(): bool
    {
        $this->dropTables();

        // Delete all tokens for this plugin
        Auth::$plugin->getTokens()->deleteTokensByOwner('social-feed');

        return true;
    }

    public function createTables(): void
    {
        $this->archiveTableIfExists('{{%socialfeed_sources}}');
        $this->createTable('{{%socialfeed_sources}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'handle' => $this->string()->notNull(),
            'enabled' => $this->boolean(),
            'type' => $this->string()->notNull(),
            'settings' => $this->text(),
            'sortOrder' => $this->smallInteger()->unsigned(),
            'cache' => $this->text(),
            'dateLastFetch' => $this->dateTime(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->archiveTableIfExists('{{%socialfeed_feeds}}');
        $this->createTable('{{%socialfeed_feeds}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'handle' => $this->string()->notNull(),
            'enabled' => $this->boolean(),
            'sources' => $this->text(),
            'sortOrder' => $this->smallInteger()->unsigned(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);

        $this->archiveTableIfExists('{{%socialfeed_posts}}');
        $this->createTable('{{%socialfeed_posts}}', [
            'id' => $this->primaryKey(),
            'sourceId' => $this->integer()->notNull(),
            'cacheKey' => $this->string()->notNull(),
            'postId' => $this->string()->notNull(),
            'data' => $this->mediumText(),
            'datePosted' => $this->dateTime()->notNull(),
            'dateCreated' => $this->dateTime()->notNull(),
            'dateUpdated' => $this->dateTime()->notNull(),
            'uid' => $this->uid(),
        ]);
    }

    public function createIndexes(): void
    {
        $this->createIndex(null, '{{%socialfeed_sources}}', ['name'], true);
        $this->createIndex(null, '{{%socialfeed_sources}}', ['handle'], true);

        $this->createIndex(null, '{{%socialfeed_feeds}}', ['name'], true);
        $this->createIndex(null, '{{%socialfeed_feeds}}', ['handle'], true);

        $this->createIndex(null, '{{%socialfeed_posts}}', ['sourceId'], true);
        $this->createIndex(null, '{{%socialfeed_posts}}', ['postId'], true);
        $this->createIndex(null, '{{%socialfeed_posts}}', ['datePosted'], true);
    }

    public function addForeignKeys(): void
    {
        $this->addForeignKey(null, '{{%socialfeed_posts}}', ['sourceId'], '{{%socialfeed_sources}}', ['id'], 'CASCADE', null);
    }

    public function dropTables(): void
    {
        $this->dropTableIfExists('{{%socialfeed_sources}}');
        $this->dropTableIfExists('{{%socialfeed_feeds}}');
        $this->dropTableIfExists('{{%socialfeed_cache}}');
    }
}
