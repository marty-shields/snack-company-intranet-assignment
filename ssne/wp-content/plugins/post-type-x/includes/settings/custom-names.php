<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages custom names settings
 *
 * Here custom names settings are defined and managed.
 *
 * @version		1.1.4
 * @package		post-type-x/functions
 * @author 		Norbert Dreszer
 */
function default_single_names() {
	$single_names = array(
		'return_to_archive' => __( '<< return to items', 'post-type-x' ),
	);

	return apply_filters( 'default_single_names', $single_names );
}

/**
 * Defines default labels for item listing pages
 *
 * @return array
 */
function default_archive_names() {
	$archive_names = array(
		'all_products'			 => __( 'All Items', 'post-type-x' ),
		'all_prefix'			 => __( 'All', 'post-type-x' ),
		'all_main_categories'	 => __( 'Main Categories', 'post-type-x' ),
		'all_subcategories'		 => '[product_category_name] ' . __( 'subcategories', 'post-type-x' ),
		'category_products'		 => '[product_category_name] ' . __( 'items', 'post-type-x' )
	);

	return $archive_names;
}

function custom_names_menu() {
	?>
	<a id="names-settings" class="nav-tab" href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php&tab=names-settings&submenu=single-names' ) ?>	"><?php _e( 'Front-end Labels', 'post-type-x' ); ?></a>
	<?php
}

add_action( 'settings-menu', 'custom_names_menu' );

function custom_names_settings() {
	register_setting( 'product_names_archive', 'archive_names' );
	register_setting( 'product_names_single', 'single_names' );
}

add_action( 'product-settings-list', 'custom_names_settings' );

function custom_names_content() {
	?>
	<div class="names-product-settings settings-wrapper"> <?php
		$tab	 = $_GET[ 'tab' ];
		$submenu = $_GET[ 'submenu' ];
		?>
		<div class="settings-submenu">
			<h3>
				<a id="single-names" class="element current" href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php&tab=names-settings&submenu=single-names' ) ?>"><?php _e( 'Item Page', 'post-type-x' ); ?></a>
				<a id="archive-names" class="element" href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php&tab=names-settings&submenu=archive-names' ) ?>"><?php _e( 'Item Listings', 'post-type-x' ); ?></a>
			</h3>
		</div><?php if ( $submenu == 'single-names' ) { ?>
			<div id="single_names" class="setting-content submenu">
				<script>
		            jQuery( '.settings-submenu a' ).removeClass( 'current' );
		            jQuery( '.settings-submenu a#single-names' ).addClass( 'current' );
				</script>
				<form method="post" action="options.php">
					<?php
					settings_fields( 'product_names_single' );
					$single_names = get_single_names();
					?>
					<h2><?php _e( 'Front-end Labels', 'post-type-x' ); ?></h2>
					<h3><?php _e( 'Item Page Labels', 'post-type-x' ); ?></h3>
					<table class="wp-list-table widefat product-settings-table" style="clear:right; text-align: left;">
						<thead><th><strong><?php _e( 'Front-end Element', 'post-type-x' ); ?></strong></th><th><strong><?php _e( 'Front-end Text', 'post-type-x' ); ?></strong></th></thead>
						<tbody>
							<tr><td><?php _e( 'Return to Items', 'post-type-x' ); ?></td><td><input type="text" name="single_names[return_to_archive]" value="<?php echo $single_names[ 'return_to_archive' ]; ?>" /></td></tr>
							<?php do_action( 'single_names_table', $single_names ) ?>
						</tbody>
					</table>
					<p class="submit">
						<input type="submit" class="button-primary" value="<?php _e( 'Save changes', 'post-type-x' ); ?>" />
					</p>
				</form>
			</div>
			<div class="helpers"><div class="wrapper"><?php main_helper(); ?>
				</div></div><?php
		} else if ( $submenu == 'archive-names' ) {
			?>
			<div id="archive_names" class="setting-content submenu">
				<script>
		            jQuery( '.settings-submenu a' ).removeClass( 'current' );
		            jQuery( '.settings-submenu a#archive-names' ).addClass( 'current' );
				</script>
				<form method="post" action="options.php"><?php
					settings_fields( 'product_names_archive' );
					$archive_names	 = get_archive_names();
					?>
					<h2><?php _e( 'Front-end Labels', 'post-type-x' ); ?></h2><?php
					$disabled		 = '';
					if ( get_integration_type() == 'simple' ) {
						$disabled = 'disabled';
						if ( is_integration_mode_selected() ) {
							implecode_warning( sprintf( __( 'Item listing pages are disabled with simple theme integration. See <a href="%s">Theme Integration Guide</a> to enable item listing pages.', 'post-type-x' ), 'https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=simple-mode&key=front-labels' ) );
						} else {
							implecode_warning( sprintf( __( 'Item listing pages are disabled due to a lack of theme integration.%s', 'post-type-x' ), sample_product_button( 'p' ) ) );
						}
					}
					?>
					<h3><?php _e( 'Item Listing Labels', 'post-type-x' ); ?></h3>
					<table class="wp-list-table widefat product-settings-table" style="clear:right; text-align: left; width: 100%;">
						<style>.names-product-settings .setting-content th {text-align: left;}</style>
						<thead><th><strong><?php _e( 'Front-end Element', 'post-type-x' ); ?></strong></th><th style="width:69%"><strong><?php _e( 'Front-end Text', 'post-type-x' ); ?></strong></th></thead>
						<tbody>
							<?php
							implecode_settings_text( __( 'Item Archive Title', 'post-type-x' ), 'archive_names[all_products]', $archive_names[ 'all_products' ], null, 1, 'wide', null, $disabled );
							implecode_settings_text( __( 'Categories Header', 'post-type-x' ), 'archive_names[all_main_categories]', $archive_names[ 'all_main_categories' ], null, 1, 'wide', null, $disabled );
							implecode_settings_text( __( 'Subcategories Header', 'post-type-x' ), 'archive_names[all_subcategories]', $archive_names[ 'all_subcategories' ], null, 1, 'wide', null, $disabled );
							implecode_settings_text( __( 'Category Prefix', 'post-type-x' ), 'archive_names[all_prefix]', $archive_names[ 'all_prefix' ], null, 1, 'wide', null, $disabled );
							implecode_settings_text( __( 'Category Items Header', 'post-type-x' ), 'archive_names[category_products]', $archive_names[ 'category_products' ], null, 1, 'wide', null, $disabled );
							?>
						</tbody>
					</table>
					<p class="submit">
						<input type="submit" <?php echo $disabled ?> class="button-primary" value="<?php _e( 'Save changes', 'post-type-x' ); ?>" />
					</p>
				</form>
			</div>
			<div class="helpers"><div class="wrapper"><?php main_helper(); ?>
				</div></div><?php } do_action( 'names-settings' );
						?>
	</div><?php
}

function get_archive_names() {
	$default_archive_names	 = default_archive_names();
	$archive_names			 = wp_parse_args( get_option( 'archive_names' ), $default_archive_names );
	return $archive_names;
}

function get_single_names() {
	$default_single_names	 = default_single_names();
	$single_names			 = get_option( 'single_names', $default_single_names );
	foreach ( $default_single_names as $key => $value ) {
		$single_names[ $key ] = isset( $single_names[ $key ] ) ? $single_names[ $key ] : $value;
	}
	return $single_names;
}
