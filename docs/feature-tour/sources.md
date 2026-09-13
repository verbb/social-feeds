# Sources
A Source allows you to connect to a social media provider to fetch Posts from. Sources can then be combined in [Feeds](docs:feature-tour/feeds) as a content aggregator.

## Provider Settings
Each provider will be different, but almost all require OAuth authentication. Create a Source and follow the documentation for the provider to get your Client ID/Secret credentials. Once configured, connect to the provider, going through the OAuth handshake to retrieve a token.

Once connected, select the content supported by that provider and account. The available choices depend on the provider's API and permissions. Check the provider's settings rather than assuming that every source can retrieve posts by user, page or hashtag.

## Fetching Source Posts
To fetch the Posts for a source, use `source.getPosts()`.

```twig
{# Get the source by its handle #}
{% set source = craft.socialFeeds.getSourceByHandle('mySourceHandle') %}

{% for post in (source ? source.getPosts() : []) %}
    ID: {{ post.id }}<br>
    Content: {{ post.getContent() }}
{% endfor %}
```

:::tip
Check out our guide on [Rendering Posts](docs:template-guides/rendering-posts) for more.
:::

