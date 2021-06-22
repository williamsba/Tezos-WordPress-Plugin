<?php

/**
 *
 * @link              https://strangework.com
 * @since             0.1
 * @package           Hen
 *
 * @wordpress-plugin
 * Plugin Name:       HEN WordPress Plugin
 * Plugin URI:        https://strangework.com
 * Description:       Display your Hic et Nunc NFT collectibles on your WordPress Website
 * Version:           0.1
 * Author:            Brad Williams
 * Author URI:        https://strangework.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hen
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Current plugin version.
 */
define( 'HEN_VERSION', '0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hen-activator.php
 */
function activate_hen() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hen-activator.php';
	Hen_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hen-deactivator.php
 */
function deactivate_hen() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hen-deactivator.php';
	Hen_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hen' );
register_deactivation_hook( __FILE__, 'deactivate_hen' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hen.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.1
 */
function run_hen() {

	$plugin = new Hen();
	$plugin->run();

}
run_hen();
