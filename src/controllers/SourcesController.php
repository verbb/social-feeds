<?php
namespace verbb\socialfeeds\controllers;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\base\SourceInterface;
use verbb\socialfeeds\records\Source as SourceRecord;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\web\Controller;

use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

use verbb\base\helpers\EnvironmentSettings;
use verbb\base\models\EnvironmentSettingsViolation;

class SourcesController extends Controller
{
    // Public Methods
    // =========================================================================

    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        $this->requireCpRequest();
        $this->requirePermission('socialFeeds-sources');

        return true;
    }

    public function actionIndex(): Response
    {
        $sources = SocialFeeds::$plugin->getSources()->getAllSources();

        return $this->renderTemplate('social-feeds/sources', [
            'sources' => $sources,
        ]);
    }

    public function actionEdit(?string $handle = null, SourceInterface $source = null): Response
    {
        $sourcesService = SocialFeeds::$plugin->getSources();

        if ($source === null) {
            if ($handle !== null) {
                $source = $sourcesService->getSourceByHandle($handle);

                if ($source === null) {
                    throw new NotFoundHttpException('Source not found');
                }
            }
        }

        $allSourceTypes = $sourcesService->getAllSourceTypes();

        $sourceInstances = [];
        $sourceOptions = [];

        foreach ($allSourceTypes as $sourceType) {
            /** @var SourceInterface $sourceInstance */
            $sourceInstance = Craft::createObject($sourceType);

            if ($source === null) {
                $source = $sourceInstance;
            }

            $sourceInstances[$sourceType] = $sourceInstance;

            $sourceOptions[] = [
                'value' => $sourceType,
                'label' => $sourceInstance::displayName(),
            ];
        }

        // Sort them by name
        ArrayHelper::multisort($sourceOptions, 'label');

        if ($handle && $sourcesService->getSourceByHandle($handle)) {
            $title = trim($source->name) ?: Craft::t('social-feeds', 'Edit Source');
        } else {
            $title = Craft::t('social-feeds', 'Create a new source');
        }

        return $this->renderTemplate('social-feeds/sources/_edit', [
            'title' => $title,
            'source' => $source,
            'sourceOptions' => $sourceOptions,
            'sourceInstances' => $sourceInstances,
            'sourceTypes' => $allSourceTypes,
        ]);
    }

    public function actionSave(): ?Response
    {
        $this->requirePostRequest();

        $sourcesService = SocialFeeds::$plugin->getSources();
        $sourceId = $this->request->getParam('sourceId') ?: null;
        $type = $this->request->getParam('type');

        if ($sourceId) {
            $oldSource = $sourcesService->getSourceById($sourceId);

            if (!$oldSource) {
                throw new BadRequestHttpException("Invalid source ID: $sourceId");
            }
        }

        $handle = $this->request->getParam('handle');
        $settings = $this->request->getParam("types.$type");

        $source = $sourcesService->createSource([
            'id' => $sourceId,
            'type' => $type,
            'name' => $this->request->getParam('name'),
            'handle' => $handle,
            'enabled' => (bool)$this->request->getParam('enabled'),
            'settings' => $settings,
        ]);

        if ($violation = $this->_getEnvironmentSettingsViolation($source, $settings, $handle)) {
            $message = $violation->reason === EnvironmentSettingsViolation::REFERENCE
                ? Craft::t('social-feeds', 'Only admins can use environment variables here.')
                : Craft::t('social-feeds', 'Enter the credentials again after changing where this connects.');

            $source->addError($violation->getAttribute() ?? 'settings', $message);

            return $this->asModelFailure($source, Craft::t('social-feeds', 'Couldn’t save source.'), 'source');
        }

        if (!$sourcesService->saveSource($source)) {
            return $this->asModelFailure($source, Craft::t('social-feeds', 'Couldn’t save source.'), 'source');
        }

        return $this->asModelSuccess($source, Craft::t('social-feeds', 'Source saved.'), 'source');
    }

    public function actionReorder(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $sourceIds = Json::decode($this->request->getRequiredBodyParam('ids'));
        SocialFeeds::$plugin->getSources()->reorderSources($sourceIds);

        return $this->asSuccess();
    }

    public function actionDelete(): ?Response
    {
        $this->requirePostRequest();

        $sourceId = $this->request->getRequiredBodyParam('id');

        if (!SocialFeeds::$plugin->getSources()->deleteSourceById($sourceId)) {
            return $this->asFailure(Craft::t('social-feeds', 'Couldn’t delete source.'));
        }

        return $this->asSuccess();
    }

    public function actionRefreshSettings(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $sourcesService = SocialFeeds::$plugin->getSources();

        $sourceHandle = $this->request->getRequiredBodyParam('source');
        $setting = $this->request->getRequiredBodyParam('setting');

        $source = $sourcesService->getSourceByHandle($sourceHandle);

        if (!$source) {
            throw new BadRequestHttpException("Invalid source: $sourceHandle");
        }

        return $this->asJson($source->getSourceSettings($setting, false));
    }


    // Private Methods
    // =========================================================================

    /**
     * Returns why the posted source settings can't be saved by the current user, or `null` when they can.
     *
     * The client ID is resolved with `App::parseEnv()` and sent to the provider in the authorisation URL the user's
     * browser opens, so only admins can point source settings at environment variables. Changing the source type
     * also means entering a variable-backed client ID or secret again, so it can't move to another provider.
     */
    private function _getEnvironmentSettingsViolation(SourceInterface $source, mixed $posted, mixed $handle): ?EnvironmentSettingsViolation
    {
        if (is_string($posted)) {
            $posted = Json::decodeIfJson($posted);
        }

        $posted = is_array($posted) ? $posted : [];

        // Config file overrides replace posted settings when the source is created, so those values never come from
        // the request.
        if (is_string($handle) && $handle !== '' && $posted) {
            $posted = array_diff_key($posted, SocialFeeds::$plugin->getSources()->getSourceOverrides($handle));
        }

        // Compare against the saved row, because loaded sources have config file overrides merged into their settings.
        // Use the created source's ID, as posted settings can also set it.
        $record = $source->id ? SourceRecord::findOne(['id' => $source->id]) : null;
        $stored = $record ? Json::decodeIfJson((string)$record->settings) : [];
        $stored = is_array($stored) ? $stored : [];
        $providerChanged = $record && $record->type !== get_class($source);

        // No source setting decides which host the credentials go to. LinkedIn's company and profile URLs only
        // identify the organisation, so they aren't destinations.
        return EnvironmentSettings::violation($posted, $stored, ['clientId', 'clientSecret'], false, $providerChanged, []);
    }
}
