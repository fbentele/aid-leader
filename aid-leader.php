<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              aid.sg
 * @since             1.0.0
 * @package           Aid_Leader
 *
 * @wordpress-plugin
 * Plugin Name:       Leaders
 * Plugin URI:        leader.aid.sg
 * Description:       Leiter Plugin der alea iacta digital gmbh
 * Version:           1.0.0
 * Author:            Florian Bentele
 * Author URI:        aid.sg
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aid-leader
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aid-leader-activator.php
 */
function activate_aid_leader() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aid-leader-activator.php';
	Aid_Leader_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aid-leader-deactivator.php
 */
function deactivate_aid_leader() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aid-leader-deactivator.php';
	Aid_Leader_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aid_leader' );
register_deactivation_hook( __FILE__, 'deactivate_aid_leader' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-aid-leader.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_aid_leader() {

	$plugin = new Aid_Leader();
	$plugin->run();

}
run_aid_leader();
