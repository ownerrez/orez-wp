<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://ownerrez.com
 * @since      1.0.0
 *
 * @package    OwnerRez
 * @subpackage OwnerRez/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    OwnerRez
 * @subpackage OwnerRez/public
 * @author     Your Name <email@example.com>
 */
class OwnerRez_ShortCodes {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $ownerrez    The ID of this plugin.
     */
    private $ownerrez;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    private $api;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $ownerrez       The name of the plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct( $ownerrez, $version ) {

        $this->ownerrez = $ownerrez;
        $this->version = $version;
        $this->api = new OwnerRez_ApiWrapper();
    }

    /**
     * Register the shortcodes for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function register_shortcodes()
    {
        add_shortcode('ownerrez', array($this, 'shortcode'));
    }

    public function shortcode($attrs, $content = "")
    {
        $allAttrs = $attrs;
        $attrs = shortcode_atts( array(
            'type' => null,
            'id' => null,
            'field' => null,
            'json' => false,
            'raw' => false,
        ), $attrs, 'ownerrez' );

        if ($attrs["type"] != null)
        {
            try {
                $f = 'type_' . $attrs["type"];

                if (method_exists($this, $f))
                    return $this->$f($attrs, $content, $allAttrs);

                return '[The "type" attribute did not match any known shortcode type. Found: '.$attrs["type"].']';
            }
            catch (Exception $ex) {
                return json_encode($ex->getMessage());
            }
        }

        return 'OwnerRez Plugin version ' . $this->version;
    }

    function camelToTitle($camelStr)
    {
        $intermediate = preg_replace('/(?!^)([[:upper:]][[:lower:]]+)/', ' $0', $camelStr);
        $titleStr = preg_replace('/(?!^)([[:lower:]])([[:upper:]])/', '$1 $2', $intermediate);

        return $titleStr;
    }

    function only_strings($result, $additionalArgs)
    {
        if (is_string($result))
            return $result;
        elseif (is_array($result))
            return join(", ", $result);
        elseif (array_key_exists("format", $additionalArgs))
            return sprintf($additionalArgs["format"], $result);
        elseif (is_numeric($result))
            return $result;
        else
            return "[Unknown]";
    }

    function filter_array ($array, $callback, $args){
        if(is_array($array) && count($array)>0)
        {
            foreach(array_keys($array) as $key){
                $temp[$key] = $array[$key];

                if ($callback($temp[$key], $args)){
                    $newarray[count($newarray ?? [])] = $array[$key];
                }
            }
        }
        return $newarray ?? null;
    }

    function get_resource($attrs, $resourceName, $id = null, $action = null, $query = null, $verb = null, $body = null)
    {
        try {
            if ($verb == null)
                $verb = "get";

            $resource = $this->api->send_request($resourceName, $verb, $action, $id, $query, $body);

            if ($resource == null) {
                return "[No record found in ".$resourceName." with the id '".$attrs["id"]."']";
            }

            if ($attrs["json"]) {
                return json_encode($resource);
            }
            elseif ($attrs["field"] != null) {
                $fieldParts = explode(',', $attrs["field"]);
                $raw = (bool)$attrs["raw"];

                $output = $resource;

                foreach ($fieldParts as $field)
                {
                    $field = lcfirst($field);

                    if (property_exists($output, $field))
                    {
                        $output = $output->$field;
                        
                        if ($raw)
                            continue;

                        if (is_array($output))
                        {
                            foreach($output as &$val)
                                if (is_string($val))
                                    $val = $this->camelToTitle($val);

                            $output = join(", ", $output);
                        }
                        else if (is_string($output))
                            $output = $this->camelToTitle($output);
                    }
                    else
                        return "[Unknown field: " . $field . ". No such field found for type: " . $attrs["type"] . "]";
                }
                
                return $output;
            }

            return $resource;
        }
        catch (\OwnerRez\Api\Exception $ex) {
            return json_encode([ 'status' => 'error', 'exception' => $ex->__toString(), 'messages' => $ex->response->getJson()->messages ]);
        }
    }

    function get_id($attrs, $trimLetters = "or")
    {
        $trimChars = " \t\n\r\0\x0B";
        if ($trimLetters != null)
            $trimChars .= strtoupper($trimLetters).strtolower($trimLetters);

        $id = intval(trim($attrs["id"], $trimChars));

        if ($id == null)
            return '[The "id" attribute is required for this shortcode. Expected: e.g. '.$trimLetters.'123456 or 123456. Found: '.$attrs["id"].']';

        return $id;
    }

    /* ShortCode type functions below */

