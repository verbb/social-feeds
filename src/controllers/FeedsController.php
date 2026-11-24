<?php
namespace verbb\socialfeeds\controllers;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\base\Source;
use verbb\socialfeeds\models\Feed;

use Craft;
use craft\db\Query;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\web\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

class FeedsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->requireCpRequest();
        $this->requirePermission('socialFeeds-feeds');

        return true;
    }

    public function actionIndex(): Response
    {
        $feeds = SocialFeeds::$plugin->getFeeds()->getAllFeeds();

        return $this->renderTemplate('social-feeds/feeds', [
            'feeds' => $feeds,
            'setup' => $this->_getSetup($feeds),
        ]);
    }

    public function actionEdit(?string $feedHandle = null, ?Feed $feed = null): Response
    {
        $feedsService = SocialFeeds::$plugin->getFeeds();

        if ($feed === null) {
            if ($feedHandle !== null) {
                $feed = $feedsService->getFeedByHandle($feedHandle);

                if (!$feed) {
                    throw new NotFoundHttpException('Feed not found');
                }
            } else {
                $feed = new Feed();
            }
        }

        if ($feed->id) {
            $title = trim($feed->name) ?: Craft::t('social-feeds', 'Edit Feed');
        } else {
            $title = Craft::t('social-feeds', 'Create a new feed');
        }

        return $this->renderTemplate('social-feeds/feeds/_edit', [
            'title' => $title,
            'feed' => $feed,
        ]);
    }

    public function actionSave(): ?Response
    {
        $this->requirePostRequest();

        $feed = new Feed();
        $feed->id = $this->request->getParam('feedId');
        $feed->name = $this->request->getParam('name');
        $feed->handle = $this->request->getParam('handle');
        $feed->enabled = (bool)$this->request->getParam('enabled');
        $feed->sources = $this->request->getParam('sources') ?: [];

        if (!SocialFeeds::$plugin->getFeeds()->saveFeed($feed)) {
            return $this->asModelFailure($feed, Craft::t('social-feeds', 'Couldn’t save feed.'), 'feed');
        }

        return $this->asModelSuccess($feed, Craft::t('social-feeds', 'Feed saved.'));
    }

    public function actionReorder(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $feedIds = Json::decode($this->request->getRequiredBodyParam('ids'));
        SocialFeeds::$plugin->getFeeds()->reorderFeeds($feedIds);

        return $this->asSuccess();
    }

    public function actionDelete(): ?Response
    {
        $this->requirePostRequest();

        $feedId = $this->request->getRequiredBodyParam('id');

        if (!SocialFeeds::$plugin->getFeeds()->deleteFeedById($feedId)) {
            return $this->asFailure(Craft::t('social-feeds', 'Couldn’t delete feed.'));
        }

        return $this->asSuccess();
    }

    public function actionPreview(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $sourceIds = array_filter((array)$this->request->getParam('sources'));
        $sources = [];

        foreach ($sourceIds as $sourceId) {
            if ($source = SocialFeeds::$plugin->getSources()->getSourceById($sourceId)) {
                $sources[] = $source;
            }
        }

        $posts = SocialFeeds::$plugin->getPosts()->getPostsForSources($sources);

        return $this->renderTemplate('social-feeds/feeds/_preview', [
            'posts' => $posts,
            'sources' => $sources,
        ]);
    }


    // Private Methods
    // =========================================================================

    private function _getSetup(array $feeds): array
    {
        $sources = SocialFeeds::$plugin->getSources()->getAllSources();
        $connectedSource = ArrayHelper::firstWhere($sources, fn(Source $source) => $source->isConnected());

        // A feed only fetches from its enabled, connected sources, so one without any can't finish setup.
        $readyFeed = ArrayHelper::firstWhere($feeds, fn(Feed $feed) => $feed->sources && $feed->getSources());

        // Stored posts or a recorded fetch both mean posts have been fetched. Each survives one of the
        // cache utilities, so clearing the cache doesn't bring setup back.
        $fetched = (new Query())->from(['{{%socialfeeds_posts}}'])->exists()
            || (new Query())->from(['{{%socialfeeds_sources}}'])->where(['not', ['dateLastFetch' => null]])->exists();

        return [
            'source' => $sources[0] ?? null,
            'connectedSource' => $connectedSource,
            'feed' => $readyFeed ?? $feeds[0] ?? null,
            'readyFeed' => $readyFeed,
            'fetched' => $fetched,
            'autoFetch' => SocialFeeds::$plugin->getSettings()->cacheDuration !== false,
        ];
    }
}
