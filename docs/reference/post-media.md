# Post Media

A post media item is an image or video attached to a [post](docs:reference/post). A post’s `images` and `videos` properties each hold an array of them.

```twig
{% for image in post.images %}
    <img src="{{ image.url }}" alt="{{ image.title }}" width="{{ image.width }}" height="{{ image.height }}">
{% endfor %}
```

## Properties

::: reference
### `id`

**Type:** `string|null`

The media item’s ID on the provider.
:::

::: reference
### `title`

**Type:** `string|null`

The media item’s title or alternative text, when the provider supplies one.
:::

::: reference
### `type`

**Type:** `string|null`

The kind of media item, either `image` or `video`.
:::

::: reference
### `url`

**Type:** `string|null`

The URL of the image or video file.
:::

::: reference
### `width`

**Type:** `int|null`

The width of the media item in pixels, when the provider reports it.
:::

::: reference
### `height`

**Type:** `int|null`

The height of the media item in pixels, when the provider reports it.
:::
