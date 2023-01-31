<?php
namespace verbb\socialfeed\controllers;

use verbb\socialfeed\SocialFeed;
use verbb\socialfeed\base\SourceInterface;

use Craft;
use craft\helpers\ArrayHelper;
use craft\helpers\Json;
use craft\web\Controller;

use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class SourcesController extends Controller
{
    // Public Methods
    // =========================================================================

    public function actionIndex(): Response
    {
        $sources = SocialFeed::$plugin->getSources()->getAllSources();

        return $this->renderTemplate('social-feed/sources', [
            'sources' => $sources,
        ]);
    }

    public function actionEdit(?string $handle = null, SourceInterface $source = null): Response
    {
        $sourcesService = SocialFeed::$plugin->getSources();

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
            $title = trim($source->name) ?: Craft::t('social-feed', 'Edit Source');
        } else {
            $title = Craft::t('social-feed', 'Create a new source');
        }

        return $this->renderTemplate('social-feed/sources/_edit', [
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

        $sourcesService = SocialFeed::$plugin->getSources();
        $sourceId = $this->request->getParam('sourceId') ?: null;
        $type = $this->request->getParam('type');

        if ($sourceId) {
            $oldSource = $sourcesService->getSourceById($sourceId);
            
            if (!$oldSource) {
                throw new BadRequestHttpException("Invalid source ID: $sourceId");
            }
        }

        $source = $sourcesService->createSource([
            'id' => $sourceId,
            'type' => $type,
            'name' => $this->request->getParam('name'),
            'handle' => $this->request->getParam('handle'),
            'enabled' => (bool)$this->request->getParam('enabled'),
            'settings' => $this->request->getParam("types.$type"),
        ]);

        if (!$sourcesService->saveSource($source)) {
            return $this->asModelFailure($source, Craft::t('social-feed', 'Couldn’t save source.'), 'source');
        }

        return $this->asModelSuccess($source, Craft::t('social-feed', 'Source saved.'), 'source');
    }

    public function actionReorder(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $sourceIds = Json::decode($this->request->getRequiredBodyParam('ids'));
        SocialFeed::$plugin->getSources()->reorderSources($sourceIds);

        return $this->asSuccess();
    }

    public function actionDelete(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $sourceId = $this->request->getRequiredBodyParam('id');

        SocialFeed::$plugin->getSources()->deleteSourceById($sourceId);

        return $this->asSuccess();
    }

    public function actionRefreshSettings(): Response
    {
        $this->requireAcceptsJson();

        $sourcesService = SocialFeed::$plugin->getSources();

        $sourceHandle = $this->request->getRequiredBodyParam('source');
        $setting = $this->request->getRequiredBodyParam('setting');

        $source = $sourcesService->getSourceByHandle($sourceHandle);
        
        if (!$source) {
            throw new BadRequestHttpException("Invalid source: $sourceHandle");
        }

        return $this->asJson($source->getSourceSettings($setting, false));
    }
}
