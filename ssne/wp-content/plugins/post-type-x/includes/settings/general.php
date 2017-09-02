<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages general settings
 *
 * Here general settings are defined and managed.
 *
 * @version        1.1.4
 * @package        post-type-x/functions
 * @author        Norbert Dreszer
 */
function general_menu() {
	?>
	<a id="general-settings" class="nav-tab" href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php&tab=product-settings' ) ?>"><?php _e( 'General Settings', 'post-type-x' ); ?></a><?php
}

add_action( 'settings-menu', 'general_menu' );

function general_settings() {
	register_setting( 'product_settings', 'product_listing_url' );
	register_setting( 'product_settings', 'product_archive' );
	register_setting( 'product_settings', 'enable_product_listing' );
	register_setting( 'product_settings', 'archive_multiple_settings' );
}

add_action( 'product-settings-list', 'general_settings' );

/**
 * Validates archive multiple settings
 *
 * @param array $new_value
 * @param array $old_value
 * @return array
 */
function archive_multiple_settings_validation( $new_value ) {
	$product_slug = get_product_slug();
	if ( $new_value[ 'category_archive_url' ] == $product_slug ) {
		$new_value[ 'category_archive_url' ] = $new_value[ 'category_archive_url' ] . '-1';
	}
	return $new_value;
}

/**
 * Validates item currency settings
 *
 * @param array $new_value
 * @return array
 */
function product_currency_settings_validation( $new_value ) {
	if ( $new_value[ 'th_sep' ] == $new_value[ 'dec_sep' ] ) {
		if ( $new_value[ 'th_sep' ] == ',' ) {
			$new_value[ 'th_sep' ] = '.';
		} else {
			$new_value[ 'th_sep' ] = ',';
		}
	}
	return $new_value;
}

add_action( 'init', 'general_options_validation_filters' );

/**
 * Initializes validation filters for general settings
 *
 */
function general_options_validation_filters() {
	add_filter( 'pre_update_option_archive_multiple_settings', 'archive_multiple_settings_validation' );
	add_filter( 'pre_update_option_product_currency_settings', 'product_currency_settings_validation' );
}

