# Source Provider
You can register your own Source Provider to add support for other social media platforms, or even extend an existing Source Provider.

```php
use modules\MySourceProvider;

use craft\events\RegisterComponentTypesEvent;
use verbb\socialfeed\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_REGISTER_SOURCE_TYPES, function(RegisterComponentTypesEvent $event) {
    $event->types[] = MySourceProvider::class;
});
```

## Example
Create the following class to house your Source Provider logic.

```php
namespace modules;

use verbb\socialfeed\base\OAuthSource;
use verbb\socialfeed\models\Post;

use League\OAuth2\Client\Provider\SomeProvider;

class MySourceProvider extends OAuthSource
{
    // Static Methods
    // =========================================================================

    public static function displayName(): string
    {
        return 'My Source Provider';
    }

    public static function getOAuthProviderClass(): string
    {
        return SomeProvider::class;
    }


    // Properties
    // =========================================================================

    public static string $providerHandle = 'mySourceProvider';


    // Public Methods
    // =========================================================================

    public function getPrimaryColor(): ?string
    {
        return '#000000';
    }

    public function getIcon(): ?string
    {
        return '<svg>...</svg>';
    }

    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('my-module/my-source/settings', [
            'source' => $this,
        ]);
    }

    public function fetchPosts(): ?array
    {
        try {
            // Construct your POST request according to the API
            $response = $this->request('POST', 'api/endpoint', [
                'json' => [
                    'text' => $payload->message,
                ],
            ]);

            $posts = [];

            foreach ($response as $item) {
                $posts[] = new Post([
                    'id' => $item['id'],
                    'text' => $item['text'],
                    // ...
                ]);
            }

            return $posts;
        } catch (Throwable $e) {
            self::apiError($this, $e, false);
        }
    }
}
```

This is the minimum amount of implementation required for a typical source provider.

Social Feed source providers are built around the [Auth](https://github.com/verbb/auth) which in turn in built around [league/oauth2-client](https://github.com/thephpleague/oauth2-client). You can see that the `getOAuthProviderClass()` must return a `League\OAuth2\Client\Provider\AbstractProvider` class.
