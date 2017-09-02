<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages support settings
 *
 * Here support settings are defined and managed.
 *
 * @version		1.0.0
 * @package		holland-utrecht-from-implecode/includes
 * @author 		Norbert Dreszer
 */
function implecode_custom_csv_menu() {
	?>
	<a id="csv-settings" class="element" href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php&tab=product-settings&submenu=csv' ) ?>"><?php _e( 'Simple CSV', 'post-type-x' ); ?></a>
	<?php
}

add_action( 'general_submenu', 'implecode_custom_csv_menu' );

function implecode_custom_csv_settings_content() {
	?>
	<?php $submenu = isset( $_GET[ 'submenu' ] ) ? $_GET[ 'submenu' ] : ''; ?>
	<?php if ( $submenu == 'csv' ) { ?>
		<div class="setting-content submenu csv-tab">
			<script>
				jQuery( '.settings-submenu a' ).removeClass( 'current' );
				jQuery( '.settings-submenu a#csv-settings' ).addClass( 'current' );
			</script>
			<h2><?php _e( 'Simple CSV', 'post-type-x' ); ?></h2>
			<h3><?php _e( 'Simple Item Export', 'post-type-x' ); ?></h3>
			<?php
			$export = isset( $_GET[ 'export_csv' ] ) ? $_GET[ 'export_csv' ] : '';
			if ( $export == 1 ) {
				$url = simple_export_to_csv();
				echo '<a style="display: block; margin-top: 20px;" href="' . $url . '">' . __( "Download CSV", 'post-type-x' ) . '</a>';
			} else {
				?>
				<a style="display: block; margin-top: 20px;" href="<?php echo admin_url( 'edit.php?post_type=al_product&page=product-settings.php&tab=product-settings&submenu=csv&export_csv=1' ) ?>"><button class="button" ><?php _e( "Export All Items to CSV file", 'post-type-x' ) ?></button></a>
				<h3><?php _e( 'Simple Item Import', 'post-type-x' ); ?></h3><?php simple_upload_csv_products_file(); ?>
			<?php } ?>
		</div>
		<div class="helpers"><div class="wrapper"><?php
				main_helper();
				doc_helper( __( 'import', 'post-type-x' ), 'product-import' );
				?>
			</div></div><?php
	}
}

add_action( 'product-settings', 'implecode_custom_csv_settings_content' );

function simple_upload_csv_products_file() {
	$upload_feedback = '';
	if ( isset( $_FILES[ 'product_csv' ] ) && ($_FILES[ 'product_csv' ][ 'size' ] > 0) ) {
		$arr_file_type		 = wp_check_filetype( basename( $_FILES[ 'product_csv' ][ 'name' ] ) );
		$uploaded_file_type	 = $arr_file_type[ 'ext' ];
		$allowed_file_type	 = 'csv';
		if ( $uploaded_file_type == $allowed_file_type ) {
			$wp_uploads_dir	 = wp_upload_dir();
			$filepath		 = $wp_uploads_dir[ 'basedir' ] . '/simple-products.csv';
			if ( move_uploaded_file( $_FILES[ 'product_csv' ][ 'tmp_name' ], $filepath ) ) {
				simple_import_product_from_csv();
			} else {
				$upload_feedback = '<div class="al-box warning">' . __( 'There was a problem with your upload.', 'post-type-x' ) . '</div>';
			}
		} else {
			$upload_feedback = '<div class="al-box warning">' . __( 'Please upload only CSV files.', 'post-type-x' ) . '</div>';
		}
		echo $upload_feedback;
	} else {
		$url = sample_import_file_url();
		echo '<form method="POST" enctype="multipart/form-data"><input type="file" accept=".csv" name="product_csv" id="product_csv" /><input type="submit" class="button" value="' . __( 'Import Items', 'post-type-x' ) . '" /></form>';
		echo '<div class="al-box info"><p>' . __( "The CSV fields should be in following order: Image URL, Item Name, Item Price, Item Categories, Short Description, Long Description.", "post-type-x" ) . '</p><p>' . __( "The first row should contain the field names. Semicolon should be used as the CSV separator.", "post-type-x" ) . '</p><a href="' . $url . '" class="button-primary">' . __( 'Download CSV Template', 'post-type-x' ) . '</a></div>';
	}
}

function simple_import_product_from_csv() {
	$fp		 = simple_prepare_csv_file( 'r' );
	$product = array();
	if ( $fp !== false ) {
		$csv_cols		 = fgetcsv( $fp, 0, ';', '"' );
		$import_array	 = simple_prepare_csv_import_array();
		if ( count( $csv_cols ) == count( $import_array ) ) {
			$i		 = 0;
			while ( ($data	 = fgetcsv( $fp, 0, ';', '"' )) !== FALSE ) {
				foreach ( $data as $key => $val ) {
					unset( $data[ $key ] );
					$new_key			 = $import_array[ $key ];
					$data[ $new_key ]	 = $val;
				}
				simple_insert_csv_product( $data );
				$i++;
			}
			echo '<div class="al-box success">';
			echo '<p>' . $i . ' ';
			_e( 'products successfully added to the catalog', 'post-type-x' ) . '.<p>';
			echo '</div>';
		} else {
			echo '<div class="al-box warning">';
			_e( 'Number of item fields and number of fields in CSV file do not match!', 'post-type-x' );
			echo '</div>';
		}
	}
	fclose( $fp );
}

