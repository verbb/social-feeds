# Post Author

A post author is the account that published a [post](docs:reference/post). Get it from a post’s `author` property, which is `null` when the provider doesn’t supply author details.

```twig
{% if post.author %}
    <a href="{{ post.author.url }}">{{ post.author.name }}</a>
{% endif %}
```

## Properties

::: reference
### `id`

**Type:** `string|null`

The author’s ID on the provider.
:::

::: reference
### `username`

**Type:** `string|null`

The author’s username on the provider, for providers that have one.
:::

::: reference
### `name`

**Type:** `string|null`

The author’s display name.
:::

::: reference
### `url`

**Type:** `string|null`

The URL of the author’s profile on the provider.
:::

::: reference
### `photo`

**Type:** `string|null`

The URL of the author’s profile photo.
:::
