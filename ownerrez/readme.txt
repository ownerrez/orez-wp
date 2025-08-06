=== OwnerRez ===
Contributors: ownerrez
Requires at least: 5.4
Tested up to: 6.6.0
Stable tag: 1.2.3
License: MIT
License URI: https://github.com/ownerrez/orez-wp/blob/master/LICENSE
Tags: vacation rental, property management, airbnb, vrbo, booking, listing, vr, rental, accomodation

The official WordPress plugin for the OwnerRez API.

== Description ==

# OwnerRez API WordPress Plugin

The official WordPress plugin for the OwnerRez API. View [the readme](https://github.com/ownerrez/orez-wp) for more information on using this plugin.

This plugin provides interconnectivity between your OwnerRez account and your WordPress website. This plugin will communicate with the [OwnerRez API](https://www.ownerrez.com).

The OwnerRez terms of service and privacy policy govern our usage of data collected through this plugin.

[Terms of Service](https://www.ownerrez.com/support/articles/privacy-security-terms-of-service)
[Privacy Policy](https://www.ownerrez.com/support/articles/privacy-security-privacy-policy)

Please submit questions or problems to [help@ownerrez.com](mailto:help@ownerrez.com)

== Installation ==

1. Extract the ownerrez archive to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Complete the registration information (username and personal access token) under Settings -> OwnerRez

== Changelog ==
= 1.2.3 =
- Do not attempt to camel-to-title case convert the description fields

= 1.2.2 =
- Escape parameters in error messages

= 1.2.1a =
- Correct readme.txt validation errors

= 1.2.1 =
- Improve security in administrative pages

= 1.2.0 =
- Update URLs to OwnerRez short domain
- Update ownerrez/orez-api dependency to 1.1.*, removed Guzzle dependency
- Added "Clear Cache" button to settings page

= 1.1.18 =
- Add support for commas in shortcode "field" parameter to access object type properties on the underlying API response

= 1.1.17 =
- Render numeric shortcode values without a format using default format instead of "[Unknown]"

= 1.1.16 = 
- Provide clearer error messages during registration.

= 1.1.15 =
- Handle external cache implementations.

= 1.1.14 =
- Initialize carousel even when WP is configured to add scripts html head.

= 1.1.13 =
- Webhooks now always return the correct http code.

= 1.1.12 =
- Html encode image captions on carousel render.

= 1.1.11 =
- Further improvements to photo carousel.

= 1.1.10 =
- Improved caption handling for photo carousel.

= 1.1.9 =
- Added video support to photo carousel.

= 1.1.6 =
- Add support for WordPress 5.7
- Add support for PHP 8

= 1.1.5 =
- Upgraded to orez-api version 1.0.4: Now validates integer quote properties and errors for invalid.

= 1.1.0 =
- Added two new widget shortcodes.
- **Breaking** Modified css classes on existing widget shortcodes.

= 1.0.5 =
Fixed a bug that occurred when Wordpress was hosted in a subfolder named /ownerrez

== Upgrade Notice ==

After upgrading to 1.1.0, please review any custom css you have added for OwnerRez widgets.
