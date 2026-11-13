# Post

A post is one item fetched from a social media provider, such as a tweet, an Instagram photo or a YouTube video. Whenever you’re dealing with a post in your template, you’re working with a `Post` object. Each provider returns different content, and a `Post` normalises it so the same template works for every source. The raw response from the provider’s API is always available through `data`.

`craft.socialFeeds.getPosts()` returns an array of posts for a [feed](docs:reference/feed), and `source.getPosts()` returns them for a single [source](docs:reference/source).

```twig
{% for post in craft.socialFeeds.getPosts('homepage', { limit: 10 }) %}
    <a href="{{ post.url }}">
        {{ post.getContent() }}
    </a>

    {% set image = post.getImage() %}

    {% if image %}
        <img src="{{ image.url }}" alt="{{ image.alt }}">
    {% endif %}

    {{ post.author.name ?? '' }} · {{ post.getFriendlyDate(post.dateCreated) }}
{% endfor %}
```

<span id="attributes"></span>

## Properties

::: reference
### `id`

**Type:** `string|null`

The post’s ID on the provider, such as a YouTube video ID.
:::

::: reference
### `uid`

**Type:** `string|null`

The provider’s unique identifier for the post item. For most providers this matches `id`.
:::

::: reference
### `title`

**Type:** `string|null`

The post’s title, for providers that give posts one, such as YouTube or a Facebook event. This is `null` for most posts.
:::

::: reference
### `text`

**Type:** `string|null`

The post’s main text or description, as plain text.
:::

::: reference
### `url`

**Type:** `string|null`

The URL of the post on the provider’s site.
:::

::: reference
### `sourceId`

**Type:** `int|null`

The ID of the [source](docs:reference/source) the post was fetched from.
:::

::: reference
### `sourceHandle`

**Type:** `string|null`

The handle of the source the post was fetched from.
:::

::: reference
### `sourceType`

**Type:** `string|null`

The handle of the source’s provider, such as `instagram` or `youTube`.
:::

::: reference
### `postType`

**Type:** `string|null`

The kind of post, for providers that classify them, such as `image`, `video`, `reel` or `event`. The values depend on the provider, and this is `null` when the provider doesn’t classify posts.
:::

::: reference
### `likes`

**Type:** `int|null`

The number of likes the post has, or `null` when the provider doesn’t report it.
:::

::: reference
### `shares`

**Type:** `int|null`

The number of shares the post has, or `null` when the provider doesn’t report it.
:::

::: reference
### `replies`

**Type:** `int|null`

The number of replies or comments the post has, or `null` when the provider doesn’t report it.
:::

::: reference
### `dateCreated`

**Type:** `DateTime|null`

The date the post was published on the provider.
:::

::: reference
### `dateUpdated`

**Type:** `DateTime|null`

The date the post was last updated on the provider, when the provider reports it.
:::

::: reference
### `author`

**Type:** `verbb\socialfeeds\models\PostAuthor|null`

The [Post Author](docs:reference/post-author) who published the post, or `null` when the provider doesn’t supply one.
:::

::: reference
### `tags`

**Type:** `array`

The post’s tags or hashtags, as strings.
:::

::: reference
### `links`

**Type:** `array`

The [Post Link](docs:reference/post-link) objects attached to the post.
:::

::: reference
### `images`

**Type:** `array`

The image [Post Media](docs:reference/post-media) objects attached to the post.
:::

::: reference
### `videos`

**Type:** `array`

The video [Post Media](docs:reference/post-media) objects attached to the post.
:::

::: reference
### `data`

**Type:** `array|null`

The full, unaltered response for the post from the provider’s API. Use it for content Social Feeds doesn’t normalise.
:::

::: reference
### `meta`

**Type:** `array|null`

Any extra provider-specific values Social Feeds keeps alongside the post.
:::

## Methods

::: reference
### `getSource()`

**Returns:** `string|null`

Returns the class name of the source type that fetched the post, such as `verbb\socialfeeds\sources\YouTube`, or `null` when that type is no longer registered. Use `sourceHandle` to look up the source itself.
:::

::: reference
### `getSourceName()`

**Returns:** `string|null`

Returns the display name of the post’s provider, such as `YouTube`.
:::

::: reference
### `getContent()`

**Returns:** `Twig\Markup`

Returns the post’s main content, ready to output. This is the `title` when the post has one, otherwise the `text`, HTML-encoded.
:::

::: reference
### `hasMedia()`

**Returns:** `bool`

Returns whether the post has at least one image or video.
:::

::: reference
### `getAuthorImage()`

**Returns:** `array|null`

Returns the author’s profile image as an array with `url` and `alt` keys, or `null` when there’s no photo.
:::

::: reference
### `getImage()`

**Returns:** `array|null`

Returns the post’s first image as an array with `url` and `alt` keys, or `null` when it has no images. The alt text names the author, provider and date.
:::

::: reference
### `getVideo()`

**Returns:** `array|null`

Returns the post’s first video as an array with a `url` key, or `null` when it has no videos.
:::

::: reference
### `getFriendlyDate()`

**Arguments:** `date` (`DateTime`), the date to compare with now, usually `post.dateCreated`.

**Returns:** `string`

Returns how long ago a date was in short form, such as `5d` or `3h`.
:::

::: reference
### `getProviderColor()`

**Returns:** `string|null`

Returns the brand colour of the post’s provider as a hex value.
:::

::: reference
### `getProviderIcon()`

**Returns:** `string|null`

Returns the icon of the post’s provider as SVG markup.
:::
