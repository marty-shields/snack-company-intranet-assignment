<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages item post type
 *
 * Here all item fields are defined.
 *
 * @version        1.1.1
 * @package        post-type-x/includes
 * @author        Norbert Dreszer
 */
add_action( 'register_catalog_styles', 'frontend_scripts' );

/**
 * Registers item related front-end scripts
 */
function frontend_scripts() {
	if ( !is_admin() ) {
		if ( is_lightbox_enabled() && is_ic_product_gallery_enabled() ) {
			wp_register_script( 'colorbox', AL_PLUGIN_BASE_PATH . 'js/colorbox/jquery.colorbox-min.js', array( 'jquery' ) );
			wp_register_style( 'colorbox', AL_PLUGIN_BASE_PATH . 'js/colorbox/colorbox.css' );
			wp_register_script( 'al_product_scripts', AL_PLUGIN_BASE_PATH . 'js/product.js?' . filemtime( AL_BASE_PATH . '/js/product.js' ), array( 'jquery', 'colorbox' ) );
		} else {
			wp_register_script( 'al_product_scripts', AL_PLUGIN_BASE_PATH . 'js/product.js?' . filemtime( AL_BASE_PATH . '/js/product.js' ), array( 'jquery' ) );
		}
	}
}

add_action( 'init', 'create_product' );

/**
 * Registers items post type
 * @global type $wp_version
 */
function create_product() {
	global $wp_version;
	$slug = get_product_slug();
	if ( is_ic_product_listing_enabled() && get_integration_type() != 'simple' ) {
		$product_listing_t = $slug;
	} else {
		$product_listing_t = false;
	}
	$names		 = get_catalog_names();
	$query_var	 = get_product_query_var();
	if ( is_plural_form_active() ) {
		$labels = array(
			'name'				 => $names[ 'plural' ],
			'singular_name'		 => $names[ 'singular' ],
			'add_new'			 => sprintf( __( 'Add New %s', 'post-type-x' ), ucfirst( $names[ 'singular' ] ) ),
			'add_new_item'		 => sprintf( __( 'Add New %s', 'post-type-x' ), ucfirst( $names[ 'singular' ] ) ),
			'edit_item'			 => sprintf( __( 'Edit %s', 'post-type-x' ), ucfirst( $names[ 'singular' ] ) ),
			'new_item'			 => sprintf( __( 'Add New %s', 'post-type-x' ), ucfirst( $names[ 'singular' ] ) ),
			'view_item'			 => sprintf( __( 'View %s', 'post-type-x' ), ucfirst( $names[ 'singular' ] ) ),
			'search_items'		 => sprintf( __( 'Search %s', 'post-type-x' ), ucfirst( $names[ 'plural' ] ) ),
			'not_found'			 => sprintf( __( 'No %s found', 'post-type-x' ), $names[ 'plural' ] ),
			'not_found_in_trash' => sprintf( __( 'No %s found in trash', 'post-type-x' ), $names[ 'plural' ] )
		);
	} else {
		$labels = array(
			'name'				 => $names[ 'plural' ],
			'singular_name'		 => $names[ 'singular' ],
			'add_new'			 => __( 'Add New', 'post-type-x' ),
			'add_new_item'		 => __( 'Add New Item', 'post-type-x' ),
			'edit_item'			 => __( 'Edit Item', 'post-type-x' ),
			'new_item'			 => __( 'Add New Item', 'post-type-x' ),
			'view_item'			 => __( 'View Item', 'post-type-x' ),
			'search_items'		 => __( 'Search Items', 'post-type-x' ),
			'not_found'			 => __( 'Nothing found', 'post-type-x' ),
			'not_found_in_trash' => __( 'Nothing found in trash', 'post-type-x' )
		);
	}
	if ( $wp_version < 3.8 ) {
		$reg_settings = array(
			'labels'				 => $labels,
			'public'				 => true,
			'has_archive'			 => $product_listing_t,
			'rewrite'				 => array( 'slug' => apply_filters( 'product_slug_value_register', $slug ), 'with_front' => false ),
			'query_var'				 => $query_var,
			'supports'				 => array( 'title', 'thumbnail' ),
			'register_meta_box_cb'	 => 'add_product_metaboxes',
			'taxonomies'			 => array( 'al_product_cat' ),
			'menu_icon'				 => plugins_url() . '/post-type-x/img/product.png',
			'capability_type'		 => 'product',
			'capabilities'			 => array(
				'publish_posts'			 => 'publish_products',
				'edit_posts'			 => 'edit_products',
				'edit_others_posts'		 => 'edit_others_products',
				'edit_published_posts'	 => 'edit_published_products',
				'edit_private_posts'	 => 'edit_private_products',
				'delete_posts'			 => 'delete_products',
				'delete_others_posts'	 => 'delete_others_products',
				'delete_private_posts'	 => 'delete_private_products',
				'delete_published_posts' => 'delete_published_products',
				'read_private_posts'	 => 'read_private_products',
				'edit_post'				 => 'edit_product',
				'delete_post'			 => 'delete_product',
				'read_post'				 => 'read_product',
			),
			'exclude_from_search'	 => false,
		);
	} else {
		$reg_settings = array(
			'labels'				 => $labels,
			'public'				 => true,
			'has_archive'			 => $product_listing_t,
			'rewrite'				 => array( 'slug' => apply_filters( 'product_slug_value_register', $slug ), 'with_front' => false ),
			'query_var'				 => $query_var,
			'supports'				 => array( 'title', 'thumbnail' ),
			'register_meta_box_cb'	 => 'add_product_metaboxes',
			'taxonomies'			 => array( 'al_product-cat' ),
			'capability_type'		 => 'product',
			'capabilities'			 => array(
				'publish_posts'			 => 'publish_products',
				'edit_posts'			 => 'edit_products',
				'edit_others_posts'		 => 'edit_others_products',
				'edit_published_posts'	 => 'edit_published_products',
				'edit_private_posts'	 => 'edit_private_products',
				'delete_posts'			 => 'delete_products',
				'delete_others_posts'	 => 'delete_others_products',
				'delete_private_posts'	 => 'delete_private_products',
				'delete_published_posts' => 'delete_published_products',
				'read_private_posts'	 => 'read_private_products',
				'edit_post'				 => 'edit_product',
				'delete_post'			 => 'delete_product',
				'read_post'				 => 'read_product',
			),
			'exclude_from_search'	 => false,
		);
	}
	register_post_type( 'al_product', $reg_settings );
}

