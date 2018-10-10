<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/zariMatan
 * @since             1.0.0
 * @package           Foody_Rating
 *
 * @wordpress-plugin
 * Plugin Name:       Foody Rating
 * Plugin URI:        foody-rating
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Matan Zari
 * Author URI:        https://github.com/zariMatan
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       foody-rating
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-foody-rating-activator.php
 */
function activate_foody_rating() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-foody-rating-activator.php';
	Foody_Rating_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-foody-rating-deactivator.php
 */
function deactivate_foody_rating() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-foody-rating-deactivator.php';
	Foody_Rating_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_foody_rating' );
register_deactivation_hook( __FILE__, 'deactivate_foody_rating' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-foody-rating.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_foody_rating() {

	$plugin = new Foody_Rating();
	$plugin->run();

}
run_foody_rating();
