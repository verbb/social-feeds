# Feeds
Feeds are a collection of [Sources](docs:feature-tour/sources) and serve as an aggregate for your Posts. For example, you might have a Source for Twitter and Facebook, but you'd like your Posts shown alongside one another in order of their published date. That's where a Feed comes in to combine these Posts.

To get started, ensure you create your Sources first. Then, create a Feed, enabling any sources you'd like included.

## Fetching Feed Posts
To fetch the Posts for a feed, use `craft.socialFeed.getPosts()`.

```twig
{% set posts = craft.socialFeed.getPosts('myFeedHandle') %}

{% for post in posts %}
    ID: {{ post.id }}<br>
    Content: {{ post.getContent() }}
{% endfor %}
```

:::tip
Check out our guide on [Rendering Posts](docs:template-guides/rendering-posts) for more.
:::
