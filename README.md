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