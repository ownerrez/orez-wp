# OwnerRez API WordPress Plugin

The official WordPress plugin for the OwnerRez API.

THIS IS A BETA LIBRARY. SUBMIT PROBLEMS AND REQUESTS AS ISSUES.

## Usage

After installation, go to Admin -> Settings -> OwnerRez and enter a username and personal access token to register your wordpress site.
 
After successful registration, API end points are accessible via admin ajax requests:

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

### Shortcodes

All ownerrez shortcodes start with `ownerrez` and require a `type` parameter. The following is a current list of supported types:

- `property` : renders property details. Required parameters: `id`, `field` (string) or `json` (boolean)
- `listing` : renders listing details. Required parameters: `id`, `field` (string) or `json` (boolean)
- `widget_photo_carousel` : renders a photo carousel for a single property. Required parameters: `id`
- `widget_amenities_list` : renders a bullet list of call-out amenities for a single property. Required parameters: `id`

Example: `[ownerrez type="property" id="orp12345" field="name"]`