    function type_property($attrs, $content, $additionalArgs)
    {
        return $this->only_strings($this->get_resource($attrs, "properties", $this->get_id($attrs, "orp")), $additionalArgs);
    }

    function type_listing($attrs, $content, $additionalArgs)
    {
        return $this->only_strings($this->get_resource($attrs, "listings", $this->get_id($attrs, "orp"), "summary", ["includeDescriptions"=>"true", "includeAmenities"=>"true"]), $additionalArgs);
    }

    function type_widget_amenities_list($attrs, $content, $additionalArgs)
    {
        $listing = $this->get_resource($attrs, "listings", $this->get_id($attrs, "orp"), "summary");

        if (property_exists($listing, "callOutAmenities")) {
            $list = "<ul class='ownerrez-callout-amenities-list'>";

            foreach ($listing->callOutAmenities as &$amenity) {
                $title = "";
                if (property_exists($amenity, "title") && $amenity->title != $amenity->text)
                    $title = $amenity->title;

                $list .= "<li title='" . $title . "'><i class='" . $amenity->icon . "'></i>" . $amenity->text . "</li>";
            }

            $list .= "</ul>";

            return $list;
        }

        return "[The current ownerrez api does not support type: widget_amenities_list]";
    }

    function type_widget_amenities_table($attrs, $content, $additionalArgs)
    {
        $listing = $this->get_resource($attrs, "listings", $this->get_id($attrs, "orp"), "summary", ["includeDescriptions"=>"true", "includeAmenities"=>"true"]);

        if (property_exists($listing, "amenities") && is_object($listing->amenities)) {
            $table = "<table class='ownerrez-amenities-table'><tbody>";

            foreach ($listing->amenities as $category => $amenities) {
                $tr = "<tr><th class='ownerrez-amenities-table-category-name'>" . $this->camelToTitle(ucfirst($category)) . "</th><td class='ownerrez-amenities-table-category-items'>";
                $tr .= $this->get_amenities_list($amenities);
                $tr .= "</td></tr>";
                $table .= $tr;
            }

            $table .= "</tbody></table>";

            return $table;
        }

        return "[The current ownerrez api does not support type: widget_amenities_table]";
    }

    function type_widget_amenities_category($attrs, $content, $additionalArgs)
    {
        $category = null;
        if (array_key_exists("category", $additionalArgs) && is_string($additionalArgs["category"]))
            $category = strtolower($additionalArgs["category"]);
        else
            return '[The "category" attribute is required for this shortcode.]';

        $listing = $this->get_resource($attrs, "listings", $this->get_id($attrs, "orp"), "summary", ["includeDescriptions"=>"true", "includeAmenities"=>"true"]);

        if (property_exists($listing, "amenityCategories") && is_array($listing->amenityCategories))
        {
            $match = $this->filter_array($listing->amenityCategories, function ($x, $args) {
                return !strcasecmp($args, $x->type);
            }, $category);

            if (!empty($match)) {
                return $this->get_amenities_list($match[0]->amenities);
            }
            else {
                return "";
            }
        }

        return "[The current ownerrez api does not support type: widget_amenities_category]";
    }

