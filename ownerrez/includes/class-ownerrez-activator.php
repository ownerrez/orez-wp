<?php

/**
 * Fired during plugin activation
 *
 * @link       http://ownerrez.com
 * @since      1.0.0
 *
 * @package    OwnerRez
 * @subpackage OwnerRez/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin"s activation.
 *
 * @since      1.0.0
 * @package    OwnerRez
 * @subpackage OwnerRez/includes
 * @author     Your Name <dev@ownerreservations.com>
 */
class OwnerRez_Activator
{

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate()
	{
		update_option("ownerrez_do_activation_redirect", true);
	}

	public static function activated()
	{
		if (get_option("ownerrez_do_activation_redirect", false)) {
			delete_option("ownerrez_do_activation_redirect");

			if (get_option("ownerrez_username") === false || get_option("ownerrez_token") === false) {
				exit(wp_redirect(admin_url("options-general.php?page=ownerrez")));
			}
		}
	}
}
