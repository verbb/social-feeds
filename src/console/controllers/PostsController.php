<?php
namespace verbb\socialfeeds\console\controllers;

use Throwable;
use verbb\socialfeeds\SocialFeeds;

use craft\console\Controller;
use craft\helpers\Console;

use yii\console\ExitCode;
use yii\helpers\BaseConsole;

/**
 * Manages Social Feeds Posts.
 */
class PostsController extends Controller
{
    // Properties
    // =========================================================================

    /**
     * @var ?string The Source handle to refresh.
     */
    public ?string $source = null;

    /**
     * @var ?int The number of posts to fetch. Default to `postsLimit` plugin setting.
     */
    public ?int $limit = null;


    // Public Methods
    // =========================================================================

    public function options($actionID): array
    {
        $options = parent::options($actionID);

        if ($actionID === 'refresh') {
            $options[] = 'source';
            $options[] = 'limit';
        }

        return $options;
    }

    /**
     * Refresh a source's posts.
     */
    public function actionRefresh(): int
    {
        if (!$this->source) {
            $this->stderr('You must provide a --source handle.' . PHP_EOL, Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $source = SocialFeeds::$plugin->getSources()->getSourceByHandle($this->source, true, true);

        if (!$source) {
            $this->stderr('Invalid source handle specified.' . PHP_EOL, Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        if ($this->limit) {
            SocialFeeds::$plugin->getSettings()->postsLimit = $this->limit;
        }

        SocialFeeds::$plugin->getPosts()->refreshPosts($source, $this);

        $this->stdout("Source handle: $this->source was refreshed successfully!" . PHP_EOL, Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Refresh all enabled sources
     *
     * @throws Throwable
     * @return int
     */
    public function actionRefreshEnabledSources(): int
    {
        $sources = SocialFeeds::$plugin->getSources()->getAllEnabledSources();

        foreach ($sources as $source) {

            $this->stdout("Refreshing posts for source: $source->handle" . PHP_EOL, Console::FG_YELLOW);

            SocialFeeds::$plugin->getPosts()->refreshPosts($source, $this);
        }

        $this->stdout('All caches for sources cleared successfully' . PHP_EOL, Console::FG_GREEN);

        return ExitCode::OK;
    }
}
