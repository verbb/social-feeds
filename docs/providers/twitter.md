# Twitter
Follow these steps to configure Twitter for Social Feeds.

:::tip
Fetching data from Twitter is only allowed on their paid, **Basic** [API Plan](https://developer.twitter.com/en/docs/twitter-api/getting-started/about-twitter-api).
:::

## Connecting to Twitter

### Step 1: Register a Twitter App
1. Go to <a href="https://developer.twitter.com/en/apps" target="_blank">Twitter Developer Portal</a> and login to your source.
1. Click the **Create project** button.
1. Enter the **Name** for your project and click the **Next** button.
1. Select the **Use case** from the dropdown list.
1. Enter the **Description** for your project and click the **Next** button.
1. Click the **Create a new App** button.
1. Enter the **App Name** and click the **Complete** button to create the application.
1. Go to **App settings**.
1. Click the **Edit** button for **Authentication settings**.
1. Fill in the form details.
    - **App permissions** set to **Read**
    - **Request email from users** is enabled
    - **Type of App** set to **Web App, Automated App or Bot**
    - In the **Callback URI / Redirect URL** field, enter the value from the **Redirect URI** field in Social Feeds.
1. Click the **Save** button.
1. Click the **Keys & Tokens** tab.
1. Click the **Regenerate** button for the **OAuth 2.0 Client ID and Client Secret**.
1. Copy the **Client ID** from {name} and paste in the **Client ID** field in Social Feeds.
1. Copy the **Client Secret** from {name} and paste in the **Client Secret** field in Social Feeds.

## Available Content
Twitter provides the following types of content as posts.

- User Tweets (Tweets from any Twitter user)
- Hashtags (Tweets containing hashtags)
- Search (Tweets matching search terms)
- Mentions (Tweets which mention your Twitter user)
- Lists (Tweets from a Twitter list)

## Seach Terms
To search for tweets, you'll need to provide search terms in a particular format for Twitter's API to understand. This is fully documented on the [Twitter Search Tweets](https://developer.twitter.com/en/docs/twitter-api/tweets/search/integrate/build-a-query) docs.

Some example search terms are:

Query | Description
--- | ---
`watching now` | Containing both “watching” and “now”. This is the default operator.
`#haiku` | Containing the hashtag “haiku”.
`from:interior` | Sent from Twitter account “interior”.
`@NASA` | Mentioning Twitter account “NASA”.
