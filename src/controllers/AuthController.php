<?php
namespace verbb\socialfeeds\controllers;

use verbb\socialfeeds\SocialFeeds;

use Craft;
use craft\web\Controller;

use yii\web\Response;

use verbb\auth\Auth;
use verbb\auth\helpers\Session;

use Throwable;

class AuthController extends Controller
{
    // Properties
    // =========================================================================

    protected array|int|bool $allowAnonymous = ['connect', 'callback'];


    // Public Methods
    // =========================================================================

    public function beforeAction($action): bool
    {
        // Don't require CSRF validation for callback requests
        if ($action->id === 'callback') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionConnect(): ?Response
    {
        $sourceHandle = $this->request->getRequiredParam('source');

        try {
            if (!($source = SocialFeeds::$plugin->getSources()->getSourceByHandle($sourceHandle))) {
                return $this->asFailure(Craft::t('social-feeds', 'Unable to find source “{source}”.', ['source' => $sourceHandle]));
            }

            // Keep track of which source instance is for, so we can fetch it in the callback
            Session::set('sourceHandle', $sourceHandle);

            return Auth::getInstance()->getOAuth()->connect('social-feeds', $source);
        } catch (Throwable $e) {
            SocialFeeds::error('Unable to authorize connect “{source}”: “{message}” {file}:{line}', [
                'source' => $sourceHandle,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->asFailure(Craft::t('social-feeds', 'Unable to authorize connect “{source}”.', ['source' => $sourceHandle]));
        }
    }

    public function actionCallback(): ?Response
    {
        // Get both the origin (failure) and redirect (success) URLs
        $origin = Session::get('origin');
        $redirect = Session::get('redirect');

        // Get the source we're current authorizing
        if (!($sourceHandle = Session::get('sourceHandle'))) {
            Session::setError('social-feeds', Craft::t('social-feeds', 'Unable to find source.'), true);

            return $this->redirect($origin);
        }

        if (!($source = SocialFeeds::$plugin->getSources()->getSourceByHandle($sourceHandle))) {
            Session::setError('social-feeds', Craft::t('social-feeds', 'Unable to find source “{source}”.', ['source' => $sourceHandle]), true);

            return $this->redirect($origin);
        }

        try {
            // Fetch the access token from the source and create a Token for us to use
            $token = Auth::getInstance()->getOAuth()->callback('social-feeds', $source);

            if (!$token) {
                Session::setError('social-feeds', Craft::t('social-feeds', 'Unable to fetch token.'), true);

                return $this->redirect($origin);
            }

            // Save the token to the Auth plugin, with a reference to this source
            $token->reference = $source->id;
            Auth::getInstance()->getTokens()->upsertToken($token);
        } catch (Throwable $e) {
            $error = Craft::t('social-feeds', 'Unable to process callback for “{source}”: “{message}” {file}:{line}', [
                'source' => $sourceHandle,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            SocialFeeds::error($error);

            // Show the error detail in the CP
            Craft::$app->getSession()->setFlash('social-feeds:callback-error', $error);

            return $this->redirect($origin);
        }

        Session::setNotice('social-feeds', Craft::t('social-feeds', '{provider} connected.', ['provider' => $source->providerName]), true);

        return $this->redirect($redirect);
    }

    public function actionDisconnect(): ?Response
    {
        $sourceHandle = $this->request->getRequiredParam('source');

        if (!($source = SocialFeeds::$plugin->getSources()->getSourceByHandle($sourceHandle))) {
            return $this->asFailure(Craft::t('social-feeds', 'Unable to find source “{source}”.', ['source' => $sourceHandle]));
        }

        // Delete all tokens for this source
        Auth::getInstance()->getTokens()->deleteTokenByOwnerReference('social-feeds', $source->id);

        return $this->asModelSuccess($source, Craft::t('social-feeds', '{provider} disconnected.', ['provider' => $source->providerName]), 'source');
    }

}
