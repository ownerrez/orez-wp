<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://ownerrez.com
 * @since             1.0.0
 * @package           OwnerRez
 *
 * @wordpress-plugin
 * Plugin Name:       OwnerRez
 * Plugin URI:        http://ownerrez.com/support/wordpress
 * Description:       Integrate your OwnerRez account with your wordpress site.
 * Version:           1.0.0
 * Author:            OwnerRez, Inc.
 * Author URI:        http://ownerrez.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ownerrez
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('OWNERREZ_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ownerrez-activator.php
 */
function activate_ownerrez()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-ownerrez-activator.php';
	OwnerRez_Activator::activate();
}

/**
 * The code that runs AFTER plugin activation.
 * This action is documented in includes/class-ownerrez-activator.php
 */
function activated_ownerrez($plugin)
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-ownerrez-activator.php';
	OwnerRez_Activator::activated();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ownerrez-deactivator.php
 */
function deactivate_ownerrez()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-ownerrez-deactivator.php';
	OwnerRez_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_ownerrez');
register_deactivation_hook(__FILE__, 'deactivate_ownerrez');

add_action("activated_plugin", "activated_ownerrez");

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-ownerrez.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ownerrez()
{
    if ( is_readable( plugin_dir_path(__FILE__) . 'lib/autoload.php' ) ) {
        require plugin_dir_path(__FILE__) . 'lib/autoload.php';
    }

	$plugin = new OwnerRez();
	$plugin->run();
}
run_ownerrez();