function get_product_query_var() {
	$names		 = get_catalog_names();
	$query_var	 = sanitize_title( strtolower( $names[ 'singular' ] ) );
	$query_var	 = (strpos( $query_var, '%' ) !== false) ? __( 'product', 'post-type-x' ) : $query_var;
	return apply_filters( 'product_query_var', $query_var );
}

function product_icons() {
	global $post_type;
	?>
	<style>
	<?php if ( isset( $_GET[ 'post_type' ] ) == 'al_product' ) : ?>
			#icon-edit {
				background: transparent url('<?php echo plugins_url() . '/post-type-x/img/product-32.png'; ?>') no-repeat;
			}

	<?php endif; ?>
	</style>
	<?php
}

add_action( 'admin_head', 'product_icons' );

function add_product_metaboxes() {
	$names				 = get_catalog_names();
	$names[ 'singular' ] = ucfirst( $names[ 'singular' ] );
	add_meta_box( 'al_product_short_desc', sprintf( __( '%s Short Description', 'post-type-x' ), $names[ 'singular' ] ), 'al_product_short_desc', 'al_product', apply_filters( 'short_desc_box_column', 'normal' ), apply_filters( 'short_desc_box_priority', 'default' ) );
	add_meta_box( 'al_product_desc', sprintf( __( '%s description', 'post-type-x' ), $names[ 'singular' ] ), 'al_product_desc', 'al_product', apply_filters( 'desc_box_column', 'normal' ), apply_filters( 'desc_box_priority', 'default' ) );
	if ( (function_exists( 'is_ic_price_enabled' ) && is_ic_price_enabled()) || (function_exists( 'is_ic_sku_enabled' ) && is_ic_sku_enabled()) ) {
		add_meta_box( 'al_product_price', sprintf( __( '%s Details', 'post-type-x' ), $names[ 'singular' ] ), 'al_product_price', 'al_product', apply_filters( 'product_price_box_column', 'side' ), apply_filters( 'product_price_box_priority', 'default' ) );
	}
	do_action( 'add_product_metaboxes', $names );
}

