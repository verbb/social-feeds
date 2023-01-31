<?php
namespace verbb\socialfeed\variables;

use verbb\socialfeed\SocialFeed;
use verbb\socialfeed\base\SourceInterface;
use verbb\socialfeed\models\Feed;

use Twig\Markup;

class SocialFeedVariable
{
    // Public Methods
    // =========================================================================

    public function getPlugin(): SocialFeed
    {
        return SocialFeed::$plugin;
    }

    public function getPluginName(): string
    {
        return SocialFeed::$plugin->getPluginName();
    }

    public function getAllSources(): array
    {
        return SocialFeed::$plugin->getSources()->getAllSources();
    }

    public function getAllEnabledSources(): array
    {
        return SocialFeed::$plugin->getSources()->getAllEnabledSources();
    }

    public function getAllConfiguredSources(): array
    {
        return SocialFeed::$plugin->getSources()->getAllConfiguredSources();
    }

    public function getSourceById(int $id): ?SourceInterface
    {
        return SocialFeed::$plugin->getSources()->getSourceById($id);
    }

    public function getSourceByHandle(string $handle): ?SourceInterface
    {
        return SocialFeed::$plugin->getSources()->getSourceByHandle($handle);
    }

    public function getAllFeeds(): array
    {
        return SocialFeed::$plugin->getFeeds()->getAllFeeds();
    }

    public function getAllEnabledFeeds(): array
    {
        return SocialFeed::$plugin->getFeeds()->getAllEnabledFeeds();
    }

    public function getFeedById(int $id): ?Feed
    {
        return SocialFeed::$plugin->getFeeds()->getFeedById($id);
    }

    public function getFeedByUid(string $uid): ?Feed
    {
        return SocialFeed::$plugin->getFeeds()->getFeedByUid($uid);
    }

    public function getFeedByHandle(string $handle): ?Feed
    {
        return SocialFeed::$plugin->getFeeds()->getFeedByHandle($handle);
    }

    public function getPosts(string $feedHandle, array $options = []): array
    {
        return SocialFeed::$plugin->getPosts()->getPostsForFeed($feedHandle, $options);
    }

    public function renderPosts(string $feedHandle, array $options = []): Markup
    {
        return SocialFeed::$plugin->getPosts()->renderPostsForFeed($feedHandle, $options);
    }

}