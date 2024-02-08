# Facebook
Follow these steps to configure Facebook for Social Feeds.

:::tip
Your Facebook App **does not** require review and approval by Facebook to use Social Feeds.
:::

## Connecting to Facebook

### Step 1. Admin Access to Facebook Page or Facebook Group
In order to fetch posts from a Facebook Page or Facebook Group, you must be an Admin for the page/group you want to access.

### Step 2: Register a Facebook App
1. Go to the <a href="https://developers.facebook.com/apps/" target="_blank">Meta for Developers</a> page.
1. Click the **Create App** button.
1. Select **Other** and **Consumer** as the app type, and fill in the rest of the details.

### Step 3: Setup Facebook Login
1. Once created, in the left-hand sidebar, click the **Add Product** button.
1. Under **Facebook Login** click the **Set Up** button.
1. Select **Web** as the type and your website address into **Site URL**, and click the **Save** button.
1. Navigate to the **Facebook Login** section in the left-hand sidebar, click **Settings**.
1. For the **Valid OAuth Redirect URIs** setting, enter the value from the **Redirect URI** field in Social Feeds.
1. Click the **Save Changes** button.
1. Navigate to **App Settings** → **Basic** item in the left-hand sidebar.
1. Enter your domain name to the **App Domains** field.
1. Enter your **Privacy Policy URL**, **Terms of Service URL** and **Site URL**.
1. Click the **Save Changes** button.
1. Change the **App Mode** to **Live**.
1. Copy the **App ID** from Facebook and paste in the **Client ID** field in Social Feeds.
1. Copy the **App Secret** from Facebook and paste in the **Client Secret** field in Social Feeds.
1. Save the Social Feeds source, ready to connect.

### Step 4: Connect to Facebook
1. In the Social Feeds source settings, click the **Connect** button and login to Facebook.
1. Ensure you pick either the Facebook Group or Facebook Page you have admin access to.

### Step 5: Select your Facebook Page or Facebook Group
1. Select either a **Facebook Page** or a **Facebook Group** that you'd like connected to.
1. Click the **Save** button for the source.

:::tip
Ensure that you pick **Facebook Login** and not **Facebook Login for Business**, which are different products. If you must use **Facebook Login for Business**, you'll need to provide additional scopes, as per the below. 
:::

### Facebook Login for Business
If you require the use of the **Facebook Login for Business** product in your Facebook App, you may do so, but note that you'll need to supply additional scopes in your [configuration](docs:get-started/configuration).

```php
<?php

return [
    '*' => [
        // ...
        'sources' => [
            'facebook' => [
                // ...
                'scopes' => [
                    'business_management',
                ],
            ],
        ],
    ]
];
```

## Available Content
Facebook provides the following types of content as posts.

- Page Feed (Posts from your Facebook page)
- Photos (Photos from your Facebook Photos page)
- Videos (Videos from your Facebook Videos page)
- Events (Events from your Facebook Events page)
- Group Feed (Posts from your Facebook group)
- Photos (Photos from your Facebook Photos group)
- Videos (Videos from your Facebook Videos group)
- Events (Events from your Facebook Events group)

