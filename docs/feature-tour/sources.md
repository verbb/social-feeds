# Sources
A Source allows you to connect to a social media provider to fetch Posts from. Sources can then be combined in [Feeds](docs:feature-tour/feeds) as a content aggregator.

## Provider Settings
Each provider will be different, but almost all require OAuth authentication. Create a Source and follow the documentation for the provider to get your Client ID/Secret credentials. Once configured, connect to the provider, going through the OAuth handshake to retrieve a token.

Once connected, you'll be able to define what content you want to fetch from the provider. For example, Facebook can fetch Posts from a public Facebook Page, or just Videos. For Twitter, you can fetch Posts from a user, or from a nominated hashtag.

## Fetching Source Posts
To fetch the Posts for a source, use `source.getPosts()`.

```twig
{# Get the source by its handle #}
{% set source = craft.socialFeeds.getSoureByHandle('mySourceHandle') %}

{% for post in source.getPosts() %}
    ID: {{ post.id }}<br>
    Content: {{ post.getContent() }}
{% endfor %}
```

:::tip
Check out our guide on [Rendering Posts](docs:template-guides/rendering-posts) for more.
:::

