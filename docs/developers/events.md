# Events

Social Feeds provides events for extending its functionality. Modules and plugins can register event listeners, typically in their `init()` methods, to change Social Feeds’ behaviour.

## Feed Events

### The `beforeSaveFeed` Event
The event that is triggered before a feed is saved. Listeners can change the feed’s properties before it’s saved, but can’t cancel the save.

```php
use verbb\socialfeeds\events\FeedEvent;
use verbb\socialfeeds\services\Feeds;
use yii\base\Event;

Event::on(Feeds::class, Feeds::EVENT_BEFORE_SAVE_FEED, function(FeedEvent $event) {
    $feed = $event->feed;
    $isNew = $event->isNew;
    // ...
});
```

### The `afterSaveFeed` Event
The event that is triggered after a feed is saved.

```php
use verbb\socialfeeds\events\FeedEvent;
use verbb\socialfeeds\services\Feeds;
use yii\base\Event;

Event::on(Feeds::class, Feeds::EVENT_AFTER_SAVE_FEED, function(FeedEvent $event) {
    $feed = $event->feed;
    $isNew = $event->isNew;
    // ...
});
```

### The `beforeDeleteFeed` Event
The event that is triggered before a feed is deleted. The deletion can’t be cancelled.

```php
use verbb\socialfeeds\events\FeedEvent;
use verbb\socialfeeds\services\Feeds;
use yii\base\Event;

Event::on(Feeds::class, Feeds::EVENT_BEFORE_DELETE_FEED, function(FeedEvent $event) {
    $feed = $event->feed;
    // ...
});
```

### The `afterDeleteFeed` Event
The event that is triggered after a feed is deleted.

```php
use verbb\socialfeeds\events\FeedEvent;
use verbb\socialfeeds\services\Feeds;
use yii\base\Event;

Event::on(Feeds::class, Feeds::EVENT_AFTER_DELETE_FEED, function(FeedEvent $event) {
    $feed = $event->feed;
    // ...
});
```

## Source Events

### The `beforeSaveSource` Event
The event that is triggered before a source is saved. Listeners can change the source’s properties before it’s saved, but can’t cancel the save.

```php
use verbb\socialfeeds\events\SourceEvent;
use verbb\socialfeeds\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_BEFORE_SAVE_SOURCE, function(SourceEvent $event) {
    $source = $event->source;
    $isNew = $event->isNew;
    // ...
});
```

### The `afterSaveSource` Event
The event that is triggered after a source is saved.

```php
use verbb\socialfeeds\events\SourceEvent;
use verbb\socialfeeds\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_AFTER_SAVE_SOURCE, function(SourceEvent $event) {
    $source = $event->source;
    $isNew = $event->isNew;
    // ...
});
```

### The `beforeDeleteSource` Event
The event that is triggered before a source is deleted. The deletion can’t be cancelled.

```php
use verbb\socialfeeds\events\SourceEvent;
use verbb\socialfeeds\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_BEFORE_DELETE_SOURCE, function(SourceEvent $event) {
    $source = $event->source;
    // ...
});
```

### The `afterDeleteSource` Event
The event that is triggered after a source is deleted.

```php
use verbb\socialfeeds\events\SourceEvent;
use verbb\socialfeeds\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_AFTER_DELETE_SOURCE, function(SourceEvent $event) {
    $source = $event->source;
    // ...
});
```

### The `registerSourceTypes` Event
The event that is triggered when Social Feeds builds the list of available source types. Append your class to `$event->types` to register it. [Custom Sources](docs:developers/custom-sources) shows a complete source class.

```php
use craft\events\RegisterComponentTypesEvent;
use modules\sitemodule\MySource;
use verbb\socialfeeds\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_REGISTER_SOURCE_TYPES, function(RegisterComponentTypesEvent $event) {
    $event->types[] = MySource::class;
});
```
