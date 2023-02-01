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

    public function actionSettings(): Response
    {
        $settings = SocialFeeds::$plugin->getSettings();

        return $this->renderTemplate('social-feeds/settings', [
            'settings' => $settings,
        ]);
    }

    public function actionResetCache(): Response
    {
        Db::update('{{%socialfeeds_sources}}', ['dateLastFetch' => null]);

        Craft::$app->getSession()->setNotice(Craft::t('social-feeds', 'Social Feeds cache reset.'));

        return $this->redirectToPostedUrl();
    }

    public function actionDeleteCache(): Response
    {
        Db::delete('{{%socialfeeds_posts}}');

        Craft::$app->getSession()->setNotice(Craft::t('social-feeds', 'Social Feeds cache deleted.'));

        return $this->redirectToPostedUrl();
    }

}