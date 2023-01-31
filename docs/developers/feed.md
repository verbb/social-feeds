# Feed
Whenever you're dealing with a feed in your template, you're actually working with a `Feed` object.

## Attributes

Attribute | Description
--- | ---
`id` | The ID of the feed.
`name` | The name of the feed.
`handle` | The handle of the feed.
`enabled` | Whether the feed is enabled or not.
`sources` | The [Source](docs:developers/source) IDs enabled for the feed.


## Methods

Method | Description
--- | ---
`getSources()` | Returns a collection of [Source](docs:developers/source) objects.
