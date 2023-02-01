<?php
namespace verbb\socialfeeds\services;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\base\SourceInterface;
use verbb\socialfeeds\models\Post;
use verbb\socialfeeds\records\Post as PostRecord;

use Craft;
use craft\base\Component;
use craft\db\Query;
use craft\helpers\App;
use craft\helpers\ArrayHelper;
use craft\helpers\DateTimeHelper;
use craft\helpers\Db;
use craft\helpers\ConfigHelper;
use craft\helpers\Template;
use craft\web\View;

use DateTime;
use DateTimeZone;
use Throwable;

use Twig\Markup;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class Posts extends Component
{
    // Public Methods
    // =========================================================================

    public function getPostsForFeed(string $feedHandle, array $options = []): array
    {
        $feed = SocialFeeds::$plugin->getFeeds()->getFeedByHandle($feedHandle);

        if (!$feed || !$feed->enabled) {
            return [];
        }

        return $this->getPostsForSources($feed->getSources(), $options);
    }

    public function renderPostsForFeed(string $feedHandle, array $options = []): Markup
    {
        $posts = $this->getPostsForFeed($feedHandle, $options);

        return Template::raw(Craft::$app->getView()->renderTemplate('social-feeds/_posts/index', [
            'posts' => $posts,
            'renderOptions' => $options,
        ], View::TEMPLATE_MODE_CP));
    }

    public function getPostsForSources(array $sources, array $options = []): array
    {
        $settings = SocialFeeds::$plugin->getSettings();

        $posts = [];

        // If loading from the cache, far more efficient to query the database in one go for multiple sources
        if ($settings->enableCache) {
            $posts = $this->getPostsFromCache($sources, $options);
        } else {
            // Fetch all source posts. They'll be out of order, however.
            // Because we're not loading from the database, we need to roll out own order/limit/offsets
            foreach ($sources as $source) {
                $posts = array_merge($posts, $source->fetchPosts());
            }

            // Order all posts by their date
            usort($posts, function($a, $b) {
                if ($a->dateCreated == $b->dateCreated) {
                    return 0;
                }

                return $a->dateCreated < $b->dateCreated;
            });

            // Handle pagination with limit/offset
            $limit = $options['limit'] ?? count($posts);
            $offset = $options['offset'] ?? 0;

            $posts = array_slice($posts, $offset, $limit, true);
        }

        return $posts;
    }

    public function getPostsForSource(SourceInterface $source, array $options = []): array
    {
        return $this->getPostsForSources([$source], $options);
    }

    public function getPostsFromCache(array $sources, array $options = []): array
    {
        $posts = [];
        $query = ['or'];

        // Has the time lapsed between the last update and now? Should we ping the API for fresh posts?
        foreach ($sources as $source) {
            $this->checkForPostRefresh($source);

            // Build the query param to check for posts for the provider and cache key. This is for efficiency
            // where we can do a single database call, rather than one per source.
            $query[] = ['sourceId' => $source->id, 'cacheKey' => $source->getCacheKey()];
        }

        if ($query === ['or']) {
            return [];
        }

        // Allow pagination of queries
        $limit = $options['limit'] ?? null;
        $offset = $options['offset'] ?? null;

        $postRecords = $this->_createPostQuery()
            ->where($query)
            ->limit($limit)
            ->offset($offset)
            ->all();

        // Deserialize from the database back to Post objects
        $serializer = $this->_getSerializer();

        foreach ($postRecords as $result) {
            $posts[] = $serializer->deserialize($result['data'], Post::class, 'json');
        }

        return $posts;
    }

    public function checkForPostRefresh(SourceInterface $source): void
    {
        $settings = SocialFeeds::$plugin->getSettings();

        // Get the plugin setting for how long to cache items for and convert it from the friendly
        // DateInterval or seconds value to an interval. Then check against now. 
        // Don't forget the last fetch could be `null`.

        // Allow us to turn off auto-checking for posts when rendering/fetching. People might like to rely on a cron.
        if ($settings->cacheDuration === false) {
            return;
        }

        $interval = DateTimeHelper::toDateInterval(ConfigHelper::durationInSeconds($settings->cacheDuration));
        $threshold = $source->dateLastFetch?->add($interval)?->setTimezone(new DateTimeZone('UTC'));
        $currentTime = DateTimeHelper::currentUTCDateTime();

        // Have we reached the cache duration threshold to fetch again?
        if ($currentTime < $threshold) {
            return;
        }

        // Fetch the posts for the source and update the database cache
        $this->refreshPosts($source);
    }

    public function refreshPosts(SourceInterface $source, $consoleInstance = null): void
    {
        try {
            $serializer = $this->_getSerializer();

            // Fetch the posts from the API
            $posts = $source->fetchPosts();

            // Upsert each post individually to the database
            foreach ($posts as $post) {
                $postRecord = PostRecord::findOne([
                    'sourceId' => $source->id,
                    'postId' => $post->id,
                ]) ?? new PostRecord();

                $postRecord->sourceId = $source->id;
                $postRecord->cacheKey = $source->getCacheKey();
                $postRecord->postId = $post->id;
                $postRecord->data = $serializer->serialize($post, 'json');
                $postRecord->datePosted = $post->dateCreated ?? new DateTime();

                $postRecord->save(false);
            }

            // Update the sources' last fetch date
            Db::update('{{%socialfeeds_sources}}', [
                'dateLastFetch' => Db::prepareDateForDb(new DateTime())
            ], ['id' => $source->id]);
        } catch (Throwable $e) {
            SocialFeeds::error('Error refreshing posts for source “{source}”: “{message}” {file}:{line}', [
                'source' => $source->handle,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            // Throw the error with `devMode`
            if (App::devMode()) {
                throw $e;
            }
        }
    }


    // Private Methods
    // =========================================================================

    private function _createPostQuery(): Query
    {
        return (new Query())
            ->select([
                'id',
                'sourceId',
                'cacheKey',
                'postId',
                'data',
                'datePosted',
                'dateCreated',
                'dateUpdated',
            ])
            ->from(['{{%socialfeeds_posts}}'])
            ->orderBy(['datePosted' => SORT_DESC]);
    }

    private function _getSerializer(): Serializer
    {
        $encoders = [new JsonEncoder()];

        $normalizers = [
            new ArrayDenormalizer(),
            new DateTimeNormalizer(),
            new ObjectNormalizer(null, null, null, new ReflectionExtractor()),
        ];

        return new Serializer($normalizers, $encoders);
    }

}
