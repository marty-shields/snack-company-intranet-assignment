<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages item functions
 *
 * Here all plugin functions are defined and managed.
 *
 * @version        1.0.0
 * @package        post-type-x/functions
 * @author        Norbert Dreszer
 */

/**
 * Returns default item image
 *
 * @return string
 */
function default_product_thumbnail() {
	if ( get_option( 'default_product_thumbnail' ) ) {
		$url = get_option( 'default_product_thumbnail' );
	} else {
		$product_id = get_the_ID();
		if ( $product_id == sample_product_id() ) {
			$url = AL_PLUGIN_BASE_PATH . 'img/implecode.jpg';
		} else {
			$url = AL_PLUGIN_BASE_PATH . 'img/no-default-thumbnail.png';
		}
	}

	return '<img src="' . $url . '"  />';
}

/**
 * Returns default item image URL
 *
 * @return string
 */
function default_product_thumbnail_url() {
	if ( get_option( 'default_product_thumbnail' ) ) {
		$url = get_option( 'default_product_thumbnail' );
	} else {
		$url = AL_PLUGIN_BASE_PATH . 'img/no-default-thumbnail.png';
	}

	return $url;
}

add_action( 'wp', 'redirect_listing_on_non_permalink' );

/**
 * Redirects the item listing page to archive page on non permalink configuration
 *
 */
function redirect_listing_on_non_permalink() {
	if ( !is_ic_permalink_product_catalog() ) {
		$product_listing_id = get_product_listing_id();
		if ( !empty( $product_listing_id ) && is_ic_product_listing_enabled() && is_page( $product_listing_id ) ) {
			$url = product_listing_url();
			wp_redirect( $url, 301 );
			exit;
		}
	}
}

function upload_product_image( $name, $button_value, $option_name, $option_value = null, $default_image = null ) {
	wp_enqueue_media();
	if ( empty( $option_value ) ) {
		$option_value = get_option( $option_name );
	}
	if ( empty( $default_image ) ) {
		$default_image = AL_PLUGIN_BASE_PATH . 'img/no-default-thumbnail.png';
	}
	if ( $option_value ) {
		$src = $option_value;
	} else {
		$src = $default_image;
	}
	?>
	<div class="custom-uploader">
		<input type="hidden" id="default" value="<?php echo $default_image; ?>"/>
		<input type="hidden" name="<?php echo $option_name; ?>" id="<?php echo $name; ?>"
			   value="<?php echo $option_value; ?>"/>

		<div class="admin-media-image"><img class="media-image" src="<?php echo $src; ?>" width="100%" height="100%"/>
		</div>
		<a href="#" class="button insert-media add_media" name="<?php echo $name; ?>_button"
		   id="button_<?php echo $name; ?>"><span class="wp-media-buttons-icon"></span> <?php echo $button_value; ?></a>
		<a class="button" id="reset-image-button"
		   href="#"><?php _e( 'Reset image', 'post-type-x' ); ?></a>
	</div>
	<script>
		jQuery( document ).ready( function () {
			jQuery( '#button_<?php echo $name; ?>' ).on( 'click', function () {
				wp.media.editor.send.attachment = function ( props, attachment ) {
					jQuery( '#<?php echo $name; ?>' ).val( attachment.url );
					jQuery( '.media-image' ).attr( "src", attachment.url );
				}

				wp.media.editor.open( this );

				return false;
			} );
		} );

		jQuery( '#reset-image-button' ).on( 'click', function () {
			jQuery( '#<?php echo $name; ?>' ).val( '' );
			src = jQuery( '#default' ).val();
			jQuery( '.media-image' ).attr( "src", src );
		} );
	</script>
	<?php
}

