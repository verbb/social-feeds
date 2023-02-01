<?php
namespace verbb\socialfeeds\services;

use verbb\socialfeeds\events\FeedEvent;
use verbb\socialfeeds\models\Feed;
use verbb\socialfeeds\records\Feed as FeedRecord;

use Craft;
use craft\base\MemoizableArray;
use craft\db\Query;
use craft\helpers\ArrayHelper;

use yii\base\Component;

use Throwable;
use Exception;

class Feeds extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_BEFORE_SAVE_FEED = 'beforeSaveFeed';
    public const EVENT_AFTER_SAVE_FEED = 'afterSaveFeed';
    public const EVENT_BEFORE_DELETE_FEED = 'beforeDeleteFeed';
    public const EVENT_AFTER_DELETE_FEED = 'afterDeleteFeed';


    // Properties
    // =========================================================================

    private ?MemoizableArray $_feeds = null;


    // Public Methods
    // =========================================================================

    public function getAllFeeds(): array
    {
        return $this->_feeds()->all();
    }

    public function getAllEnabledFeeds(): array
    {
        return $this->_feeds()->where('enabled', true)->all();
    }

    public function getAllFeedsByParams(array $params): array
    {
        $limit = ArrayHelper::remove($params, 'limit');

        $query = $this->_createFeedQuery()->where($params)->limit($limit)->all();

        return array_map(function($result) {
            return new Feed($result);
        }, $query);
    }

    public function getFeedById(int $id): ?Feed
    {
        return $this->_feeds()->firstWhere('id', $id);
    }

    public function getFeedByUid(string $uid): ?Feed
    {
        return $this->_feeds()->firstWhere('uid', $uid, true);
    }

    public function getFeedByHandle(string $handle): ?Feed
    {
        return $this->_feeds()->firstWhere('handle', $handle, true);
    }

    public function getFeedByParams(array $params): ?Feed
    {
        $params['limit'] = 1;

        return $this->getAllFeedsByParams($params)[0] ?? null;
    }

    public function saveFeed(Feed $feed, bool $runValidation = true): bool
    {
        $isNewFeed = !$feed->id;

        // Fire a 'beforeSaveFeed' event
        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_FEED)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_FEED, new FeedEvent([
                'feed' => $feed,
                'isNew' => $isNewFeed,
            ]));
        }

        if ($runValidation && !$feed->validate()) {
            Craft::info('Feed not saved due to validation error.', __METHOD__);
            return false;
        }

        $feedRecord = $this->_getFeedRecordById($feed->id);
        $feedRecord->name = $feed->name;
        $feedRecord->handle = $feed->handle;
        $feedRecord->enabled = $feed->enabled;
        $feedRecord->sources = array_values(array_filter($feed->sources));

        if ($isNewFeed) {
            $maxSortOrder = (new Query())
                ->from(['{{%socialfeeds_feeds}}'])
                ->max('[[sortOrder]]');

            $feedRecord->sortOrder = $maxSortOrder ? $maxSortOrder + 1 : 1;
        }

        $feedRecord->save(false);

        if (!$feed->id) {
            $feed->id = $feedRecord->id;
        }

        // Clear caches
        $this->_feeds = null;

        // Fire an 'afterSaveFeed' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE_FEED)) {
            $this->trigger(self::EVENT_AFTER_SAVE_FEED, new FeedEvent([
                'feed' => $feed,
                'isNew' => $isNewFeed,
            ]));
        }

        return true;
    }

    public function reorderFeeds(array $feedIds): bool
    {
        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            foreach ($feedIds as $feedOrder => $feedId) {
                $feedRecord = $this->_getFeedRecordById($feedId);
                $feedRecord->sortOrder = $feedOrder + 1;
                $feedRecord->save();
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    public function deleteFeedById(int $feedId): bool
    {
        $feed = $this->getFeedById($feedId);

        if (!$feed) {
            return false;
        }

        return $this->deleteFeed($feed);
    }

    public function deleteFeed(Feed $feed): bool
    {
        // Fire a 'beforeDeleteFeed' event
        if ($this->hasEventHandlers(self::EVENT_BEFORE_DELETE_FEED)) {
            $this->trigger(self::EVENT_BEFORE_DELETE_FEED, new FeedEvent([
                'feed' => $feed,
            ]));
        }

        Craft::$app->getDb()->createCommand()
            ->delete('{{%socialfeeds_feeds}}', ['id' => $feed->id])
            ->execute();

        // Fire an 'afterDeleteFeed' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_DELETE_FEED)) {
            $this->trigger(self::EVENT_AFTER_DELETE_FEED, new FeedEvent([
                'feed' => $feed,
            ]));
        }

        // Clear caches
        $this->_feeds = null;

        return true;
    }


    // Private Methods
    // =========================================================================

    private function _feeds(): MemoizableArray
    {
        if (!isset($this->_feeds)) {
            $feeds = [];

            foreach ($this->_createFeedQuery()->all() as $result) {
                $feeds[] = new Feed($result);
            }

            $this->_feeds = new MemoizableArray($feeds);
        }

        return $this->_feeds;
    }

    private function _createFeedQuery(): Query
    {
        return (new Query())
            ->select([
                'id',
                'name',
                'handle',
                'enabled',
                'sources',
                'sortOrder',
                'dateCreated',
                'dateUpdated',
            ])
            ->from(['{{%socialfeeds_feeds}}'])
            ->orderBy(['sortOrder' => SORT_ASC]);
    }

    private function _getFeedRecordById(int $feedId = null): ?FeedRecord
    {
        if ($feedId !== null) {
            $feedRecord = FeedRecord::findOne(['id' => $feedId]);

            if (!$feedRecord) {
                throw new Exception(Craft::t('social-feeds', 'No feed exists with the ID “{id}”.', ['id' => $feedId]));
            }
        } else {
            $feedRecord = new FeedRecord();
        }

        return $feedRecord;
    }

}
