basic search pages: https://wordpress.org/support/article/creating-a-search-page/#creating-a-search-page

a basic api integration: https://github.com/2Fwebd/feedier-wordpress-plugin/blob/master/feedier.php

another api integration (using transient caching): https://webdesign.tutsplus.com/articles/how-to-incorporate-external-apis-in-your-wordpress-theme-or-plugin--cms-33542

great article about wp api integration patterns: https://www.toptal.com/wordpress/wordpress-api-integration

walk through integrating gh api: https://www.smashingmagazine.com/2016/03/making-a-wordpress-plugin-that-uses-service-apis/


## Ideas: 

1. Build a PHP wrapper for the api. Allows any plugin or theme developer to include our api in their code and make calls to our api using just a username and access token or oauth token. This would probably be what Cuvee would need if their design firm is gonna build them a custom wordpress theme. Concerns might be how to link a property to a wordpress page/post, but they could probably do that with some internal custom fields. This api should have options for transient caching enabled by default on most requests to avoid chatty backend.
2. Use REST API to invalidate transient cache in wordpress instead of depending on expiration. Don't know if this would be essential to cuvee if they are only hitting the api for availability info.
3. Build widgets and/or shortcodes that render properties, search forms, rates, etc in nice semantic html for stylability. I don't think this would be useful for cuvee, but could be very useful to someone with no php knowledge who just wants to render property info or a booking/availability form on their site.
4. Build a wordpress custom post type and store property data in wordpress using REST API to push updates from OR with webhooks. This may not be necessary at all if we find that 1 and 2 together are really sufficient. This would just eager cache the data on WP instead of waiting for a request to get it.
