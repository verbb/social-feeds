<?php
namespace verbb\socialfeed\controllers;

use verbb\socialfeed\SocialFeed;

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
        $settings = SocialFeed::$plugin->getSettings();

        return $this->renderTemplate('social-feed/settings', [
            'settings' => $settings,
        ]);
    }

    public function actionResetCache(): Response
    {
        Db::update('{{%socialfeed_sources}}', ['dateLastFetch' => null]);

        Craft::$app->getSession()->setNotice(Craft::t('social-feed', 'Social Feed cache reset.'));

        return $this->redirectToPostedUrl();
    }

    public function actionDeleteCache(): Response
    {
        Db::delete('{{%socialfeed_posts}}');

        Craft::$app->getSession()->setNotice(Craft::t('social-feed', 'Social Feed cache deleted.'));

        return $this->redirectToPostedUrl();
    }

}