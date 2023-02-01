<?php
namespace verbb\socialfeeds\console\controllers;

use verbb\socialfeeds\SocialFeeds;

use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

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

    public function actionRefresh(): int
    {
        if (!$this->source) {
            $this->stderr('You must provide a --source handle.' . PHP_EOL, Console::FG_RED);

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $source = SocialFeeds::$plugin->getSources()->getSourceByHandle($this->source, true, true);

        if ($this->limit) {
            SocialFeeds::$plugin->getSettings()->postsLimit = $this->limit;
        }

        if ($source) {
            SocialFeeds::$plugin->getPosts()->refreshPosts($source, $this);
        }

        return ExitCode::OK;
    }
}
