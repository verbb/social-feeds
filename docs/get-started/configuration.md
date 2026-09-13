# Configuration

You can customise Social Feeds’s settings using a PHP configuration file. This is optional: each setting has a default, so you only need to include the values you want to change.

To override a setting, create `social-feeds.php` in your Craft project’s `/config` directory and return an array of setting names and values. For example, the following will change the name displayed in the control panel:

```php
<?php

return [
    'pluginName' => 'Social Feeds Tools',
];
```

All other settings keep their defaults. Add any further settings you want to change to the same array. The options below explain the available settings and their defaults.

## Configuration Options

::: reference
### `pluginName`

**Type:** `string` · **Default:** `'Social Feeds'`

If you wish to customise the plugin name.
:::


::: reference
### `hasCpSection`

**Type:** `bool` · **Default:** `true`

Whether to have the plugin pages appear on the main CP sidebar menu.
:::


::: reference
### `enableCache`

**Type:** `bool` · **Default:** `true`

Whether to enable the cache for posts.
:::


::: reference
### `cacheDuration`

**Type:** `mixed` · **Default:** `'PT6H'`

When the cache is enabled, how long until the plugin checks for new posts. Accepts a [Date Interval](https://www.php.net/manual/en/dateinterval.construct.php) or a number of seconds.
:::


::: reference
### `postsLimit`

**Type:** `int` · **Default:** `50`

The number of posts to fetch from providers. Note that some providers enforce their own limits that cannot be changed.
:::


::: reference
### `redirectUri`

**Type:** `string|null` · **Default:** `null`

Optionally override the OAuth redirect URI for detached or multi-domain setups. This applies to all sources.
:::


### Redirect URI Override
By default, Social Feeds will continue to use its legacy callback URI. If you need to use a different callback URI, such as for detached domains or an `/actions/...` callback, set `redirectUri` at the plugin level.

```php
'redirectUri' => 'https://craft.example.com/actions/social-feeds/auth/callback',
```

### Sources
Supply your client configurations as per the below. The `key` for each item should be the source `handle`.

```php
return [
    '*' => [
        // ...
        'sources' => [
            'facebook' => [
                'enabled' => true,
                'clientId' => '••••••••••••••••••••••••••••',
                'clientSecret' => '••••••••••••••••••••••••••••',

                // Add in any additional OAuth scopes
                'scopes' => [
                    'business_management',
                ],

                // Add in any additional OAuth authorization options, used when redirecting
                // to the provider to start the OAuth authorization process
                'authorizationOptions' => [
                    'extra' => 'value',
                ],
            ],
            'twitter' => [
                'clientId' => '••••••••••••••••••••••••••••',
                'clientSecret' => '••••••••••••••••••••••••••••',
            ],
        ],
    ],
];
```

## Control Panel
You can also manage configuration settings through the Control Panel by visiting Settings → Social Feeds.
