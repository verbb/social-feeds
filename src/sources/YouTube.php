<?php
namespace verbb\socialfeeds\sources;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\base\OAuthSource;
use verbb\socialfeeds\models\Post;
use verbb\socialfeeds\models\PostAuthor;
use verbb\socialfeeds\models\PostMedia;

use craft\helpers\App;

use Throwable;

use verbb\auth\providers\Google as GoogleProvider;

class YouTube extends OAuthSource
{
    // Static Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string
    {
        return GoogleProvider::class;
    }


    // Properties
    // =========================================================================

    public static string $providerHandle = 'youTube';

    public ?string $proxyRedirect = null;
    public bool $enableChannel = true;
    public ?string $channelId = null;
    public bool $enableUser = true;
    public ?string $userId = null;
    public bool $enablePlaylist = false;
    public ?string $playlistId = null;
    public bool $enableSearch = false;
    public ?string $searchTerms = null;


    // Public Methods
    // =========================================================================

    public function getProxyRedirect(): ?bool
    {
        return App::parseBooleanEnv($this->proxyRedirect);
    }

    public function getRedirectUri(): ?string
    {
        $uri = parent::getRedirectUri();

        // Allow a proxy to our server to forward on the request - just for local dev ease
        if ($this->getProxyRedirect()) {
            return "https://formie.verbb.io?return=$uri";
        }

        return $uri;
    }

    public function getDefaultScopes(): array
    {
        return [
            'https://www.googleapis.com/auth/youtube.readonly',
        ];
    }

    public function getAuthorizationUrlOptions(): array
    {
        return [
            // If we ever need to clear the cache on an authorized request
            // 'prompt' => 'consent',
            'access_type' => 'offline',
        ];
    }

    public function fetchPosts(): ?array
    {
        $settings = SocialFeeds::$plugin->getSettings();

        $posts = [];
        $items = [];

        try {
            if ($this->enableChannel || $this->enableUser) {
                $channelParam = ['mine' => true];

                if ($this->enableChannel) {
                    $channelParam = ['id' => $this->channelId];
                } else if ($this->enableUser) {
                    $channelParam = ['forUsername' => $this->userId];
                }

                $response = $this->request('GET', 'youtube/v3/channels', [
                    'query' => array_merge([
                        'part' => 'id,snippet,statistics,contentDetails,contentOwnerDetails',
                        'maxResults' => $settings->postsLimit,
                    ], $channelParam),
                ]);

                $channels = $response['items'] ?? [];

                foreach ($channels as $channel) {
                    $response = $this->request('GET', 'youtube/v3/playlistItems', [
                        'query' => [
                            'playlistId' => $channel['contentDetails']['relatedPlaylists']['uploads'] ?? null,
                            'part' => 'id,snippet,contentDetails',
                            'maxResults' => $settings->postsLimit,
                        ],
                    ]);

                    $videoResponseData = $response['items'] ?? [];

                    // Add the author info from the channel
                    foreach ($videoResponseData as $key => $d) {
                        $videoResponseData[$key]['author'] = $channel;
                    }

                    $items = array_merge($items, $videoResponseData);
                }
            }

            if ($this->enablePlaylist) {
                $response = $this->request('GET', 'youtube/v3/playlistItems', [
                    'query' => [
                        'playlistId' => $this->playlistId,
                        'part' => 'id,snippet,contentDetails',
                        'maxResults' => $settings->postsLimit,
                    ],
                ]);

                $items = $response['items'] ?? [];
            }

            if ($this->enableSearch) {
                $response = $this->request('GET', 'youtube/v3/search', [
                    'query' => array_merge([
                        'type' => 'video',
                        'part' => 'id',
                        'maxResults' => $settings->postsLimit,
                    ], $this->_getParams($this->searchTerms)),
                ]);

                $searchItems = $response['items'] ?? [];

                $videoIds = [];

                foreach ($searchItems as $searchItem) {
                    $videoIds[] = $searchItem['id']['videoId'] ?? null;
                }

                $videoIds = implode(',', array_filter($videoIds));

                $response = $this->request('GET', 'youtube/v3/videos', [
                    'query' => [
                        'part' => 'id,statistics,snippet,contentDetails',
                        'id' => $videoIds,
                        'maxResults' => $settings->postsLimit,
                    ],
                ]);

                $items = $response['items'] ?? [];
            }

            foreach ($items as $item) {
                $author = null;
                $links = [];
                $images = [];

                $thumbnails = $item['snippet']['thumbnails'] ?? [];
                $image = end($thumbnails);

                if ($image) {
                    $images[] = new PostMedia([
                        'type' => PostMedia::TYPE_IMAGE,
                        'url' => $image['url'] ?? null,
                        'width' => $image['width'] ?? null,
                        'height' => $image['height'] ?? null,
                    ]);
                }

                if (isset($item['author'])) {
                    $thumbnails = $item['author']['snippet']['thumbnails'] ?? [];
                    $image = end($thumbnails);

                    $author = new PostAuthor([
                        'id' => $item['author']['id'] ?? null,
                        'name' => $item['author']['snippet']['title'] ?? null,
                        'url' => 'https://www.youtube.com/channel/' . ($item['author']['id'] ?? null),
                        'photo' => $image['url'] ?? null,
                    ]);
                }

                $posts[] = new Post([
                    'sourceId' => $this->id,
                    'sourceHandle' => $this->handle,
                    'sourceType' => self::$providerHandle,
                    'id' => $item['snippet']['resourceId']['videoId'] ?? $item['id'] ?? null,
                    'uid' => $item['id'] ?? null,
                    'title' => $item['snippet']['title'] ?? null,
                    'text' => $item['snippet']['description'] ?? null,
                    'url' => 'https://www.youtube.com/watch?v=' . ($item['snippet']['resourceId']['videoId'] ?? null),
                    'likes' => $item['statistics']['likeCount'] ?? null,
                    'dateCreated' => $item['snippet']['publishedAt'] ?? null,
                    'data' => $item,
                    'tags' => $item['snippet']['tags'] ?? null,
                    'images' => $images,
                    'author' => $author,
                    'links' => $links,
                ]);
            }
        } catch (Throwable $e) {
            self::apiError($this, $e, false);
        }
        
        return $posts;
    }


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [
            ['channelId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->enableChannel;
            },
        ];

        $rules[] = [
            ['userId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->enableUser;
            },
        ];

        $rules[] = [
            ['playlistId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->enablePlaylist;
            },
        ];

        $rules[] = [
            ['searchTerms'], 'required', 'when' => function($model) {
                return $model->enabled && $model->enableSearch;
            },
        ];

        return $rules;
    }


    // Private Methods
    // =========================================================================

    private function _getParams(string $string): array
    {
        $params = [];

        foreach (explode('&', $string) as $part) {
            if ($part = trim($part)) {
                $parts = explode('=', $part);
                $key = $parts[0] ?? null;
                $value = $parts[1] ?? null;

                if ($key && $value) {
                    $params[$key] = $value;
                }
            }
        }

        return $params;
    }
}
