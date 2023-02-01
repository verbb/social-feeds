<?php
namespace verbb\socialfeeds\sources;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\base\OAuthSource;
use verbb\socialfeeds\models\Post;
use verbb\socialfeeds\models\PostAuthor;
use verbb\socialfeeds\models\PostLink;
use verbb\socialfeeds\models\PostMedia;

use Throwable;

use verbb\auth\Auth;
use verbb\auth\providers\Facebook as FacebookProvider;

class Facebook extends OAuthSource
{
    // Static Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string
    {
        return FacebookProvider::class;
    }

    
    // Properties
    // =========================================================================

    public static string $providerHandle = 'facebook';
    
    public bool $enableProfile = true;
    public bool $enablePhotos = false;
    public bool $enableVideos = false;
    public bool $enableEvents = false;
    public ?string $endpoint = null;
    public ?string $groupId = null;
    public ?string $pageId = null;


    // Public Methods
    // =========================================================================

    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [
            ['endpoint'], 'required', 'when' => function($model) {
                return $model->enabled;
            },
        ];

        $rules[] = [
            ['groupId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->endpoint === 'group' && $model->isConnected();
            },
        ];

        $rules[] = [
            ['pageId'], 'required', 'when' => function($model) {
                return $model->enabled && $model->endpoint === 'page' && $model->isConnected();
            },
        ];

