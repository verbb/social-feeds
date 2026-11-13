# Displaying Feed Posts

Use a configured feed handle to fetch posts for custom markup, or render the feed with the plugin’s rendering helper. Keep the same feed handle in both approaches so you can compare their output.

These examples use a feed with the handle `homepage`. Create the feed and add its sources first, as described in [Feeds](docs:feature-tour/feeds). Place either call in the template where the posts should appear, such as your homepage template.

```twig
{% set posts = craft.socialFeeds.getPosts('homepage', { limit: 6 }) %}

<div class="social-posts">
    {% for post in posts %}
        <a href="{{ post.url }}">
            {% set image = post.getImage() %}

            {% if image %}
                <img src="{{ image.url }}" alt="{{ image.alt }}">
            {% endif %}

            <p>{{ post.getContent() }}</p>
            <small>{{ post.getSourceName() }}, {{ post.getFriendlyDate(post.dateCreated) }}</small>
        </a>
    {% else %}
        <p>No posts to show yet.</p>
    {% endfor %}
</div>
```

This shows the six newest posts from all the feed's sources, each linking to the original post. The same handle with `renderPosts()` outputs the plugin's ready-made cards instead.

```twig
{{ craft.socialFeeds.renderPosts('homepage', { limit: 6 }) }}
```

[Rendering Posts](docs:template-guides/rendering-posts) compares the two approaches and shows the masonry layout.

## Fetching Posts for Your Own Markup

`craft.socialFeeds.getPosts(feedHandle, options)` returns an array of [Post](docs:reference/post) objects from every enabled, connected source in the feed, newest first. `feedHandle` is required. The array is empty when there's no feed with that handle or the feed is disabled.

`options` is an optional hash that accepts `limit` and `offset`, which you can use to paginate the posts. Without a `limit`, every stored post is returned.

```twig
{# The second page of six posts #}
{% set posts = craft.socialFeeds.getPosts('homepage', {
    limit: 6,
    offset: 6,
}) %}
```

When caching is enabled, posts come from Social Feeds' database and are refreshed from each provider once the cache duration passes. See [Cache](docs:feature-tour/cache) for how this works.

## Rendering Posts With the Plugin's Markup

`craft.socialFeeds.renderPosts(feedHandle, options)` outputs the feed's posts as cards, using the plugin's own markup and styles. It fetches posts the same way as `getPosts()`, so `feedHandle` is required, and `options` accepts the same `limit` and `offset` keys. It outputs an empty container when there's no enabled feed with that handle.

`options` also accepts `layout`. Set it to `masonry` for a [masonry](https://masonry.desandro.com/) layout, or leave it out for the default grid. The call registers the styles the cards need, and the masonry script when you use that layout, so you don't need to include them yourself.

```twig
{{ craft.socialFeeds.renderPosts('homepage', {
    layout: 'masonry',
    limit: 12,
}) }}
```
