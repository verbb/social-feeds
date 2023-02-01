<?php
namespace verbb\socialfeeds\variables;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\base\SourceInterface;
use verbb\socialfeeds\models\Feed;

use Twig\Markup;

class SocialFeedsVariable
{
    // Public Methods
    // =========================================================================

    public function getPlugin(): SocialFeeds
    {
        return SocialFeeds::$plugin;
    }

    public function getPluginName(): string
    {
        return SocialFeeds::$plugin->getPluginName();
    }

    public function getAllSources(): array
    {
        return SocialFeeds::$plugin->getSources()->getAllSources();
    }

    public function getAllEnabledSources(): array
    {
        return SocialFeeds::$plugin->getSources()->getAllEnabledSources();
    }

    public function getAllConfiguredSources(): array
    {
        return SocialFeeds::$plugin->getSources()->getAllConfiguredSources();
    }

    public function getSourceById(int $id): ?SourceInterface
    {
        return SocialFeeds::$plugin->getSources()->getSourceById($id);
    }

    public function getSourceByHandle(string $handle): ?SourceInterface
    {
        return SocialFeeds::$plugin->getSources()->getSourceByHandle($handle);
    }

    public function getAllFeeds(): array
    {
        return SocialFeeds::$plugin->getFeeds()->getAllFeeds();
    }

    public function getAllEnabledFeeds(): array
    {
        return SocialFeeds::$plugin->getFeeds()->getAllEnabledFeeds();
    }

    public function getFeedById(int $id): ?Feed
    {
        return SocialFeeds::$plugin->getFeeds()->getFeedById($id);
    }

    public function getFeedByUid(string $uid): ?Feed
    {
        return SocialFeeds::$plugin->getFeeds()->getFeedByUid($uid);
    }

    public function getFeedByHandle(string $handle): ?Feed
    {
        return SocialFeeds::$plugin->getFeeds()->getFeedByHandle($handle);
    }

    public function getPosts(string $feedHandle, array $options = []): array
    {
        return SocialFeeds::$plugin->getPosts()->getPostsForFeed($feedHandle, $options);
    }

    public function renderPosts(string $feedHandle, array $options = []): Markup
    {
        return SocialFeeds::$plugin->getPosts()->renderPostsForFeed($feedHandle, $options);
    }

}