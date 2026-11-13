# Queries

Social Feeds lets a headless frontend fetch its [feeds](docs:reference/feed), [sources](docs:reference/source) and [posts](docs:reference/post) through Craft’s GraphQL API. Every query sits inside a single `socialFeeds` query, which groups the plugin’s content together.

## Set Up the Schema

In **GraphQL → Schemas**, edit the schema your frontend uses and enable **Query Social Feeds** under **Social Feeds**. Without it, the `socialFeeds` query isn’t part of the schema. Use **GraphQL → GraphiQL** with that schema to try the examples below, and see [Craft’s GraphQL documentation](https://craftcms.com/docs/5.x/development/graphql.html) for endpoints and tokens.

Posts come from the same cache as your templates. When a source’s cache duration has passed, a query that requests its posts fetches fresh posts from the provider first, just like rendering a feed in Twig.

## Plugin Queries

### The `socialFeeds` Query

Returns the Social Feeds content the schema can read. It takes no arguments, and you select `feeds`, `feed`, `sources` or `source` inside it.

```graphql
{
  socialFeeds {
    feeds {
      handle
    }
    sources {
      handle
      providerName
    }
  }
}
```

```json
{
  "data": {
    "socialFeeds": {
      "feeds": [
        { "handle": "homepage" }
      ],
      "sources": [
        { "handle": "instagram", "providerName": "Instagram" },
        { "handle": "youTube", "providerName": "YouTube" }
      ]
    }
  }
}
```

### The `feeds` Query

Returns a list of [`FeedInterface`](docs:graphql/interfaces-types#the-feedinterface-interface) objects. Use it inside `socialFeeds` to list feeds, optionally narrowed by these arguments.

| Argument | Type | Description |
| --- | --- | --- |
| `id` | `[Int]` | Narrows the results to feeds with these IDs. |
| `handle` | `[String]` | Narrows the results to feeds with these handles. |
| `uid` | `[String]` | Narrows the results to feeds with these UIDs. |
| `limit` | `Int` | Sets the maximum number of feeds to return. |

```graphql
{
  socialFeeds {
    feeds(handle: ["homepage", "footer"]) {
      name
      handle
      enabled
    }
  }
}
```

```json
{
  "data": {
    "socialFeeds": {
      "feeds": [
        { "name": "Homepage", "handle": "homepage", "enabled": true },
        { "name": "Footer", "handle": "footer", "enabled": true }
      ]
    }
  }
}
```

### The `feed` Query

Returns a single [`FeedInterface`](docs:graphql/interfaces-types#the-feedinterface-interface) object. It takes the same arguments as `feeds`, and needs at least one of `id`, `handle` or `uid`.

```graphql
{
  socialFeeds {
    feed(handle: "homepage") {
      name
      sources {
        name
        providerName
      }
    }
  }
}
```

```json
{
  "data": {
    "socialFeeds": {
      "feed": {
        "name": "Homepage",
        "sources": [
          { "name": "Instagram", "providerName": "Instagram" }
        ]
      }
    }
  }
}
```

### The `sources` Query

Returns a list of [`SourceInterface`](docs:graphql/interfaces-types#the-sourceinterface-interface) objects. Use it inside `socialFeeds` to list sources, optionally narrowed by these arguments.

| Argument | Type | Description |
| --- | --- | --- |
| `id` | `[Int]` | Narrows the results to sources with these IDs. |
| `handle` | `[String]` | Narrows the results to sources with these handles. |
| `uid` | `[String]` | Narrows the results to sources with these UIDs. |
| `limit` | `Int` | Sets the maximum number of sources to return. |

```graphql
{
  socialFeeds {
    sources(handle: ["instagram", "youTube"]) {
      name
      handle
      isConnected
      dateLastFetch
    }
  }
}
```

```json
{
  "data": {
    "socialFeeds": {
      "sources": [
        { "name": "Instagram", "handle": "instagram", "isConnected": true, "dateLastFetch": "2026-11-12T09:30:00+11:00" },
        { "name": "YouTube", "handle": "youTube", "isConnected": true, "dateLastFetch": "2026-11-12T09:30:02+11:00" }
      ]
    }
  }
}
```

### The `source` Query

Returns a single [`SourceInterface`](docs:graphql/interfaces-types#the-sourceinterface-interface) object. It takes the same arguments as `sources`, and needs at least one of `id`, `handle` or `uid`.

```graphql
{
  socialFeeds {
    source(handle: "instagram") {
      name
      primaryColor
      posts(limit: 2) {
        id
        text
        url
      }
    }
  }
}
```

```json
{
  "data": {
    "socialFeeds": {
      "source": {
        "name": "Instagram",
        "primaryColor": "#e4405f",
        "posts": [
          { "id": "17895695668004550", "text": "New season stock has landed.", "url": "https://www.instagram.com/p/C1a2b3c4d5e/" },
          { "id": "17895695668004551", "text": "Behind the scenes at the shoot.", "url": "https://www.instagram.com/p/C1a2b3c4d5f/" }
        ]
      }
    }
  }
}
```

## Query Posts

Request a source’s posts through its `posts` field, which returns a list of [`PostInterface`](docs:graphql/interfaces-types#the-postinterface-interface) objects with the newest first.

| Argument | Type | Description |
| --- | --- | --- |
| `limit` | `Int` | Sets the number of posts to return, from 0 to 100. Defaults to 50. |
| `offset` | `Int` | Skips this many posts first, for paging through results. Defaults to 0. |

This example fetches the second page of ten posts, with each post’s author and first images:

```graphql
query SourcePosts($handle: [String]) {
  socialFeeds {
    source(handle: $handle) {
      posts(limit: 10, offset: 10) {
        text
        url
        likes
        dateCreated
        author {
          name
          photo
        }
        images {
          url
          width
          height
        }
      }
    }
  }
}
```

```json
{
  "handle": ["instagram"]
}
```

```json
{
  "data": {
    "socialFeeds": {
      "source": {
        "posts": [
          {
            "text": "New season stock has landed.",
            "url": "https://www.instagram.com/p/C1a2b3c4d5e/",
            "likes": 128,
            "dateCreated": "2026-11-10T14:05:00+11:00",
            "author": { "name": "Example Store", "photo": "https://example.com/profile.jpg" },
            "images": [
              { "url": "https://example.com/post.jpg", "width": 1080, "height": 1350 }
            ]
          }
        ]
      }
    }
  }
}
```

`tags`, `data` and `meta` return JSON strings, so parse them in your client. [Interfaces & Types](docs:graphql/interfaces-types) lists every field you can request.