function general_settings_content() {
	$submenu = isset( $_GET[ 'submenu' ] ) ? $_GET[ 'submenu' ] : '';
	?>
	<div class="overall-product-settings settings-wrapper" style="clear:both;">
		<div class="settings-submenu">
			<h3>
				<a id="general-settings" class="element current"
				   href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php&tab=product-settings&submenu=general-settings' ) ?>"><?php _e( 'General Settings', 'post-type-x' ); ?></a>
				   <?php do_action( 'general_submenu' ); ?>
			</h3>
		</div>

		<?php if ( $submenu == 'general-settings' OR $submenu == '' ) { ?>
			<div class="setting-content submenu">
				<script>
					jQuery( '.settings-submenu a' ).removeClass( 'current' );
					jQuery( '.settings-submenu a#general-settings' ).addClass( 'current' );
				</script>
				<h2><?php _e( 'General Settings', 'post-type-x' ); ?></h2>

				<form method="post" action="options.php">
					<?php
					settings_fields( 'product_settings' );
					$enable_product_listing		 = get_option( 'enable_product_listing', 1 );
					//$product_listing_url		 = product_listing_url();
					$product_archive			 = get_product_listing_id();
					$archive_multiple_settings	 = get_multiple_settings();
					/*
					  $page_get					 = get_page_by_path( $product_listing_url );

					  if ( $product_archive != '' ) {
					  $new_product_listing_url = get_page_uri( $product_archive );
					  if ( $new_product_listing_url != '' ) {
					  update_option( 'product_listing_url', $new_product_listing_url );
					  } else {
					  update_option( 'product_listing_url', __( 'items', 'post-type-x' ) );
					  }
					  } else if ( !empty( $page_get->ID ) ) {
					  update_option( 'product_archive', $page_get->ID );
					  $product_archive = $page_get->ID;
					  } */
					$disabled					 = '';
					if ( !is_advanced_mode_forced() ) {
						?>
						<h3><?php _e( 'Theme Integration', 'post-type-x' ); ?></h3><?php
						if ( get_integration_type() == 'simple' ) {
							$disabled = 'disabled';
						}
						if ( is_integration_mode_selected() ) {
							$selected = true;
							if ( get_integration_type() == 'simple' ) {
								implecode_warning( '<p>' . __( 'The simple mode allows to use Post Type X most features. You can build the item listing pages and category pages by using a [show_products] shortcode. Simple mode uses your theme page layout so it can show unwanted elements on item page. If it does please switch to Advanced Mode and see if it works out of the box.', 'post-type-x' ) . '</p><p>' . __( 'Switching to Advanced Mode also gives additional features: automatic item listing, category pages, item search and category widget. Building a item catalog in Advanced Mode will be less time consuming as you don’t need to use a shortcode for everything.', 'post-type-x' ) . '</p>' . sample_product_button( 'p', __( 'Restart Integration Wizard', 'post-type-x' ) ) );
							}
							?>
							<table>
								<?php
								implecode_settings_radio( __( 'Choose theme integration type', 'post-type-x' ), 'archive_multiple_settings[integration_type]', $archive_multiple_settings[ 'integration_type' ], array( 'simple' => __( 'Simple Integration', 'post-type-x' ), 'advanced' => __( 'Advanced Integration', 'post-type-x' ) ) );
								?></table>
							<table class="advanced_mode_settings"><?php
								implecode_settings_number( __( 'Catalog Container Width', 'post-type-x' ), 'archive_multiple_settings[container_width]', $archive_multiple_settings[ 'container_width' ], '%' );
								implecode_settings_text_color( __( 'Catalog Container Background', 'post-type-x' ), 'archive_multiple_settings[container_bg]', $archive_multiple_settings[ 'container_bg' ] );
								implecode_settings_number( __( 'Catalog Container Padding', 'post-type-x' ), 'archive_multiple_settings[container_padding]', $archive_multiple_settings[ 'container_padding' ], 'px' );
								if ( !defined( 'AL_SIDEBAR_PLUGIN_BASE_PATH' ) ) {
									implecode_settings_radio( __( 'Default Sidebar', 'post-type-x' ), 'archive_multiple_settings[default_sidebar]', $archive_multiple_settings[ 'default_sidebar' ], array( 'none' => __( 'Disabled', 'post-type-x' ), 'left' => __( 'Left', 'post-type-x' ), 'right' => __( 'Right', 'post-type-x' ) ) );
								}
								implecode_settings_checkbox( __( 'Disable Item Name', 'post-type-x' ), 'archive_multiple_settings[disable_name]', $archive_multiple_settings[ 'disable_name' ] );
								?>
							</table>
							<?php
							if ( get_integration_type() == 'advanced' ) {
								echo sample_product_button( 'p', __( 'Restart Integration Wizard', 'post-type-x' ) );
							}
						} else {
							$selected = false;
							?>
							<table style="display: none">
								<?php
								implecode_settings_radio( __( 'Choose theme integration type', 'post-type-x' ), 'archive_multiple_settings[integration_type]', $archive_multiple_settings[ 'integration_type' ], array( 'simple' => __( 'Simple Integration', 'post-type-x' ), 'advanced' => __( 'Advanced Integration', 'post-type-x' ) ) );
								?></table>
							<?php
							echo '<a href="' . sample_product_url() . '" class="button-primary">' . __( 'Start Auto Adjustment', 'post-type-x' ) . '</a>';
						}
					}
					?>
					<h3><?php _e( 'Catalog', 'post-type-x' ); ?></h3>
					<table><?php
						implecode_settings_text( __( 'Catalog Singular Name', 'post-type-x' ), 'archive_multiple_settings[catalog_singular]', $archive_multiple_settings[ 'catalog_singular' ], null, 1, null, __( 'Admin panel customisation setting. Change it to what you sell.', 'post-type-x' ) );
						implecode_settings_text( __( 'Catalog Plural Name', 'post-type-x' ), 'archive_multiple_settings[catalog_plural]', $archive_multiple_settings[ 'catalog_plural' ], null, 1, null, __( 'Admin panel customisation setting. Change it to what you sell.', 'post-type-x' ) );
						?>
					</table>

					<h3><?php _e( 'Main listing page', 'post-type-x' ); ?></h3><?php
					/* if ( $disabled == 'disabled' ) {
					  implecode_warning( sprintf( __( 'Item listing page is disabled with simple theme integration. See <a href="%s">Theme Integration Guide</a> to enable item listing page with pagination or use [show_products] shortcode on the page selected below.', 'post-type-x' ), 'https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=simple-mode&key=product-listing' ) );
					  } */
					?>
					<table>
						<tr>
							<td style="width: 180px">
								<?php _e( 'Enable Item Listing Page', 'post-type-x' ); ?>:
							</td>
							<td>
								<input title="<?php _e( 'Disable and use [show_products] shortcode to display the items.', 'post-type-x' ); ?>" type="checkbox" name="enable_product_listing" value="1"<?php checked( 1, $enable_product_listing ); ?> />
							</td>
						</tr>
						<tr>
							<td>
								<?php _e( 'Choose Item Listing Page', 'post-type-x' ); ?>:
							</td>
							<td><?php
								if ( $enable_product_listing == 1 ) {
									$listing_url = product_listing_url();
									select_page( 'product_archive', __( 'Default', 'post-type-x' ), $product_archive, true, $listing_url );
								} else {
									select_page( 'product_archive', __( 'Default', 'post-type-x' ), $product_archive, true );
								}
								?>
							</td>
						</tr> <?php /*
						  <tr>
						  <td><?php _e('Item listing URL', 'post-type-x'); ?>:</td>
						  <td class="archive-url-td"><a target="_blank" class="archive-url" href="<?php echo product_listing_url() ?>"><?php
						  $listin_url = product_listing_url();
						  $listin_urllen = strlen($listin_url);
						  if ($listin_urllen > 40) {
						  $listin_url = substr($listin_url, 0, 20).'...'.substr($listin_url, $listin_urllen - 20, $listin_urllen);
						  }
						  echo $listin_url;
						  ?></a></td>
						  </tr> */ ?>
						<tr>
							<td><?php _e( 'Item listing shows at most', 'post-type-x' ); ?> </td>
							<td><input
									title="<?php _e( 'You can also use shortcode with products_limit attribute to set this.', 'post-type-x' ); ?>"
									size="30" class="number-box" type="number" step="1" min="0"
									name="archive_multiple_settings[archive_products_limit]" id="archive_products_limit"
									value="<?php echo $archive_multiple_settings[ 'archive_products_limit' ]; ?>"/> <?php _e( 'items', 'post-type-x' ); ?>
								.
							</td>
						</tr><?php
						implecode_settings_radio( __( 'Item listing shows', 'post-type-x' ), 'archive_multiple_settings[product_listing_cats]', $archive_multiple_settings[ 'product_listing_cats' ], array( 'off' => __( 'Items', 'post-type-x' ), 'on' => __( 'Items & Main Categories', 'post-type-x' ), 'cats_only' => __( 'Main Categories', 'post-type-x' ) ) );
						$sort_options = get_product_sort_options();
						implecode_settings_radio( __( 'Item order', 'post-type-x' ), 'archive_multiple_settings[product_order]', $archive_multiple_settings[ 'product_order' ], $sort_options, true, __( 'This is also the default setting for sorting drop-down.', 'post-type-x' ) );
						do_action( 'product_listing_page_settings' );
						?>
					</table><?php
					//implecode_info(__('You can also use shortcode to show your items whenever you want on the website. Just paste on any page: [show_products] and you will display all items in place of the shortcode. <br><br>To show items from just one category, use: [show_products category="2"] where 2 is category ID (you can display several categories by inserting comma separated IDs). <br><br>To display items by IDs, use: [show_products product="5"], where 5 is item ID.', 'post-type-x'));
					?>
					<h3><?php _e( 'Categories Settings', 'post-type-x' ); ?></h3><?php
					if ( $disabled != '' ) {
						if ( $selected ) {
							implecode_warning( sprintf( __( 'Category pages are disabled with simple theme integration. See <a href="%s">Theme Integration Guide</a> to enable category pages or use [show_products category="1"] (where "1" is category ID) on any page to show items from certain category.', 'post-type-x' ), 'https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=simple-mode&key=categories-settings' ) );
						} else {
							implecode_warning( sprintf( __( 'Category pages are disabled due to a lack of theme integration.%s', 'post-type-x' ), sample_product_button( 'p' ) ) );
						}
					}
					?>
					<table>
						<?php if ( is_ic_permalink_product_catalog() ) { ?>
							<tr>
								<td><?php _e( 'Categories Parent URL', 'post-type-x' ); ?>:</td>
								<?php
								$site_url	 = site_url();
								$urllen		 = strlen( $site_url );
								if ( $urllen > 25 ) {
									$site_url = substr( $site_url, 0, 11 ) . '...' . substr( $site_url, $urllen - 11, $urllen );
								}
								?>
								<td class="longer"><?php echo $site_url ?>/<input <?php echo $disabled ?> type="text"
																										  name="archive_multiple_settings[category_archive_url]"
																										  title="<?php _e( 'Cannot be the same as item listing page slug.', 'post-type-x' ) ?>"
																										  id="category_archive_url"
																										  value="<?php echo sanitize_title( $archive_multiple_settings[ 'category_archive_url' ] ); ?>"/>/<?php _e( 'category-name', 'post-type-x' ) ?>
									/
								</td>
							</tr><?php
						}
						implecode_settings_radio( __( 'Category Page shows', 'post-type-x' ), 'archive_multiple_settings[category_top_cats]', $archive_multiple_settings[ 'category_top_cats' ], array( 'off' => __( 'Items', 'post-type-x' ), 'on' => __( 'Items & Subcategories', 'post-type-x' ), 'only_subcategories' => __( 'Subcategories', 'post-type-x' ) ) );
						implecode_settings_radio( __( 'Categories Display', 'post-type-x' ), 'archive_multiple_settings[cat_template]', $archive_multiple_settings[ 'cat_template' ], array( 'template' => __( 'Template', 'post-type-x' ), 'link' => __( 'URLs', 'post-type-x' ) ), true, array( 'template' => __( 'Display categories with the same listing theme as items.', 'post-type-x' ), 'link' => __( 'Display categories as simple links.', 'post-type-x' ) ) );
						implecode_settings_checkbox( __( 'Disable Image on Category Page', 'post-type-x' ), 'archive_multiple_settings[cat_image_disabled]', $archive_multiple_settings[ 'cat_image_disabled' ] );
						do_action( 'product_category_settings', $archive_multiple_settings );
						?>
					</table>
					<h3><?php _e( 'SEO Settings', 'post-type-x' ); ?></h3><?php
					if ( $disabled != '' ) {
						if ( $selected ) {
							implecode_warning( sprintf( __( 'SEO settings are disabled with simple theme integration. See <a href="%s">Theme Integration Guide</a> to enable SEO settings.', 'post-type-x' ), 'https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=simple-mode&key=seo-settings' ) );
						} else {
							implecode_warning( sprintf( __( 'SEO settings are disabled due to a lack of theme integration.%s', 'post-type-x' ), sample_product_button( 'p' ) ) );
						}
					}
					?>
					<table>
						<?php
						implecode_settings_text( __( 'Archive SEO Title', 'post-type-x' ), 'archive_multiple_settings[seo_title]', $archive_multiple_settings[ 'seo_title' ] );
						implecode_settings_checkbox( __( 'Enable SEO title separator', 'post-type-x' ), 'archive_multiple_settings[seo_title_sep]', $archive_multiple_settings[ 'seo_title_sep' ] )
						?>

					</table>
					<h3><?php _e( 'Breadcrumbs Settings', 'post-type-x' ); ?></h3><?php
					if ( $disabled != '' ) {
						if ( $selected ) {
							implecode_warning( sprintf( __( 'Breadcrumbs are disabled with simple theme integration. See <a href="%s">Theme Integration Guide</a> to enable item breadcrumbs.', 'post-type-x' ), 'https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=simple-mode&key=breadcrumbs-settings' ) );
						} else {
							implecode_warning( sprintf( __( 'Breadcrumbs are disabled due to a lack of theme integration.%s', 'post-type-x' ), sample_product_button( 'p' ) ) );
						}
					}
					?>
					<table>
						<tr>
							<td><?php _e( 'Enable Item Breadcrumbs:', 'post-type-x' ); ?> </td>
							<td><input <?php echo $disabled ?> type="checkbox"
															   name="archive_multiple_settings[enable_product_breadcrumbs]"
															   value="1"<?php checked( 1, isset( $archive_multiple_settings[ 'enable_product_breadcrumbs' ] ) ? $archive_multiple_settings[ 'enable_product_breadcrumbs' ] : ''  ); ?> />
							</td>
						</tr>
						<tr>
							<td><?php _e( 'Main listing breadcrumbs title:', 'post-type-x' ); ?> </td>
							<td><input <?php echo $disabled ?> type="text"
															   name="archive_multiple_settings[breadcrumbs_title]"
															   id="breadcrumbs_title"
															   value="<?php echo $archive_multiple_settings[ 'breadcrumbs_title' ]; ?>"/>
							</td>
						</tr>

					</table>

					<?php do_action( 'general-settings' ); ?>
					<p class="submit">
						<input type="submit" class="button-primary"
							   value="<?php _e( 'Save changes', 'post-type-x' ); ?>"/>
					</p>
				</form>
			</div>
			<div class="helpers">
				<div class="wrapper"><?php
					main_helper();
					doc_helper( __( 'shortcode', 'post-type-x' ), 'product-catalog-shortcodes' );
					doc_helper( __( 'sorting', 'post-type-x' ), 'product-order-settings' );
					//did_know_helper('support', __('You can get instant support by email','post-type-x'), 'https://implecode.com/wordpress/plugins/premium-support/')
					?>
				</div>
			</div>
			<?php
		}
		do_action( 'product-settings' );


		permalink_options_update();
		?>
	</div>

	<?php
}

