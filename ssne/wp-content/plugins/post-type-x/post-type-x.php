<?php

/**
 * Plugin Name: Post Type X
 * Plugin URI: https://implecode.com/wordpress/post-type-x/#cam=in-plugin-urls&key=plugin-url
 * Description: A minimalistic, modular catalog tool which comes with fully customizable, responsive front-end design, search and categories.
 * Version: 1.0.4
 * Author: impleCode
 * Author URI: https://implecode.com/#cam=in-plugin-urls&key=author-url
 * Text Domain: post-type-x
 * Domain Path: /lang/

  Copyright: 2015 impleCode.
  License: GNU General Public License v3.0
  License URI: http://www.gnu.org/licenses/gpl-3.0.html */
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !(is_admin() && isset( $_GET[ 'action' ] ) && $_GET[ 'action' ] == 'activate' && isset( $_GET[ 'plugin' ] ) && $_GET[ 'plugin' ] == 'ecommerce-product-catalog/ecommerce-product-catalog.php') ) {
	add_action( 'plugins_loaded', 'start_post_type_x', 15 );
}

function post_type_x_activate() {
	add_option( 'post_type_x_activated', 1 );
}

register_activation_hook( __FILE__, 'post_type_x_activate' );

function start_post_type_x() {
	if ( !defined( 'AL_BASE_PATH' ) ) {

		define( 'AL_BASE_PATH', dirname( __FILE__ ) );
		define( 'AL_PLUGIN_BASE_PATH', plugins_url( '/', __FILE__ ) );
		define( 'AL_PLUGIN_MAIN_FILE', __FILE__ );

		require_once(AL_BASE_PATH . '/functions/index.php' );
		require_once(AL_BASE_PATH . '/includes/index.php' );
		require_once(AL_BASE_PATH . '/includes/product.php' );
		require_once(AL_BASE_PATH . '/includes/product-settings.php' );
		require_once(AL_BASE_PATH . '/includes/settings-defaults.php' );
		require_once(AL_BASE_PATH . '/functions/base.php' );
		require_once(AL_BASE_PATH . '/functions/capabilities.php' );
		require_once(AL_BASE_PATH . '/functions/functions.php' );
		require_once(AL_BASE_PATH . '/templates.php' );
		require_once(AL_BASE_PATH . '/theme-product_adder_support.php' );
		require_once(AL_BASE_PATH . '/functions/shortcodes.php' );
		require_once(AL_BASE_PATH . '/functions/activation.php' );
		require_once(AL_BASE_PATH . '/ext-comp/index.php' );
		if ( is_admin() && get_option( 'post_type_x_activated', 0 ) == 1 ) {
			add_action( 'admin_init', 'add_product_caps' );
			add_action( 'admin_init', 'epc_activation_function' );
			delete_option( 'post_type_x_activated' );
			wp_redirect( admin_url( 'plugins.php' ) );
		}
		add_action( 'wp_enqueue_scripts', 'implecode_enqueue_styles' );

		/**
		 * Adds catalog front-end styles and scripts
		 *
		 */
		function implecode_enqueue_styles() {
			wp_enqueue_style( 'al_product_styles' );
			$colorbox_set = json_decode( apply_filters( 'colorbox_set', '{"transition": "elastic", "initialWidth": 200, "maxWidth": "90%", "maxHeight": "90%", "rel":"gal"}' ) );
			wp_localize_script( 'al_product_scripts', 'product_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'lightbox_settings' => $colorbox_set ) );
			wp_enqueue_script( 'al_product_scripts' );
			do_action( 'enqueue_catalog_scripts' );
		}

		add_action( 'admin_init', 'implecode_register_admin_styles' );

		/**
		 * Registers catalog admin styles and scripts
		 */
		function implecode_register_admin_styles() {
			wp_register_style( 'al_product_admin_styles', plugins_url() . '/' . dirname( plugin_basename( __FILE__ ) ) . '/css/al_product-admin.css?' . filemtime( plugin_dir_path( __FILE__ ) . '/css/al_product-admin.css' ), array( 'wp-color-picker' ) );
			wp_register_script( 'jquery-validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js', array( 'jquery' ) );
			wp_register_script( 'jquery-validate-add', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/additional-methods.min.js', array( 'jquery-validate' ) );
			wp_register_script( 'admin-scripts', AL_PLUGIN_BASE_PATH . 'js/admin-scripts.js?' . filemtime( AL_BASE_PATH . '/js/admin-scripts.js' ), array( 'jquery-ui-sortable', 'jquery-ui-tooltip', 'jquery-validate-add', 'wp-color-picker' ) );
			do_action( 'register_catalog_admin_styles' );
		}

		add_action( 'init', 'implecode_register_styles' );

		/**
		 * Registers catalog styles and scripts
		 */
		function implecode_register_styles() {
			wp_register_style( 'al_product_styles', plugins_url() . '/' . dirname( plugin_basename( __FILE__ ) ) . '/css/al_product.css?' . filemtime( plugin_dir_path( __FILE__ ) . '/css/al_product.css' ), array( 'dashicons' ) );
			do_action( 'register_catalog_styles' );
		}

		add_action( 'admin_enqueue_scripts', 'implecode_run_admin_styles' );

		/**
		 * Adds catalog admin styles and scripts
		 *
		 */
		function implecode_run_admin_styles() {
			wp_enqueue_style( 'al_product_styles' );
			wp_enqueue_style( 'al_product_admin_styles' );
			do_action( 'enqueue_catalog_admin_styles' );
			if ( is_ic_admin_page() ) {
				wp_enqueue_script( 'admin-scripts' );
				do_action( 'enqueue_catalog_admin_scripts' );
			}
		}

		add_action( 'plugins_loaded', 'implecode_addons', 20 );

		/**
		 * Executes all installed catalog extensions
		 */
		function implecode_addons() {
			load_plugin_textdomain( 'post-type-x', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
			if ( !is_network_admin() ) {
				do_action( 'post_type_x_addons' );
				do_action( 'ecommerce-prodct-catalog-addons' );
			}
		}

	}
}
