<?php
namespace verbb\socialfeeds\models;

use verbb\socialfeeds\SocialFeeds;

use craft\base\Model;

use DateTime;

class Feed extends Model
{
    // Properties
    // =========================================================================

    public ?int $id = null;
    public ?string $name = null;
    public ?string $handle = null;
    public ?bool $enabled = null;
    public ?array $sources = null;
    public ?int $sortOrder = null;
    public ?DateTime $dateCreated = null;
    public ?DateTime $dateUpdated = null;

    private ?array $_sources = null;


    // Public Methods
    // =========================================================================

    public function getSources(): ?array
    {
        if ($this->_sources !== null) {
            return $this->_sources;
        }

        $sources = [];
        $sourcesService = SocialFeeds::$plugin->getSources();

        foreach ($this->sources as $sourceId) {
            if ($source = $sourcesService->getSourceById($sourceId, true, true)) {
                $sources[] = $source;
            }
        }

        return $this->_sources = $sources;
    }

}