if ( !function_exists( 'select_page' ) ) {

	function select_page( $option_name, $first_option, $selected_value, $buttons = false, $custom_view_url = false,
					   $echo = 1, $custom = false ) {
		$args		 = array(
			'sort_order'	 => 'ASC',
			'sort_column'	 => 'post_title',
			'hierarchical'	 => 1,
			'exclude'		 => '',
			'include'		 => '',
			'meta_key'		 => '',
			'meta_value'	 => '',
			'authors'		 => '',
			'child_of'		 => 0,
			'parent'		 => -1,
			'exclude_tree'	 => '',
			'number'		 => '',
			'offset'		 => 0,
			'post_type'		 => 'page',
			'post_status'	 => 'publish'
		);
		$pages		 = get_pages( $args );
		$select_box	 = '<div class="select-page-wrapper"><select id="' . $option_name . '" name="' . $option_name . '"><option value="noid">' . $first_option . '</option>';
		foreach ( $pages as $page ) {
			$select_box .= '<option name="' . $option_name . '[' . $page->ID . ']" value="' . $page->ID . '" ' . selected( $page->ID, $selected_value, 0 ) . '>' . $page->post_title . '</option>';
		}
		if ( $custom ) {
			$select_box .= '<option value="custom"' . selected( 'custom', $selected_value, 0 ) . '>' . __( 'Custom URL', 'post-type-x' ) . '</option>';
		}
		$select_box .= '</select>';
		if ( $buttons && ($selected_value != 'noid' || $custom_view_url != '') ) {
			$edit_link	 = get_edit_post_link( $selected_value );
			$front_link	 = $custom_view_url ? $custom_view_url : get_permalink( $selected_value );
			if ( !empty( $edit_link ) ) {
				$select_box .= ' <a class="button button-small" style="vertical-align: middle;" href="' . $edit_link . '">' . __( 'Edit' ) . '</a>';
			}
			if ( !empty( $front_link ) ) {
				$select_box .= ' <a class="button button-small" style="vertical-align: middle;" href="' . $front_link . '">' . __( 'View Page' ) . '</a>';
			}
		}
		$select_box .= '</div>';
		return echo_ic_setting( $select_box, $echo );
	}

}

function show_page_link( $page_id ) {
	$page_url	 = post_permalink( $page_id );
	$page_link	 = '<a target="_blank" href=' . $page_url . '>' . $page_url . '</a>';
	echo $page_link;
}

function verify_page_status( $page_id ) {
	$page_status = get_post_status( $page_id );
	if ( $page_status != 'publish' AND $page_status != '' ) {
		echo '<div class="al-box warning">This page has wrong status: ' . $page_status . '.<br>Don\'t forget to publish it before going live!</div>';
	}
}

/**
 *
 * @param string $which color, size, box or none
 * @param int $echo
 * @return string
 */
function design_schemes( $which = null, $echo = 1 ) {
	$custom_design_schemes	 = unserialize( DEFAULT_DESIGN_SCHEMES );
	$design_schemes			 = get_option( 'design_schemes', $custom_design_schemes );
	if ( $which == 'color' ) {
		$output = $design_schemes[ 'price-color' ];
	} else if ( $which == 'size' ) {
		$output = $design_schemes[ 'price-size' ];
	} else if ( $which == 'box' ) {
		$output = $design_schemes[ 'box-color' ];
	} else if ( $which == 'none' ) {
		$output = '';
	} else {
		$output = $design_schemes[ 'price-color' ] . ' ' . $design_schemes[ 'price-size' ];
	}
	return echo_ic_setting( apply_filters( 'design_schemes_output', $output ), $echo );
}

/* Single Item Functions */
add_action( 'before_product_entry', 'single_product_header', 10, 2 );

/**
 * Displays header on item pages
 *
 * @param object $post
 * @param array $single_names
 */
function single_product_header( $post, $single_names ) {
	if ( get_integration_type() != 'simple' ) {
		?>
		<header class="entry-header product-page-header">
			<?php do_action( 'single_product_header', $post, $single_names ); ?>
		</header><?php
	}
}

add_action( 'single_product_header', 'add_product_name' );

/**
 * Shows item name on item page
 */
function add_product_name() {
	if ( is_ic_product_name_enabled() ) {
		echo '<h1 class="entry-title product-name">' . get_the_title() . '</h1>';
	}
}

add_action( 'before_product_listing_entry', 'product_listing_header', 10, 2 );

/**
 * Shows item listing header
 *
 * @param object $post
 * @param array $archive_names
 */
