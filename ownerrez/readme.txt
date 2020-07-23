=== Plugin Name ===
Contributors: ownerrez
Tags:
Requires at least: 5.4.2
Tested up to: 5.4.2
Stable tag: trunk
License: MIT
License URI: https://github.com/ownerrez/orez-wp/blob/master/LICENSE

The official WordPress plugin for the OwnerRez API.

== Description ==

# OwnerRez API WordPress Plugin

The official WordPress plugin for the OwnerRez API.

## Usage

After installation, go to Admin -> Settings -> OwnerRez and enter a username and personal access token to register your wordpress site.

After successful registration, API end points documented at TODO are accessible via admin ajax requests:

```$javascript
var ownerrezRequest = {
    resource: 'properties', // required
    verb: 'get', // optional. default is 'get'
    action: 'search', // optional
    id: null, // usually optional. required by some verbs or actions
    query: {  // for 'get' verbs, query will be included as the querystring. For other verbs, query will be attached as the json request body.
        limit: 5,
        offset: 0
    }
};

jQuery.post(ajaxurl,
    {
        action: 'ownerrez',
        call: ownerrezRequest
    },
    function(response) { /* do something with the json response from OwnerRez */ }
);
```

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Extract the ownerrez archive to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Complete the registration information (username and personal access token) under Settings -> OwnerRez

== Frequently Asked Questions ==


== Screenshots ==


== Changelog ==

= 1.0.1 =

== Upgrade Notice ==

= 1.0.1 =