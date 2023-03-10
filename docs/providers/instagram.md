# Instagram
Follow these steps to configure Instagram for Social Feeds.

:::tip
Your Facebook/Instagram App **does not** require review and approval by Facebook/Instagram to use Social Feeds.
:::

:::warning
In order to fetch from Instagram, you must ensure the following:

- Your Instagram account is set to "Business" and not "Creator".
- Your Instagram account is linked to a Facebook page.
:::

## Connecting to Instagram

### Step 1: Register a Facebook App
1. Go to the <a href="https://developers.facebook.com/apps/" target="_blank">Meta for Developers</a> page.
1. Click the **Create App** button.
1. Select **None** as the **App Type**, and fill in the rest of the details to create the app.
1. Once created, in the left-hand sidebar, click the **Add Product** button.
1. Under **Instagram Basic Display** click the **Set Up** button.
1. Click the **Create New App** button.
1. Enter the name of your new Facebook app, and click the **Click Create App** button.
1. For the **Valid OAuth Redirect URIs** setting, enter the value from the **Redirect URI** field in Social Feeds.
1. For the **Deauthorize Callback URL** and **Data Deletion Request Callback URL** settings, enter your website URL.
1. Navigate to **App Roles** → **Roles** in the left-hand sidebar.
1. Under the **Instagram Testers** section, click the **Add Instagram Testers** button.
1. Provide your Instagram source’s username(s).
1. Click the **Submit** button to send the invitation.
    - Go to <a href="https://instagram.com/" target="_blank">Instagram</a> and login to the source you just invited.
    - Navigate to **(Profile Icon)** → **Edit Profile** → **Apps and Websites**.
    - Under the **Tester Invites** tab, accept the invitation.
1. Navigate to **Settings* → **Basic**.
1. Copy the **App ID** from Facebook and paste in the **Client ID** field in Social Feeds.
1. Copy the **App Secret** from Facebook and paste in the **Client Secret** field in Social Feeds.

### Step 2: Connect to Instagram
1. In the Social Feeds feed settings, click the **Connect** button and login to Instagram/Facebook.

### Step 3: Select your Instagram Business Account
1. Select the **Instagram Business Account** that is linked to your Facebook page, to pull content from.
1. Click the **Save** button for the account.

## Available Content
Instagram provides the following types of content as posts.

- Profile Posts (Posts from your Instagram profile)
- Hashtags (Posts containing hashtags)
- Tagged Posts (Posts you have been tagged in)
