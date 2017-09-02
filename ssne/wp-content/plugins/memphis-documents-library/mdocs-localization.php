<?php
// ********** MEMPHIS DOCUMENTS LIBRARY VERSION *********************//
define('MDOCS_VERSION', '3.1.1');
//*************************************************************************************//
$upload_dir = wp_upload_dir();
$mdocs_zip = get_option('mdocs-zip');
// LOCALIZATION INIT
function mdocs_localization() {
	//FOR TESTING LANG FILES
	//global $locale; $locale = 'he_IL';
	$loaded = load_plugin_textdomain('mdocs', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action('init', 'mdocs_localization');
//PASS VARIABLES TO JAVASCRIPT
function mdocs_js_handle($script) {
	wp_localize_script( $script, 'mdocs_js', array(
		'version_file' => __("You are about to delete this file.  Once deleted you will lose this file!\n\n'Cancel' to stop, 'OK' to delete.",'mdocs'),
		'version_delete' => __("You are about to delete this version.  Once deleted you will lose this version of the file!\n\n'Cancel' to stop, 'OK' to delete.",'mdocs'),
		'category_delete' => __("You are about to delete this folder.  Any file in this folder will be lost!\n\n'Cancel' to stop, 'OK' to delete.",'mdocs'),
		'remove' => __('Remove','mdocs'),
		'new_category' => __('New Folder','mdocs'),
		'leave_page' => __('Are you sure you want to navigate away from this page?','mdocs'),
		'category_support' => __('Currently Memphis Documents Library only supports two sub categories.','mdocs'),
		'restore_warning' => __('Are you sure you want continue.  All you files, posts and directories will be delete.','mdocs'),
		'add_folder' => __('Add Folder', 'mdocs'),
		'update_doc' => __('Updating Document', 'mdocs'),
		'update_doc_btn' => __('Update Document' , 'mdocs'),
		'add_doc' => __('Adding Document', 'mdocs'),
		'add_doc_btn' => __('Add Document', 'mdocs'),
		'current_file' => __('Current File','mdocs'),
		'patch_text_3_0_1' => __('UPDATE HAS STARTER, DO NOT LEAVE THIS PAGE!'),
		'patch_text_3_0_2' => __('Go grab a coffee this my take awhile.'),
		'create_export_file' => __('Creating the export file, please be patient.'),
		'export_creation_complete_starting_download' => __('Export file creation complete, staring download of zip file.'),
		'levels'=> 2,
		'blog_id' => get_current_blog_id(),
		'plugin_url' => plugins_url().'/memphis-documents-library/',
		'wp_root' => get_option('mdocs-wp-root'),
		'ajaxurl' => admin_url( 'admin-ajax.php' ), 
	));
}
// PROCESS AJAX REQUESTS
add_action( 'wp_ajax_nopriv_myajax-submit', 'mdocs_ajax_processing' );
add_action( 'wp_ajax_myajax-submit', 'mdocs_ajax_processing' );
function mdocs_ajax_processing() {
	switch($_POST['type']) {
		case 'file':
			mdocs_load_preview();
			break;
		case 'img':
			mdocs_load_preview();
			break;
		case 'show':
			mdocs_load_preview();
			break;
		case 'add-mime':
			mdocs_update_mime();
			break;
		case 'remove-mime':
			mdocs_update_mime();
			break;
		case 'restore-mime':
			mdocs_update_mime();
			break;
		case 'restore':
			mdocs_restore_default();
			break;
		case 'sort':
			mdocs_sort();
			break;
		case 'rating':
			mdocs_ratings();
			break;
		case 'rating-submit':
			mdocs_set_rating(intval($_POST['mdocs_file_id']));
			break;
		case 'nav-collaspse':
			//mdocs_nav_size(true);
			break;
		case 'nav-expand':
			//mdocs_nav_size(false);
			break;
		case 'add-doc':
			mdocs_add_update_ajax('Add Document');
			break;
		case 'update-doc':
			mdocs_add_update_ajax('Update Document');
			break;
		case 'mdocs-v3-0-patch':
			//mdocs_box_view_update_v3_0();
			break;
		case 'mdocs-v3-0-patch-run-updater':
			//mdocs_v3_0_patch_run_updater();
			break;
		case 'mdocs-v3-0-patch-cancel-updater':
			//mdocs_v3_0_patch_cancel_updater();
			break;
		case 'show-desc':
			mdocs_show_description(intval($_POST['mdocs_file_id']));
			break;
		case 'search-users':
			mdocs_search_users($_POST['user-search-string'], $_POST['owner'], $_POST['contributors']);
			break;
		case 'show-social':
			echo mdocs_social(intval($_POST['doc-index']));
			break;
		case 'box-view-refresh':
			$mdocs = mdocs_array_sort();
			$file = get_site_url().'/?mdocs-file='.$mdocs[$_POST['index']]['id'].'&mdocs-url=false&is-box-view=true';
			$boxview = new mdocs_box_view();
			$results = $boxview->uploadFile($file);
			$mdocs[$_POST['index']]['box-view-id'] = $results['id'];
			update_option('mdocs-list', $mdocs);
			echo '<div class="alert alert-success" role="alert" id="box-view-updated">'.$mdocs[$_POST['index']]['filename'].' '.__('preview has been updated.', 'mdocs').'</div>';
			break;
		case 'lost-file-search-start':
			find_lost_files();
			break;
		case 'lost-file-save':
			save_lost_files();
			break;
		case 'mdocs-export':
			mdocs_export_zip();
			mdocs_download_export_file($_POST['zip-file']);
			break;
		case 'mdocs-cat-index':
			$check_index = intval($_POST['check-index']);
			do {
				$found = mdocs_find_cat('mdocs-cat-'.$check_index);
				$empty_index = $check_index;
				$check_index++;
			} while ($found == true);
			update_option('mdocs-num-cats', $empty_index);
			echo $empty_index;
			break;
	}
	exit;
}
function mdocs_get_inline_css() {
	$num_show = 0;
	if(get_option('mdocs-show-downloads')==1) $num_show++;
	if(get_option('mdocs-show-author')==1) $num_show++;
	if(get_option('mdocs-show-version')==1) $num_show++;
	if(get_option('mdocs-show-update')==1) $num_show++;
	if(get_option('mdocs-show-ratings')==1) $num_show++;
	$mdocs_font_size = get_option('mdocs-font-size');
	if($num_show==5) $title_width = '35%';
	if($num_show==4) $title_width = '45%';
	if($num_show==3) $title_width = '55%';
	if($num_show==2) $title_width = '65%';
	if($num_show==1) $title_width = '75%';
	$download_button_color = get_option('mdocs-download-text-color-normal');
	$download_button_bg = get_option('mdocs-download-color-normal'); 
	$download_button_hover_color = get_option('mdocs-download-text-color-hover');
	$download_button_hover_bg = get_option('mdocs-download-color-hover');
	$set_inline_style = "
		.mdocs-list-table #title { width: $title_width !important }
		.mdocs-download-btn-config:hover { background: $download_button_hover_bg; color: $download_button_hover_color; }
		.mdocs-download-btn-config { color: $download_button_color; background: $download_button_bg ; }
		.mdocs-download-btn, .mdocs-download-btn:active { color: $download_button_color !important; background: $download_button_bg !important;  }
		.mdocs-download-btn:hover { background: $download_button_hover_bg !important; color: $download_button_hover_color !important;}
		.mdocs-container table { font-size: ".$mdocs_font_size."px !important; }
		.mdocs-container #title { font-size: ".$mdocs_font_size."px !important; }
	";
	return $set_inline_style;
}
function mdocs_get_inline_admin_css() {
	$num_show = 0;
	if(get_option('mdocs-show-downloads')==1) $num_show++;
	if(get_option('mdocs-show-author')==1) $num_show++;
	if(get_option('mdocs-show-version')==1) $num_show++;
	if(get_option('mdocs-show-update')==1) $num_show++;
	if(get_option('mdocs-show-ratings')==1) $num_show++;
	if($num_show==5) $title_width = '35%';
	if($num_show==4) $title_width = '45%';
	if($num_show==3) $title_width = '55%';
	if($num_show==2) $title_width = '65%';
	if($num_show==1) $title_width = '75%';
	$download_button_color = get_option('mdocs-download-text-color-normal');
	$download_button_bg = get_option('mdocs-download-color-normal'); 
	$download_button_hover_color = get_option('mdocs-download-text-color-hover');
	$download_button_hover_bg = get_option('mdocs-download-color-hover');
	$set_inline_style = "
		body { background: transparent; }
		dd, li { margin: 0; }
		.mdocs-list-table #title { width: $title_width !important }
		.mdocs-download-btn-config:hover { background: $download_button_hover_bg; color: $download_button_hover_color; }
		.mdocs-download-btn-config { color: $download_button_color; background: $download_button_bg ; }
		.mdocs-download-btn, .mdocs-download-btn:active { color: $download_button_color !important; background: $download_button_bg !important;  }
		.mdocs-download-btn:hover { background: $download_button_hover_bg !important; color: $download_button_hover_color !important;}
	";
	return $set_inline_style;
}
function mdocs_localize() {
	global $upload_dir, $mdocs_zip;
	$query = new WP_Query('pagename=mdocuments-library');	
	$permalink = get_permalink($query->post->ID);
	if( strrchr($permalink, '?page_id=')) $mdocs_link = site_url().'/'.strrchr($permalink, '?page_id=');
	else $mdocs_link = site_url().'/'.$query->post->post_name.'/';
	define('MDOCS_ZIP_STATUS_OK',__('Memphis Documents Library has an export file on this WordPress instance it was created on '.gmdate('F jS Y \a\t g:i A',@filemtime($upload_dir['basedir'].'/mdocs/'.$mdocs_zip)+MDOCS_TIME_OFFSET).'.<br><br><!--Click <a href="'.$upload_dir['baseurl'].'/mdocs/'.$mdocs_zip.'" tiltle="Old Export File">here</a> to download this version of the export file.-->','mdocs'));
	define('MDOCS_ZIP_STATUS_FAIL',__('Memphis Documents Library has no export file on this WordPress instance.  You may want to create an export file now.','mdocs'));
	define('MDOCS_DEFAULT_DESC', __('This file is part of the Documents Library.','mdocs'));	
	//ERRORS
	define('MDOCS_ERROR_1',__('No file was uploaded, please try again.','mdocs'));
	define('MDOCS_ERROR_2',__('Sorry, this file type is not permitted for security reasons.  If you want to add this file type please goto the setting page of Memphis Documents Library and add it to the Allowed File Type menu.','mdocs'));
	define('MDOCS_ERROR_3',__('No categories found.  The upload process can not proceed.','mdocs'));
	define('MDOCS_ERROR_4',__('Data was not submitted.  The submit process is out of sync, please refresh your browser and try again.','mdocs'));
	define('MDOCS_ERROR_5', __('File Upload Error.  Please try again.','mdocs'));
	define('MDOCS_ERROR_6', __('You are already at the most recent version of this document.','mdocs'));
	define('MDOCS_ERROR_7', __('The import file is too large, please update your php.ini files upload_max_filesize.','mdocs'));
	define('MDOCS_ERROR_8', __('An error occurred when creating a folder, please try again.','mdocs'));
	define('MDOCS_ERROR_9', __('You have reached the maxium number of input variable allowed for your servers configuration, this means you can not edit folders anymore.  To be able to edit folders again, please increase the variable max_input_vars in your php.ini file.','mdocs'));
}
?>