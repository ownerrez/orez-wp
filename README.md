# OwnerRez API WordPress Plugin

The official WordPress plugin for the OwnerRez API.

Please submit questions or problems to help@ownerreservations.com

## Usage

After installation, go to Admin -> Settings -> OwnerRez and enter a username and personal access token to register your wordpress site.
 
After registration, you can use this plugin to insert shortcodes for your property details and special widgets, or interact directly with our API using WP admin ajax.

### Shortcodes

All ownerrez shortcodes start with `ownerrez` and require a `type` parameter. Some `type` values may additionally require other parameters such as `id` or `field` to further control what is rendered to your page.

The following is an example shortcode: 

```
[ownerrez type="property" id="orp12345" field="name"]
```

#### Fields

This is a current list of supported values for `type` for rendering specific details from your OwnerRez properties:

- `property` : Render property details for a single property.\
  Parameters:
  - `id` (required) : The property ID
  - `field` (required, unless `json` is set to `true`) : The details you would like to render
    - Common values include: `name`, `externalName`, `maxGuests`, `maxPets`, `minNights`, `maxNights`, `checkIn`, `checkOut`
    - For a complete list of available fields, go to: coming soon
  - `json` (optional) : If `true` is used, `field` is not required, and the entire set of available property details will be rendered as a JSON object.
- `listing` : Render listing details for a single property.\
  Parameters:
  - `id` (required) : The property ID
  - `field` (required, unless `json` is set to `true`) : The details you would like to render
    - Common values include: `description`, `shortDescription`, `headline`, `latitude`, `longitude`, `bedroomCount`, `bathroomCount`, `sleepsMax`, `occupancyMax`, `nightlyRateMin`, `nightlyRateMax`
    - For a complete list of available fields, go to: coming soon
  - `json` (optional) : If `true` is used, `field` is not required, and the entire set of available property listing details will be rendered as a JSON object.
  
#### Widgets

This is a current list of supported values for `type` for rendering predefined html layouts for your OwnerRez properties:

- `widget_photo_carousel` : Render a photo carousel for a single property.\
  Parameters:
  - `id` (required) : The property ID
- `widget_amenities_list` : Render a bullet list of call-out amenities for a single property.\
  Parameters:
  - `id` (required) : The property ID

### AJAX

API end points are accessible via admin ajax requests:

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