function product_listing_header( $post, $archive_names ) {
	if ( get_integration_type() != 'simple' ) {
		?>
		<header class="entry-header product-listing-header">
			<?php do_action( 'product_listing_header', $post, $archive_names ); ?>
		</header><?php
	}
}

add_action( 'product_listing_header', 'add_product_listing_name' );

/**
 * Shows item listing title tag
 */
function add_product_listing_name() {
	if ( is_ic_taxonomy_page() ) {
		$archive_names	 = get_archive_names();
		//$the_tax		 = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
		$the_tax		 = get_queried_object();
		if ( !empty( $archive_names[ 'all_prefix' ] ) ) {
			$title = $archive_names[ 'all_prefix' ] . ' ' . $the_tax->name;
		} else {
			$title = $the_tax->name;
		}
	} else if ( is_ic_product_search() ) {
		$title = __( 'Search Results for:', 'post-type-x' ) . ' ' . $_GET[ 's' ];
	} else if ( is_ic_product_listing() ) {
		$title = get_product_listing_title();
	} else {
		$title = get_the_title();
	}
	echo '<h1 class="entry-title product-listing-name">' . $title . '</h1>';
}

function show_short_desc( $post, $single_names ) {
	$shortdesc = get_product_short_description( $post->ID );
	?>
	<div class="shortdesc">
		<?php echo apply_filters( 'product_short_description', $shortdesc ); ?>
	</div>
	<?php
}

add_action( 'product_details', 'show_short_desc', 5, 2 );
add_filter( 'product_short_description', 'wptexturize' );
add_filter( 'product_short_description', 'convert_smilies' );
add_filter( 'product_short_description', 'convert_chars' );
add_filter( 'product_short_description', 'wpautop' );
add_filter( 'product_short_description', 'shortcode_unautop' );
add_filter( 'product_short_description', 'do_shortcode', 11 );

function show_product_description( $post, $single_names ) {
	$product_description = get_product_description( $post->ID );
	if ( !empty( $product_description ) ) {
		?>
		<div class="product-description"><?php
			if ( get_integration_type() == 'simple' ) {
				echo apply_filters( 'product_simple_description', $product_description );
			} else {
				echo apply_filters( 'the_content', $product_description );
			}
			?>
		</div>
		<?php
	}
}

add_action( 'after_product_details', 'show_product_description', 10, 2 );
add_filter( 'product_simple_description', 'wptexturize' );
add_filter( 'product_simple_description', 'convert_smilies' );
add_filter( 'product_simple_description', 'convert_chars' );
add_filter( 'product_simple_description', 'wpautop' );
add_filter( 'product_simple_description', 'shortcode_unautop' );

//add_filter('product_simple_description', 'do_shortcode', 11);

add_filter( 'the_content', 'show_simple_product_listing' );

/**
 * Shows item listing in simple mode if no shortcode exists.
 *
 * @param string $content
 * @return string
 */
function show_simple_product_listing( $content ) {
	if ( is_main_query() && in_the_loop() && get_integration_type() == 'simple' && is_ic_product_listing() && is_ic_product_listing_enabled() ) {
		if ( !has_shortcode( $content, 'show_products' ) ) {
			$archive_multiple_settings = get_multiple_settings();
			$content .= do_shortcode( '[show_products products_limit="' . $archive_multiple_settings[ 'archive_products_limit' ] . '"]' );
		}
	}
	return $content;
}

/* Archive Functions */

function get_quasi_post_type( $post_type = null ) {
	if ( empty( $post_type ) && is_home_archive() ) {
		$post_type = 'al_product';
	} else if ( empty( $post_type ) ) {
		$post_type = get_post_type();
	}
	$quasi_post_type = substr( $post_type, 0, 10 );
	return $quasi_post_type;
}

function get_quasi_post_tax_name( $tax_name, $exact = true ) {
	if ( $exact ) {
		$quasi_tax_name = substr( $tax_name, 0, 14 );
	} else if ( strpos( $tax_name, 'al_product-cat' ) !== false ) {
		$quasi_tax_name = 'al_product-cat';
	}
	return $quasi_tax_name;
}

