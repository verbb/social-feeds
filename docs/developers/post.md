# Post
Whenever you're dealing with a post in your template, you're actually working with a `Post` object. As each provider returns different content, some attributes will return different content. However, the purpose of a `Post` object is to normalize the content from a provider.

You can access the raw content fetched from a provider's API using the `data` attribute.

<span id="attributes"></span>

## Properties

::: reference
### `id`

**Type:** `string|null`

The ID of the post.
:::

::: reference
### `uid`

**Type:** `string|null`

The unique or secondary identifer.
:::

::: reference
### `text`

**Type:** `string|null`

The main text for the post.
:::

::: reference
### `url`

**Type:** `string|null`

The URL to the post on the provider site.
:::

::: reference
### `sourceId`

**Type:** `int|null`

The ID for the [Source](docs:developers/source).
:::

::: reference
### `sourceHandle`

**Type:** `string|null`

The handle for the [Source](docs:developers/source).
:::

::: reference
### `sourceType`

**Type:** `string|null`

The type of [Source](docs:developers/source).
:::

::: reference
### `postType`

**Type:** `string|null`

The type of post this is classified as.
:::

::: reference
### `likes`

**Type:** `int|null`

The number of likes this post has received.
:::

::: reference
### `shares`

**Type:** `int|null`

The number of shares this post has received.
:::

::: reference
### `replies`

**Type:** `int|null`

The number of replies this post has received.
:::

::: reference
### `author`

**Type:** `verbb\socialfeeds\models\PostAuthor|null`

The [Post Author](#post-author).
:::

::: reference
### `tags`

**Type:** `array`

A collection of tags or hashtags for the post.
:::

::: reference
### `links`

**Type:** `array`

A collection of [Post Link](#post-link) objects.
:::

::: reference
### `images`

**Type:** `array`

A collection of [Post Media](#post-media) objects.
:::

::: reference
### `videos`

**Type:** `array`

A collection of [Post Media](#post-media) objects.
:::

::: reference
### `data`

**Type:** `array|null`

A full response from the social media provider.
:::

::: reference
### `meta`

**Type:** `array|null`

Any additional content for the post type for easy-access.
:::


## Methods

::: reference
### `getSource()`

**Returns:** `string|null`

Returns the source this post was made from.
:::

::: reference
### `getContent()`

**Returns:** `Twig\Markup`

Returns text or message as the main content of the post.
:::

::: reference
### `hasMedia()`

**Returns:** `bool`

Whether or not a post contains an image or video.
:::

::: reference
### `getAuthorImage()`

**Returns:** `array|null`

Returns the author's profile image (if one exists).
:::

::: reference
### `getImage()`

**Returns:** `array|null`

Returns the first image (typically the primary image) for the post.
:::

::: reference
### `getVideo()`

**Returns:** `array|null`

Returns the first video (typically the primary video) for the post.
:::

::: reference
### `getFriendlyDate(date)`

**Returns:** `string`

Returns a "friendly" representation of a date (e.g. `5.2M`).
:::

::: reference
### `getProviderColor`

The primary brand color of the provider connected.
:::

::: reference
### `getProviderIcon`

The SVG icon of the source provider connected.
:::



## Post Author

#<span id="attributes"></span>

## Properties

::: reference
### `id`

**Type:** `string|null`

The ID of the author.
:::

::: reference
### `username`

The username of the author.
:::

::: reference
### `name`

The name of the author.
:::

::: reference
### `url`

**Type:** `string|null`

The url of the author.
:::

::: reference
### `photo`

The photo of the author.
:::



## Post Media
A media item represents either a video or an image.

#<span id="attributes"></span>

## Properties

::: reference
### `id`

**Type:** `string|null`

The ID of the media item.
:::

::: reference
### `title`

**Type:** `string|null`

The title of the media item.
:::

::: reference
### `type`

The type of media item (`video` or `image`).
:::

::: reference
### `url`

**Type:** `string|null`

The url of the media item.
:::

::: reference
### `width`

The width of the media item.
:::

::: reference
### `height`

The height of the media item.
:::



## Post Link

#<span id="attributes"></span>

## Properties

::: reference
### `id`

**Type:** `string|null`

The ID of the link.
:::

::: reference
### `title`

**Type:** `string|null`

The title of the link.
:::

::: reference
### `url`

**Type:** `string|null`

The ID of the link.
:::
