<?php
namespace verbb\socialfeeds\migrations;

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
        Auth::$plugin->getTokens()->deleteTokensByOwner('social-feeds');

        return true;
    }

    public function createTables(): void
    {
        $this->archiveTableIfExists('{{%socialfeeds_sources}}');
        $this->createTable('{{%socialfeeds_sources}}', [
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

        $this->archiveTableIfExists('{{%socialfeeds_feeds}}');
        $this->createTable('{{%socialfeeds_feeds}}', [
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

        $this->archiveTableIfExists('{{%socialfeeds_posts}}');
        $this->createTable('{{%socialfeeds_posts}}', [
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
        $this->createIndex(null, '{{%socialfeeds_sources}}', ['name'], true);
        $this->createIndex(null, '{{%socialfeeds_sources}}', ['handle'], true);

        $this->createIndex(null, '{{%socialfeeds_feeds}}', ['name'], true);
        $this->createIndex(null, '{{%socialfeeds_feeds}}', ['handle'], true);

        $this->createIndex(null, '{{%socialfeeds_posts}}', ['sourceId'], false);
        $this->createIndex(null, '{{%socialfeeds_posts}}', ['cacheKey'], false);
        $this->createIndex(null, '{{%socialfeeds_posts}}', ['postId'], false);
        $this->createIndex(null, '{{%socialfeeds_posts}}', ['datePosted'], false);
    }

    public function addForeignKeys(): void
    {
        $this->addForeignKey(null, '{{%socialfeeds_posts}}', ['sourceId'], '{{%socialfeeds_sources}}', ['id'], 'CASCADE', null);
    }

    public function dropTables(): void
    {
        $this->dropTableIfExists('{{%socialfeeds_sources}}');
        $this->dropTableIfExists('{{%socialfeeds_feeds}}');
        $this->dropTableIfExists('{{%socialfeeds_cache}}');
    }
}