        return $rules;
    }

    public function getOAuthProviderConfig(): array
    {
        $config = parent::getOAuthProviderConfig();
        $config['graphApiVersion'] = 'v15.0';

        return $config;
    }

    public function getAuthorizationUrlOptions(): array
    {
        return [
            // API version 7.0+
            'scope' => [
                'pages_read_engagement',
                'pages_manage_metadata',
                'pages_read_user_content',
                'groups_access_member_info',
            ],
        ];
    }

    public function fetchSourceSettings(string $settingsKey): ?array
    {
        try {
            if ($settingsKey === 'pageId') {
                $pages = [];

                $response = $this->request('GET', 'me/accounts');
                $sources = $response['data'] ?? [];

                foreach ($sources as $source) {
                    $pages[] = [
                        'label' => $source['name'] ?? null,
                        'value' => $source['id'] ?? null,
                    ];
                }

                return $pages;
            }

            if ($settingsKey === 'groupId') {
                $pages = [];

                $response = $this->request('GET', 'me/groups');
                $sources = $response['data'] ?? [];

                foreach ($sources as $source) {
                    $pages[] = [
                        'label' => $source['name'] ?? null,
                        'value' => $source['id'] ?? null,
                    ];
                }

                return $pages;
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

        try {
            if ($this->endpoint == 'page') {
                // This will fail if not a page (Business or Group) so catch and continue
                try {
                    $response = $this->request('GET', $this->pageId, [
                        'query' => ['fields' => 'access_token'],
                    ]);

                    $pageAccessToken = $response['access_token'] ?? null;

                    // Update the token in Auth to use this from now on.
                    if ($pageAccessToken && $token = $this->getToken()) {
                        $token->accessToken = $pageAccessToken;

                        Auth::$plugin->getTokens()->saveToken($token);
                    }
                } catch (Throwable $e) {
                    self::apiError($this, $e, false);
                }
            }

            $postType = null;
            $endpoint = [];
            $fields = [];

            if ($this->endpoint === 'page') {
                $endpoint[] = $this->pageId;
            } else if ($this->endpoint === 'group') {
                $endpoint[] = $this->groupId;
            }

            if ($this->enableProfile) {
                $postType = 'post';
                $endpoint[] = 'posts';

                $fields = [
                    // 'actions',
                    // 'admin_creator',
                    // 'allowed_advertising_objectives',
                    // 'application',
                    'backdated_time',
                    'call_to_action',
                    // 'can_reply_privately',
                    // 'child_attachments',
                    // 'comments_mirroring_domain',
                    // 'coordinates',
                    'created_time',
                    // 'event',
                    // 'expanded_height',
                    // 'expanded_width',
                    // 'feed_targeting',
                    'from{picture,id,name,link}',
                    'full_picture',
                    // 'height',
                    // 'icon',
                    // 'id',
                    // 'instagram_eligibility',
                    // 'is_app_share',
                    // 'is_eligible_for_promotion',
                    // 'is_expired',
                    // 'is_hidden',
                    // 'is_inline_created',
                    // 'is_instagram_eligible',
                    // 'is_popular',
                    // 'is_published',
                    // 'is_spherical',
                    'message',
                    'message_tags',
                    // 'multi_share_end_card',
                    // 'multi_share_optimized',
                    // 'parent_id',
                    // 'place',
                    // 'privacy',
                    // 'promotable_id',
                    // 'promotion_status',
                    // 'properties',
                    // 'scheduled_publish_time',
                    'shares',
                    'status_type',
                    // 'story',
                    'story_tags',
                    // 'subscribed',
                    // 'target',
                    // 'targeting',
                    // 'timeline_visibility',
                    'updated_time',
                    // 'via',
                    // 'video_buying_eligibility',
                    // 'width',

                    'attachments{title,description,media_type,unshimmed_url,target{id},multi_share_end_card,media{source,image},subattachments}',
                    'comments.summary(true).limit(0)',
                    // 'dynamic_posts',
                    // 'insights',
                    'likes.summary(true).limit(0)',
                    'permalink_url',
                    // 'reactions',
                    // 'sharedposts',
                    // 'sponsor_tags',
                    // 'to',
                ];  
            } else if ($this->enablePhotos) {
                $postType = 'photo';
                $endpoint[] = 'photos?type=uploaded';

                $fields = [
                    'album',
                    'alt_text',
                    'alt_text_custom',
                    // 'backdated_time',
                    // 'backdated_time_granularity',
                    // 'can_backdate',
                    // 'can_delete',
                    // 'can_tag',
                    'created_time',
                    'event',
                    'from{picture,id,name,link}',
                    'height',
                    // 'icon',
                    'id',
                    'images',
                    'link',
                    'name',
                    'name_tags',
                    // 'page_story_id',
                    'place',
                    'target',
                    'updated_time',
                    'webp_images',
                    'width',

                    'comments.summary(true).limit(0)',
                    'insights',
                    'likes',
                    'picture',
                    // 'sponsor_tags',
                ];
            } else if ($this->enableVideos) {
                $postType = 'video';
                $endpoint[] = 'videos';

                $fields = [
                    // 'ad_breaks',
                    // 'backdated_time',
                    // 'backdated_time_granularity',
                    'content_category',
                    'content_tags',
                    'created_time',
                    'custom_labels',
                    'description',
                    'embed_html',
                    // 'embeddable',
                    'event',
                    // 'format',
                    'from{picture,id,name,link}',
                    // 'icon',
                    'id',
                    // 'is_crosspost_video',
                    // 'is_crossposting_eligible',
                    // 'is_episode',
                    // 'is_instagram_eligible',
                    // 'is_reference_only',
                    'length',
                    'live_status',
                    // 'music_video_copyright',
                    'place',
                    'post_views',
                    // 'premiere_living_room_status',
                    // 'privacy',
                    'published',
                    // 'scheduled_publish_time',
                    'source',
                    // 'status',
                    'title',
                    'universal_video_id',
                    'updated_time',
                    'views',

                    'captions',
                    'comments.summary(true).limit(0)',
                    // 'crosspost_shared_pages',
                    'likes.summary(true).limit(0)',
                    'permalink_url',
                    'picture',
                    // 'poll_settings',
                    // 'polls',
                    // 'sponsor_tags',
                    'tags',
                    'thumbnails',
                    'video_insights',
                ];
            } else if ($this->enableEvents) {
                $postType = 'event';
                $endpoint[] = 'events';

                $fields = [
                    'attending_count',
                    // 'can_guests_invite',
                    'category',
                    'cover',
                    'created_time',
                    'declined_count',
                    'description',
                    // 'discount_code_enabled',
                    'end_time',
                    // 'event_times',
                    // 'guest_list_enabled',
                    'id',
                    'interested_count',
                    // 'is_canceled',
                    // 'is_draft',
                    // 'is_online',
                    // 'is_page_owned',
                    'maybe_count',
                    'name',
                    'noreply_count',
                    'online_event_format',
                    'online_event_third_party_url',
                    'owner',
                    'parent_group',
                    'place',
                    'scheduled_publish_time',
                    'start_time',
                    'ticket_uri',
                    'ticket_uri_start_sales_time',
                    'ticketing_privacy_uri',
                    'ticketing_terms_uri',
                    'timezone',
                    'type',
                    'updated_time',

                    'comments.summary(true).limit(0)',
                    'feed',
                    'live_videos',
                    'photos',
                    'picture',
                    'roles',
                    'ticket_tiers',
                    'videos',
                ];
            }

            $response = $this->request('GET', implode('/', $endpoint), [
                'query' => [
                    'fields' => implode(',', $fields),
                    'limit' => $settings->postsLimit,
                ],
            ]);

            $data = $response['data'] ?? [];

            foreach ($data as $item) {
                $tags = [];
                $links = [];
                $images = [];
                $videos = [];
                $meta = [];

                if ($postType === 'post') {
                    foreach (($item['attachments']['data'] ?? []) as $attachment) {
                        if ($attachment['media_type'] === 'photo' || $attachment['media_type'] === 'video') {
                            $images[] = new PostMedia([
                                'type' => PostMedia::TYPE_IMAGE,
                                'id' => $attachment['target']['id'] ?? null,
                                'url' => $attachment['media']['image']['src'] ?? null,
                                'width' => $attachment['media']['image']['width'] ?? null,
                                'height' => $attachment['media']['image']['height'] ?? null,
                            ]);

                            if ($attachment['media_type'] === 'video') {
                                $videos[] = new PostMedia([
                                    'type' => PostMedia::TYPE_VIDEO,
                                    'id' => $attachment['target']['id'] ?? null,
                                    'url' => 'https://www.facebook.com/v2.3/plugins/video.php?href=' . ($attachment['unshimmed_url'] ?? null),
                                ]);
                            }
                        }

                        if ($attachment['media_type'] === 'link') {
                            $links[] = new PostLink([
                                'title' => $attachment['title'] ?? null,
                                'url' => $attachment['unshimmed_url'] ?? null,
                            ]);
                        }
                    }
                }

                if ($postType === 'photo') {
                    $images[] = new PostMedia([
                        'type' => PostMedia::TYPE_IMAGE,
                        'title' => $item['alt_text'] ?? null,
                        'url' => $item['images'][0]['source'] ?? null,
                        'width' => $item['images'][0]['width'] ?? null,
                        'height' => $item['images'][0]['height'] ?? null,
                    ]);

                    $item['permalink_url'] = $item['link'] ?? null;

                    $meta = [
                        'album' => $item['album'] ?? null,
                    ];
                }

                if ($postType === 'video') {
                    $images[] = new PostMedia([
                        'type' => PostMedia::TYPE_IMAGE,
                        'id' => $item['thumbnails']['data'][0]['id'] ?? null,
                        'url' => $item['thumbnails']['data'][0]['uri'] ?? null,
                        'width' => $item['thumbnails']['data'][0]['width'] ?? null,
                        'height' => $item['thumbnails']['data'][0]['height'] ?? null,
                    ]);

                    $videos[] = new PostMedia([
                        'type' => PostMedia::TYPE_VIDEO,
                        'url' => 'https://www.facebook.com/v2.3/plugins/video.php?href=https://facebook.com/' . ($item['permalink_url'] ?? null),
                    ]);

                    $meta = [
                        'embed_html' => $item['embed_html'] ?? null,
                        'length' => $item['length'] ?? null,
                        'views' => $item['views'] ?? null,
                    ];
                }

                if ($postType === 'event') {
                    $meta = [
                        'start_time' => $item['start_time'] ?? null,
                        'end_time' => $item['end_time'] ?? null,
                        'attending_count' => $item['attending_count'] ?? null,
                        'declined_count' => $item['declined_count'] ?? null,
                        'interested_count' => $item['interested_count'] ?? null,
                        'maybe_count' => $item['maybe_count'] ?? null,
                        'location' => $item['place'] ?? null,
                    ];

                    $item['message'] = $item['description'] ?? null;
                }

                $posts[] = new Post([
                    'sourceId' => $this->id,
                    'sourceHandle' => $this->handle,
                    'sourceType' => self::$providerHandle,
                    'id' => $item['id'] ?? null,
                    'title' => $item['name'] ?? null,
                    'text' => $item['message'] ?? null,
                    'url' => $item['permalink_url'] ?? null,
                    'postType' => $postType,
                    'likes' => $item['likes']['summary']['total_count'] ?? null,
                    'shares' => $item['shares']['count'] ?? null,
                    'replies' => $item['comments']['summary']['total_count'] ?? null,
                    'dateCreated' => $item['created_time'] ?? null,
                    'dateUpdated' => $item['updated_time'] ?? null,
                    'data' => $item,
                    'tags' => $tags,
                    'links' => $links,
                    'images' => $images,
                    'videos' => $videos,
                    'meta' => $meta,

                    'author' => new PostAuthor([
                        'id' => $item['from']['id'] ?? null,
                        'name' => $item['from']['name'] ?? null,
                        'url' => $item['from']['link'] ?? null,
                        'photo' => $item['from']['picture']['data']['url'] ?? null,
                    ]),
                ]);
            }
        } catch (Throwable $e) {
            self::apiError($this, $e, false);
        }

        return $posts;
    }
}
