<?php
namespace verbb\socialfeeds;

use verbb\socialfeeds\base\PluginTrait;
use verbb\socialfeeds\gql\interfaces\SocialFeedsInterface;
use verbb\socialfeeds\gql\queries\SocialFeedsQuery;
use verbb\socialfeeds\models\Settings;
use verbb\socialfeeds\utilities\CacheUtility;
use verbb\socialfeeds\variables\SocialFeedsVariable;

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

class SocialFeeds extends Plugin
{
    // Properties
    // =========================================================================

    public bool $hasCpSettings = true;
    public string $schemaVersion = '1.0.1';


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
        return Craft::t('social-feeds', $this->getSettings()->pluginName);
    }

    public function getSettingsResponse(): mixed
    {
        return Craft::$app->getResponse()->redirect(UrlHelper::cpUrl('social-feeds/settings'));
    }

    public function getCpNavItem(): ?array
    {
        $nav = parent::getCpNavItem();

        $nav['label'] = $this->getPluginName();

        if (Craft::$app->getUser()->checkPermission('socialFeeds-feeds')) {
            $nav['subnav']['feeds'] = [
                'label' => Craft::t('social-feeds', 'Feeds'),
                'url' => 'social-feeds/feeds',
            ];
        }

        if (Craft::$app->getUser()->checkPermission('socialFeeds-sources')) {
            $nav['subnav']['sources'] = [
                'label' => Craft::t('social-feeds', 'Sources'),
                'url' => 'social-feeds/sources',
            ];
        }

        if (Craft::$app->getUser()->getIsAdmin() && Craft::$app->getConfig()->getGeneral()->allowAdminChanges) {
            $nav['subnav']['settings'] = [
                'label' => Craft::t('social-feeds', 'Settings'),
                'url' => 'social-feeds/settings',
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
            $event->rules['social-feeds'] = 'social-feeds/feeds/index';
            $event->rules['social-feeds/feeds'] = 'social-feeds/feeds/index';
            $event->rules['social-feeds/feeds/new'] = 'social-feeds/feeds/edit';
            $event->rules['social-feeds/feeds/<feedHandle:{handle}>'] = 'social-feeds/feeds/edit';
            $event->rules['social-feeds/sources'] = 'social-feeds/sources/index';
            $event->rules['social-feeds/sources/new'] = 'social-feeds/sources/edit';
            $event->rules['social-feeds/sources/<handle:{handle}>'] = 'social-feeds/sources/edit';
            $event->rules['social-feeds/settings'] = 'social-feeds/plugin/settings';
        });
    }

    private function _registerSiteRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_SITE_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['social-feeds/auth/callback'] = 'social-feeds/auth/callback';
        });
    }

    private function _registerVariables(): void
    {
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('socialFeeds', SocialFeedsVariable::class);
        });
    }

    private function _registerPermissions(): void
    {
        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $event->permissions[] = [
                'heading' => Craft::t('social-feeds', 'Social Feeds'),
                'permissions' => [
                    'socialFeeds-feeds' => ['label' => Craft::t('social-feeds', 'Feeds')],
                    'socialFeeds-sources' => ['label' => Craft::t('social-feeds', 'Sources')],
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
            $event->types[] = SocialFeedsInterface::class;
        });

        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_QUERIES, function(RegisterGqlQueriesEvent $event) {
            $queries = SocialFeedsQuery::getQueries();
                    
            foreach ($queries as $key => $value) {
                $event->queries[$key] = $value;
            }
        });

        Event::on(Gql::class, Gql::EVENT_REGISTER_GQL_SCHEMA_COMPONENTS, function (RegisterGqlSchemaComponentsEvent $event) {  
            $label = Craft::t('social-feeds', 'Social Feeds');

            $event->queries[$label]['socialFeeds.all:read'] = ['label' => Craft::t('social-feeds', 'Query Social Feeds')];
        });
    }
}
