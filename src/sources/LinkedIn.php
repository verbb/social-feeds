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
use verbb\auth\providers\LinkedIn as LinkedInProvider;

class LinkedIn extends OAuthSource
{
    // Static Methods
    // =========================================================================

    public static function getOAuthProviderClass(): string
    {
        return LinkedInProvider::class;
    }

    
    // Properties
    // =========================================================================

    public static string $providerHandle = 'linkedIn';
    
    public bool $enableProfile = true;
    public bool $enableCompany = false;
    public ?string $profileUrl = null;
    public ?string $companyUrl = null;


    // Public Methods
    // =========================================================================

    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [
            ['companyUrl'], 'required', 'when' => function($model) {
                return $model->enabled && $model->enableCompany;
            },
        ];

        return $rules;
    }

    public function getOAuthProviderConfig(): array
    {
        $config = parent::getOAuthProviderConfig();

        // Use the "Posts API"
        // https://learn.microsoft.com/en-us/linkedin/marketing/integrations/community-management/shares/posts-api
        $config['restProtocolVersion'] = '2.0.0';
        $config['restVersion'] = '202509';

        // We need to reset the default scopes, because different ones are required depending on whether
        // posting to a personal page, or an organization. This is because the "Community Management API"
        // which is needed to post to an organisation can be the only product for a LinkedIn app.
        $config['defaultScopes'] = [];

        return $config;
    }

    public function getDefaultScopes(): array
    {
        // Posting to a company page requires the "Community Management API" permission.
        if ($this->enableCompany) {
            return [
                'r_basicprofile',
                'r_organization_social',
                'r_organization_social_feed',
            ];
        }

        return [];
    }

    public function fetchPosts(): ?array
    {
        $settings = SocialFeeds::$plugin->getSettings();

        $posts = [];
        $posts = [];

        try {
            $company = null;

            if ($this->enableCompany) {
                $companyUrl = $this->companyUrl ?? '';
                $companyId = basename(parse_url($companyUrl, PHP_URL_PATH));

                // Check if it's a vanity name in the URL, a different fetch required
                if (!is_numeric($companyId)) {
                    $response = $this->request('GET', 'https://api.linkedin.com/rest/organizations', [
                        'query' => [
                            'q' => 'vanityName',
                            'vanityName' => $companyId,
                        ],
                    ]);

                    $company = $response['elements'][0] ?? null;
                } else {
                    $company = $this->request('GET', 'https://api.linkedin.com/rest/organizations/' . $companyId);
                }
            }

            if (!$company) {
                return [];
            }

            // Fetch the company images for the author.
            $companyImage = null;
            $companyImageUrn = str_replace(':digitalmediaAsset:', ':image:', ($company['logoV2']['cropped'] ?? ''));

            if ($companyImageUrn) {
                $response = $this->request('GET', 'https://api.linkedin.com/rest/images/' . urlencode($companyImageUrn));

                $companyImage = $response['downloadUrl'] ?? null;
            }

            $author = new PostAuthor([
                'id' => $company['id'] ?? null,
                'username' => $company['vanityName'] ?? null,
                'name' => $company['localizedName'] ?? null,
                'photo' => $companyImage,
            ]);

            // We have to use the full URL here, the client will assume `api.linkedin.com/v2`.
            $response = $this->request('GET', 'https://api.linkedin.com/rest/posts', [
                'query' => [
                    'q' => 'author',
                    'author' => 'urn:li:organization:' . $company['id'],
                ],
            ]);

            $data = $response['elements'] ?? [];

            foreach ($data as $item) {
                $links = [];
                $images = [];
                $videos = [];

                if ($link = ($item['content']['article'] ?? null)) {
                    $links[] = new PostLink([
                        'title' => $link['title'] ?? null,
                        'url' => $link['source'] ?? null,
                    ]);
                }

                if ($media = ($item['content']['media'] ?? null)) {
                    $mediaId = $media['id'] ?? null;
                    $mediaTitle = $media['altText'] ?? $media['title'] ?? null;

                    if (str_contains($mediaId, ':image:')) {
                        $response = $this->request('GET', 'https://api.linkedin.com/rest/images/' . urlencode($mediaId));

                        $images[] = new PostMedia([
                            'type' => PostMedia::TYPE_IMAGE,
                            'id' => $mediaId,
                            'title' => $mediaTitle,
                            'url' => $response['downloadUrl'] ?? null,
                        ]);
                    } else if (str_contains($mediaId, ':video:')) {
                        $response = $this->request('GET', 'https://api.linkedin.com/rest/videos/' . urlencode($mediaId));

                        $images[] = new PostMedia([
                            'type' => PostMedia::TYPE_IMAGE,
                            'id' => $mediaId,
                            'title' => $mediaTitle,
                            'url' => $response['thumbnail'] ?? null,
                        ]);

                        $videos[] = new PostMedia([
                            'type' => PostMedia::TYPE_VIDEO,
                            'id' => $mediaId,
                            'title' => $mediaTitle,
                            'url' => $response['downloadUrl'] ?? null,
                        ]);
                    }
                }

                $posts[] = new Post([
                    'sourceId' => $this->id,
                    'sourceHandle' => $this->handle,
                    'sourceType' => self::$providerHandle,
                    'id' => $item['id'] ?? null,
                    'text' => $item['commentary'] ?? null,
                    'url' => 'https://www.linkedin.com/feed/update/' . $item['id'],
                    'postType' => $this->_getPostType($item),
                    'dateCreated' => $this->_getTimestamp($item['createdAt'] ?? null),
                    'dateUpdated' => $this->_getTimestamp($item['lastModifiedAt'] ?? null),
                    'author' => $author,
                    'data' => $item,
                    'links' => $links,
                    'images' => $images,
                    'videos' => $videos,
                ]);
            }
        } catch (Throwable $e) {
            self::apiError($this, $e, false);
        }

        return $posts;
    }


    // Private Methods
    // =========================================================================

    private function _getTimestamp(int|string|null $timestamp): ?int
    {
        if (!$timestamp) {
            return null;
        }

        // Timestamps from LinkedIn are in milliseconds
        return (int)($timestamp / 1000);
    }

    private function _getPostType(array $post): string
    {
        $content = $post['content'] ?? [];

        if (isset($content['article'])) {
            return 'article';
        }

        if (isset($content['media'])) {
            $mediaId = $content['media']['id'] ?? '';

            if (str_starts_with($mediaId, 'urn:li:video:')) {
                return 'video';
            }

            if (str_starts_with($mediaId, 'urn:li:image:')) {
                return 'image';
            }

            return 'media';
        }

        return 'text';
    }
}
