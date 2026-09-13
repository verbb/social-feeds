# Displaying Feed Posts

Use a configured feed handle to fetch posts for custom markup, or render the feed with the plugin’s rendering helper. Keep the same feed handle in both approaches so you can compare their output.

## Calls Used in This Task

### `craft.socialFeeds.getPosts(feedHandle, options)`
Returns a collection of [Post](docs:developers/post) objects for the provided [Feed](docs:developers/feed) handle.

### `craft.socialFeeds.renderPosts(feedHandle, options)`
Returns the HTML of rendered [Post](docs:developers/post) objects for the provided [Feed](docs:developers/feed) handle.
