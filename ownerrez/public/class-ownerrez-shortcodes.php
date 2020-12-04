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
            'json' => false
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

    function only_strings($result)
    {
        if (is_string($result))
            return $result;
        elseif (is_array($result))
            return join(", ", $result);
        else
            return "[Unknown]";
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
                $field = lcfirst($attrs["field"]);

                if (property_exists($resource, $field))
                {
                    $output = $resource->$field;

                    if (is_array($output))
                    {
                        foreach($output as &$val)
                            if (is_string($val))
                                $val = $this->camelToTitle($val);

                        return join(", ", $output);
                    }
                    else if (is_string($output))
                        return $this->camelToTitle($output);
                    else
                        return $output;
                }
            }

            return $resource;
        }
        catch (\GuzzleHttp\Exception\ServerException $ex) {
            return json_encode([ 'status' => 'error', 'exception' => $ex->__toString(), 'messages' => json_decode($ex->getResponse()->getBody())->messages ]);
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
        return $this->only_strings($this->get_resource($attrs, "properties", $this->get_id($attrs, "orp")));
    }

    function type_listing($attrs, $content, $additionalArgs)
    {
        return $this->only_strings($this->get_resource($attrs, "listings", $this->get_id($attrs, "orp"), "summary", ["includeDescriptions"=>"true", "includeAmenities"=>"true"]));
    }

    function type_widget_amenities_list($attrs, $content, $additionalArgs)
    {
        $listing = $this->get_resource($attrs, "listings", $this->get_id($attrs, "orp"), "summary");

        if (property_exists($listing, "callOutAmenities")) {
            $list = "<ul class='ownerrez-amenities-list'>";

            foreach ($listing->callOutAmenities as &$amenity) {
                $title = "";
                if (property_exists($amenity, "title") && $amenity->title != $amenity->text)
                    $title = $amenity->title;

                $list .= "<li title='" . $title . "'><i class='" . $amenity->icon . "'></i>" . $amenity->text . "</li>";
            }

            $list .= "</ul>";

            return $list;
        }

        return "[Unsupported api version]";
    }

    private $photoCarouselEnqueued = false;

    function type_widget_photo_carousel($attrs, $content, $additionalArgs)
    {
        if (!$this->photoCarouselEnqueued) {
            wp_enqueue_style("ownerrez-lgstyle1", "https://cdn.orez.io/hc/content/lightslider.min.css", null, $this->version);
            wp_enqueue_style("ownerrez-lgstyle2", "https://cdn.orez.io/hc/content/lightgallery.min.css", null, $this->version);
            wp_enqueue_script("ownerrez-lgscript", "https://cdn.orez.io/hc/scripts/lgbundle.min.js", array( 'jquery' ), $this->version);
            wp_enqueue_script("ownerrez-photo-carousel", OWNERREZ_ROOT . '/public/js/ownerrez-photo-carousel.js', array( 'jquery' ), $this->version);

            $this->photoCarouselEnqueued = true;
        }

        $images = $this->get_resource($attrs, "properties", $this->get_id($attrs, "orp"), "images");

        $sliderUl = "<ul class='ownerrez-photo-carousel loading-slider' data-id='" . $this->get_id($attrs, "orp") . "'>";

        foreach ($images as &$image) {
            $caption = "";
            if (property_exists($image, "caption"))
                $caption = $image->caption;

            $sliderUl .= "<li data-thumb='" . $image->croppedUrl . "' data-src='" . $image->originalUrl . "'><img src='" . $image->largeUrl . "' data-sub-html='" . $caption . "' />";

            if (strlen($caption) > 0)
                $sliderUl .= "<div class='caption'>" . $caption . "</div>";

            $sliderUl .= "</li>";
        }

        $sliderUl .= "</ul>";

        return $sliderUl;
    }
}
