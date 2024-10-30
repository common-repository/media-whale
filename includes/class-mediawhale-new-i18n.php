<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       Danyal A.
 * @since      1.0.0
 *
 * @package    Mediawhale_New
 * @subpackage Mediawhale_New/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Mediawhale_New
 * @subpackage Mediawhale_New/includes
 * @author     Danyal A. <saqibishaq302@gmail.com>
 */
class Mediawhale_New_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'mediawhale-new',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
