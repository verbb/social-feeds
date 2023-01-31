# Configuration
Create a `social-feed.php` file under your `/config` directory with the following options available to you. You can also use multi-environment options to change these per environment.

The below shows the defaults already used by Social Feed, so you don't need to add these options unless you want to modify the values.

```php
<?php

return [
    '*' => [
        'pluginName' => 'Social Feed',
        'hasCpSection' => true,
        'enableCache' => true,
        'cacheDuration' => 'PT6H',
        'postsLimit' => 50,
        'sources' => [],
    ]
];
```

## Configuration options
- `pluginName` - If you wish to customise the plugin name.
- `hasCpSection` - Whether to have the plugin pages appear on the main CP sidebar menu.
- `enableCache` - Whether to enable the cache for posts.
- `cacheDuration` - When the cache is enabled, how long until the plugin checks for new posts. Accepts a [Date Interval](https://www.php.net/manual/en/dateinterval.construct.php) or a number of seconds.
- `postsLimit` - The number of posts to fetch from providers. Note that some providers enforce their own limits that cannot be changed.

### Sources
Supply your client configurations as per the below. The `key` for each item should be the source `handle`.

```php
return [
    'sources' => [
        'facebook' => [
            'enabled' => true,
            'clientId' => 'xxxxxxxxxxxx',
            'clientSecret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        ],
        'twitter' => [
            'clientId' => 'xxxxxxxxxxxx',
            'clientSecret' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx',
        ],
    ],
];
```

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Social Feed.
