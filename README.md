# OwnerRez API WordPress Plugin

The official WordPress plugin for the OwnerRez API.

Please submit questions or problems to help@ownerreservations.com

## Usage

After installation, go to Admin -> Settings -> OwnerRez and enter a username and personal access token to register your wordpress site.
 
After registration, you can use this plugin to insert shortcodes for your property details and special widgets, or interact directly with our API using WP admin ajax.

For full documentation, visit the OwnerRez support articles:

- [Overview](https://www.ownerreservations.com/support/articles/wordpress-plugin-overview)
- [Setup & Connecting](https://www.ownerreservations.com/support/articles/wordpress-plugin-setup-connecting)
- [Shortcodes](https://www.ownerreservations.com/support/articles/wordpress-plugin-shortcodes)
- [Common Issues & Questions](https://www.ownerreservations.com/support/articles/wp-plugin-faq)


## Custom AJAX

OwnerRez API end points are accessible via admin ajax requests. This feature is currently under development. If you would like to
use this feature, please [let us know](mailto:help@ownerreservations.com) about your use case. 

```javascript
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
