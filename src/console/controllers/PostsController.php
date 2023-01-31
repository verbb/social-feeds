<?php
namespace verbb\socialfeed\console\controllers;

use verbb\socialfeed\SocialFeed;

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

        $source = SocialFeed::$plugin->getSources()->getSourceByHandle($this->source, true, true);

        if ($this->limit) {
            SocialFeed::$plugin->getSettings()->postsLimit = $this->limit;
        }

        if ($source) {
            SocialFeed::$plugin->getPosts()->refreshPosts($source, $this);
        }

        return ExitCode::OK;
    }
}
