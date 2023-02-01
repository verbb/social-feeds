<?php
namespace verbb\socialfeeds\sources;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\base\OAuthSource;
use verbb\socialfeeds\helpers\SocialFeedsHelper;
use verbb\socialfeeds\models\Post;
use verbb\socialfeeds\models\PostAuthor;
use verbb\socialfeeds\models\PostMedia;

use Throwable;

use verbb\auth\providers\Facebook as InstagramProvider;

class Instagram extends OAuthSource
{
    // Static Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string
    {
        return InstagramProvider::class;
    }

    
    // Properties
    // =========================================================================

    public static string $providerHandle = 'instagram';

    public bool $enableProfile = true;
    public bool $enableHashtags = false;
    public bool $enableTags = false;
    public ?string $accountId = null;
    public ?string $hashtags = null;
    public ?string $hashtagsOrderBy = 'recent';


    // Public Methods
    // =========================================================================

    public function getAuthorizationUrlOptions(): array
    {
        return [
            'scope' => [
                'pages_show_list',
                'instagram_basic',
                'instagram_manage_comments',
                'pages_read_engagement',
            ],
        ];
    }

    public function getOAuthProviderConfig(): array
    {
        $config = parent::getOAuthProviderConfig();
        $config['graphApiVersion'] = 'v15.0';

        return $config;
    }

    public function fetchSourceSettings(string $settingsKey): ?array
    {
        try {
            if ($settingsKey === 'accountId') {
                $accounts = [];

                $response = $this->request('GET', 'me/accounts', [
                    'query' => [
                        'fields' => 'instagram_business_account,access_token',
                        'limit' => 500,
                    ],
                ]);

                $pages = $response['data'] ?? [];

                foreach ($pages as $page) {
                    $instagramId = $page['instagram_business_account']['id'] ?? null;

                    if ($instagramId) {
                        $response = $this->request('GET', $instagramId, [
                            'query' => [
                                'fields' => 'name,username,profile_picture_url',
                            ],
                        ]);

                        $accounts[] = [
                            'label' => $response['username'] ?? null,
                            'value' => $response['id'] ?? null,
                        ];
                    }
                }

                return $accounts;
            }
        } catch (Throwable $e) {
            self::apiError($this, $e);
        }

        return parent::fetchSourceSettings($settingsKey);
    }

