<?php
namespace verbb\socialfeeds\base;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\models\Post;

use Craft;
use craft\base\SavableComponent;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use craft\validators\HandleValidator;

use verbb\auth\helpers\Provider as ProviderHelper;

use DateTime;
use Exception;

use GuzzleHttp\Exception\RequestException;

abstract class Source extends SavableComponent implements SourceInterface
{
    // Static Methods
    // =========================================================================

    public static function apiError($source, $exception, $throwError = true): void
    {
        $messageText = $exception->getMessage();

        // Check for Guzzle errors, which are truncated in the exception `getMessage()`.
        if ($exception instanceof RequestException && $exception->getResponse()) {
            $messageText = (string)$exception->getResponse()->getBody();
        }

        $message = Craft::t('social-feeds', 'API error: “{message}” {file}:{line}', [
            'message' => $messageText,
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
        ]);

        SocialFeeds::error($source->name . ': ' . $message);

        if ($throwError) {
            throw new Exception($message);
        }
    }

    public static function getPostContent(Post $post): ?string
    {
        return null;
    }

    public static function getPostMedia(Post $post): ?string
    {
        return null;
    }


    // Properties
    // =========================================================================

    public ?string $name = null;
    public ?string $handle = null;
    public ?bool $enabled = null;
    public ?int $sortOrder = null;
    public array $cache = [];
    public ?DateTime $dateLastFetch = null;
    public ?string $uid = null;


    // Abstract Methods
    // =========================================================================

    abstract public static function getOAuthProviderClass(): string;
    abstract public function fetchPosts(): ?array;


    // Public Methods
    // =========================================================================

    public function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [['name', 'handle'], 'required'];
        $rules[] = [['id'], 'number', 'integerOnly' => true];

        $rules[] = [
            ['handle'],
            HandleValidator::class,
            'reservedWords' => [
                'dateCreated',
                'dateUpdated',
                'edit',
                'id',
                'title',
                'uid',
            ],
        ];

        return $rules;
    }

    public function getProviderName(): string
    {
        return static::displayName();
    }

    public function getProviderHandle(): string
    {
        return static::$providerHandle;
    }

    public function getPrimaryColor(): ?string
    {
        return ProviderHelper::getPrimaryColor(static::$providerHandle);
    }

    public function getIcon(): ?string
    {
        return ProviderHelper::getIcon(static::$providerHandle);
    }

    public function isConnected(): bool
    {
        return false;
    }

    public function getSourceSettings(string $settingsKey, bool $useCache = true): ?array
    {
        if ($useCache) {
            // Return even if empty, we don't want to force setting the value unless told to
            return $this->getSettingCache($settingsKey);
        }

        $settings = $this->fetchSourceSettings($settingsKey);

        if ($settings) {
            $this->setSettingCache([$settingsKey => $settings]);
        }

        return $settings;
    }

    public function fetchSourceSettings(string $settingsKey): ?array
    {
        return [];
    }

    public function getPosts(array $options = []): ?array
    {
        return SocialFeeds::$plugin->getPosts()->getPostsForSource($this, $options);
    }

    public function getSettingsHtml(): ?string
    {
        $handle = StringHelper::toKebabCase(static::$providerHandle);

        return Craft::$app->getView()->renderTemplate('social-feeds/sources/_types/' . $handle . '/settings', [
            'source' => $this,
        ]);
    }

    public function getCacheKey(): string
    {
        // Create a cache key that's determined by the settings of the source, so that if a setting changes
        // it'll invalidate all the posts for those specific settings. This ensures cached posts don't bleed into
        // other settings and not assuming all cached posts for the source belong to the current settings.
        $settings = $this->getSettings();
        unset($settings['clientId'], $settings['clientSecret']);

        return md5(Json::encode($settings));
    }


    // Protected Methods
    // =========================================================================

    protected function setSettingCache(array $values): void
    {
        $this->cache = array_merge($this->cache, $values);

        $data = Json::encode($this->cache);

        // Direct DB update to keep it out of PC, plus speed
        Craft::$app->getDb()->createCommand()
            ->update('{{%socialfeeds_sources}}', ['cache' => $data], ['id' => $this->id])
            ->execute();
    }

    protected function getSettingCache(string $key): mixed
    {
        return $this->cache[$key] ?? null;
    }
}
