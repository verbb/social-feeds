<?php
namespace verbb\socialfeeds\base;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\services\Feeds;
use verbb\socialfeeds\services\Posts;
use verbb\socialfeeds\services\Service;
use verbb\socialfeeds\services\Sources;

use Craft;

use yii\log\Logger;

use verbb\auth\Auth;
use verbb\base\BaseHelper;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static SocialFeeds $plugin;


    // Static Methods
    // =========================================================================

    public static function log(string $message, array $params = []): void
    {
        $message = Craft::t('social-feeds', $message, $params);

        Craft::getLogger()->log($message, Logger::LEVEL_INFO, 'social-feeds');
    }

    public static function error(string $message, array $params = []): void
    {
        $message = Craft::t('social-feeds', $message, $params);

        Craft::getLogger()->log($message, Logger::LEVEL_ERROR, 'social-feeds');
    }


    // Public Methods
    // =========================================================================

    public function getFeeds(): Feeds
    {
        return $this->get('feeds');
    }

    public function getPosts(): Posts
    {
        return $this->get('posts');
    }

    public function getService(): Service
    {
        return $this->get('service');
    }

    public function getSources(): Sources
    {
        return $this->get('sources');
    }


    // Private Methods
    // =========================================================================

    private function _registerComponents(): void
    {
        $this->setComponents([
            'feeds' => Feeds::class,
            'posts' => Posts::class,
            'service' => Service::class,
            'sources' => Sources::class,
        ]);

        Auth::registerModule();
        BaseHelper::registerModule();
    }

    private function _registerLogTarget(): void
    {
        BaseHelper::setFileLogging('social-feeds');
    }

}