    public function fetchPosts(): ?array
    {
        $settings = SocialFeeds::$plugin->getSettings();

        $posts = [];

        $response = ['data' => []];

        $extendedFields = [
            'caption',
            'comments_count',
            'id',
            'ig_id',
            'is_comment_enabled',
            'is_shared_to_feed',
            'like_count',
            'media_product_type',
            'media_type',
            'media_url',
            'owner{id,ig_id,profile_picture_url,name,username}',
            'permalink',
            'shortcode',
            'thumbnail_url',
            'timestamp',
            'username',
            
            'children{media_type,media_url,thumbnail_url}',
            'comments.summary(true).limit(0)',
            'likes.summary(true).limit(0)',
        ];

        $standardFields = [
            'caption',
            'children{media_url,id,media_type,permalink}',
            'comments_count',
            'id',
            'like_count' ,
            'media_type',
            'media_url',
            'permalink',
            'timestamp',
        ];

        try {
            if ($this->enableProfile) {
                $profileResponse = $this->request('GET', "$this->accountId/media", [
                    'query' => [
                        'fields' => implode(',', $extendedFields),
                        'limit' => $settings->postsLimit,
                    ],
                ]);

                $profileResponseData = $profileResponse['data'] ?? [];
                $response['data'] = array_merge($response['data'], $profileResponseData);
            }
            
            if ($this->enableHashtags) {
                $hashtags = SocialFeedsHelper::splitString($this->hashtags);

                foreach ($hashtags as $hashtag) {
                    // Fetch the hashtag ID from Facebook or the cache
                    $hashTagId = $this->_getOrSetHashtag($hashtag);
                    $endpoint = null;

                    if ($this->hashtagsOrderBy === 'recent') {
                        $endpoint = 'recent_media';
                    } else if ($this->hashtagsOrderBy === 'rated') {
                        $endpoint = 'top_media';
                    }

                    $tagResponse = $this->request('GET', "$hashTagId/$endpoint", [
                        'query' => [
                            'user_id' => $this->accountId,
                            'fields' => implode(',', $standardFields),
                            'limit' => $settings->postsLimit,
                        ],
                    ]);

                    $tagResponseData = $tagResponse['data'] ?? [];
                    $response['data'] = array_merge($response['data'], $tagResponseData);
                }
            }

            if ($this->enableTags) {
                $taggedResponse = $this->request('GET', "$this->accountId/tags", [
                    'query' => [
                        'user_id' => $this->accountId,
                        'fields' => implode(',', $standardFields),
                        'limit' => $settings->postsLimit,
                    ],
                ]);

                $taggedResponseData = $taggedResponse['data'] ?? [];
                $response['data'] = array_merge($response['data'], $taggedResponseData);
            }

            $data = $response['data'] ?? [];

            foreach ($data as $item) {
                $tags = [];
                $links = [];

                $mediaType = $item['media_type'] ?? null;

                $images = $this->_getMediaItems($item)['images'];
                $videos = $this->_getMediaItems($item)['videos'];

                if ($mediaType === 'CAROUSEL_ALBUM') {
                    foreach (($item['children']['data'] ?? []) as $attachment) {
                        $images = array_merge($images, $this->_getMediaItems($attachment)['images']);
                        $videos = array_merge($videos, $this->_getMediaItems($attachment)['videos']);
                    }
                }

                $posts[] = new Post([
                    'sourceId' => $this->id,
                    'sourceHandle' => $this->handle,
                    'sourceType' => self::$providerHandle,
                    'id' => $item['id'] ?? null,
                    'text' => $item['caption'] ?? null,
                    'url' => $item['permalink'] ?? null,
                    'postType' => strtolower(($item['media_type'] ?? null)),
                    'dateCreated' => $item['timestamp'] ?? null,
                    'data' => $item,
                    'tags' => $tags,
                    'links' => $links,
                    'images' => $images,
                    'videos' => $videos,

                    'author' => new PostAuthor([
                        'id' => $item['owner']['id'] ?? null,
                        'username' => $item['owner']['username'] ?? null,
                        'name' => $item['owner']['name'] ?? null,
                        'photo' => $item['owner']['profile_picture_url'] ?? null,
                    ]),
                ]);
            }
        } catch (Throwable $e) {
            self::apiError($this, $e, false);
        }
        
        return $posts;
    }


    // Private Methods
    // =========================================================================

    private function _getMediaItems(array $item): array
    {
        $images = [];
        $videos = [];
        $mediaType = $item['media_type'] ?? null;

        if ($mediaType === 'IMAGE') {
            $images[] = new PostMedia([
                'type' => PostMedia::TYPE_IMAGE,
                'url' => $item['media_url'] ?? null,
            ]);
        }

        if ($mediaType === 'VIDEO') {
            $images[] = new PostMedia([
                'type' => PostMedia::TYPE_IMAGE,
                'url' => $item['thumbnail_url'] ?? null,
            ]);

            $videos[] = new PostMedia([
                'type' => PostMedia::TYPE_VIDEO,
                'url' => $item['media_url'] ?? null,
            ]);
        }

        return ['images' => $images, 'videos' => $videos];
    }

    private function _getOrSetHashtag(string $hashtag): string
    {
        $cachedHashtags = $this->cache['hashtags'] ?? [];

        // Query hashtags to get their ID, and save to the db cache to save extra lookups.
        // There are API limits to searching for hashtags.
        if (isset($cachedHashtags[$hashtag])) {
            return $cachedHashtags[$hashtag];
        }

        $response = $this->request('GET', 'ig_hashtag_search', [
            'query' => [
                'user_id' => $this->accountId,
                'q' => $hashtag,
            ],
        ]);

        $cachedHashtags[$hashtag] = $response['data'][0]['id'] ?? null;

        // Update the cached hashtags
        $this->setSettingCache(['hashtags' => $cachedHashtags]);

        return $cachedHashtags[$hashtag];
    }
}
