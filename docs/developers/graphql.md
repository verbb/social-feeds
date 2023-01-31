# GraphQL
Social Feed supports accessing [Feed](docs:feature-tour/feeds) and [Source](docs:feature-tour/sources) objects via GraphQL, along with [Post](docs:feature-tour/posts) objects. Be sure to read about [Craft's GraphQL support](https://craftcms.com/docs/4.x/graphql.html).

## Feeds

### Example

:::code
```graphql GraphQL
{
    socialShare {
        feeds(handle: ["myFeed", "myOtherFeed"]) {
            handle
        }

        feed(handle: "myFeed") {
            name
            handle
            enabled

            sources {
                name
            }
        }
    }
}
```

```json JSON Response
{
    "data": {
        "socialFeed": {
            "feeds": [
                {
                    "handle": "myFeed"
                },
                {
                    "handle": "myOtherFeed"
                }
            ],
            "feed": {
                "name": "My Feed",
                "handle": "myFeed",
                "enabled": true,
                "sources": [
                    {
                        "name": "My Source"
                    }
                ]
            }
        }
    }
}
```
:::

### The `feeds` query
This query is used to query [Feed](docs:feature-tour/feeds) objects. You can also use the singular `feed` to fetch a single Feed.

| Argument | Type | Description
| - | - | -
| `id`| `[Int]` | Narrows the query results based on the feeds’s ID.
| `handle`| `[String]` | Narrows the query results based on the feeds’s handle.
| `uid`| `[String]` | Narrows the query results based on the feeds’s UID.
| `limit`| `Int` | Sets the limit for paginated results.


### The `FeedInterface` interface
This is the interface implemented by all feeds.

| Field | Type | Description
| - | - | -
| `name`| `String` | The feed’s name.
| `handle`| `String` | The feed’s handle.
| `enabled`| `Boolean` | Whether the feed is enabled.
| `sources`| `[SourceInterface]` | The feed’s sources.


## Sources

### Example

:::code
```graphql GraphQL
{
    socialShare {
        sources(handle: ["mySource", "myOtherSource"]) {
            handle
        }

        source(handle: "mySource") {
            name
            handle
            enabled

            posts {
                id
                text
            }
        }
    }
}
```

```json JSON Response
{
    "data": {
        "socialFeed": {
            "sources": [
                {
                    "handle": "mySource"
                },
                {
                    "handle": "myOtherSource"
                }
            ],
            "source": {
                "name": "My Feed",
                "handle": "mySource",
                "enabled": true,
                "posts": [
                    {
                        "id": "900949524373003",
                        "text": "Just a sample post."
                    }
                ]
            }
        }
    }
}
```
:::

### The `sources` query
This query is used to query [Source](docs:feature-tour/sources) objects. You can also use the singular `source` to fetch a single Source.

| Argument | Type | Description
| - | - | -
| `id`| `[Int]` | Narrows the query results based on the sources’s ID.
| `handle`| `[String]` | Narrows the query results based on the sources’s handle.
| `uid`| `[String]` | Narrows the query results based on the sources’s UID.
| `limit`| `Int` | Sets the limit for paginated results.


### The `SourceInterface` interface
This is the interface implemented by all feeds.

| Field | Type | Description
| - | - | -
| `name`| `String` | The source’s name.
| `handle`| `String` | The source’s handle.
| `enabled`| `Boolean` | Whether the source is enabled.
| `dateLastFetch`| `DateTime` | The date of the last time Posts were fetched.
| `providerName`| `String` | The name of the source provider connected.
| `providerHandle`| `String` | The handle of the source provider connected.
| `primaryColor`| `String` | The primary brand color of the provider connected.
| `icon`| `String` | The SVG icon of the source provider connected.
| `isConnected`| `Boolean` | Whether the source provider has been connected and has a token.
| `posts`| `[PostInterface]` | The source’s posts.


## Posts

### Example

:::code
```graphql GraphQL
{
    socialShare {
        sources(handle: ["mySource", "myOtherSource"]) {
            posts {
                id
                text
            }
        }
    }
}
```

```json JSON Response
{
    "data": {
        "socialFeed": {
            "posts(limit: 10)": [
                {
                    "id": "900949524373003",
                    "text": "Just a sample post."
                }
            ]
        }
    }
}
```
:::

### The `posts` query
This query is used to query [Post](docs:feature-tour/posts) objects. It must be called from a `SourceInterface`, or a `FeedInterface`.

| Argument | Type | Description
| - | - | -
| `limit`| `Int` | Sets the limit for paginated results.
| `offset`| `Int` | Sets the offset for paginated results.


### The `PostInterface` interface
This is the interface implemented by all posts.

| Field | Type | Description
| - | - | -
| `title`| `String` | The post’s title.
| `text`| `String` | The post’s text.
| `url`| `String` | The post’s url.
| `sourceId`| `Int` | The post’s source ID.
| `sourceHandle `| `String`| The post’s source handle.
| `sourceType`| `String` | The post’s source type.
| `postType`| `String` | The post’s type.
| `likes`| `Int` | The post’s number of likes.
| `shares`| `Int` | The post’s number of shares.
| `replies`| `Int` | The post’s number of replies.
| `dateCreated`| `DateTime` | The post’s created date.
| `dateUpdated`| `DateTime` | The post’s updated date.
| `author`| `PostAuthor` | The post’s author.
| `tags`| `String` | The post’s tags as a JSON string.
| `links`| `[PostLink]` | The post’s links.
| `images`| `[PostMedia]` | The post’s images.
| `videos`| `[PostMedia]` | The post’s videos.
| `data`| `String` | The post’s raw data as a JSON string.
| `meta`| `String` | The post’s meta data as a JSON string.


### The `PostAuthorInterface` interface
This is the interface implemented by post authors.

| Field | Type | Description
| - | - | -
| `id`| `String` | The post author’s ID.
| `username`| `String` | The author’s username.
| `name`| `String` | The author’s name.
| `url`| `String` | The author’s url.
| `photo`| `String` | The author’s photo.


### The `PostLinkInterface` interface
This is the interface implemented by post links.

| Field | Type | Description
| - | - | -
| `id`| `String` | The post link’s ID.
| `title`| `String` | The link’s title.
| `url`| `String` | The link’s url.


### The `PostMediaInterface` interface
This is the interface implemented by post media items.

| Field | Type | Description
| - | - | -
| `id`| `String` | The post media’s ID.
| `title`| `String` | The post media’s title.
| `type`| `String` | The post media’s type.
| `url`| `String` | The post media’s url.
| `width`| `Int` | The post media’s width.
| `height`| `Int` | The post media’s height.

