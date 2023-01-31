<?php
namespace verbb\socialfeed\models;

use verbb\socialfeed\SocialFeed;

use Craft;
use craft\base\Model;
use craft\helpers\DateTimeHelper;
use craft\helpers\Template;

use DateTime;

use Twig\Markup;

use verbb\auth\helpers\Provider as ProviderHelper;

class Post extends Model
{
    // Properties
    // =========================================================================

    public ?string $id = null;
    public ?string $uid = null;
    public ?string $title = null;
    public ?string $text = null;
    public ?string $url = null;
    public ?int $sourceId = null;
    public ?string $sourceHandle = null;
    public ?string $sourceType = null;
    public ?string $postType = null;
    public ?int $likes = null;
    public ?int $shares = null;
    public ?int $replies = null;
    public ?DateTime $dateCreated = null;
    public ?DateTime $dateUpdated = null;

    public ?PostAuthor $author = null;

    public array $tags = [];
    public array $links = [];
    public array $images = [];
    public array $videos = [];
    public ?array $data = null;
    public ?array $meta = null;


    // Public Methods
    // =========================================================================

    public function getSource(): ?string
    {
        foreach (SocialFeed::$plugin->getSources()->getAllSourceTypes() as $type) {
            if ($this->sourceType === $type::$providerHandle) {
                return $type;
            }
        }

        return null;
    }

    public function getContent(): Markup
    {
        // Allow sources to override content
        if ($source = $this->getSource()) {
            if ($content = $source::getPostContent($this)) {
                return Template::raw((string)$content);
            }
        }

        $content = $this->title ?: $this->text;

        return Template::raw((string)$content);
    }

    public function hasMedia(): bool
    {
        return $this->images || $this->videos;
    }

    public function getAuthorImage(): ?array
    {
        $name = $this->author->name ?? null;
        $url = $this->author->photo ?? null;

        if (!$url) {
            return null;
        }

        return [
            'alt' => 'Profile image for ' . $name,
            'url' => $url,
        ];
    }

    public function getImage(): ?array
    {
        $url = $this->images[0]->url ?? null;

        if (!$url) {
            return null;
        }

        $providerName = $this->getSource()::displayName();
        $name = $this->author->name ?? null;

        $altText = 'Photo from ' . $name . ' on ' . $providerName . ' at ' . $this->dateCreated->format('Y-m-d H:i:s');

        return [
            'alt' => $altText,
            'url' => $url,
        ];
    }

    public function getVideo(): ?array
    {
        $url = $this->videos[0]->url ?? null;

        if (!$url) {
            return null;
        }

        return [
            'url' => $url,
        ];
    }

    public function getFriendlyDate(DateTime $date): string
    {
        $diff = (new DateTime)->diff($date);

        return self::humanDuration($diff);
    }

    public function getProviderColor(): ?string
    {
        return ProviderHelper::getPrimaryColor($this->sourceType);
    }

    public function getProviderIcon(): ?string
    {
        return ProviderHelper::getIcon($this->sourceType);
    }
    

    // Deserializer Methods
    // =========================================================================

    public function addImages(PostMedia $image): void
    {
        $this->images[] = $image;
    }

    public function removeImages(PostMedia $image): void
    {
    }

    public function addVideos(PostMedia $video): void
    {
        $this->videos[] = $video;
    }

    public function removeVideos(PostMedia $video): void
    {
    }

    public function addLinks(PostLink $link): void
    {
        $this->links[] = $link;
    }

    public function removeLinks(PostLink $link): void
    {
    }


    // Private Methods
    // =========================================================================

    private static function humanDuration(mixed $dateInterval, ?bool $showSeconds = null): string
    {
        $dateInterval = DateTimeHelper::toDateInterval($dateInterval);

        if ($dateInterval->y > 0) {
            return Craft::t('app', '{num}y', ['num' => $dateInterval->y]);
        }

        if ($dateInterval->days > 0) {
            return Craft::t('app', '{num}d', ['num' => $dateInterval->days]);
        }

        if ($dateInterval->h) {
            return Craft::t('app', '{num}h', ['num' => $dateInterval->h]);
        }

        if ($dateInterval->i) {
            return Craft::t('app', '{num}m', ['num' => $dateInterval->i]);
        }

        if ($dateInterval->s) {
            return Craft::t('app', '{num}s', ['num' => $dateInterval->s]);
        }

        return '';
    }

}