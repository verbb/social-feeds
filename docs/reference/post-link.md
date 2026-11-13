# Post Link

A post link is a link attached to a [post](docs:reference/post), such as a shared article. A post’s `links` property holds an array of them.

```twig
{% for link in post.links %}
    <a href="{{ link.url }}">{{ link.title ?? link.url }}</a>
{% endfor %}
```

## Properties

::: reference
### `id`

**Type:** `string|null`

The link’s ID on the provider, when it has one.
:::

::: reference
### `title`

**Type:** `string|null`

The link’s title, when the provider supplies one.
:::

::: reference
### `url`

**Type:** `string|null`

The URL the link points to.
:::
