<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages item post type
 *
 * Here all item fields are defined.
 *
 * @version		1.0.0
 * @package		post-type-x/includes
 * @author 		Norbert Dreszer
 */
add_action( 'init', 'create_product_categories', 9 );

/**
 * Registers item categories
 *
 */
function create_product_categories() {
	$archive_multiple_settings	 = get_multiple_settings();
	$category_enable			 = true;
	if ( get_integration_type() == 'simple' ) {
		$category_enable = false;
	}
	if ( is_plural_form_active() ) {
		$names				 = get_catalog_names();
		$names[ 'singular' ] = ucfirst( $names[ 'singular' ] );
		$labels				 = array(
			'name'				 => sprintf( __( '%s Categories', 'post-type-x' ), $names[ 'singular' ] ),
			'singular_name'		 => sprintf( __( '%s Category', 'post-type-x' ), $names[ 'singular' ] ),
			'search_items'		 => sprintf( __( 'Search %s Categories', 'post-type-x' ), $names[ 'singular' ] ),
			'all_items'			 => sprintf( __( 'All %s Categories', 'post-type-x' ), $names[ 'singular' ] ),
			'parent_item'		 => sprintf( __( 'Parent %s Category', 'post-type-x' ), $names[ 'singular' ] ),
			'parent_item_colon'	 => sprintf( __( 'Parent %s Category:', 'post-type-x' ), $names[ 'singular' ] ),
			'edit_item'			 => sprintf( __( 'Edit %s Category', 'post-type-x' ), $names[ 'singular' ] ),
			'update_item'		 => sprintf( __( 'Update %s Category', 'post-type-x' ), $names[ 'singular' ] ),
			'add_new_item'		 => sprintf( __( 'Add New %s Category', 'post-type-x' ), $names[ 'singular' ] ),
			'new_item_name'		 => sprintf( __( 'New %s Category', 'post-type-x' ), $names[ 'singular' ] ),
			'menu_name'			 => sprintf( __( '%s Categories', 'post-type-x' ), $names[ 'singular' ] ),
		);
	} else {
		$labels = array(
			'name'				 => __( 'Categories', 'post-type-x' ),
			'singular_name'		 => __( 'Category', 'post-type-x' ),
			'search_items'		 => __( 'Search Categories', 'post-type-x' ),
			'all_items'			 => __( 'All Categories', 'post-type-x' ),
			'parent_item'		 => __( 'Parent Category', 'post-type-x' ),
			'parent_item_colon'	 => __( 'Parent Category:', 'post-type-x' ),
			'edit_item'			 => __( 'Edit Category', 'post-type-x' ),
			'update_item'		 => __( 'Update Category', 'post-type-x' ),
			'add_new_item'		 => __( 'Add New Category', 'post-type-x' ),
			'new_item_name'		 => __( 'New Category', 'post-type-x' ),
			'menu_name'			 => __( 'Categories', 'post-type-x' )
		);
	}

	$args = array(
		'public'			 => $category_enable,
		'hierarchical'		 => true,
		'labels'			 => $labels,
		'show_ui'			 => true,
		'show_admin_column'	 => true,
		'query_var'			 => true,
		'rewrite'			 => array( 'hierarchical' => true, 'slug' => apply_filters( 'product_category_slug_value_register', sanitize_title( $archive_multiple_settings[ 'category_archive_url' ] ) ), 'with_front' => false ),
		'capabilities'		 => array(
			'manage_terms'	 => 'manage_product_categories',
			'edit_terms'	 => 'edit_product_categories',
			'delete_terms'	 => 'delete_product_categories',
			'assign_terms'	 => 'assign_product_categories'
		)
	);

	register_taxonomy( 'al_product-cat', 'al_product', $args );
	register_taxonomy_for_object_type( 'al_product-cat', 'al_product' );
}
