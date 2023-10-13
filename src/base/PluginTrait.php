<?php
namespace verbb\socialfeeds\base;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\services\Feeds;
use verbb\socialfeeds\services\Posts;
use verbb\socialfeeds\services\Service;
use verbb\socialfeeds\services\Sources;

use verbb\base\LogTrait;
use verbb\base\helpers\Plugin;

use verbb\auth\Auth;

trait PluginTrait
{
    // Properties
    // =========================================================================

    public static ?SocialFeeds $plugin = null;


    // Traits
    // =========================================================================

    use LogTrait;
    

    // Static Methods
    // =========================================================================

    public static function config(): array
    {
        Auth::registerModule();
        Plugin::bootstrapPlugin('social-feeds');

        return [
            'components' => [
                'feeds' => Feeds::class,
                'posts' => Posts::class,
                'service' => Service::class,
                'sources' => Sources::class,
            ],
        ];
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

}