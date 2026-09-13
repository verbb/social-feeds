# Feeds
Feeds are a collection of [Sources](docs:feature-tour/sources) and serve as an aggregate for your Posts. For example, you might have a Source for Twitter and Facebook, but you'd like your Posts shown alongside one another in order of their published date. That's where a Feed comes in to combine these Posts.

To get started, ensure you create your Sources first. Then, create a Feed, enabling any sources you'd like included.

## Fetching Feed Posts
To fetch the Posts for a feed, use `craft.socialFeeds.getPosts()`.

```twig
{% set posts = craft.socialFeeds.getPosts('myFeedHandle') %}

{% for post in posts %}
    ID: {{ post.id }}<br>
    Content: {{ post.getContent() }}
{% endfor %}
```

:::tip
Check out our guide on [Rendering Posts](docs:template-guides/rendering-posts) for more.
:::

## Check the Combined Feed

For example, connect two sources used by your organisation, then create a feed with the handle `news` and enable both sources. Replace `myFeedHandle` in the example above with `news` and put it in the page's Twig template. Check that the output includes posts from the intended accounts.

Publish or identify a recent post on one of those accounts and check the feed again after its configured [cache](docs:feature-tour/cache) refresh. A source represents the connection, a Post represents a fetched item, and a Feed combines items for display. If one account is missing, check that source's connection and content settings before changing the template.
