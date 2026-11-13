# Interfaces & Types

Social Feeds adds interfaces to Craft’s GraphQL schema for its feeds, sources and posts. [Queries](docs:graphql/queries) shows how to request them through the `socialFeeds` query. All fields are nullable.

## Interfaces

### The `SocialFeedsInterface` Interface

The `socialFeeds` query returns this interface, which holds the plugin’s queries. Select `feeds`, `feed`, `sources` or `source` inside it, as described in [Queries](docs:graphql/queries#plugin-queries).

### The `FeedInterface` Interface

A [feed](docs:reference/feed), returned by the `feeds` and `feed` queries.

::: reference
#### `id`

**Type:** `ID`

The ID of the feed.
:::

::: reference
#### `uid`

**Type:** `String`

The unique identifier of the feed.
:::

::: reference
#### `name`

**Type:** `String`

The name of the feed.
:::

::: reference
#### `handle`

**Type:** `String`

The handle of the feed.
:::

::: reference
#### `enabled`

**Type:** `Boolean`

Whether the feed is enabled.
:::

::: reference
#### `sources`

**Type:** `[SourceInterface]`

The feed’s sources. This field takes the same arguments as the [`sources`](docs:graphql/queries#the-sources-query) query.
:::

### The `SourceInterface` Interface

A [source](docs:reference/source), returned by the `sources` and `source` queries and a feed’s `sources` field.

::: reference
#### `id`

**Type:** `ID`

The ID of the source.
:::

::: reference
#### `uid`

**Type:** `String`

The unique identifier of the source.
:::

::: reference
#### `name`

**Type:** `String`

The name of the source.
:::

::: reference
#### `handle`

**Type:** `String`

The handle of the source.
:::

::: reference
#### `enabled`

**Type:** `Boolean`

Whether the source is enabled.
:::

::: reference
#### `dateLastFetch`

**Type:** `DateTime`

The date the source last fetched posts from its provider, or `null` when it hasn’t fetched yet.
:::

::: reference
#### `providerName`

**Type:** `String`

The display name of the source’s provider, such as `YouTube`.
:::

::: reference
#### `providerHandle`

**Type:** `String`

The handle of the source’s provider, such as `youTube`.
:::

::: reference
#### `primaryColor`

**Type:** `String`

The provider’s brand colour as a hex value.
:::

::: reference
#### `icon`

**Type:** `String`

The provider’s icon as SVG markup.
:::

::: reference
#### `isConnected`

**Type:** `Boolean`

Whether the source is connected to its provider with a saved access token.
:::

::: reference
#### `posts`

**Type:** `[PostInterface]`

The source’s posts, newest first. This field takes `limit` and `offset` arguments, described in [Query Posts](docs:graphql/queries#query-posts).
:::

### The `PostInterface` Interface

A [post](docs:reference/post), returned by a source’s `posts` field.

```graphql
posts(limit: 1) {
  text
  postType
  tags
  links {
    title
    url
  }
  videos {
    url
  }
}
```

```json
{
  "posts": [
    {
      "text": "Our new range is out now.",
      "postType": "video",
      "tags": "[\"launch\",\"summer\"]",
      "links": [],
      "videos": [
        { "url": "https://example.com/video.mp4" }
      ]
    }
  ]
}
```

::: reference
#### `id`

**Type:** `ID`

The post’s ID on the provider.
:::

::: reference
#### `uid`

**Type:** `String`

The provider’s unique identifier for the post item.
:::

::: reference
#### `title`

**Type:** `String`

The post’s title, for providers that give posts one, such as YouTube. This is `null` for most posts.
:::

::: reference
#### `text`

**Type:** `String`

The post’s main text or description.
:::

::: reference
#### `url`

**Type:** `String`

The URL of the post on the provider’s site.
:::

::: reference
#### `sourceId`

**Type:** `Int`

The ID of the source the post was fetched from.
:::

::: reference
#### `sourceHandle`

**Type:** `String`

The handle of the source the post was fetched from.
:::

::: reference
#### `sourceType`

**Type:** `String`

The handle of the source’s provider, such as `instagram`.
:::

::: reference
#### `postType`

**Type:** `String`

The kind of post, for providers that classify them, such as `image`, `video` or `event`.
:::

::: reference
#### `likes`

**Type:** `Int`

The number of likes the post has, or `null` when the provider doesn’t report it.
:::

::: reference
#### `shares`

**Type:** `Int`

The number of shares the post has, or `null` when the provider doesn’t report it.
:::

::: reference
#### `replies`

**Type:** `Int`

The number of replies or comments the post has, or `null` when the provider doesn’t report it.
:::

::: reference
#### `dateCreated`

**Type:** `DateTime`

The date the post was published on the provider.
:::

::: reference
#### `dateUpdated`

**Type:** `DateTime`

The date the post was last updated on the provider, when the provider reports it.
:::

::: reference
#### `author`

**Type:** `PostAuthorInterface`

The author who published the post, or `null` when the provider doesn’t supply one.
:::

::: reference
#### `tags`

**Type:** `String`

A JSON string of the post’s tags or hashtags, such as `["summer","sale"]`.
:::

::: reference
#### `links`

**Type:** `[PostLinkInterface]`

The links attached to the post.
:::

::: reference
#### `images`

**Type:** `[PostMediaInterface]`

The images attached to the post.
:::

::: reference
#### `videos`

**Type:** `[PostMediaInterface]`

The videos attached to the post.
:::

::: reference
#### `data`

**Type:** `String`

A JSON string of the full response for the post from the provider’s API, with access tokens and other secrets removed.
:::

::: reference
#### `meta`

**Type:** `String`

A JSON string of any extra provider-specific values kept alongside the post.
:::

### The `PostAuthorInterface` Interface

The [author](docs:reference/post-author) of a post, returned by a post’s `author` field.

::: reference
#### `id`

**Type:** `String`

The author’s ID on the provider.
:::

::: reference
#### `username`

**Type:** `String`

The author’s username on the provider, for providers that have one.
:::

::: reference
#### `name`

**Type:** `String`

The author’s display name.
:::

::: reference
#### `url`

**Type:** `String`

The URL of the author’s profile on the provider.
:::

::: reference
#### `photo`

**Type:** `String`

The URL of the author’s profile photo.
:::

### The `PostLinkInterface` Interface

A [link](docs:reference/post-link) attached to a post, returned by a post’s `links` field.

::: reference
#### `id`

**Type:** `String`

The link’s ID on the provider, when it has one.
:::

::: reference
#### `title`

**Type:** `String`

The link’s title, when the provider supplies one.
:::

::: reference
#### `url`

**Type:** `String`

The URL the link points to.
:::

### The `PostMediaInterface` Interface

An [image or video](docs:reference/post-media) attached to a post, returned by a post’s `images` and `videos` fields.

::: reference
#### `id`

**Type:** `String`

The media item’s ID on the provider.
:::

::: reference
#### `title`

**Type:** `String`

The media item’s title or alternative text, when the provider supplies one.
:::

::: reference
#### `type`

**Type:** `String`

The kind of media item, either `image` or `video`.
:::

::: reference
#### `url`

**Type:** `String`

The URL of the image or video file.
:::

::: reference
#### `width`

**Type:** `Int`

The width of the media item in pixels, when the provider reports it.
:::

::: reference
#### `height`

**Type:** `Int`

The height of the media item in pixels, when the provider reports it.
:::