if ( !function_exists( 'al_product_price' ) ) {

	function al_product_price() {
		global $post;
		echo '<input type="hidden" name="pricemeta_noncename" id="pricemeta_noncename" value="' .
		wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
		$price_table = '';
		echo apply_filters( 'admin_product_details', $price_table, $post->ID );
	}

}

function al_product_short_desc() {
	global $post;
	echo '<input type="hidden" name="shortdescmeta_noncename" id="shortdescmeta_noncename" value="' .
	wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
	$shortdesc			 = get_product_short_description( $post->ID );
	$short_desc_settings = array( 'media_buttons'	 => false, 'textarea_rows'	 => 5, 'tinymce'		 => array(
			'menubar'	 => false,
			'toolbar1'	 => 'bold,italic,underline,blockquote,strikethrough,bullist,numlist,alignleft,aligncenter,alignright,undo,redo,link,unlink,fullscreen',
			'toolbar2'	 => '',
			'toolbar3'	 => '',
			'toolbar4'	 => '',
		) );
	wp_editor( $shortdesc, 'excerpt', $short_desc_settings );
}

function al_product_desc() {
	global $post;
	echo '<input type="hidden" name="descmeta_noncename" id="descmeta_noncename" value="' .
	wp_create_nonce( plugin_basename( __FILE__ ) ) . '" />';
	$desc			 = get_product_description( $post->ID );
	$desc_settings	 = array( 'textarea_rows' => 30 );
	wp_editor( $desc, 'content', $desc_settings );
}

function implecode_save_products_meta( $post_id, $post ) {
	$post_type_now = substr( $post->post_type, 0, 10 );
	if ( $post_type_now == 'al_product' ) {
		$pricemeta_noncename = isset( $_POST[ 'pricemeta_noncename' ] ) ? $_POST[ 'pricemeta_noncename' ] : '';
		if ( !empty( $pricemeta_noncename ) && !wp_verify_nonce( $pricemeta_noncename, plugin_basename( __FILE__ ) ) ) {
			return $post->ID;
		}
		if ( !isset( $_POST[ 'action' ] ) ) {
			return $post->ID;
		} else if ( isset( $_POST[ 'action' ] ) && $_POST[ 'action' ] != 'editpost' ) {
			return $post->ID;
		}
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post->ID;
		}
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $post->ID;
		}
		if ( !current_user_can( 'edit_post', $post->ID ) )
			return $post->ID;
		$product_meta[ 'excerpt' ]	 = !empty( $_POST[ 'excerpt' ] ) ? $_POST[ 'excerpt' ] : '';
		$product_meta[ 'content' ]	 = !empty( $_POST[ 'content' ] ) ? $_POST[ 'content' ] : '';

		$product_meta = apply_filters( 'product_meta_save', $product_meta );
		foreach ( $product_meta as $key => $value ) {
			$current_value = get_post_meta( $post->ID, $key, true );
			if ( isset( $value ) && !isset( $current_value ) ) {
				add_post_meta( $post->ID, $key, $value, true );
			} else if ( isset( $value ) && $value != $current_value ) {
				update_post_meta( $post->ID, $key, $value );
			} else if ( !isset( $value ) && $current_value ) {
				delete_post_meta( $post->ID, $key );
			}
		}
		do_action( 'product_edit_save', $post );
	}
}

add_action( 'post_updated', 'implecode_save_products_meta', 1, 2 );

add_action( 'do_meta_boxes', 'change_image_box' );

