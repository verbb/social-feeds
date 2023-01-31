# Console Commands

## Posts

### Refresh Posts
For a provided Source, refresh the Posts by fetching new ones from the provider API. This circumvents any cache duration timeout.

Option | Description
--- | ---
`--source` | The handle of the [Source](docs:developers/source) to refresh Posts for.
`--limit` | The number of posts to fetch from the provider API. Default to `postsLimit` plugin setting.

```shell
./craft social-feed/posts/refresh --source=mySourceHandle
```
