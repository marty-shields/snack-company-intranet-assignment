<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages functions necessary on plugin activation.
 *
 *
 * @version		1.1.3
 * @package		post-type-x/functions
 * @author 		Norbert Dreszer
 */
function epc_activation_function() {
	create_products_page();
	create_sample_product();
	implecode_plugin_review_notice_hide();
	permalink_options_update();
}

function create_products_page() {
	$product_page = array(
		'post_title'	 => __( 'Products', 'post-type-x' ),
		'post_type'		 => 'page',
		'post_content'	 => '',
		'post_status'	 => 'publish',
		'comment_status' => 'closed'
	);

	$plugin_data	 = get_plugin_data( AL_PLUGIN_MAIN_FILE );
	$plugin_version	 = $plugin_data[ "Version" ];
	$first_version	 = get_option( 'PTX_first_activation_version', '1.0' );

	if ( $first_version == '1.0' ) {
		add_option( 'PTX_first_activation_version', $plugin_version );
		add_option( 'post_type_x_ver', $plugin_version );
		$post_id = wp_insert_post( $product_page );
		add_option( 'product_archive_page_id', $post_id );
	}
}

function create_sample_product() {
	if ( !is_advanced_mode_forced() ) {
		$product_sample							 = array(
			'post_title'	 => __( 'Sample Item Page', 'post-type-x' ),
			'post_type'		 => 'al_product',
			'post_content'	 => '',
			'post_status'	 => 'publish',
			'comment_status' => 'closed'
		);
		$product_id								 = wp_insert_post( $product_sample );
		$product_field[ '_price' ]				 = 30;
		$product_field[ '_sku' ]				 = 'INT102';
		$product_field[ '_attribute-label1' ]	 = __( 'Color', 'post-type-x' );
		$product_field[ '_attribute-label2' ]	 = __( 'Size', 'post-type-x' );
		$product_field[ '_attribute-label3' ]	 = __( 'Weight', 'post-type-x' );
		$product_field[ '_attribute1' ]			 = __( 'White', 'post-type-x' );
		$product_field[ '_attribute2' ]			 = __( 'Big', 'post-type-x' );
		$product_field[ '_attribute3' ]			 = 130;
		$product_field[ '_attribute-unit1' ]	 = '';
		$product_field[ '_attribute-unit2' ]	 = '';
		$product_field[ '_attribute-unit3' ]	 = __( 'lbs', 'post-type-x' );
		$product_field[ '_shipping-label1' ]	 = 'UPS';
		$product_field[ '_shipping1' ]			 = 15;
		//$product_field[ 'excerpt' ]				 = '[theme_integration class="fixed-box"]';
		$product_field[ 'excerpt' ]				 = '<p>' . __( 'Welcome on item test page. This is short description. It should show up on the left of the item image and below item name. You shouldn\'t see nothing between item name and short description. No author, time or date. Absolutely nothing. If there is something that you don\'t want to see than you probably need Advanced Integration Mode.', 'post-type-x' ) . '</p>';
		$product_field[ 'excerpt' ] .= '<p><strong>' . __( 'Please read this page carefully to fully understand the difference between simple and advanced mode and how the item page looks like.', 'post-type-x' ) . '</strong></p>';

		$long_desc					 = '<p>' . __( 'This section is item long description. It should appear under the attributes table. Between the short description and the attributes table you should see the price, SKU and shipping options (all can be disabled). The attributes also can be disabled.', 'post-type-x' ) . '</p>';
		$long_desc .= '<h2>' . __( 'Advanced Theme Integration Mode', 'post-type-x' ) . '</h2>';
		$long_desc .= '<p>' . sprintf( __( 'With Advanced Mode you will be able to use Post Type X in %s. The item listing page, category pages, item search and category widget will be enabled in advanced mode. You can enable the Advanced Mode %s free. To see how please see <a target="_blank" href="%s">Theme Integration Guide</a>', 'post-type-x' ), '100%', '100%', 'https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=typex-sample-product-page&key=integration-mode-test' ) . '</p>';
		$long_desc .= '<p>' . __( 'The Advanced Mode works out of the box on all default WordPress themes and all themes with the integration done properly.', 'post-type-x' ) . '</p>';
		$long_desc .= '<h2>' . __( 'Simple Theme Integration Mode', 'post-type-x' ) . '</h2>';
		$long_desc .= '<p>' . sprintf( __( 'The simple mode allows to use Post Type X most features. You can build the item listing pages and category pages by using a %s shortcode. Simple mode uses your theme page layout so it can show unwanted elements on item page. If it does please switch to Advanced Mode and see if it works out of the box.', 'post-type-x' ), '[[show_products]]' ) . '</p>';
		$long_desc .= '<p>' . __( 'Switching to Advanced Mode also gives additional features: automatic item listing, category pages, item search and category widget. Building a item catalog in Advanced Mode will be less time consuming as you don\'t need to use a shortcode for everything.', 'post-type-x' ) . '</p>';
		$long_desc .= '<h2>' . __( 'How to switch to Advanced Mode?', 'post-type-x' ) . '</h2>';
		$long_desc .= '<p>' . sprintf( __( 'Click <a href="%s">here</a> to test the Automatic Advanced Mode. If the test goes well you can keep it enabled and enjoy full Post Type X functionality. If the page layout during the test will not be satisfying please see <a target="_blank" href="%s">Theme Integration Guide</a>', 'post-type-x' ), '?test_advanced=1', 'https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=typex-sample-product-page&key=integration-mode-test' ) . '</p>';
		$long_desc .= '<p>' . __( 'The theme integration guide will show you a step by step process. If you finish it successfully the integration will be done. It is recommended to use theme integration guide even if the page looks good in simple mode or automatic advanced mode because it reassures 100% theme integrity.', 'post-type-x' ) . '</p>';
		$long_desc .= '<h2>' . __( 'Item Description End', 'post-type-x' ) . '</h2>';
		$long_desc .= '<p>' . __( 'Below the item description you should see nothing apart of return to items URL and Advanced Mode Test which will not show up on your item pages. When using advanced mode also the related items will show up.', 'post-type-x' ) . '</p>';
		$long_desc .= '<p>' . sprintf( __( 'Thank you for choosing Post Type X. If you have any questions or comments please use <a target="_blank" href="%s">plugin support forum</a>.', 'post-type-x' ), 'https://wordpress.org/support/plugin/post-type-x' ) . '</p>';
		//$long_desc .= '[theme_integration]';
		$product_field[ 'content' ]	 = $long_desc;
		foreach ( $product_field as $key => $value ) {
			add_post_meta( $product_id, $key, $value, true );
		}
		update_option( 'sample_product_id', $product_id );
		return $product_id;
	}
}