function change_image_box() {
	$names = get_catalog_names();
	remove_meta_box( 'postimagediv', 'al_product', 'side' );
	add_meta_box( 'postimagediv', sprintf( __( '%s Image', 'post-type-x' ), ucfirst( $names[ 'singular' ] ) ), 'post_thumbnail_meta_box', 'al_product', apply_filters( 'product_image_box_column', 'side' ), apply_filters( 'product_image_box_priority', 'high' ) );
}

function change_thumbnail_html( $content ) {
	if ( is_ic_catalog_admin_page() ) {
		add_filter( 'admin_post_thumbnail_html', 'modify_add_product_image_label' );
	}
}

add_action( 'admin_head-post-new.php', 'change_thumbnail_html' );
add_action( 'admin_head-post.php', 'change_thumbnail_html' );

function modify_add_product_image_label( $label ) {
	if ( is_plural_form_active() ) {
		$names				 = get_catalog_names();
		$names[ 'singular' ] = strtolower( $names[ 'singular' ] );
		$label				 = str_replace( __( 'Set featured image' ), sprintf( __( 'Set %s image', 'post-type-x' ), $names[ 'singular' ] ), $label );
		$label				 = str_replace( __( 'Remove featured image' ), sprintf( __( 'Remove %s image', 'post-type-x' ), $names[ 'singular' ] ), $label );
	} else {
		$label	 = str_replace( __( 'Set featured image' ), __( 'Set image', 'post-type-x' ), $label );
		$label	 = str_replace( __( 'Remove featured image' ), __( 'Remove image', 'post-type-x' ), $label );
	}
	return $label;
}

function set_product_messages( $messages ) {
	global $post, $post_ID;
	$quasi_post_type = get_quasi_post_type();
	$post_type		 = get_post_type( $post_ID );
	if ( $quasi_post_type == 'al_product' ) {
		$obj		 = get_post_type_object( $post_type );
		$singular	 = $obj->labels->singular_name;

		$messages[ $post_type ] = array(
			0	 => '',
			1	 => sprintf( __( '%s updated. <a href="%s">View ' . strtolower( $singular ) . '</a>' ), $singular, esc_url( get_permalink( $post_ID ) ) ),
			2	 => __( 'Custom field updated.' ),
			3	 => __( 'Custom field deleted.' ),
			4	 => sprintf( __( '%s updated.', 'post-type-x' ), $singular ),
			5	 => isset( $_GET[ 'revision' ] ) ? sprintf( __( $singular . ' restored to revision from %s' ), $singular, wp_post_revision_title( (int) $_GET[ 'revision' ], false ) ) : false,
			6	 => sprintf( __( $singular . ' published. <a href="%s">View ' . strtolower( $singular ) . '</a>' ), esc_url( get_permalink( $post_ID ) ), $singular ),
			7	 => __( 'Page saved.' ),
			8	 => sprintf( __( '%s submitted. <a target="_blank" href="%s">Preview %s</a>' ), $singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ), strtolower( $singular ) ),
			9	 => sprintf( __( '%3$s scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview ' . strtolower( $singular ) . '</a>' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ), $singular ),
			10	 => sprintf( __( '%s draft updated. <a target="_blank" href="%s">Preview ' . strtolower( $singular ) . '</a>' ), $singular, esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);
	}
	return $messages;
}

add_filter( 'post_updated_messages', 'set_product_messages' );

/**
 * Returns item description
 *
 * @param int $product_id
 * @return string
 */
function get_product_description( $product_id ) {
	$product_desc = get_post_meta( $product_id, 'content', true );
	return apply_filters( 'get_product_description', $product_desc, $product_id );
}

/**
 * Returns item short description
 *
 * @param int $product_id
 * @return string
 */
function get_product_short_description( $product_id ) {
	$product_desc = get_post_meta( $product_id, 'excerpt', true );
	return apply_filters( 'get_product_short_description', $product_desc, $product_id );
}

require_once(AL_BASE_PATH . '/includes/product-categories.php');