function product_breadcrumbs() {
	if ( get_integration_type() != 'simple' && !is_front_page() ) {
		global $post;
		$post_type	 = get_post_type();
		$home_page	 = get_home_url();
		if ( function_exists( 'additional_product_listing_url' ) && $post_type != 'al_product' ) {
			$catalog_id			 = catalog_id( $post_type );
			$product_archives	 = additional_product_listing_url();
			$product_archive	 = $product_archives[ $catalog_id ];
			$archives_ids		 = get_option( 'additional_product_archive_id' );
			$breadcrumbs_options = get_option( 'product_breadcrumbs', unserialize( DEFAULT_PRODUCT_BREADCRUMBS ) );
			if ( empty( $breadcrumbs_options[ 'enable_product_breadcrumbs' ][ $catalog_id ] ) || !empty( $breadcrumbs_options[ 'enable_product_breadcrumbs' ][ $catalog_id ] ) && $breadcrumbs_options[ 'enable_product_breadcrumbs' ][ $catalog_id ] != 1 ) {
				return;
			}
			$product_archive_title_options = $breadcrumbs_options[ 'breadcrumbs_title' ][ $catalog_id ];
			if ( $product_archive_title_options != '' ) {
				$product_archive_title = $product_archive_title_options;
			} else {
				$product_archive_title = get_the_title( $archives_ids[ $catalog_id ] );
			}
		} else {
			$archive_multiple_settings = get_multiple_settings();
			if ( empty( $archive_multiple_settings[ 'enable_product_breadcrumbs' ] ) || !empty( $archive_multiple_settings[ 'enable_product_breadcrumbs' ] ) && $archive_multiple_settings[ 'enable_product_breadcrumbs' ] != 1 ) {
				return;
			}

			$product_archive = product_listing_url();
			if ( $archive_multiple_settings[ 'breadcrumbs_title' ] != '' ) {
				$product_archive_title = $archive_multiple_settings[ 'breadcrumbs_title' ];
			} else {
				$product_archive_title = get_product_listing_title();
			}
		}
		$additional = '';
		if ( is_ic_product_page() ) {
			$current_product = get_the_title();
		} else if ( is_ic_taxonomy_page() ) {
			$obj				 = get_queried_object();
			$current_product	 = $obj->name;
			$taxonomy			 = isset( $obj->taxonomy ) ? $obj->taxonomy : 'al_product-cat';
			$current_category_id = $obj->term_id;
			$parents			 = array_filter( explode( '|', ic_get_product_category_parents( $current_category_id, $taxonomy, true, '|' ) ) );
			array_pop( $parents );
			foreach ( $parents as $parent ) {
				if ( !empty( $parent ) ) {
					$additional .= ' » <span typeof="v:Breadcrumb">
		<span class="breadcrumb_last" property="v:title">' . $parent . '</span>
	</span>';
				}
			}
		} else if ( is_search() ) {
			$current_product = __( 'Product Search', 'al-ecommerce-product-catalog' );
		} else {
			$current_product = '';
		}
		$bread = '<p id="breadcrumbs"><span xmlns:v="http://rdf.data-vocabulary.org/#"><span typeof="v:Breadcrumb"><a href="' . $home_page . '" rel="v:url" property="v:title">' . __( 'Home', 'al-ecommerce-product-catalog' ) . '</a></span>';
		if ( !empty( $product_archive ) ) {
			$bread .= ' » <span typeof="v:Breadcrumb"><a href="' . $product_archive . '" rel="v:url" property="v:title">' . $product_archive_title . '</a></span>';
		}
		if ( !empty( $additional ) ) {
			$bread .= $additional;
		}
		if ( !empty( $current_product ) ) {
			$bread .= ' » <span typeof="v:Breadcrumb"><span class="breadcrumb_last" property="v:title">' . $current_product . '</span></span></span>';
		}
		$bread .= '</p>';
		return $bread;
	}
}

