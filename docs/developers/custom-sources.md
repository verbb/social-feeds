# Custom Sources

A source connects Social Feeds to one social media provider and turns its API responses into posts. Social Feeds includes sources for Facebook, Instagram, LinkedIn, X (Twitter) and YouTube. A custom source lets you fetch posts from another platform, or change how an existing provider’s posts are fetched by extending its class.

Sources connect with OAuth through Verbb’s [Auth](https://github.com/verbb/auth) package, which is built on [league/oauth2-client](https://github.com/thephpleague/oauth2-client). Auth includes providers for many platforms, so a custom source usually only needs to fetch and map the posts.

## Create the Source Class

Extend `verbb\socialfeeds\base\OAuthSource`. This example fetches shots from Dribbble using Auth’s Dribbble provider, and maps each one to a [Post](docs:reference/post). Create it at `modules/sitemodule/DribbbleSource.php` in a module autoloaded as `modules\sitemodule`.

```php
<?php
namespace modules\sitemodule;

use Craft;
use Throwable;
use verbb\auth\providers\Dribbble as DribbbleProvider;
use verbb\socialfeeds\base\OAuthSource;
use verbb\socialfeeds\models\Post;
use verbb\socialfeeds\models\PostMedia;
use verbb\socialfeeds\SocialFeeds;

class DribbbleSource extends OAuthSource
{
    public static string $providerHandle = 'dribbble';

    public static function displayName(): string
    {
        return 'Dribbble';
    }

    public static function getOAuthProviderClass(): string
    {
        return DribbbleProvider::class;
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('social-feeds/sources/_oauth', [
            'source' => $this,
        ]);
    }

    public function fetchPosts(): ?array
    {
        $posts = [];

        try {
            $shots = $this->request('GET', 'user/shots', [
                'query' => [
                    'per_page' => SocialFeeds::$plugin->getSettings()->postsLimit,
                ],
            ]);

            foreach ($shots as $shot) {
                $images = [];

                if ($imageUrl = $shot['images']['hidpi'] ?? $shot['images']['normal'] ?? null) {
                    $images[] = new PostMedia([
                        'type' => PostMedia::TYPE_IMAGE,
                        'url' => $imageUrl,
                        'title' => $shot['title'] ?? null,
                    ]);
                }

                $posts[] = new Post([
                    'sourceId' => $this->id,
                    'sourceHandle' => $this->handle,
                    'sourceType' => self::$providerHandle,
                    'id' => (string)$shot['id'],
                    'uid' => (string)$shot['id'],
                    'title' => $shot['title'] ?? null,
                    'text' => strip_tags($shot['description'] ?? ''),
                    'url' => $shot['html_url'] ?? null,
                    'dateCreated' => $shot['published_at'] ?? null,
                    'tags' => $shot['tags'] ?? [],
                    'images' => $images,
                    'data' => $shot,
                ]);
            }
        } catch (Throwable $e) {
            self::apiError($this, $e, false);
        }

        return $posts;
    }
}
```

`$providerHandle` identifies the provider. Social Feeds stores it on each post as `sourceType`, and uses it to look up the provider’s colour and icon. `fetchPosts()` returns every post it can fetch, up to the `postsLimit` setting, and Social Feeds saves them to the database. `apiError()` logs a failed request, and passing `false` stops it from throwing, so a provider outage doesn’t break the page showing the feed.

## Register the Source

Social Feeds finds sources through the `registerSourceTypes` event. Register your class from your module or plugin’s `init()` method.

```php
use craft\events\RegisterComponentTypesEvent;
use modules\sitemodule\DribbbleSource;
use verbb\socialfeeds\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_REGISTER_SOURCE_TYPES, function(RegisterComponentTypesEvent $event) {
    $event->types[] = DribbbleSource::class;
});
```

## Connect and Test the Source

Go to **Social Feeds → Sources**, create a source and choose **Dribbble** as its type. Enter the client ID and secret from your Dribbble app, using the redirect URI shown on the settings page, then save the source and connect it. Add the source to a feed and render the feed’s posts, or run `php craft social-feeds/posts/refresh --source=yourSourceHandle` to fetch posts straight away. Any API errors are written to the Social Feeds log.

## Methods

A source class must implement `getOAuthProviderClass()` and `fetchPosts()`. Override the others when your source needs them.

::: reference
### `displayName()`

**Returns:** `string`

Returns the provider name shown in the control panel and on posts. This is a static method.
:::

::: reference
### `getOAuthProviderClass()`

**Returns:** `string`

Returns the class of the OAuth provider the source connects with, such as a provider from [Auth](https://github.com/verbb/auth) or any `league/oauth2-client` provider. This is a static method and is required.
:::

::: reference
### `fetchPosts()`

**Returns:** `array|null`

Returns the posts fetched from the provider’s API, as [Post](docs:reference/post) objects. Social Feeds calls it when the cache needs refreshing and saves the results. This method is required.
:::

::: reference
### `getSettingsHtml()`

**Returns:** `string|null`

Returns the settings shown when editing the source in the control panel. Include `social-feeds/sources/_oauth` for the client ID and secret fields.
:::

::: reference
### `getDefaultScopes()`

**Returns:** `array`

Returns the OAuth scopes the source requests when connecting, on top of the provider’s own defaults.
:::

::: reference
### `getAuthorizationUrlOptions()`

**Returns:** `array`

Returns extra options for the provider’s authorisation URL, such as `access_type`. Start with `parent::getAuthorizationUrlOptions()` so the scopes are kept.
:::

::: reference
### `getPrimaryColor()`

**Returns:** `string|null`

Returns the provider’s brand colour as a hex value. The default looks up `$providerHandle` in Auth’s list of known providers.
:::

::: reference
### `getIcon()`

**Returns:** `string|null`

Returns the provider’s icon as SVG markup. The default looks up `$providerHandle` in Auth’s list of known providers.
:::

::: reference
### `getPostContent()`

**Arguments:** `post` ([`Post`](docs:reference/post)), the post being rendered.

**Returns:** `string|null`

Returns HTML to use as a post’s content in place of its title or text, or `null` to keep the default. This is a static method, called by `post.getContent()`.
:::

::: reference
### `fetchSourceSettings()`

**Arguments:** `settingsKey` (`string`), the key the settings template requests.

**Returns:** `array|null`

Returns the choices for a dynamic settings field, such as the pages an account can access. The settings template asks for them by key, and Social Feeds caches the result on the source.
:::
