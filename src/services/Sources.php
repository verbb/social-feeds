<?php
namespace verbb\socialfeeds\services;

use verbb\socialfeeds\SocialFeeds;
use verbb\socialfeeds\sources as sourceTypes;
use verbb\socialfeeds\base\SourceInterface;
use verbb\socialfeeds\events\SourceEvent;
use verbb\socialfeeds\records\Source as SourceRecord;

use Craft;
use craft\base\MemoizableArray;
use craft\db\Query;
use craft\errors\MissingComponentException;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\ArrayHelper;
use craft\helpers\Component as ComponentHelper;
use craft\helpers\Json;

use yii\base\Component;
use yii\base\InvalidConfigException;

use Exception;
use Throwable;

class Sources extends Component
{
    // Constants
    // =========================================================================

    public const EVENT_REGISTER_SOURCE_TYPES = 'registerSourceTypes';
    public const EVENT_BEFORE_SAVE_SOURCE = 'beforeSaveSource';
    public const EVENT_AFTER_SAVE_SOURCE = 'afterSaveSource';
    public const EVENT_BEFORE_DELETE_SOURCE = 'beforeDeleteSource';
    public const EVENT_AFTER_DELETE_SOURCE = 'afterDeleteSource';


    // Properties
    // =========================================================================

    private ?MemoizableArray $_sources = null;
    private ?array $_overrides = null;


    // Public Methods
    // =========================================================================

    public function getAllSourceTypes(): array
    {
        $sourceTypes = [
            sourceTypes\Facebook::class,
            sourceTypes\Instagram::class,
            sourceTypes\Twitter::class,
            sourceTypes\YouTube::class,
        ];

        $event = new RegisterComponentTypesEvent([
            'types' => $sourceTypes,
        ]);

        $this->trigger(self::EVENT_REGISTER_SOURCE_TYPES, $event);

        return $event->types;
    }

    public function createSource(mixed $config): SourceInterface
    {
        $handle = $config['handle'] ?? null;
        $settings = $config['settings'] ?? [];

        // Allow config settings to override source settings
        if ($handle && $settings) {
            $configOverrides = $this->getSourceOverrides($handle);

            if ($configOverrides) {
                if (is_string($settings)) {
                    $settings = Json::decode($settings);
                }

                $config['settings'] = array_merge($settings, $configOverrides);
            }
        }                

        try {
            return ComponentHelper::createComponent($config, SourceInterface::class);
        } catch (MissingComponentException|InvalidConfigException $e) {
            $config['errorMessage'] = $e->getMessage();
            $config['expectedType'] = $config['type'];
            unset($config['type']);
            return new sourceTypes\MissingSource($config);
        }
    }

    public function getAllSources(): array
    {
        return $this->_sources()->all();
    }

    public function getAllEnabledSources(): array
    {
        return $this->_sources()->where('enabled', true)->all();
    }

    public function getAllConfiguredSources(): array
    {
        $sources = [];

        foreach ($this->getAllEnabledSources() as $source) {
            if ($source->isConfigured()) {
                $sources[] = $source;
            }
        }

        return $sources;
    }

    public function getAllSourcesByParams(array $params): array
    {
        $limit = ArrayHelper::remove($params, 'limit');

        $query = $this->_createSourceQuery()->where($params)->limit($limit)->all();

        return array_map(function($result) {
            return $this->createSource($result);
        }, $query);
    }

    public function getSourceById(int $id, bool $enabledOnly = false, bool $connectedOnly = false): ?SourceInterface
    {
        $source = $this->_sources()->firstWhere('id', $id);

        if ($source && (($enabledOnly && !$source->enabled) || ($connectedOnly && !$source->isConnected()))) {
            return null;
        }

        return $source;
    }

    public function getSourceByHandle(string $handle, bool $enabledOnly = false, bool $connectedOnly = false): ?SourceInterface
    {
        $source = $this->_sources()->firstWhere('handle', $handle, true);
    
        if ($source && (($enabledOnly && !$source->enabled) || ($connectedOnly && !$source->isConnected()))) {
            return null;
        }

        return $source;
    }

    public function getSourceByParams(array $params): ?SourceInterface
    {
        $params['limit'] = 1;

        return $this->getAllSourcesByParams($params)[0] ?? null;
    }

