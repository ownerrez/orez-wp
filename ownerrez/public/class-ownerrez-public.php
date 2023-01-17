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
class OwnerRez_Public {

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

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in OwnerRez_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The OwnerRez_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->ownerrez, plugins_url('/ownerrez/public/css/ownerrez-public.css'), array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in OwnerRez_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The OwnerRez_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( "ownerrez-public.js", plugins_url('/ownerrez/public/js/ownerrez-public.js'), array( 'jquery' ), $this->version, false );

	}

    public function webhook() {
        if(substr($_SERVER["REQUEST_URI"], 0, strlen('/ownerrez/')) === '/ownerrez/') {
            $webhook = trim(preg_split("/ownerrez/", $_SERVER["REQUEST_URI"])[1], " \t\r\n/");

            $result = new stdClass();

            if ($webhook === "clear-transients") {
                $result->authorized = false;
                $result->succeeded = false;

                $token = $_SERVER['PHP_AUTH_PW'];
                $expected = get_option('ownerrez_webhookToken', null);

                // verify username and token
                if (!isset($_SERVER['PHP_AUTH_PW']) || !hash_equals($expected, $token)) {
                    header('WWW-Authenticate: Basic');
                    header('HTTP/1.0 401 Unauthorized');
                }
                else {
                    $result->authorized = true;

                    try {
                        $this->clear_transients();
                        $result->succeeded = true;
                    }
                    catch (Exception $ex) {
                        header('HTTP/1.0 500 Internal Server Error');
                        $result->exception = $ex->getMessage();
                    }
                }
            }
            else
            {
                header('HTTP/1.0 404 Not Found');
                $result->exception = "Unknown webhook: " . $webhook;
            }

            echo json_encode($result);
            exit();
        }
    }

    function clear_transients()
    {
        if ( wp_using_ext_object_cache() ) {
            // cache is stored somewhere other than the options table... it's either flush all or loop all...
            wp_cache_flush();
        }
        else {
            foreach ($this->get_transient_keys_with_prefix("orapi.") as $key) {
                delete_transient($key);
            }
        }
    }

    /**
     * Gets all transient keys in the database with a specific prefix.
     *
     * Note that this doesn't work for sites that use a persistent object
     * cache, since in that case, transients are stored in memory.
     *
     * @param string $prefix Prefix to search for.
     * @return array Transient keys with prefix, or empty array on error.
     */
    function get_transient_keys_with_prefix( $prefix ) {
        global $wpdb;

        $prefix = $wpdb->esc_like('_transient_' . $prefix);
        $sql = "SELECT `option_name` FROM $wpdb->options WHERE `option_name` LIKE '%s'";
        $keys = $wpdb->get_results($wpdb->prepare($sql, $prefix . '%'), ARRAY_A);

        if (is_wp_error($keys)) {
            return [];
        }

        return array_map(function ($key) {
            // Remove '_transient_' from the option name.
            return ltrim($key['option_name'], '_transient_');
        }, $keys);
    }
}
