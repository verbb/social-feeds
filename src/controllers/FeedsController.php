<?php
namespace verbb\socialfeeds\controllers;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\models\Feed;

use Craft;
use craft\helpers\Json;
use craft\web\Controller;

use yii\web\NotFoundHttpException;
use yii\web\Response;

class FeedsController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        $feeds = SocialFeeds::$plugin->getFeeds()->getAllFeeds();

        return $this->renderTemplate('social-feeds/feeds', [
            'feeds' => $feeds,
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
            return $this->asModelFailure($feed, modelName: 'feed');
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

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $feedId = $this->request->getRequiredBodyParam('id');

        SocialFeeds::$plugin->getFeeds()->deleteFeedById($feedId);

        return $this->asSuccess();
    }

    public function actionPreview(): Response
    {
        $this->requireAcceptsJson();

        $sourceIds = array_filter($this->request->getParam('sources'));
        $sources = [];

        foreach ($sourceIds as $sourceId) {
            if ($source = SocialFeeds::$plugin->getSources()->getSourceById($sourceId)) {
                $sources[] = $source;
            }
        }

        $posts = SocialFeeds::$plugin->getPosts()->getPostsForSources($sources);

        return $this->renderTemplate('social-feeds/feeds/_preview', [
            'posts' => $posts,
        ]);
    }
}
