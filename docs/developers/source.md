# Source
Whenever you're dealing with an source in your template, you're actually working with a `Source` object.

## Attributes

Attribute | Description
--- | ---
`name` | The name of the source.
`handle` | The handle of the source.
`enabled` | Whether the source is enabled or not.
`primaryColor` | The primary brand color of the provider connected.
`icon` | The SVG icon of the source provider connected.
`providerName` | The name of the source provider connected.


## Methods

Method | Description
--- | ---
`isConfigured()` | Whether the source provider has been configured.
`isConnected()` | Whether the source provider has been connected and has a token.
`getToken()` | The access token for a source provider.
`getPosts(options)` | Returns a collection of [Post](docs:developers/post) objects.
