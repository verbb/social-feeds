<?php
namespace verbb\socialfeeds\base;

use verbb\socialfeeds\SocialFeeds;

use Craft;

use verbb\auth\Auth;
use verbb\auth\base\OAuthProviderInterface;
use verbb\auth\base\OAuthProviderTrait;
use verbb\auth\helpers\RedirectUri;
use verbb\auth\models\Token;

abstract class OAuthSource extends Source implements OAuthProviderInterface
{
    // Traits
    // =========================================================================

    use OAuthProviderTrait;
    

    // Public Methods
    // =========================================================================

    public function settingsAttributes(): array
    {
        // These won't be picked up in a Trait
        $attributes = parent::settingsAttributes();
        $attributes[] = 'clientId';
        $attributes[] = 'clientSecret';

        return $attributes;
    }

    public function isConfigured(): bool
    {
        return $this->clientId && $this->clientSecret;
    }

    public function isConnected(): bool
    {
        return (bool)$this->getToken();
    }

    public function getRedirectUri(): ?string
    {
        return RedirectUri::getCallbackUri(SocialFeeds::$plugin->getSettings()->redirectUri, 'social-feeds/auth/callback');
    }

    public function getDefaultScopes(): array
    {
        return [];
    }

    public function getAuthorizationUrlOptions(): array
    {
        // Use any auth options defined in config files
        $options = $this->authorizationOptions;

        // Combine default scopes at the provider level, with account level ones, and any in the config.
        $defaultScopes = $this->getOAuthProvider()->defaultScopes();
        $options['scope'] = array_values(array_unique(array_merge($defaultScopes, $this->getDefaultScopes(), $this->scopes)));

        return $options;
    }

    public function getToken(): ?Token
    {
        if ($this->id) {
            return Auth::getInstance()->getTokens()->getTokenByOwnerReference('social-feeds', $this->id);
        }

        return null;
    }


    // Protected Methods
    // =========================================================================

    protected function defineRules(): array
    {
        $rules = parent::defineRules();

        $rules[] = [
            ['clientId', 'clientSecret'], 'required', 'when' => function($model) {
                return $model->enabled;
            },
        ];

        return $rules;
    }
}