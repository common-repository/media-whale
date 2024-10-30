<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              Danyal A.
 * @since             1.0.0
 * @package           Mediawhale_New
 *
 * @wordpress-plugin
 * Plugin Name:       Mediawhale
 * Plugin URI:        mediawhale
 * Description:       Convert text to speech. Simple. Elegant.
 * Version:           1.2.1
 * Author:            mediawhale.
 * Author URI:        mediawhale.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       mediawhale-new
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
define( 'MEDIAWHALE_NEW_VERSION', '1.1' );
define('MEDIAWHALE_DIR',plugin_dir_path( __FILE__ ));
define('MEDIAWHALE_URL',plugin_dir_url( __FILE__ ));
/*
Converter File
*/
require plugin_dir_path( __FILE__ ) . 'admin/misc.php';    

/*
AJAX Code
*/

require plugin_dir_path( __FILE__ ) . 'admin/ajax.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-mediawhale-new.php';

/*
Converter File
*/
require plugin_dir_path( __FILE__ ) . 'converter/converter.php';

/*
Settings File
*/
require plugin_dir_path( __FILE__ ) . 'admin/settings.php';

function mediawhale_activation_redirect( $plugin ) {
    if( $plugin == plugin_basename( __FILE__ ) ) {
        exit( wp_redirect( admin_url( 'admin.php?page=mediawhale' ) ) );
    }
}
add_action( 'activated_plugin', 'mediawhale_activation_redirect' );
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_mediawhale_new() {

	$plugin = new Mediawhale_New();
	$plugin->run();

}
run_mediawhale_new();