function ic_get_product_category_parents( $id, $taxonomy, $link = false, $separator = '/', $nicename = false,
										  $visited = array() ) {
	$chain	 = '';
	$parent	 = get_term( $id, $taxonomy );

	if ( is_wp_error( $parent ) ) {
		return $parent;
	}

	if ( $nicename )
		$name	 = $parent->slug;
	else
		$name	 = $parent->name;

	if ( $parent->parent && ($parent->parent != $parent->term_id) && !in_array( $parent->parent, $visited ) ) {
		$visited[] = $parent->parent;
		$chain .= ic_get_product_category_parents( $parent->parent, $taxonomy, $link, $separator, $nicename, $visited );
	}

	if ( !$link ) {
		$chain .= $name . $separator;
	} else {
		$url = get_term_link( $parent );
		$chain .= '<a href="' . $url . '">' . $name . '</a>' . $separator;
	}
	return $chain;
}

function get_product_name( $product_id = null ) {
	return get_the_title( $product_id );
}

function get_product_url( $product_id = null ) {
	return get_permalink( $product_id );
}

add_action( 'single_product_begin', 'add_product_breadcrumbs' );
add_action( 'product_listing_begin', 'add_product_breadcrumbs' );

/**
 * Shows item breadcrumbs
 *
 */
function add_product_breadcrumbs() {
	echo product_breadcrumbs();
}

function al_product_register_widgets() {
	register_widget( 'product_cat_widget' );
	register_widget( 'product_widget_search' );
	do_action( 'implecode_register_widgets' );
}

add_action( 'widgets_init', 'al_product_register_widgets' );

if ( !function_exists( 'permalink_options_update' ) ) {

	/**
	 * Updates the permalink rewrite option that triggers the rewrite function
	 */
	function permalink_options_update() {
		update_option( 'al_permalink_options_update', 1 );
	}

}
if ( !function_exists( 'check_permalink_options_update' ) ) {

	/**
	 * Checks if the permalinks should be rewritten and does it if necessary
	 */
	function check_permalink_options_update() {
		$options_update = get_option( 'al_permalink_options_update', 'none' );
		if ( $options_update != 'none' ) {
			flush_rewrite_rules();
			update_option( 'al_permalink_options_update', 'none' );
		}
	}

}

add_action( 'init', 'check_permalink_options_update', 99 );

function is_lightbox_enabled() {
	$enable_catalog_lightbox = get_option( 'catalog_lightbox', 1 );
	$return					 = false;
	if ( $enable_catalog_lightbox == 1 ) {
		$return = true;
	}
	return apply_filters( 'is_lightbox_enabled', $return );
}

add_action( 'before_product_details', 'show_product_gallery', 10, 2 );

/**
 * Shows item gallery on item page
 *
 * @param int $product_id
 * @param array $single_options
 * @return string
 */
function show_product_gallery( $product_id, $single_options ) {
	if ( $single_options[ 'enable_product_gallery' ] == 1 ) {
		echo get_product_gallery( $product_id, $single_options );
	} else {
		return;
	}
}

/**
 * Returns whole item gallery for item page
 *
 * @param int $product_id
 * @param array $v_single_options
 * @return string
 */
function get_product_gallery( $product_id, $v_single_options = null ) {
	$single_options = isset( $v_single_options ) ? $v_single_options : get_product_page_settings();
	if ( $single_options[ 'enable_product_gallery' ] == 1 ) {
		$product_gallery = '';
		ob_start();
		do_action( 'before_product_image' );
		$product_gallery .= ob_get_clean();
		$product_gallery .= '<div class="entry-thumbnail product-image">';
		ob_start();
		do_action( 'above_product_image' );
		$product_gallery .= ob_get_clean();
		$image_size		 = apply_filters( 'product_image_size', 'medium' );
		if ( has_post_thumbnail( $product_id ) ) {
			if ( is_lightbox_enabled() ) {
				$img_url = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'large' );
				$product_gallery .= '<a class="a-product-image" href="' . $img_url[ 0 ] . '">' . get_the_post_thumbnail( $product_id, $image_size ) . '</a>';
			} else {
				$product_gallery .= get_the_post_thumbnail( $product_id, $image_size );
			}
		} else if ( $single_options[ 'enable_product_gallery_only_when_exist' ] != 1 ) {
			$product_gallery .= default_product_thumbnail();
		}
		ob_start();
		do_action( 'below_product_image', $product_id );
		$product_gallery .= ob_get_clean();
		$product_gallery .= '</div>';
		ob_start();
		do_action( 'after_product_image', $product_id );
		$product_gallery .= ob_get_clean();
		return $product_gallery;
	} else {
		return;
	}
}

