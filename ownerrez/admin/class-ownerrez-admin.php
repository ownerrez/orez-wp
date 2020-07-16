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
 * @author     Your Name <dev@ownerreservations.com>
 */
class OwnerRez_Admin
{

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
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      OwnerRez_Api    $api    The connection to OwnerRez.
	 */
	private $api;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $ownerrez       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 * @param      OwnerRez_Api    $api    The OwnerRez api connector.
	 */
	public function __construct($ownerrez, $version, $api)
	{
		$this->ownerrez = $ownerrez;
		$this->version = $version;
		$this->api = $api;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{
		wp_enqueue_style($this->ownerrez, plugin_dir_url(__FILE__) . 'css/ownerrez-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{
		wp_enqueue_script($this->ownerrez, plugin_dir_url(__FILE__) . 'js/ownerrez-admin.js', array('jquery'), $this->version, false);
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

				$username = (!empty($_POST["ownerrez_username"])) ? $_POST["ownerrez_username"] : get_option('ownerrez_username');
				$token = (!empty($_POST["ownerrez_token"])) ? $_POST["ownerrez_token"] : get_option('ownerrez_token');
				$status = (!empty($_GET["status"])) ? $_GET["status"] : NULL;

				orez_render_admin($username, $token, $status);
			}
		);
	}

	public function menu_settings_save()
	{
		// Get the options that were sent
		$username = (!empty($_POST["ownerrez_username"])) ? $_POST["ownerrez_username"] : NULL;
		$token = (!empty($_POST["ownerrez_token"])) ? $_POST["ownerrez_token"] : NULL;

		// test creds
		$result = $this->api->test_credentials($username, $token);

		if ($result) {
			// save creds
			update_option("ownerrez_username", $username, true);
			update_option("ownerrez_token", $token, true);

			header("Location: " . get_bloginfo("url") . "/wp-admin/options-general.php?page=ownerrez&status=success");
			exit;
		} else {
			header("Location: " . get_bloginfo("url") . "/wp-admin/options-general.php?page=ownerrez&status=connection-failure");
			exit;
		}
	}
}
