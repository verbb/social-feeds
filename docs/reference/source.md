# Source

A source connects to one social media provider, such as Instagram or YouTube, and fetches posts from it. Whenever you’re dealing with a source in your template, you’re working with a `Source` object. Fetch one by its handle, or get the sources in a [feed](docs:reference/feed) with `feed.getSources()`.

```twig
{% set source = craft.socialFeeds.getSourceByHandle('mySourceHandle') %}

{% if source and source.isConnected() %}
    {% for post in source.getPosts({ limit: 5 }) %}
        {{ post.getContent() }}
    {% endfor %}
{% endif %}
```

<span id="attributes"></span>

## Properties

::: reference
### `id`

**Type:** `int|null`

The ID of the source.
:::

::: reference
### `name`

**Type:** `string|null`

The name of the source, as shown in the control panel.
:::

::: reference
### `handle`

**Type:** `string|null`

The handle of the source, used to fetch it in templates and console commands.
:::

::: reference
### `enabled`

**Type:** `bool|null`

Whether the source is enabled.
:::

::: reference
### `dateLastFetch`

**Type:** `DateTime|null`

The date the source last fetched posts from its provider. This is `null` when the source hasn’t fetched yet or its cache was cleared.
:::

::: reference
### `providerName`

**Type:** `string`

The display name of the source’s provider, such as `YouTube`. This reads `getProviderName()`.
:::

::: reference
### `providerHandle`

**Type:** `string`

The handle of the source’s provider, such as `youTube`. Posts from this source store it as their `sourceType`.
:::

::: reference
### `primaryColor`

**Type:** `string|null`

The provider’s brand colour as a hex value, such as `#ff0000`. This reads `getPrimaryColor()`.
:::

::: reference
### `icon`

**Type:** `string|null`

The provider’s icon as SVG markup. This reads `getIcon()`.
:::

::: reference
### `cpEditUrl`

**Type:** `string|null`

The URL to edit the source in the control panel.
:::

## Methods

::: reference
### `isConfigured()`

**Returns:** `bool`

Returns whether the source has both a client ID and client secret saved.
:::

::: reference
### `isConnected()`

**Returns:** `bool`

Returns whether the source is connected to its provider with a saved access token.
:::

::: reference
### `getToken()`

**Returns:** `verbb\auth\models\Token|null`

Returns the source’s saved access token, or `null` when it isn’t connected.
:::

::: reference
### `getPosts()`

**Arguments:** `options` (`array`, optional), which accepts `limit` and `offset` keys.

**Returns:** `array|null`

Returns the source’s [Post](docs:reference/post) objects. When the cache is enabled, posts come from the database and are refreshed from the provider once the cache duration has passed.
:::