function product_gallery_enabled( $enable, $enable_inserted, $post ) {
	$details_class = 'no-image';
	if ( $enable == 1 ) {
		if ( $enable_inserted == 1 && !has_post_thumbnail() ) {
			return $details_class;
		} else {
			return;
		}
	} else {
		return $details_class;
	}
}

function product_post_type_array() {
	$array = apply_filters( 'product_post_type_array', array( 'al_product' ) );
	return $array;
}

function product_taxonomy_array() {
	$array = apply_filters( 'product_taxonomy_array', array( 'al_product-cat' ) );
	return $array;
}

function array_to_url( $array ) {
	$url = urlencode( serialize( $array ) );
	return $url;
}

function url_to_array( $url ) {
	$array = unserialize( stripslashes( urldecode( $url ) ) );
	return $array;
}

function exclude_products_search( $search, &$wp_query ) {
	global $wpdb;
	if ( empty( $search ) )
		return $search;
	$search .= " AND (($wpdb->posts.post_type NOT LIKE '%al_product%'))";
	return $search;
}

function modify_product_search( $query ) {
	if ( !is_admin() && $query->is_search == 1 && $query->is_main_query() && ((isset( $_GET[ 'post_type' ] ) && strpos( $_GET[ 'post_type' ], 'al_product' ) === false) || (!isset( $query->query_vars[ 'post_type' ] ) || (isset( $query->query_vars[ 'post_type' ] ) && strpos( $query->query_vars[ 'post_type' ], 'al_product' ) === false ))) ) {
		add_filter( 'posts_search', 'exclude_products_search', 10, 2 );
	}
}

add_action( 'pre_get_posts', 'modify_product_search', 10, 1 );
add_action( 'wp', 'modify_product_listing_title_tag', 99 );

function modify_product_listing_title_tag() {
	if ( is_ic_product_listing() ) {
		add_filter( 'wp_title', 'product_archive_title', 99, 3 );
		add_filter( 'wp_title', 'product_archive_custom_title', 99, 3 );
	}
}

/**
 * Modifies main item listing title tag
 *
 * @global type $post
 * @param type $title
 * @param type $sep
 * @param type $seplocation
 * @return type
 */
function product_archive_custom_title( $title = null, $sep = null, $seplocation = null ) {
	global $post;
	if ( is_post_type_archive( 'al_product' ) && is_object( $post ) && $post->post_type == 'al_product' ) {
		$settings = get_multiple_settings();
		if ( $settings[ 'seo_title' ] != '' ) {
			$settings					 = get_option( 'archive_multiple_settings', unserialize( DEFAULT_ARCHIVE_MULTIPLE_SETTINGS ) );
			$settings[ 'seo_title' ]	 = isset( $settings[ 'seo_title' ] ) ? $settings[ 'seo_title' ] : '';
			$settings[ 'seo_title_sep' ] = isset( $settings[ 'seo_title_sep' ] ) ? $settings[ 'seo_title_sep' ] : '';
			if ( $settings[ 'seo_title_sep' ] == 1 ) {
				if ( $sep != '' ) {
					$sep = ' ' . $sep . ' ';
				}
			} else {
				$sep = '';
			}
			if ( $seplocation == 'right' ) {
				$title = $settings[ 'seo_title' ] . $sep;
			} else {
				$title = $sep . $settings[ 'seo_title' ];
			}
		}
	}
	return $title;
}