function sample_product_id() {
	return get_option( 'sample_product_id' );
}

function sample_product_url() {
	$product_id			 = sample_product_id();
	$sample_product_url	 = get_permalink( $product_id );
	$sample_product_url	 = esc_url( add_query_arg( 'test_advanced', 1, $sample_product_url ) );
	if ( !$sample_product_url || get_post_status( $product_id ) != 'publish' ) {
		$sample_product_url = esc_url( add_query_arg( 'create_sample_product_page', 'true' ) );
	}
	return $sample_product_url;
}

function sample_product_button( $p = null, $text = null ) {
	$text = isset( $text ) ? $text : __( 'Start Auto Adjustment', 'post-type-x' );
	if ( !isset( $p ) ) {
		return '<a href="' . sample_product_url() . '" class="button-primary">' . $text . '</a>';
	} else {
		return '<p><a href="' . sample_product_url() . '" class="button-primary">' . $text . '</a></p>';
	}
}

function ecommerce_product_catalog_upgrade() {
	if ( is_admin() ) {
		$plugin_data			 = get_plugin_data( AL_PLUGIN_MAIN_FILE );
		$plugin_version			 = $plugin_data[ "Version" ];
		$database_plugin_version = get_option( 'post_type_x_ver', $plugin_version );
		if ( $database_plugin_version != $plugin_version ) {
			update_option( 'post_type_x_ver', $plugin_version );
			$first_version = (string) get_option( 'PTX_first_activation_version', $plugin_version );
			/*
			  if ( version_compare( $first_version, '1.0.0' ) < 0 && version_compare( $database_plugin_version, '1.0.0' ) < 0 ) {

			  }
			 */
			flush_rewrite_rules();
		}
	}
}

add_action( 'admin_init', 'ecommerce_product_catalog_upgrade' );
