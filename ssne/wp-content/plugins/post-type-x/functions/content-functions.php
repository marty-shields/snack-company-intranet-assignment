<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages item content functions
 *
 * Here all plugin content functions are defined and managed.
 *
 * @version		1.0.0
 * @package		post-type-x/functions
 * @author 		Norbert Dreszer
 */
/* General */

/* Classic List */

function c_list_desc( $post_id = null, $shortdesc = null ) {
	if ( $shortdesc == '' ) {
		$shortdesc = clean_short_description( $post_id );
	} else {
		$shortdesc	 = strip_tags( $shortdesc );
		$shortdesc	 = trim( strip_shortcodes( $shortdesc ) );
		$shortdesc	 = str_replace( array( "\r\n" ), ' ', $shortdesc );
	}
	$desclenght	 = strlen( $shortdesc );
	$more		 = '';
	$limit		 = apply_filters( 'c_list_desc_limit', 243 );
	if ( $desclenght > $limit ) {
		$more = ' [...]';
	}
	return apply_filters( 'c_list_desc_content', mb_substr( $shortdesc, 0, $limit ) . $more, $post_id );
}

/**
 * Returns short description text without HTML
 *
 * @param int $product_id
 * @return string
 */
function clean_short_description( $product_id, $new_line = ' ' ) {
	$shortdesc	 = get_product_short_description( $product_id );
	$shortdesc	 = strip_tags( $shortdesc );
	$shortdesc	 = trim( strip_shortcodes( $shortdesc ) );
	$shortdesc	 = str_replace( array( "\r\n" ), $new_line, $shortdesc );
	return $shortdesc;
}

/* Single Item */
add_action( 'single_product_end', 'add_back_to_products_url', 99, 2 );

/**
 *
 * @param object $post
 * @param array $single_names
 * @param string $taxonomies
 */
function add_back_to_products_url( $post, $single_names ) {
	if ( is_ic_product_listing_enabled() ) {
		echo get_back_to_products_url( $single_names );
	}
}

/**
 * Returns back to items URL
 *
 * @param array $v_single_names
 * @return string
 */
function get_back_to_products_url( $v_single_names = null ) {
	if ( is_ic_product_listing_enabled() ) {
		$single_names	 = isset( $v_single_names ) ? $v_single_names : get_single_names();
		$url			 = '<a href="' . product_listing_url() . '">' . $single_names[ 'return_to_archive' ] . '</a>';
		return $url;
	}
	return;
}

/**
 * Shows item search form
 */
function product_search_form() {
	$search_button_text = __( 'Search', 'post-type-x' );
	echo '<form role="search" method="get" class="search-form product_search_form" action="' . esc_url( home_url( '/' ) ) . '">
<input type="hidden" name="post_type" value="' . get_current_screen_post_type() . '" />
<input class="product-search-box" type="search" value="' . get_search_query() . '" id="s" name="s" placeholder="' . __( 'Item Search', 'post-type-x' ) . '" />
<input class="search-submit product-search-submit" type="submit" value="' . $search_button_text . '" />
</form>';
}
