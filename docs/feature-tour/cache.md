# Cache
As we rely on fetching [Posts](docs:feature-tour/posts) from social media provider API's, we certainly wouldn't want that hitting their API endpoints every time a page is loaded. That would produce slow rendering, and very quickly reach API usage quotas!

Instead, Social Feeds features a caching mechanism, where once fetched, Posts are stored in the databse. The next time `getPosts()` is called (for a Feed or Source), we fetch the Posts from our local database, instead of via the providers API.

:::tip
Posts are cached per Source, not per Feed. So if you have multiple Feeds using the same Sources, you'll be able to utilise the cache performance site-wide.
:::

## How it Works
It all begins when you call `getPosts()` or `renderPosts()` for a Feed or Source. This will fetch the latest Posts from the respective social media API's, and create [Post](docs:developers/post) objects to represent a post.

The Posts are saved to the `socialfeeds_posts` database table. We also then store the timestamp (`dateLastFetch`) against the Source in the `socialfeeds_sources` database table.

Then, upon the next request to `getPosts()` or `renderPosts()`, we check to see if the current time is greater than the `dateLastFetch` time plus the `cacheDuration` plugin setting. If false, only the Posts in the `socialfeeds_posts` are returned. If true, will trigger another API call to fetch new posts. Existing Posts in the database will still be kept, but also updated if they are included in the API call result.

Through this, Social Feeds will be able to serve up your Posts quickly, and save on API calls.

## Fetching New Posts
Cached content sounds great, but at some point you'll want to show _new_ posts from social media of course! There are a couple of different mechanisms to fetch fresh content.

### Automatically On-Render (Default)
By default, Social Feeds will automatically check for new posts, each time `getPosts()` or `renderPosts()` is called, provided that the current time is greater than the `dateLastFetch` plus 6 hours (the plugin default for `cacheDuration`). If 6 hours has passed from the last fetch of Posts, we'll fetch new content.

You can adjust the `cacheDuration` setting as appropriate if you'd like content checked more frequently or less. Do note that it's inadvisable to check for new Posts too often, as some providers have strict limits on the number of API calls you can make within a given timeframe. As a rule of thumb, setting this no lower than 15 minutes is advisable.

While this approach is a hands-off, convenient method - calling the API when rendering can result in slow page loads. This is true if your Feed contains multiple Sources. To address this we'd recommend using the Console Command.

### Console Command
Another method is to offload fetching new posts to a [console command](docs:developers/console-commands). This allows the process of fetching new posts to be completely in the background, not affecting your users when browsing the site. It also gives you the ability to refresh content on-demand, rather than relying on a timestamp expiring.

To accomplish this, set `cacheDuration = false` in your plugin settings. This will ensure that when calling `getPosts()` or `renderPosts()` we **never** check for new posts. It will now only ever return results from the database.

Then, to setup our cache-fetching process, add a Cron job, or a Daemon at the desired interval to fetch new posts. You can call the following console command whenever you'd like to fetch new Posts.

```shell
./craft social-feeds/posts/refresh --source=mySourceHandle
```

## Disabling the Cache
It's **highly** inadvisable to disable the cache, but you may like to do this when debugging. The `enableCache` plugin setting will control this, but be aware that API requests will be triggered every time the page loads, bypassing the database cache.

## Clearing the Cache
Clearing the cache involves removing the `dateLastFetch` value for all Sources. You can use the **Utilities** → **Social Feeds** utility to clear the cache. Note that the Posts in your database will still exist, but new Posts will be fetched on next render.

## Deleting the Cache
Deleting the cache should only be done when you understand the consequences of doing this, as it will likely lead to data lost. This will **permanantly** delete all the Posts in your `socialfeeds_posts` database table. To get Posts to show for your Feed/Source, they'll need to be fetched from the provider again.

The reason this may result in data loss is that some providers have limits on how many posts are returned via their APIs. Twitter for example only returns tweets via their hashtags for the last 7 days. If your Feed has been running for a few months, accumulating that content, once deleted those posts will be unable to be fetched from the API again.

You can use the **Utilities** → **Social Feeds** utility to delete the cache.
