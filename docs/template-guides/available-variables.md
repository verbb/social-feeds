# Available Variables
The following methods are available to call in your Twig templates:

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

### `craft.socialFeeds.getPosts(feedHandle, options)`
Returns a collection of [Post](docs:developers/post) objects for the provided [Feed](docs:developers/feed) handle.

### `craft.socialFeeds.renderPosts(feedHandle, options)`
Returns the HTML of rendered [Post](docs:developers/post) objects for the provided [Feed](docs:developers/feed) handle.
