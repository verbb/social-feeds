# Events
Social Feed provides a collection of events for extending its functionality. Modules and plugins can register event listeners, typically in their `init()` methods, to modify Social Feed’s behavior.

## Feed Events

### The `beforeSaveFeed` event
The event that is triggered before a feed is saved.

```php
use verbb\socialfeed\events\FeedEvent;
use verbb\socialfeed\services\Feeds;
use yii\base\Event;

Event::on(Feeds::class, Feeds::EVENT_BEFORE_SAVE_FEED, function(FeedEvent $event) {
    $feed = $event->feed;
    $isNew = $event->isNew;
    // ...
});
```

### The `afterSaveFeed` event
The event that is triggered after a feed is saved.

```php
use verbb\socialfeed\events\FeedEvent;
use verbb\socialfeed\services\Feeds;
use yii\base\Event;

Event::on(Feeds::class, Feeds::EVENT_AFTER_SAVE_FEED, function(FeedEvent $event) {
    $feed = $event->feed;
    $isNew = $event->isNew;
    // ...
});
```

### The `beforeDeleteFeed` event
The event that is triggered before a feed is deleted.

```php
use verbb\socialfeed\events\FeedEvent;
use verbb\socialfeed\services\Feeds;
use yii\base\Event;

Event::on(Feeds::class, Feeds::EVENT_BEFORE_DELETE_FEED, function(FeedEvent $event) {
    $feed = $event->feed;
    // ...
});
```

### The `afterDeleteFeed` event
The event that is triggered after a feed is deleted.

```php
use verbb\socialfeed\events\FeedEvent;
use verbb\socialfeed\services\Feeds;
use yii\base\Event;

Event::on(Feeds::class, Feeds::EVENT_AFTER_DELETE_FEED, function(FeedEvent $event) {
    $feed = $event->feed;
    // ...
});
```

## Source Events

### The `beforeSaveSource` event
The event that is triggered before a source is saved.

```php
use verbb\socialfeed\events\SourceEvent;
use verbb\socialfeed\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_BEFORE_SAVE_SOURCE, function(SourceEvent $event) {
    $source = $event->source;
    $isNew = $event->isNew;
    // ...
});
```

### The `afterSaveSource` event
The event that is triggered after a source is saved.

```php
use verbb\socialfeed\events\SourceEvent;
use verbb\socialfeed\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_AFTER_SAVE_SOURCE, function(SourceEvent $event) {
    $source = $event->source;
    $isNew = $event->isNew;
    // ...
});
```

### The `beforeDeleteSource` event
The event that is triggered before a source is deleted.

```php
use verbb\socialfeed\events\SourceEvent;
use verbb\socialfeed\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_BEFORE_DELETE_SOURCE, function(SourceEvent $event) {
    $source = $event->source;
    // ...
});
```

### The `afterDeleteSource` event
The event that is triggered after a source is deleted.

```php
use verbb\socialfeed\events\SourceEvent;
use verbb\socialfeed\services\Sources;
use yii\base\Event;

Event::on(Sources::class, Sources::EVENT_AFTER_DELETE_SOURCE, function(SourceEvent $event) {
    $source = $event->source;
    // ...
});
```