    public function saveSource(SourceInterface $source, bool $runValidation = true): bool
    {
        $isNewSource = !$source->id;

        // Fire a 'beforeSaveSource' event
        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_SOURCE)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_SOURCE, new SourceEvent([
                'source' => $source,
                'isNew' => $isNewSource,
            ]));
        }

        if ($runValidation && !$source->validate()) {
            SocialFeeds::info('Source not saved due to validation error.');
            return false;
        }

        // Ensure we support Emoji's properly
        $settings = $source->settings;

        $sourceRecord = $this->_getSourceRecordById($source->id);
        $sourceRecord->name = $source->name;
        $sourceRecord->handle = $source->handle;
        $sourceRecord->enabled = $source->enabled;
        $sourceRecord->type = get_class($source);
        $sourceRecord->settings = $settings;

        // Saving a source should always wipe the last fetch. Likely invalid now with settings changed
        $sourceRecord->dateLastFetch = null;

        if ($isNewSource) {
            $maxSortOrder = (new Query())
                ->from(['{{%socialfeeds_sources}}'])
                ->max('[[sortOrder]]');

            $sourceRecord->sortOrder = $maxSortOrder ? $maxSortOrder + 1 : 1;
        }

        $sourceRecord->save(false);

        if (!$source->id) {
            $source->id = $sourceRecord->id;
        }

        // Fire an 'afterSaveSource' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE_SOURCE)) {
            $this->trigger(self::EVENT_AFTER_SAVE_SOURCE, new SourceEvent([
                'source' => $source,
                'isNew' => $isNewSource,
            ]));
        }

        return true;
    }

    public function reorderSources(array $sourceIds): bool
    {
        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            foreach ($sourceIds as $sourceOrder => $sourceId) {
                $sourceRecord = $this->_getSourceRecordById($sourceId);
                $sourceRecord->sortOrder = $sourceOrder + 1;
                $sourceRecord->save();
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    public function getSourceOverrides(string $handle): array
    {
        if ($this->_overrides === null) {
            $this->_overrides = Craft::$app->getConfig()->getConfigFromFile('social-feeds');
        }

        return $this->_overrides['sources'][$handle] ?? [];
    }

    public function deleteSourceById(int $sourceId): bool
    {
        $source = $this->getSourceById($sourceId);

        if (!$source) {
            return false;
        }

        return $this->deleteSource($source);
    }

    public function deleteSource(SourceInterface $source): bool
    {
        // Fire a 'beforeDeleteSource' event
        if ($this->hasEventHandlers(self::EVENT_BEFORE_DELETE_SOURCE)) {
            $this->trigger(self::EVENT_BEFORE_DELETE_SOURCE, new SourceEvent([
                'source' => $source,
            ]));
        }

        Craft::$app->getDb()->createCommand()
            ->delete('{{%socialfeeds_sources}}', ['id' => $source->id])
            ->execute();

        // Fire an 'afterDeleteSource' event
        if ($this->hasEventHandlers(self::EVENT_AFTER_DELETE_SOURCE)) {
            $this->trigger(self::EVENT_AFTER_DELETE_SOURCE, new SourceEvent([
                'source' => $source,
            ]));
        }

        // Clear caches
        $this->_sources = null;

        return true;
    }


    // Private Methods
    // =========================================================================

    private function _sources(): MemoizableArray
    {
        if (!isset($this->_sources)) {
            $sources = [];

            foreach ($this->_createSourceQuery()->all() as $result) {
                $sources[] = $this->createSource($result);
            }

            $this->_sources = new MemoizableArray($sources);
        }

        return $this->_sources;
    }

    private function _createSourceQuery(): Query
    {
        return (new Query())
            ->select([
                'id',
                'name',
                'handle',
                'enabled',
                'type',
                'settings',
                'sortOrder',
                'cache',
                'dateLastFetch',
                'dateCreated',
                'dateUpdated',
                'uid',
            ])
            ->from(['{{%socialfeeds_sources}}'])
            ->orderBy(['sortOrder' => SORT_ASC]);
    }

    private function _getSourceRecordById(int $sourceId = null): ?SourceRecord
    {
        if ($sourceId !== null) {
            $sourceRecord = SourceRecord::findOne(['id' => $sourceId]);

            if (!$sourceRecord) {
                throw new Exception(Craft::t('social-feeds', 'No source exists with the ID “{id}”.', ['id' => $sourceId]));
            }
        } else {
            $sourceRecord = new SourceRecord();
        }

        return $sourceRecord;
    }

}