function get_multiple_settings() {
	$archive_multiple_settings = get_option( 'archive_multiple_settings', unserialize( DEFAULT_ARCHIVE_MULTIPLE_SETTINGS ) );
	if ( is_advanced_mode_forced() || (isset( $_GET[ 'test_advanced' ] ) && ($_GET[ 'test_advanced' ] == 1 || $_GET[ 'test_advanced' ] == 'ok')) ) {
		$archive_multiple_settings[ 'integration_type' ] = 'advanced';
	} else {
		$archive_multiple_settings[ 'integration_type' ] = isset( $archive_multiple_settings[ 'integration_type' ] ) ? $archive_multiple_settings[ 'integration_type' ] : 'simple';
	}
	$archive_multiple_settings[ 'seo_title_sep' ]		 = isset( $archive_multiple_settings[ 'seo_title_sep' ] ) ? $archive_multiple_settings[ 'seo_title_sep' ] : '';
	$archive_multiple_settings[ 'seo_title' ]			 = isset( $archive_multiple_settings[ 'seo_title' ] ) ? $archive_multiple_settings[ 'seo_title' ] : '';
	$archive_multiple_settings[ 'category_archive_url' ] = isset( $archive_multiple_settings[ 'category_archive_url' ] ) ? $archive_multiple_settings[ 'category_archive_url' ] : 'product-category';
	$archive_multiple_settings[ 'category_archive_url' ] = empty( $archive_multiple_settings[ 'category_archive_url' ] ) ? 'product-category' : $archive_multiple_settings[ 'category_archive_url' ];
	$archive_multiple_settings[ 'product_listing_cats' ] = isset( $archive_multiple_settings[ 'product_listing_cats' ] ) ? $archive_multiple_settings[ 'product_listing_cats' ] : 'on';
	$archive_multiple_settings[ 'category_top_cats' ]	 = isset( $archive_multiple_settings[ 'category_top_cats' ] ) ? $archive_multiple_settings[ 'category_top_cats' ] : 'on';
	$archive_multiple_settings[ 'cat_template' ]		 = isset( $archive_multiple_settings[ 'cat_template' ] ) ? $archive_multiple_settings[ 'cat_template' ] : 'template';
	$archive_multiple_settings[ 'product_order' ]		 = isset( $archive_multiple_settings[ 'product_order' ] ) ? $archive_multiple_settings[ 'product_order' ] : 'newest';
	$archive_multiple_settings[ 'catalog_plural' ]		 = isset( $archive_multiple_settings[ 'catalog_plural' ] ) ? $archive_multiple_settings[ 'catalog_plural' ] : __( 'Catalog', 'post-type-x' );
	$archive_multiple_settings[ 'catalog_singular' ]	 = isset( $archive_multiple_settings[ 'catalog_singular' ] ) ? $archive_multiple_settings[ 'catalog_singular' ] : __( 'Item', 'post-type-x' );
	$archive_multiple_settings[ 'cat_image_disabled' ]	 = isset( $archive_multiple_settings[ 'cat_image_disabled' ] ) ? $archive_multiple_settings[ 'cat_image_disabled' ] : '';
	$archive_multiple_settings[ 'container_width' ]		 = isset( $archive_multiple_settings[ 'container_width' ] ) ? $archive_multiple_settings[ 'container_width' ] : 100;
	$archive_multiple_settings[ 'container_bg' ]		 = isset( $archive_multiple_settings[ 'container_bg' ] ) ? $archive_multiple_settings[ 'container_bg' ] : '';
	$archive_multiple_settings[ 'container_padding' ]	 = isset( $archive_multiple_settings[ 'container_padding' ] ) ? $archive_multiple_settings[ 'container_padding' ] : 0;
	$archive_multiple_settings[ 'disable_name' ]		 = isset( $archive_multiple_settings[ 'disable_name' ] ) ? $archive_multiple_settings[ 'disable_name' ] : '';
	$archive_multiple_settings[ 'default_sidebar' ]		 = isset( $archive_multiple_settings[ 'default_sidebar' ] ) ? $archive_multiple_settings[ 'default_sidebar' ] : 'none';
	return apply_filters( 'catalog_multiple_settings', $archive_multiple_settings );
}

