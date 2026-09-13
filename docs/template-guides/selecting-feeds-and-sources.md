# Selecting Feeds and Sources

Choose a feed by the handle configured for it. A feed describes what you want to display; its sources provide the connected accounts. Use enabled-source lookups when disabled connections should be excluded.

## Calls Used in This Task

### `craft.socialFeeds.getAllFeeds()`
Returns a collection of [Feed](docs:developers/feed) objects.

### `craft.socialFeeds.getAllEnabledFeeds()`
Returns a collection of enabled [Feed](docs:developers/feed) objects.

### `craft.socialFeeds.getFeedById(id)`
Returns a [Feed](docs:developers/feed) object by its ID.

### `craft.socialFeeds.getFeedByHandle(handle)`
Returns a [Feed](docs:developers/feed) object by its handle.

### `craft.socialFeeds.getAllSources()`
Returns a collection of [Source](docs:developers/source) objects.

### `craft.socialFeeds.getAllEnabledSources()`
Returns a collection of enabled [Source](docs:developers/source) objects.

### `craft.socialFeeds.getAllConfiguredSources()`
Returns a collection of configured [Source](docs:developers/source) objects.

### `craft.socialFeeds.getSourceById(id)`
Returns a [Source](docs:developers/source) object by its ID.

### `craft.socialFeeds.getSourceByHandle(handle)`
Returns a [Source](docs:developers/source) object by its handle.

