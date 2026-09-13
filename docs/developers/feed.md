# Feed
Whenever you're dealing with a feed in your template, you're actually working with a `Feed` object.

<span id="attributes"></span>

## Properties

::: reference
### `id`

**Type:** `int|null`

The ID of the feed.
:::

::: reference
### `name`

**Type:** `string|null`

The name of the feed.
:::

::: reference
### `handle`

**Type:** `string|null`

The handle of the feed.
:::

::: reference
### `enabled`

**Type:** `bool|null`

Whether the feed is enabled or not.
:::

::: reference
### `sources`

**Type:** `array|null`

The [Source](docs:developers/source) IDs enabled for the feed.
:::



## Methods

::: reference
### `getSources()`

**Returns:** `array|null`

Returns a collection of [Source](docs:developers/source) objects.
:::
