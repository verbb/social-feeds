# Source
Whenever you're dealing with an source in your template, you're actually working with a `Source` object.

<span id="attributes"></span>

## Properties

::: reference
### `name`

**Type:** `string|null`

The name of the source.
:::

::: reference
### `handle`

**Type:** `string|null`

The handle of the source.
:::

::: reference
### `enabled`

**Type:** `bool|null`

Whether the source is enabled or not.
:::

::: reference
### `primaryColor`

**Type:** `string|null`

The primary brand color of the provider connected.
:::

::: reference
### `icon`

**Type:** `string|null`

The SVG icon of the source provider connected.
:::

::: reference
### `providerName`

**Type:** `string`

The name of the source provider connected.
:::



## Methods

::: reference
### `isConfigured()`

Whether the source provider has been configured.
:::

::: reference
### `isConnected()`

**Returns:** `bool`

Whether the source provider has been connected and has a token.
:::

::: reference
### `getToken()`

The access token for a source provider.
:::

::: reference
### `getPosts(options)`

**Returns:** `array|null`

Returns a collection of [Post](docs:developers/post) objects.
:::
