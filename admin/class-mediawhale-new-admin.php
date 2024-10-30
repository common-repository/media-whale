<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       Danyal A.
 * @since      1.0.0
 *
 * @package    Mediawhale_New
 * @subpackage Mediawhale_New/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Mediawhale_New
 * @subpackage Mediawhale_New/admin
 * @author     Danyal A. <saqibishaq302@gmail.com>
 */
class Mediawhale_New_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mediawhale_New_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mediawhale_New_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name."select2", plugin_dir_url( __FILE__ ) . 'css/select2.min.css', array(), $this->version, 'all' );
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/mediawhale-new-admin.css', array(), $this->version, 'all' );


	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Mediawhale_New_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Mediawhale_New_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( 'mediawhale_select2', plugin_dir_url( __FILE__ ) . 'js/select2.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( 'mediawhale_admin', plugin_dir_url( __FILE__ ) . 'js/mediawhale-new-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( 'mediawhale_admin', 'mediawhale_ajax',
            array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'mediawhale_converter' ) ) );
	}

}
