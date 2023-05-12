# Posts
A Post represents a content object, pulled in from social media providers from a [Source](docs:feature-tour/sources). It's the primary source of content, and what you'll want to actually _show_ on your site.

## Getting Posts
You'll need to either fetch posts from a [Feed](docs:feature-tour/feeds) or a [Source](docs:feature-tour/sources).

```twig
{# Get the source by its handle #}
{% set source = craft.socialFeeds.getSourceByHandle('mySourceHandle') %}
{% set posts = source.getPosts() %}

{# OR get the Posts from a Feed (multiple Sources) #}
{% set posts = craft.socialFeeds.getPosts('myFeedHandle') %}

{% for post in posts %}
    ID: {{ post.id }}<br>
    Content: {{ post.getContent() }}
{% endfor %}
```

:::tip
Check out our guide on [Rendering Posts](docs:template-guides/rendering-posts) for more.
:::

