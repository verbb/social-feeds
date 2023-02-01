# Rendering Posts
There are two main methods of rendering [Post](docs:developers/post) objects, manual or automatic.

## Getting Posts
You'll need to either fetch posts from a [Feed](docs:feature-tour/feeds) or a [Source](docs:feature-tour/sources).

```twig
{# Get the source by its handle #}
{% set source = craft.socialFeeds.getSoureByHandle('mySourceHandle') %}
{% set posts = source.getPosts() %}

{% for post in posts %}
    ID: {{ post.id }}<br>
    Content: {{ post.getContent() }}
{% endfor %}

{# OR get the Posts from a Feed (multiple Sources) #}
{% set posts = craft.socialFeeds.getPosts('myFeedHandle') %}

{% for post in posts %}
    ID: {{ post.id }}<br>
    Content: {{ post.getContent() }}
{% endfor %}
```

It'll be up to you on how to render your Posts!

## Rendering Posts
Another approach is to let Social Feeds handle the rendering of your Posts for you. This can only be done for a [Feed](docs:feature-tour/feeds).

```twig
{{ craft.socialFeeds.renderPosts('myFeedHandle') }}
```

This will render your Posts as cards, with all the CSS applied without having to lift a finger.

### Render Options
You can also pass in some additional options to render.

```twig
{% set options = {
    layout: 'masonry',
    limit: 20,
    offset: 10,
} %}

{{ craft.socialFeeds.renderPosts('myFeedHandle', options) }}
```

Here, we're setting the layout to use a [masonry](https://masonry.desandro.com/) style layout and using `limit` and `offset` to paginate results.

