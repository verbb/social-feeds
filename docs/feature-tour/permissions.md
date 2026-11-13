# Permissions

Social Feeds' permissions separate the people who decide what a feed shows from the people who connect your social media accounts. A content editor might add a new hashtag source to the homepage feed, while connecting a Facebook page or Instagram account stays with whoever manages those accounts. Set them in **Settings → Users → User Groups**, or on an individual user's **Permissions** tab.

**Access Social Feeds** is listed under **General**, with Craft's other permissions for reaching each plugin's section. The other two are listed under the **Social Feeds** heading.

| Permission | What It Allows |
| --- | --- |
| Access Social Feeds | Reach the Social Feeds section of the control panel. |
| Feeds | Open **Feeds** to create, edit, reorder and delete [feeds](docs:feature-tour/feeds), choose which sources each one includes, and preview their posts. |
| Sources | Open **Sources** to create, edit, reorder and delete [sources](docs:feature-tour/sources), enter each provider's credentials, and connect or disconnect the social media account through OAuth. |

**Feeds** and **Sources** work inside the plugin's section, so anyone given either needs **Access Social Feeds** too. Each one adds its own item to the section's navigation, and the two are independent, so someone can manage feeds without seeing any provider credentials. Settings are for admins only, and admins have every permission.

If you turn off `hasCpSection`, the plugin's section is removed from the control panel's navigation and **Access Social Feeds** is no longer listed. See [Configuration](docs:get-started/configuration) for that setting.

**Sources** gives access to your providers' credentials, and lets someone connect a source to a different account or disconnect it. Give it to the people who look after your social media accounts, and **Feeds** to the editors who arrange what appears on the site.
