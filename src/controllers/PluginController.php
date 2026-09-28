<?php
namespace verbb\socialfeeds\controllers;

use verbb\socialfeeds\SocialFeeds;

use Craft;
use craft\helpers\Db;
use craft\web\Controller;

use yii\web\Response;

class PluginController extends Controller
{
    // Public Methods
    // =========================================================================

    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if (in_array($action->id, ['reset-cache', 'delete-cache'], true)) {
            $this->requireCpRequest();
            $this->requirePermission('utility:social-feeds-cache');
        }

        return true;
    }

    public function actionSettings(): Response
    {
        $settings = SocialFeeds::$plugin->getSettings();

        return $this->renderTemplate('social-feeds/settings', [
            'settings' => $settings,
        ]);
    }

    public function actionResetCache(): Response
    {
        $this->requirePostRequest();

        Db::update('{{%socialfeeds_sources}}', ['dateLastFetch' => null]);

        Craft::$app->getSession()->setNotice(Craft::t('social-feeds', 'Social Feeds cache reset.'));

        return $this->redirectToPostedUrl();
    }

    public function actionDeleteCache(): Response
    {
        $this->requirePostRequest();

        Db::delete('{{%socialfeeds_posts}}');

        Craft::$app->getSession()->setNotice(Craft::t('social-feeds', 'Social Feeds cache deleted.'));

        return $this->redirectToPostedUrl();
    }

}
