<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://ownerrez.com
 * @since      1.0.0
 *
 * @package    OwnerRez
 * @subpackage OwnerRez/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    OwnerRez
 * @subpackage OwnerRez/admin
 * @author     OwnerRez Inc <dev@ownerreservations.com>
 */
class OwnerRez_Admin
{
    const DEFAULT_API_ROOT = 'https://api.ownerreservations.com/';

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
	 * @param      string    $ownerrez       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($ownerrez, $version)
	{
		$this->ownerrez = $ownerrez;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->ownerrez, plugins_url('/ownerrez/admin/css/ownerrez-admin.css'), array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->ownerrez, plugins_url('/ownerrez/admin/js/ownerrez-admin.js'), array('jquery'), $this->version, false);
	}

	public function menu_settings()
	{
		add_submenu_page(
			"options-general.php",  // Which menu parent
			"OwnerRez",            // Page title
			"OwnerRez",            // Menu title
			"manage_options",       // Minimum capability (manage_options is an easy way to target administrators)
			"ownerrez",            // Menu slug
			function () {
				include plugin_dir_path(dirname(__FILE__)) . "admin/partials/ownerrez-admin-display.php";

                $apiRoot = !empty($_POST["ownerrez_apiRoot"]) ? esc_url_raw($_POST["ownerrez_apiRoot"], ["http", "https"]) : get_option('ownerrez_apiRoot', self::DEFAULT_API_ROOT);
				$username = !empty($_POST["ownerrez_username"]) ? sanitize_email($_POST["ownerrez_username"]) : get_option('ownerrez_username');
				$token = !empty($_POST["ownerrez_token"]) ? sanitize_text_field($_POST["ownerrez_token"]) : get_option('ownerrez_token');
				$status = !empty($_GET["status"]) ? sanitize_text_field($_GET["status"]) : NULL;

				orez_render_admin($username, $token, $status, $apiRoot, get_option('ownerrez_externalSiteName'));
			}
		);
	}

	public function menu_settings_save()
	{
		// Get the options that were sent
        $apiRoot = !empty($_POST["ownerrez_apiRoot"]) ? esc_url_raw($_POST["ownerrez_apiRoot"], ["http", "https"]) : self::DEFAULT_API_ROOT;
		$username = !empty($_POST["ownerrez_username"]) ? sanitize_email($_POST["ownerrez_username"]) : NULL;
		$token = !empty($_POST["ownerrez_token"]) ? sanitize_text_field($_POST["ownerrez_token"]) : NULL;

		$webhookUrl = wp_guess_url() . "/ownerrez";
		$webhookToken = get_option("ownerrez_webhookToken");

		if ($webhookToken === false)
		    $webhookToken = wp_generate_password(20, false);

        try {
            // test creds
            $client = new OwnerRez\Api\Client($username, $token, $apiRoot);
            $result = json_decode($client->externalSites()->register($webhookUrl, $webhookToken));

            if (isset($result->id)) {
                // save creds
                update_option("ownerrez_apiRoot", $apiRoot, true);
                update_option("ownerrez_username", $username, true);
                update_option("ownerrez_token", $token, true);
                update_option("ownerrez_externalSiteId", $result->id, true);
                update_option("ownerrez_externalSiteName", $result->name, true);
                update_option("ownerrez_webhookToken", $webhookToken, true);

                header("Location: " . get_bloginfo("url") . "/wp-admin/options-general.php?page=ownerrez&status=success");
                exit;
            } else {
                header("Location: " . get_bloginfo("url") . "/wp-admin/options-general.php?page=ownerrez&status=connection-failure");
                exit;
            }
        }
        catch (Exception $ex)
        {
            error_log($ex->getMessage());

            header("Location: " . get_bloginfo("url") . "/wp-admin/options-general.php?page=ownerrez&status=connection-failure");
            exit;
        }
	}

    public function plugin_links($links)
    {
        $url = esc_url( add_query_arg('page', 'ownerrez', get_admin_url() . 'admin.php') );

        // Create the link.
        $settings_link = "<a href='$url'>" . __( 'Settings' ) . "</a>";

        // Adds the link to the end of the array.
        array_push(
            $links,
            $settings_link
        );

        return $links;
    }
}
