# Console Commands

Social Feeds includes console commands you can run from your Craft project’s directory.

## Posts

::: reference
### `social-feeds/posts/refresh`

Fetches new posts for one source from its provider and saves them, regardless of the [cache duration](docs:get-started/configuration#cacheduration). Run it when you want a source’s posts updated straight away, or from a cron job for a source that needs fetching on its own schedule.

| Option | Description | Default |
| --- | --- | --- |
| `--source` | The handle of the [Source](docs:reference/source) to refresh. Required. | |
| `--limit` | The number of posts to fetch from the provider. Some providers enforce their own limits. | The `postsLimit` setting |

```shell
php craft social-feeds/posts/refresh --source=mySourceHandle
php craft social-feeds/posts/refresh --source=mySourceHandle --limit=20
```
:::

::: reference
### `social-feeds/posts/refresh-enabled-sources`

Fetches new posts for every enabled source and saves them, regardless of the cache duration. Each source fetches up to the `postsLimit` setting. A cron job running this command keeps every feed up to date without waiting for a visitor to trigger a fetch.

```shell
php craft social-feeds/posts/refresh-enabled-sources
```
:::
