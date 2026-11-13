# Feed

A feed combines the posts from one or more [sources](docs:reference/source) so you can show them together. Whenever you’re dealing with a feed in your template, you’re working with a `Feed` object. Fetch one by its handle, then loop over its sources.

```twig
{% set feed = craft.socialFeeds.getFeedByHandle('homepage') %}

{% for source in feed.getSources() %}
    {{ source.name }}
{% endfor %}
```

To show a feed’s posts, use `craft.socialFeeds.getPosts('homepage')` as described in [Displaying Feed Posts](docs:template-guides/displaying-feed-posts).

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

The name of the feed, as shown in the control panel.
:::

::: reference
### `handle`

**Type:** `string|null`

The handle of the feed, used to fetch it and its posts in templates.
:::

::: reference
### `enabled`

**Type:** `bool|null`

Whether the feed is enabled. A disabled feed returns no posts.
:::

::: reference
### `sources`

**Type:** `array|null`

The IDs of the [sources](docs:reference/source) included in the feed. Use `getSources()` to get the source objects.
:::

::: reference
### `dateCreated`

**Type:** `DateTime|null`

The date the feed was created.
:::

::: reference
### `dateUpdated`

**Type:** `DateTime|null`

The date the feed was last updated.
:::

::: reference
### `uid`

**Type:** `string|null`

The feed’s unique identifier, used in project config.
:::

## Methods

::: reference
### `getSources()`

**Returns:** `array|null`

Returns the [Source](docs:reference/source) objects included in the feed. Sources that no longer exist are left out.
:::
