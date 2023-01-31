# Post
Whenever you're dealing with a post in your template, you're actually working with a `Post` object. As each provider returns different content, some attributes will return different content. However, the purpose of a `Post` object is to normalize the content from a provider.

You can access the raw content fetched from a provider's API using the `data` attribute.

## Attributes

Attribute | Description
--- | ---
`id` | The ID of the post.
`uid` | The unique or secondary identifer.
`text` | The main text for the post.
`url` | The URL to the post on the provider site.
`sourceId` | The ID for the [Source](docs:developers/source).
`sourceHandle` | The handle for the [Source](docs:developers/source).
`sourceType` | The type of [Source](docs:developers/source).
`postType` | The type of post this is classified as.
`likes` | The number of likes this post has received.
`shares` | The number of shares this post has received.
`replies` | The number of replies this post has received.
`author` | The [Post Author](#post-author).
`tags` | A collection of tags or hashtags for the post.
`links` | A collection of [Post Link](#post-link) objects.
`images` | A collection of [Post Media](#post-media) objects.
`videos` | A collection of [Post Media](#post-media) objects.
`data` | A full response from the social media provider.
`meta` | Any additional content for the post type for easy-access.

## Methods

Method | Description
--- | ---
`getSource()` | Returns the source this post was made from.
`getContent()` | Returns text or message as the main content of the post.
`hasMedia()` | Whether or not a post contains an image or video.
`getAuthorImage()` | Returns the author's profile image (if one exists).
`getImage()` | Returns the first image (typically the primary image) for the post.
`getVideo()` | Returns the first video (typically the primary video) for the post.
`getFriendlyDate(date)` | Returns a "friendly" representation of a date (e.g. `5.2M`).
`getProviderColor` | The primary brand color of the provider connected.
`getProviderIcon` | The SVG icon of the source provider connected.


## Post Author

### Attributes

Attribute | Description
--- | ---
`id` | The ID of the author.
`username` | The username of the author.
`name` | The name of the author.
`url` | The url of the author.
`photo` | The photo of the author.


## Post Media
A media item represents either a video or an image.

### Attributes

Attribute | Description
--- | ---
`id` | The ID of the media item.
`title` | The title of the media item.
`type` | The type of media item (`video` or `image`).
`url` | The url of the media item.
`width` | The width of the media item.
`height` | The height of the media item.


## Post Link

### Attributes

Attribute | Description
--- | ---
`id` | The ID of the link.
`title` | The title of the link.
`url` | The ID of the link.