    function type_widget_listing_details($attrs, $content, $additionalArgs)
    {
        $listing = $this->get_resource($attrs, "listings", $this->get_id($attrs, "orp"), "summary", ["includeDescriptions"=>"true", "includeAmenities"=>"true"]);

        if (property_exists($listing, "amenityCategories") && is_array($listing->amenityCategories))
        {
            $output = "<div class='ownerrez-listing-details'>";

            if (property_exists($listing, "amenitiesAdditionalInfo"))
                $output .= "<div class='ownerrez-listing-details-additional-info'>" . $listing->amenitiesAdditionalInfo . "</div>";

            $output .= "<table class='ownerrez-amenities-table'>";

            foreach ($listing->amenityCategories as $category)
            {
                $output .= "<tr><th class='ownerrez-amenities-table-category-name'>" . $category->caption . "</th><td class='ownerrez-amenities-table-category-items'>";
                $output .= $this->get_amenities_list($category->amenities);
                $output .= "</td>";
            }

            return $output . "</table></div>";
        }

        return "[The current ownerrez api does not support type: widget_listing_details]";
    }

    /**
     * @param $amenities
     * @return string
     */
    public function get_amenities_list($amenities): string
    {
        $ul = "<ul class='ownerrez-amenities-list'>";

        foreach ($amenities as $amenity) {
            $ul .= "<li class='ownerrez-amenities-list-item'>" . $this->camelToTitle(ucfirst($amenity->text));
            if (!empty($amenity->title)) {
                $ul .= '<i class="fas fa-info-circle" title="' . $amenity->title . '"></i>';
            }
            $ul .= "</li>";
        }

        $ul .= "</ul>";

        return $ul;
    }

    private $photoCarouselEnqueued = false;

    function type_widget_photo_carousel($attrs, $content, $additionalArgs)
    {
        if (!$this->photoCarouselEnqueued) {
            wp_enqueue_style("ownerrez-lgstyle", "https://cdn.orez.io/hc/content/lgbundle.min.css", null, $this->version);
            wp_enqueue_script("ownerrez-lgscript", "https://cdn.orez.io/hc/scripts/lgbundle.min.js", array( 'jquery' ), $this->version);
//            wp_enqueue_style("ownerrez-lgstyle", "https://hosteddev.ownerrez.com/content/lgbundle.min.css", null, $this->version);
//            wp_enqueue_script("ownerrez-lgscript", "https://hosteddev.ownerrez.com/scripts/lgbundle.min.js", array( 'jquery' ), $this->version);

            $this->photoCarouselEnqueued = true;
        }

        $images = $this->get_resource($attrs, "properties", $this->get_id($attrs, "orp"), "images");

        $sliderUl = "<ul class='ownerrez-photo-carousel loading-slider' data-id='" . $this->get_id($attrs, "orp") . "'>";

        foreach ($images as &$image) {
            $caption = "";
            if (property_exists($image, "caption") && !is_null($image->caption))
                $caption = htmlentities2($image->caption);

            $srcUrl = $image->originalUrl;
            $posterAttr = "";

            if (property_exists($image, "videoUrl") && !is_null($image->videoUrl)) {
                $srcUrl = $image->videoUrl . (strpos($image->videoUrl, "?") ? "&" : "?") . 'mute=0&muted=false';
                $posterAttr = " data-poster='" . $image->originalUrl . "'";
            }

            $sliderUl .= "<li data-thumb='" . $image->croppedUrl . "' data-src='" . $srcUrl . "'" . $posterAttr . "><img src='" . $image->largeUrl . "' alt='" . $caption . "' data-sub-html='.caption' style='width:100%;height:100%;object-fit:cover;' />";

            if (property_exists($image, "videoUrl") && !is_null($image->videoUrl)) {
                $sliderUl .= '<svg viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" focusable="false" aria-labelledby="Play video" role="img" class="lg-video-play-icon">
                        <title>Play video</title>
                        <circle cx="50%" cy="50%" r="48"></circle>
                        <polygon points="35,30 35,70 70,50"></polygon>
                    </svg>';
            }

            if (strlen($caption) > 0)
                $sliderUl .= "<div class='caption'>" . $caption . "</div>";

            $sliderUl .= "</li>";
        }

        $sliderUl .= '</ul>
		<ul class="loading-pager"><li>Loading...</li></ul>';

        return $sliderUl;
    }
}