function product_archive_title( $title = null, $sep = null, $seplocation = null ) {
	global $post;
	if ( is_ic_product_listing() && is_object( $post ) && $post->post_type == 'al_product' ) {
		$settings = get_multiple_settings();
		if ( $settings[ 'seo_title' ] == '' ) {
			$id = get_product_listing_id();
			if ( !empty( $id ) ) {
				$title = get_single_post_title( $id, $sep, $seplocation );
			}
		}
	}
	return $title;
}

function get_single_post_title( $post_id, $sep, $seplocation ) {
	global $wp_query;
	$wp_query	 = new WP_Query( 'page_id=' . $post_id );
	remove_filter( 'wp_title', 'product_archive_title', 99, 3 );
	$title		 = wp_title( $sep, false, $seplocation );
	wp_reset_query();
	return $title;
}

function add_support_link( $links, $file ) {

	$plugin = plugin_basename( AL_PLUGIN_MAIN_FILE );

	// create link
	if ( $file == $plugin ) {
		return array_merge(
		$links, array( sprintf( '<a href="edit.php?post_type=al_product&page=product-settings.php&tab=product-settings&submenu=support">%s</a>', __( 'Support' ) ) )
		);
	}

	return $links;
}

add_filter( 'plugin_row_meta', 'add_support_link', 10, 2 );

function implecode_al_box( $text, $type = 'info' ) {
	echo '<div class="al-box ' . $type . '">';
	echo $text;
	echo '</div>';
}

function get_product_image_id( $attachment_url = '' ) {
	global $wpdb;
	$attachment_id = false;
	if ( '' == $attachment_url ) {
		return;
	}
	$upload_dir_paths = wp_upload_dir();
	if ( false !== strpos( $attachment_url, $upload_dir_paths[ 'baseurl' ] ) ) {
		$attachment_url	 = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
		$attachment_url	 = str_replace( $upload_dir_paths[ 'baseurl' ] . '/', '', $attachment_url );
		$attachment_id	 = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
	}
	return $attachment_id;
}

function get_product_image_url( $product_id ) {
	$img_url = wp_get_attachment_image_src( get_post_thumbnail_id( $product_id ), 'large' );
	if ( !$img_url ) {
		$img_url[ 0 ] = default_product_thumbnail_url();
	}
	return $img_url[ 0 ];
}

/**
 * Returns all items array
 * @return array
 */
function get_all_catalog_products( $orderby = null ) {
	$args = array(
		'post_type'		 => product_post_type_array(),
		'post_status'	 => 'publish',
		'posts_per_page' => -1,
	);
	if ( !empty( $orderby ) ) {
		$args[ 'orderby' ] = $orderby;
	}
	$products = get_posts( $args );
	return $products;
}

function all_ctalog_products_dropdown( $option_name, $first_option, $selected_value ) {
	$pages		 = get_all_catalog_products();
	$select_box	 = '<select class="all_products_dropdown" id="' . $option_name . '" name="' . $option_name . '"><option value="noid">' . $first_option . '</option>';
	foreach ( $pages as $page ) {
		$select_box .= '<option class="id_' . $page->ID . '" name="' . $option_name . '[' . $page->ID . ']" value="' . $page->ID . '" ' . selected( $page->ID, $selected_value, 0 ) . '>' . $page->post_title . '</option>';
	}
	$select_box .= '</select>';
	return $select_box;
}

function thumbnail_support_products() {
	$support		 = get_theme_support( 'post-thumbnails' );
	$support_array	 = product_post_type_array();
	if ( is_array( $support ) ) {
		$support_array = array_merge( $support[ 0 ], $support_array );
		add_theme_support( 'post-thumbnails', $support_array );
	} else if ( !$support ) {
		add_theme_support( 'post-thumbnails', $support_array );
	} else {
		add_theme_support( 'post-thumbnails' );
	}
}

add_action( 'after_setup_theme', 'thumbnail_support_products', 99 );
add_action( 'pre_get_posts', 'set_product_order' );

/**
 * Sets default item order
 *
 * @param object $query
 */