function get_catalog_names() {
	$multiple_settings	 = get_multiple_settings();
	$names[ 'singular' ] = $multiple_settings[ 'catalog_singular' ];
	$names[ 'plural' ]	 = $multiple_settings[ 'catalog_plural' ];
	return apply_filters( 'product_catalog_names', $names );
}

function get_integration_type() {
	$settings = get_multiple_settings();
	return $settings[ 'integration_type' ];
}

function get_product_sort_options() {
	$sort_options = apply_filters( 'product_sort_options', array( 'newest' => __( 'Sort by Newest', 'post-type-x' ), 'product-name' => __( 'Sort by Item Name', 'post-type-x' ) ) );
	return $sort_options;
}

function get_product_listing_id() {
	$product_archive_created = get_option( 'product_archive_page_id', '0' );
	if ( FALSE === get_post_status( $product_archive_created ) ) {
		$product_archive_created = '0';
	}
	$listing_id = get_option( 'product_archive', $product_archive_created );
	return apply_filters( 'product_listing_id', $listing_id );
}

/**
 * Returns item listing URL
 *
 * @return string
 */
function product_listing_url() {
	$listing_url = '';
	if ( is_ic_permalink_product_catalog() && 'noid' != ($page_id	 = get_product_listing_id()) ) {
		if ( !empty( $page_id ) ) {
			$listing_url = get_permalink( $page_id );
		}
	}
	if ( empty( $listing_url ) ) {
		$listing_url = get_post_type_archive_link( 'al_product' );
	}
	return apply_filters( 'product_listing_url', $listing_url );
}

function get_product_slug() {
	$page_id = get_product_listing_id();
	$slug	 = untrailingslashit( get_page_uri( $page_id ) );
	if ( empty( $slug ) ) {
		$slug = __( 'items', 'post-type-x' );
	}
	return apply_filters( 'product_slug', $slug );
}

add_action( 'updated_option', 'rewrite_permalinks_after_update' );

function rewrite_permalinks_after_update( $option ) {
	if ( $option == 'product_archive' || $option == 'archive_multiple_settings' ) {
		flush_rewrite_rules();
	}
}