function simple_prepare_csv_file( $type = 'w' ) {
	$csv_temp	 = wp_upload_dir();
	ini_set( 'auto_detect_line_endings', true );
	$fp			 = fopen( $csv_temp[ 'basedir' ] . '/simple-products.csv', $type ) or die( implecode_warning( sprintf( __( 'Permission error. Please check WordPress uploads %sfolder permissions%s.', 'post-type-x' ), '<a href="https://codex.wordpress.org/Changing_File_Permissions">', '</a>' ), 0 ) );
	return $fp;
}

function simple_prepare_csv_import_array() {
	$arr = array( 'image_url', 'product_name', 'product_price', 'product_categories', 'product_short_desc', 'product_desc' );
	return $arr;
}

function simple_insert_csv_product( $data ) {
	$post	 = array(
		'ID'			 => '',
		'post_title'	 => $data[ 'product_name' ],
		'post_status'	 => 'publish',
		'post_type'		 => 'al_product',
	);
	$id		 = wp_insert_post( $post );
	if ( $id != false ) {
		update_post_meta( $id, '_price', $data[ 'product_price' ] );
		update_post_meta( $id, 'excerpt', $data[ 'product_short_desc' ] );
		update_post_meta( $id, 'content', $data[ 'product_desc' ] );
		$image_url = get_product_image_id( $data[ 'image_url' ] );
		set_post_thumbnail( $id, $image_url );
		wp_set_object_terms( $id, $data[ 'product_categories' ], 'al_product-cat' );
		set_time_limit( 30 );
	}
	return $id;
}

function prepare_sample_import_file() {
	$fields								 = array();
	$fields[ 1 ][ 'image_url' ]			 = __( 'Image URL', 'post-type-x' );
	$fields[ 1 ][ 'product_name' ]		 = __( 'Item Name', 'post-type-x' );
	$fields[ 1 ][ 'product_price' ]		 = __( 'Item Price', 'post-type-x' );
	$fields[ 1 ][ 'product_categories' ] = __( 'Item Categories', 'post-type-x' );
	$fields[ 1 ][ 'product_short_desc' ] = __( 'Short Description', 'post-type-x' );
	$fields[ 1 ][ 'product_desc' ]		 = __( 'Long Description', 'post-type-x' );
	return array_filter( $fields );
}

function sample_import_file_url() {
	$fp		 = simple_prepare_csv_file();
	$fields	 = prepare_sample_import_file();
	fprintf( $fp, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
	foreach ( $fields as $field ) {
		fputcsv( $fp, $field, ';', '"' );
	}
	simple_close_csv_file( $fp );
	$csv_temp = wp_upload_dir();
	return $csv_temp[ 'baseurl' ] . '/simple-products.csv';
}

function simple_close_csv_file( $fp ) {
	fclose( $fp );
	ini_set( 'auto_detect_line_endings', false );
}

function simple_get_all_exported_products() {
	$args		 = array(
		'posts_per_page'	 => -1,
		'orderby'			 => 'title',
		'order'				 => 'ASC',
		'post_type'			 => 'al_product',
		'post_status'		 => 'publish',
		'suppress_filters'	 => true );
	$products	 = get_posts( $args );
	return $products;
}

function simple_prepare_products_to_export() {
	$products							 = simple_get_all_exported_products();
	$fields								 = array();
	$fields[ 1 ][ 'image_url' ]			 = __( 'Image URL', 'post-type-x' );
	$fields[ 1 ][ 'product_name' ]		 = __( 'Item Name', 'post-type-x' );
	$fields[ 1 ][ 'product_price' ]		 = __( 'Item Price', 'post-type-x' );
	$fields[ 1 ][ 'product_categories' ] = __( 'Item Categories', 'post-type-x' );
	$fields[ 1 ][ 'product_short_desc' ] = __( 'Short Description', 'post-type-x' );
	$fields[ 1 ][ 'product_desc' ]		 = __( 'Long Description', 'post-type-x' );
	$z									 = 2;
	foreach ( $products as $product ) {
		$image							 = wp_get_attachment_image_src( get_post_thumbnail_id( $product->ID ), 'full' );
		$desc							 = get_product_description( $product->ID );
		$short_desc						 = get_product_short_description( $product->ID );
		$fields[ $z ][ 'image_url' ]	 = $image[ 0 ];
		$fields[ $z ][ 'product_name' ]	 = $product->post_title;
		$fields[ $z ][ 'product_price' ] = get_post_meta( $product->ID, '_price', true );
		$category_array					 = get_the_terms( $product->ID, 'al_product-cat' );
		$category						 = array();
		if ( !empty( $category_array ) ) {
			foreach ( $category_array as $p_cat ) {
				$value		 = html_entity_decode( $p_cat->name );
				$category[]	 = $value;
			}
		}
		$fields[ $z ][ 'product_categories' ]	 = implode( ' | ', $category );
		$fields[ $z ][ 'product_short_desc' ]	 = $short_desc;
		$fields[ $z ][ 'product_desc' ]			 = $desc;
		$z++;
	}
	return array_filter( $fields );
}

function simple_export_to_csv() {
	$fp		 = simple_prepare_csv_file();
	$fields	 = simple_prepare_products_to_export();
	fprintf( $fp, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );
	foreach ( $fields as $field ) {
		fputcsv( $fp, $field, ';', '"' );
	}
	simple_close_csv_file( $fp );
	$csv_temp = wp_upload_dir();
	return $csv_temp[ 'baseurl' ] . '/simple-products.csv';
}
