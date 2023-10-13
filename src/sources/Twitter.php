<?php
namespace verbb\socialfeeds\sources;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\base\OAuthSource;
use verbb\socialfeeds\helpers\SocialFeedsHelper;
use verbb\socialfeeds\models\Post;
use verbb\socialfeeds\models\PostAuthor;
use verbb\socialfeeds\models\PostLink;
use verbb\socialfeeds\models\PostMedia;

use Throwable;

use verbb\auth\providers\Twitter as TwitterProvider;

class Twitter extends OAuthSource
{
    // Static Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string
    {
        return TwitterProvider::class;
    }

    public static function getPostContent(Post $post): ?string
    {
        return null;
    }


    // Properties
    // =========================================================================

    public static string $providerHandle = 'twitter';

    public bool $enableUserHandles = false;
    public ?string $userHandles = null;
    public bool $enableHashtags = false;
    public ?string $hashtags = null;
    public bool $enableSearch = false;
    public ?string $searchTerms = null;
    public bool $enableMentions = false;
    public bool $enableLists = false;
    public ?string $listId = null;


    // Public Methods
    // =========================================================================

    public function getDefaultScopes(): array
    {
        return [
            'tweet.read',
            'users.read',
            'offline.access',
        ];
    }

    public function fetchPosts(): ?array
    {
        $settings = SocialFeeds::$plugin->getSettings();

        $posts = [];
        $query = [];

        $data = [];
        $users = [];
        $media = [];

        $userHandles = SocialFeedsHelper::splitString($this->userHandles);
        $hashtags = SocialFeedsHelper::splitString($this->hashtags);

        try {
            if ($this->enableUserHandles) {
                // Get User IDs from their handles
                $response = $this->request('GET', 'users/by', [
                    'query' => [
                        'usernames' => implode(',', $userHandles),
                    ],
                ]);

                foreach (($response['data'] ?? []) as $userInfo) {
                    $userId = $userInfo['id'];

                    $userTweetResponse = $this->request('GET', "users/$userId/tweets", [
                        'query' => [
                            'tweet.fields' => 'in_reply_to_user_id,author_id,attachments,created_at,entities,geo,id,public_metrics,source,text',
                            'user.fields' => 'id,username,name,profile_image_url,url',
                            'media.fields' => 'alt_text,duration_ms,height,media_key,preview_image_url,type,url,variants,width',
                            'expansions' => 'author_id,attachments.media_keys,in_reply_to_user_id',
                            'exclude' => 'retweets,replies',
                            'max_results' => $settings->postsLimit,
                        ],
                    ]);

                    // Extract tweets, media and users
                    $data = array_merge($data, ($userTweetResponse['data'] ?? []));
                    $users = array_merge($users, ($userTweetResponse['includes']['users'] ?? []));
                    $media = array_merge($media, ($userTweetResponse['includes']['media'] ?? []));
                }
            }

            if ($this->enableHashtags) {
                $query[] = '((#' . implode(' OR #', $hashtags) . ') -is:retweet)';
            }

            if ($this->enableSearch) {
                $query[] = '(' . $this->searchTerms . ')';
            }

            if ($this->enableMentions) {
                $query[] = '(has:mentions)';
            }

            if ($this->enableLists) {
                $query[] = '(list:' . $this->listId . ')';
            }

            if ($query) {
                $response = $this->request('GET', 'tweets/search/recent', [
                    'query' => [
                        'query' => implode(' ', $query),
                        'tweet.fields' => 'in_reply_to_user_id,author_id,attachments,created_at,entities,geo,id,public_metrics,source,text',
                        'user.fields' => 'id,username,name,profile_image_url,url',
                        'media.fields' => 'alt_text,duration_ms,height,media_key,preview_image_url,type,url,organic_metrics,promoted_metrics,variants,width',
                        'expansions' => 'author_id,attachments.media_keys,in_reply_to_user_id',
                        'max_results' => $settings->postsLimit, // Max 7 days at API level
                    ],
                ]);

                // Extract tweets, media and users
                $data = array_merge($data, ($response['data'] ?? []));
                $users = array_merge($users, ($response['includes']['users'] ?? []));
                $media = array_merge($media, ($response['includes']['media'] ?? []));
            }

            $userItems = [];
            $mediaItems = [];

            // Process extracted media and user information which is only included once, and used by reference
            foreach ($media as $attachment) {
                $id = $attachment['media_key'] ?? null;

                if ($id) {
                    if ($attachment['type'] === 'animated_gif' || $attachment['type'] === 'video') {
                        $mediaItems[$id][] = new PostMedia([
                            'type' => PostMedia::TYPE_IMAGE,
                            'id' => $id,
                            'url' => $attachment['preview_image_url'] ?? null,
                            'width' => $attachment['width'] ?? null,
                            'height' => $attachment['height'] ?? null,
                        ]);

                        foreach (($attachment['variants'] ?? []) as $variant) {
                            if ($variant['content_type'] === 'video/mp4') {
                                $mediaItems[$id][] = new PostMedia([
                                    'type' => PostMedia::TYPE_VIDEO,
                                    'url' => $variant['url'] ?? null,
                                ]);
                            }
                        }
                    } else if ($attachment['type'] === 'photo') {
                        $mediaItems[$id][] = new PostMedia([
                            'type' => PostMedia::TYPE_IMAGE,
                            'id' => $id,
                            'url' => $attachment['url'] ?? null,
                            'width' => $attachment['width'] ?? null,
                            'height' => $attachment['height'] ?? null,
                        ]);
                    }
                }
            }

            foreach ($users as $user) {
                $id = $user['id'] ?? null;

                if ($id) {
                    $userItems[$id] = new PostAuthor([
                        'id' => $user['id'] ?? null,
                        'username' => $user['username'] ?? null,
                        'url' => 'https://twitter.com/' . ($user['username'] ?? null),
                        'name' => $user['name'] ?? null,
                        'photo' => $user['profile_image_url'] ?? null,
                    ]);
                }
            }

            foreach ($data as $item) {
                $tags = [];
                $links = [];
                $images = [];
                $videos = [];

                foreach (($item['entities']['urls'] ?? []) as $attachment) {
                    if (isset($attachment['unwound_url'])) {
                        $links[] = new PostLink([
                            'title' => $attachment['title'] ?? null,
                            'url' => $attachment['expanded_url'] ?? null,
                        ]);
                    }
                }

                foreach (($item['entities']['hashtags'] ?? []) as $tag) {
                    $tags[] = $tag['tag'] ?? null;
                }

                foreach (($item['attachments']['media_keys'] ?? []) as $attachmentId) {
                    $media = $mediaItems[$attachmentId] ?? [];

                    foreach ($media as $m) {
                        if ($m->type === PostMedia::TYPE_IMAGE) {
                            $images[] = $m;
                        } else if ($m->type === PostMedia::TYPE_VIDEO) {
                            $videos[] = $m;
                        }
                    }
                }

                $author = $userItems[$item['author_id']] ?? null;

                // We have to do some extra work to format the text with links, hashtags, mentions and images
                $text = $item['text'] ?? '';

                $processedLinks = [];
                foreach (($item['entities']['urls'] ?? []) as $url) {
                    if (isset($url['media_key'])) {
                        $text = str_replace($url['url'], '', $text);
                    } else {
                        // Prevent processing it twice
                        if (!in_array($url['url'], $processedLinks)) {
                            $text = str_replace($url['url'], '<a href="' . $url['url'] . '" target="_blank">' . $url['display_url'] . '</a>', $text);

                            $processedLinks[] = $url['url'];
                        }
                    }
                }

                foreach (($item['entities']['hashtags'] ?? []) as $hashtag) {
                    $text = str_replace('#' . $hashtag['tag'], '<a href="https://twitter.com/search?q=%23' . $hashtag['tag'] . '" target="_blank">#' . $hashtag['tag'] . '</a>', $text);
                }

                foreach (($item['entities']['mentions'] ?? []) as $mention) {
                    $text = str_replace('@' . $mention['username'], '<a href="https://twitter.com/' . $mention['username'] . '" target="_blank">@' . $mention['username'] . '</a>', $text);
                }

                $text = trim($text);

                $posts[] = new Post([
                    'sourceId' => $this->id,
                    'sourceHandle' => $this->handle,
                    'sourceType' => self::$providerHandle,
                    'id' => $item['id'] ?? null,
                    'text' => $text,
                    'url' => 'https://twitter.com/' . ($author->username ?? '') . '/status/' . $item['id'],
                    'likes' => $item['public_metrics']['like_count'] ?? null,
                    'shares' => $item['public_metrics']['retweet_count'] ?? null,
                    'replies' => $item['public_metrics']['reply_count'] ?? null,
                    'dateCreated' => $item['created_at'] ?? null,
                    'data' => $item,
                    'tags' => $tags,
                    'links' => $links,
                    'images' => $images,
                    'videos' => $videos,
                    'author' => $author,
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
            ['userHandles'], 'required', 'when' => function($model) {
                return $model->enabled && $model->enableUserHandles;
            },
        ];

        $rules[] = [
            ['hashtags'], 'required', 'when' => function($model) {
                return $model->enabled && $model->enableHashtags;
            },
        ];

        $rules[] = [
            ['searchTerms'], 'required', 'when' => function($model) {
                return $model->enabled && $model->enableSearch;
            },
        ];

        $rules[] = [
            ['listId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->enableLists;
            },
        ];

        return $rules;
    }
}