function set_product_order( $query ) {
	if ( !is_admin() && !isset( $_GET[ 'order' ] ) && $query->is_main_query() && (is_ic_product_listing( $query ) || is_ic_taxonomy_page()) ) {
		$archive_multiple_settings = get_multiple_settings();
		if ( !isset( $_GET[ 'product_order' ] ) ) {
			if ( $archive_multiple_settings[ 'product_order' ] == 'product-name' ) {
				$query->set( 'orderby', 'title' );
				$query->set( 'order', 'ASC' );
			}
			$query = apply_filters( 'modify_product_order', $query, $archive_multiple_settings );
		} else if ( $_GET[ 'product_order' ] != 'newest' && !empty( $_GET[ 'product_order' ] ) ) {
			$orderby = translate_product_order();
			$query->set( 'orderby', $orderby );
			$query->set( 'order', 'ASC' );
			$query	 = apply_filters( 'modify_product_order-dropdown', $query, $archive_multiple_settings );
		}
	}
}

add_filter( 'shortcode_query', 'set_shortcode_product_order' );
add_filter( 'home_product_listing_query', 'set_shortcode_product_order' );

function set_shortcode_product_order( $shortcode_query ) {
	$archive_multiple_settings = get_multiple_settings();
	if ( !isset( $_GET[ 'product_order' ] ) ) {
		if ( $archive_multiple_settings[ 'product_order' ] == 'product-name' ) {
			$shortcode_query[ 'orderby' ]	 = 'title';
			$shortcode_query[ 'order' ]		 = 'ASC';
		}
		$shortcode_query = apply_filters( 'shortcode_modify_product_order', $shortcode_query, $archive_multiple_settings );
	} else if ( $_GET[ 'product_order' ] != 'newest' && !empty( $_GET[ 'product_order' ] ) ) {
		$orderby						 = translate_product_order();
		$shortcode_query[ 'orderby' ]	 = $orderby;
		$shortcode_query[ 'order' ]		 = 'ASC';
		$shortcode_query				 = apply_filters( 'shortcode_modify_product_order-dropdown', $shortcode_query, $archive_multiple_settings );
	}
	return $shortcode_query;
}

function translate_product_order() {
	$orderby = ($_GET[ 'product_order' ] == 'product-name') ? 'title' : $_GET[ 'product_order' ];
	$orderby = apply_filters( 'product_order_translate', $orderby );
	return $orderby;
}

function ic_products_count() {
	$count = wp_count_posts( 'al_product' );
	return $count->publish;
}

/**
 * Returns per row setting for current item listing theme
 * @return int
 */
function get_current_per_row() {
	$archive_template	 = get_product_listing_template();
	$per_row			 = 3;
	if ( $archive_template == 'default' ) {
		$settings	 = get_modern_grid_settings();
		$per_row	 = $settings[ 'per-row' ];
	} else if ( $archive_template == 'grid' ) {
		$settings	 = get_classic_grid_settings();
		$per_row	 = $settings[ 'entries' ];
	}
	return apply_filters( 'current_per_row', $per_row, $archive_template );
}

function get_current_screen_tax() {
	$obj		 = get_queried_object();
	$taxonomies	 = array();
	if ( isset( $obj->ID ) ) {
		$taxonomies = get_object_taxonomies( $obj );
	} else if ( isset( $obj->taxonomies ) ) {
		$taxonomies = $obj->taxonomies;
	} else if ( isset( $obj->taxonomy ) ) {
		$taxonomies = array( $obj->taxonomy );
	}
	foreach ( $taxonomies as $tax ) {
		if ( strpos( $tax, 'al_product-cat' ) !== false ) {
			return $tax;
		}
	}
	return 'al_product-cat';
}

function get_current_screen_post_type( $true = false ) {
	$obj		 = get_queried_object();
	$post_type	 = 'al_product';
	if ( isset( $obj->post_type ) && strpos( $obj->post_type, 'al_product' ) !== false ) {
		$post_type = $obj->post_type;
	} else if ( isset( $obj->name ) && strpos( $obj->name, 'al_product' ) !== false ) {
		$post_type = $obj->name;
	} else if ( isset( $_GET[ 'post_type' ] ) && strpos( $_GET[ 'post_type' ], 'al_product' ) !== false ) {
		$post_type = $_GET[ 'post_type' ];
	}
	return $post_type;
}
