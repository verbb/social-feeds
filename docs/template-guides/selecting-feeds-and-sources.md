# Selecting Feeds and Sources

Choose a feed by the handle configured for it. A feed describes what you want to display, and its sources provide the connected accounts. Use enabled-source lookups when disabled connections should be excluded.

Most templates only need a feed's handle, which you pass to `craft.socialFeeds.getPosts()` or `craft.socialFeeds.renderPosts()` as described in [Displaying Feed Posts](docs:template-guides/displaying-feed-posts). The calls below are useful when a template needs to know about the feed or its sources first, such as showing a heading with the feed's name or linking to each connected account.

```twig
{% set feed = craft.socialFeeds.getFeedByHandle('homepage') %}

{% if feed and feed.enabled %}
    <h2>{{ feed.name }}</h2>

    <p>
        Follow us on
        {% for source in feed.getSources() %}
            {{ source.providerName }}{{ not loop.last ? ', ' }}
        {% endfor %}
    </p>
{% endif %}
```

`feed.getSources()` only returns the feed's sources that are enabled and connected to their provider, which are the same sources its posts come from.

## Listing Feeds

`craft.socialFeeds.getAllFeeds()` returns an array of every [Feed](docs:reference/feed) object, including disabled feeds. It takes no arguments.

`craft.socialFeeds.getAllEnabledFeeds()` returns an array of the enabled [Feed](docs:reference/feed) objects. It takes no arguments.

```twig
{% for feed in craft.socialFeeds.getAllEnabledFeeds() %}
    <h2>{{ feed.name }}</h2>
    {{ craft.socialFeeds.renderPosts(feed.handle, { limit: 6 }) }}
{% endfor %}
```

## Getting One Feed

`craft.socialFeeds.getFeedById(id)` returns the [Feed](docs:reference/feed) object with the given ID, or `null` if there isn't one. `id` is required. Disabled feeds are returned too, so check `feed.enabled` if that matters to your template.

`craft.socialFeeds.getFeedByHandle(handle)` returns the [Feed](docs:reference/feed) object with the given handle, or `null` if there isn't one. `handle` is required and must match exactly, including its case. Like `getFeedById()`, it returns disabled feeds.

`craft.socialFeeds.getFeedByUid(uid)` works the same way with a feed's UID, which stays the same across environments when you use project config.

```twig
{% set feed = craft.socialFeeds.getFeedByUid('a1b2c3d4-e5f6-7890-abcd-ef1234567890') %}
```

## Listing Sources

`craft.socialFeeds.getAllSources()` returns an array of every [Source](docs:reference/source) object, including disabled sources and sources that aren't connected. It takes no arguments.

`craft.socialFeeds.getAllEnabledSources()` returns an array of the enabled [Source](docs:reference/source) objects. It takes no arguments.

`craft.socialFeeds.getAllConfiguredSources()` returns an array of the enabled [Source](docs:reference/source) objects that have their client ID and secret saved. It takes no arguments. A configured source isn't necessarily connected yet, so check `source.isConnected()` before relying on its posts.

## Getting One Source

`craft.socialFeeds.getSourceById(id)` returns the [Source](docs:reference/source) object with the given ID, or `null` if there isn't one. `id` is required. Disabled and unconnected sources are returned too.

`craft.socialFeeds.getSourceByHandle(handle)` returns the [Source](docs:reference/source) object with the given handle, or `null` if there isn't one. `handle` is required and must match exactly. Like `getSourceById()`, it returns disabled and unconnected sources.

Use it when you want posts from a single account rather than a whole feed. A source's `getPosts()` method takes the same `limit` and `offset` options as `craft.socialFeeds.getPosts()`.

```twig
{% set source = craft.socialFeeds.getSourceByHandle('instagram') %}

{% if source and source.enabled and source.isConnected() %}
    {% for post in source.getPosts({ limit: 4 }) %}
        <a href="{{ post.url }}">{{ post.getContent() }}</a>
    {% endfor %}
{% endif %}
```
