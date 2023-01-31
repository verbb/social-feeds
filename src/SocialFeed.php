<?php
namespace verbb\socialfeed;

use verbb\socialfeed\base\PluginTrait;
use verbb\socialfeed\gql\interfaces\SocialFeedInterface;
use verbb\socialfeed\gql\queries\SocialFeedQuery;
use verbb\socialfeed\models\Settings;
use verbb\socialfeed\utilities\CacheUtility;
use verbb\socialfeed\variables\SocialFeedVariable;

use Craft;
use craft\base\Model;
use craft\base\Plugin;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterGqlQueriesEvent;
use craft\events\RegisterGqlSchemaComponentsEvent;
use craft\events\RegisterGqlTypesEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\helpers\UrlHelper;
use craft\services\Gql;
use craft\services\UserPermissions;
use craft\services\Utilities;
use craft\web\UrlManager;
use craft\web\twig\variables\CraftVariable;

use yii\base\Event;

class SocialFeed extends Plugin
{
    // Properties
    // =========================================================================

    public bool $hasCpSettings = true;
    public string $schemaVersion = '1.0.0';


    // Traits
    // =========================================================================

    use PluginTrait;


    // Public Methods
    // =========================================================================

    public function init(): void
    {
        parent::init();

        self::$plugin = $this;

        $this->_registerComponents();
        $this->_registerLogTarget();
        $this->_registerVariables();
        $this->_registerGraphQl();

        if (Craft::$app->getRequest()->getIsCpRequest()) {
            $this->_registerCpRoutes();
            $this->_registerUtilities();
        }

        if (Craft::$app->getRequest()->getIsSiteRequest()) {
            $this->_registerSiteRoutes();
        }
        
        if (Craft::$app->getEdition() === Craft::Pro) {
            $this->_registerPermissions();
        }

        $this->hasCpSection = $this->getSettings()->hasCpSection;
    }

    public function getPluginName(): string
    {
        return Craft::t('social-feed', $this->getSettings()->pluginName);
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('social-feed/settings'));
    }

    public function getCpNavItem(): ?array
    {
        $nav = parent::getCpNavItem();

        $nav['label'] = $this->getPluginName();

        if (Craft::$app->getUser()->checkPermission('socialFeed-feeds')) {
            $nav['subnav']['feeds'] = [
                'label' => Craft::t('social-feed', 'Feeds'),
                'url' => 'social-feed/feeds',
            ];
        }

        if (Craft::$app->getUser()->checkPermission('socialFeed-sources')) {
            $nav['subnav']['sources'] = [
                'label' => Craft::t('social-feed', 'Sources'),
                'url' => 'social-feed/sources',
            ];
        }

        if (Craft::$app->getUser()->getIsAdmin() && Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            $nav['subnav']['settings'] = [
                'label' => Craft::t('social-feed', 'Settings'),
                'url' => 'social-feed/settings',
            ];
        }

        return $nav;
    }


    // Protected Methods
    // =========================================================================

    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }


    // Private Methods
    // =========================================================================

    private function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['social-feed'] = 'social-feed/feeds/index';
            $event->rules['social-feed/feeds'] = 'social-feed/feeds/index';
            $event->rules['social-feed/feeds/new'] = 'social-feed/feeds/edit';
            $event->rules['social-feed/feeds/<feedHandle:{handle}>'] = 'social-feed/feeds/edit';
            $event->rules['social-feed/sources'] = 'social-feed/sources/index';
            $event->rules['social-feed/sources/new'] = 'social-feed/sources/edit';
            $event->rules['social-feed/sources/<handle:{handle}>'] = 'social-feed/sources/edit';
            $event->rules['social-feed/settings'] = 'social-feed/plugin/settings';
        });
    }

    private function _registerSiteRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['social-feed/auth/callback'] = 'social-feed/auth/callback';
        });
    }

    private function _registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('socialFeed', SocialFeedVariable::class);
        });
    }

    private function _registerPermissions(): void
    {
        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $event->permissions[] = [
                'heading' => Craft::t('social-feed', 'Social Feed'),
                'permissions' => [
                    'socialFeed-feeds' => ['label' => Craft::t('social-feed', 'Feeds')],
                    'socialFeed-sources' => ['label' => Craft::t('social-feed', 'Sources')],
                ],
            ];
        });
    }

    private function _registerUtilities(): void
    {
        Event::on(Utilities::class, Utilities::EVENT_REGISTER_UTILITY_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = CacheUtility::class;
        });
    }

    private function _registerGraphQl(): void
    {
        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_TYPES, function(RegisterGqlTypesEvent $event) {
            $event->types[] = SocialFeedInterface::class;
        });

        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_QUERIES, function(RegisterGqlQueriesEvent $event) {
            $queries = SocialFeedQuery::getQueries();
                    
            foreach ($queries as $key => $value) {
                $event->queries[$key] = $value;
            }
        });

        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_SCHEMA_COMPONENTS, function (RegisterGqlSchemaComponentsEvent $event) {  
            $label = Craft::t('social-feed', 'Social Feed');

            $event->queries[$label]['socialFeed.all:read'] = ['label' => Craft::t('social-feed', 'Query Social Feed')];
        });
    }
}
