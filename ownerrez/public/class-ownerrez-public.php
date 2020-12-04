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

		wp_enqueue_style( $this->ownerrez, OWNERREZ_ROOT . '/public/css/ownerrez-public.css', array(), $this->version, 'all' );
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

		wp_enqueue_script( $this->ownerrez, OWNERREZ_ROOT . '/public/js/ownerrez-public.js', array( 'jquery' ), $this->version, false );

	}
}
