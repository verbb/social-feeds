<?php
namespace verbb\socialfeeds\controllers;

use verbb\socialfeeds\SocialFeeds;

use Craft;
use craft\elements\User;
use craft\web\Controller;

use yii\web\Response;

use verbb\auth\Auth;
use verbb\auth\helpers\Session;

use Throwable;

class AuthController extends Controller
{
    // Properties
    // =========================================================================

    protected array|int|bool $allowAnonymous = ['callback'];


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
        $this->requirePermission('socialFeeds-sources');
        $this->requirePostRequest();

        $sourceHandle = $this->request->getRequiredParam('source');

        try {
            if (!($source = SocialFeeds::$plugin->getSources()->getSourceByHandle($sourceHandle))) {
                return $this->asFailure(Craft::t('social-feeds', 'Unable to find source “{source}”.', ['source' => $sourceHandle]));
            }

            $context = [
                'sourceHandle' => $sourceHandle,
            ];

            if ($this->request->getIsCpRequest()) {
                if ($redirect = $this->request->getValidatedBodyParam('redirect')) {
                    $context['redirect'] = $this->getView()->renderObjectTemplate($redirect, $source);
                }
            }

            return Auth::getInstance()->getOAuth()->connect('social-feeds', $source, $source->id, $context);
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
        $oauth = Auth::getInstance()->getOAuth();

        if ($response = $oauth->prepareCallback('social-feeds')) {
            return $response;
        }

        $oauth->claimAuthorizedCallback('social-feeds', fn(User $user): bool => $user->can('socialFeeds-sources'));
        
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
            $token = $oauth->callback('social-feeds', $source, $source->id);

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
        $this->requirePermission('socialFeeds-sources');
        $this->requirePostRequest();

        $sourceHandle = $this->request->getRequiredParam('source');

        if (!($source = SocialFeeds::$plugin->getSources()->getSourceByHandle($sourceHandle))) {
            return $this->asFailure(Craft::t('social-feeds', 'Unable to find source “{source}”.', ['source' => $sourceHandle]));
        }

        // Delete all tokens for this source
        Auth::getInstance()->getTokens()->deleteTokenByOwnerReference('social-feeds', $source->id);

        return $this->asModelSuccess($source, Craft::t('social-feeds', '{provider} disconnected.', ['provider' => $source->providerName]), 'source');
    }

}
