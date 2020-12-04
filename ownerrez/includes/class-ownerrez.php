<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://ownerrez.com
 * @since      1.0.0
 *
 * @package    OwnerRez
 * @subpackage OwnerRez/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    OwnerRez
 * @subpackage OwnerRez/includes
 * @author     Dev <dev@ownerreservations.com>
 */
class OwnerRez
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      OwnerRez_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $ownerrez    The string used to uniquely identify this plugin.
	 */
	protected $ownerrez;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('OWNERREZ_VERSION')) {
			$this->version = OWNERREZ_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->ownerrez = 'ownerrez';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - OwnerRez_Loader. Orchestrates the hooks of the plugin.
	 * - OwnerRez_i18n. Defines internationalization functionality.
	 * - OwnerRez_Admin. Defines all hooks for the admin area.
	 * - OwnerRez_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ownerrez-loader.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ownerrez-i18n.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-ownerrez-apiwrapper.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ownerrez-public.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-ownerrez-shortcodes.php';
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ownerrez-admin.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-ownerrez-ajax.php';

		$this->loader = new OwnerRez_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the OwnerRez_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale()
	{
		$plugin_i18n = new OwnerRez_i18n();

		$this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{
		$plugin_admin = new OwnerRez_Admin($this->get_ownerrez(), $this->get_version());

		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
		$this->loader->add_filter('admin_menu', $plugin_admin, 'menu_settings');
		$this->loader->add_action('admin_post_save_ownerrez_settings', $plugin_admin, 'menu_settings_save');
        $this->loader->add_filter('plugin_action_links_ownerrez/ownerrez.php', $plugin_admin, 'plugin_links');

		// define admin ajax end points
        $plugin_ajax = new OwnerRez_Ajax($this->get_ownerrez(), $this->get_version());

        $this->loader->add_filter('wp_ajax_ownerrez', $plugin_ajax, 'call');
        $this->loader->add_filter('wp_ajax_nopriv_ownerrez', $plugin_ajax, 'call_nopriv');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks()
	{
		$plugin_public = new OwnerRez_Public($this->get_ownerrez(), $this->get_version());

		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
		$this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');

		$plugin_shortcodes = new OwnerRez_ShortCodes($this->get_ownerrez(), $this->get_version());

		$this->loader->add_action('init', $plugin_shortcodes, 'register_shortcodes');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_ownerrez()
	{
		return $this->ownerrez;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    OwnerRez_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
