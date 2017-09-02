<?php
function mdocs_dashboard_menu() {
	global $add_error, $current_user;
	
	$mdocs_allow_upload = get_option('mdocs-allow-upload');
	if(!is_array($mdocs_allow_upload)) $mdocs_allow_upload = array();
	$wp_roles = get_editable_roles(); 
	if($current_user->roles[0] == 'administrator') {
		$role_object = get_role('administrator');
		$role_object->add_cap( 'mdocs-dashboard');
	}
	
	foreach($mdocs_allow_upload as $index => $role) {
		if($current_user->roles[0] == $index) {
			$role_object = get_role($index);
			$role_object->add_cap( 'mdocs-dashboard');
		}
	}
	add_menu_page( __('Memphis Documents Library','mdocs'), __('Memphis Docs','mdocs'), 'mdocs-dashboard', 'memphis-documents.php', 'mdocs_dashboard', MDOC_URL.'/assets/imgs/kon.ico'  );
	
	
	/* REMOVING MEMPHIS CUSTOM LOGIN INTEGRATION
	//MEMPHIS CUSTOM LOGIN INTEGRATION 3.0 AND HIGHER
	$plugin_path = preg_replace('/memphis-documents-library/','',dirname(__FILE__));
	if (is_plugin_active('memphis-wordpress-custom-login/memphis-wp-login.php')) $memphis_custom_login = (get_plugin_data($plugin_path.'memphis-wordpress-custom-login/memphis-wp-login.php'));
	if(isset($memphis_custom_login['Version'])) $memphis_version = intval($memphis_custom_login['Version']);
	else $memphis_version = 0;
	if (!is_plugin_active('memphis-wordpress-custom-login/memphis-wp-login.php') || $memphis_version < 3) {
		add_menu_page( __('Memphis Documents Library','mdocs'), __('Memphis Docs','mdocs'), 'administrator', 'memphis-documents.php', 'mdocs_dashboard', MDOC_URL.'/assets/imgs/kon.ico'  );
	}
	*/
	if ( is_admin() ){
		add_action('admin_init','mdocs_register_settings');
		add_action('admin_enqueue_scripts', 'mdocs_admin_script');
	}
	// ERRORS AND UPDATES
	if(isset($_FILES['mdocs']) && $_FILES['mdocs']['name'] == '' && $_POST['mdocs-type'] == 'mdocs-add')  { mdocs_errors(MDOCS_ERROR_1,'error'); $add_error = true; }
}

function mdocs_dashboard() {
	global $add_error;
	if(isset($_FILES['mdocs']) && $_FILES['mdocs']['name'] != '' && $_POST['mdocs-type'] == 'mdocs-add') mdocs_file_upload();
	elseif(isset($_FILES['mdocs']) && $_POST['mdocs-type'] == 'mdocs-update') mdocs_file_upload();
	elseif(isset($_GET['action']) && $_GET['action'] == 'delete-doc' && !isset($_POST['mdocs-type'])) mdocs_delete();
	elseif(isset($_GET['action']) && $_GET['action'] == 'delete-version') mdocs_delete_version();
	elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-import') {
		if(mdocs_file_upload_max_size() < $_FILES['size']) mdocs_errors(MDOCS_ERROR_7, 'error');
		else mdocs_import_zip();
	} elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-revision') mdocs_update_revision();
	elseif(isset($_GET['action']) && $_GET['action'] == 'mdocs-versions') mdocs_versions();
	elseif(isset($_POST['action']) && $_POST['action'] == 'mdocs-update-cats') mdocs_update_cats();
	mdocs_dashboard_view();
}

function mdocs_dashboard_view() {
	if($current_cat == null) $current_cat = $_GET['mdocs-cat'];
	if($current_cat == 'import') mdocs_import($current_cat);
	elseif($current_cat == 'export') mdocs_export($current_cat);
	elseif($current_cat == 'cats') mdocs_edit_cats($current_cat);
	elseif($current_cat == 'settings') mdocs_settings($current_cat);
	elseif($current_cat == 'batch') mdocs_batch_upload($current_cat);
	elseif($current_cat == 'short-codes') mdocs_shortcodes($current_cat);
	elseif($current_cat == 'filesystem-cleanup') mdocs_filesystem_cleanup($current_cat);
	elseif($current_cat == 'restore') mdocs_restore_defaults($current_cat);
	elseif($current_cat == 'allowed-file-types') mdocs_allowed_file_types($current_cat);
	elseif($current_cat == 'find-lost-files') mdocs_find_lost_files($current_cat);
	elseif($current_cat == 'server-compatibility') mdocs_server_compatibility($current_cat);
	else echo mdocs_the_list();
}

function mdocs_delete() {
	if ( $_REQUEST['mdocs-nonce'] == MDOCS_NONCE ) {
		$mdocs = get_option('mdocs-list');
		//$mdocs = mdocs_sort_by($mdocs, 0, 'dashboard', false);
		$mdocs = mdocs_array_sort();
		$index = $_GET['mdocs-index'];
		$upload_dir = wp_upload_dir();
		$mdocs_file = $mdocs[$index];
		if(is_array($mdocs[$index]['archived'])) foreach($mdocs[$index]['archived'] as $key => $value) @unlink($upload_dir['basedir'].'/mdocs/'.$value);
		wp_delete_attachment( intval($mdocs_file['id']), true );
		wp_delete_post( intval($mdocs_file['parent']), true );
		if(file_exists($upload_dir['basedir'].'/mdocs/'.$mdocs_file['filename'])) @unlink($upload_dir['basedir'].'/mdocs/'.$mdocs_file['filename']);
		unset($mdocs[$index]);
		$mdocs = array_values($mdocs);
		mdocs_save_list($mdocs);
	} else mdocs_errors(MDOCS_ERROR_4,'error');
}

function mdocs_add_update_ajax($edit_type='Add Document') {
	$cats = get_option('mdocs-cats');
	$mdocs = mdocs_array_sort();
	$mdoc_index = '';
	if(isset($_POST['mdocs-id'])) {	
		foreach($mdocs as $index => $doc) {
			if($_POST['mdocs-id'] == $doc['id']) {
				$mdoc_index = $index; break;
			}
		}
		$mdocs_post = get_post($mdocs[$mdoc_index]['parent']);
		$mdocs_desc = $mdocs_post->post_excerpt;
	}
	if(!is_string($mdoc_index) && $edit_type == 'Update Document' || $edit_type == 'Add Document') {
		if($edit_type == 'Update Document') $mdoc_type = 'mdocs-update';
		else $mdoc_type = 'mdocs-add';
		
		$post_tags = wp_get_post_tags($mdocs[$mdoc_index]['parent']);
		foreach($post_tags as $post_tag) $the_tags .= $post_tag->name.', ';
		$the_tags = rtrim($the_tags, ', ');
		$mdocs[$mdoc_index]['post-tags'] = $the_tags;
		$date_format = get_option('mdocs-date-format');
		$mdocs[$mdoc_index]['timestamp'] = gmdate($date_format,time()+MDOCS_TIME_OFFSET);
		$json = json_encode($mdocs[$mdoc_index]);
		echo $json;
	} else {
		$error['error'] = __('Index value not found, something has gone wrong.', 'mdocs')."\n\r";
		$error['error'] .= __('[ Index Value ]', 'mdocs').' => '.$mdoc_index."\n\r";
		$error['error'] .= __('[ Edit Type ]', 'mdocs').' => '.$edit_type;
		echo json_encode($error);
	}
}

function mdocs_uploader() {
	global $current_user;
	$cats = get_option('mdocs-cats');
	@session_start();
	if(isset($_SESSION['mdocs-nonce'])) $mdocs_session = $_SESSION['mdocs-nonce'];
	else $mdocs_session = '';
	@session_write_close();
?>
<div class="row">
	<div class="col-md-12" id="mdocs-add-update-container">
		<div class="page-header">
			<h1 id="mdocs-add-update-header"></h1>
		</div>
		<div class="">
			<form class="form-horizontal" enctype="multipart/form-data" action="" method="POST" id="mdocs-add-update-form">
				<input type="hidden" name="mdocs-current-user" value="<?php echo $current_user->user_login; ?>" />
				<input type="hidden" name="mdocs-type" value="" />
				<input type="hidden" name="mdocs-index" value="" />
				<input type="hidden" name="mdocs-cat" value="" />
				<input type="hidden" name="mdocs-pname" value="" />
				<input type="hidden" name="mdocs-nonce" value="<?php echo $mdocs_session; ?>" />
				<input type="hidden" name="mdocs-post-status-sys" value="" />
				
				<div class="well well-lg">
					<div class="page-header">
						<h2 id="mdocs-add-update-header"><?php _e('File Properties','mdocs'); ?></h2>
					</div>
					<div class="form-group form-group-lg has-success">
						<label class="col-sm-2 control-label" for="mdocs-name"><?php _e('File Name','mdocs'); ?></label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="mdocs-name" id="mdocs-name" />
						</div>
					</div>
					<div class="form-group form-group-lg has-warning">
						<label class="col-sm-2 control-label" for="mdocs-cat"><?php _e('Folder','mdocs'); ?></label>
						<div class="col-sm-10">
							<select class="form-control" name="mdocs-cat">
							<?php mdocs_get_cats($cats, $current_cat); ?>
							</select>
						</div>
					</div>
					<div class="form-group form-group-lg has-error">
						<label class="col-sm-2 control-label" for="mdocs-version"><?php _e('Version','mdocs'); ?></label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="mdocs-version" value="1.0" />
						</div>
					</div>
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label" for="mdocs-last-modified"><?php _e('Date','mdocs'); ?></label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="mdocs-last-modified" value="" />
						</div>
					</div>
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label" for="mdocs"><?php _e('File Uploader','mdocs'); ?></label>
						<div class="col-sm-10">
							<input class="form-control" type="file" name="mdocs" />
							<p class="help-block" id="mdocs-current-doc"></p>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="mdocs-file-status"><?php _e('File Status','mdocs'); ?></label>
						<div class="col-sm-10">
							<select class="form-control input-lg" name="mdocs-file-status" id="mdocs-file-status" >
								<option value="public" ><?php _e('Public','mdocs'); ?></option>
								<option value="hidden" ><?php _e('Hidden','mdocs'); ?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="mdocs-post-status"><?php _e('Post Status','mdocs'); ?></label>
						<div class="col-sm-10">
							<select class="form-control input-lg" name="mdocs-post-status" id="mdocs-post-status" >
								<option value="publish" ><?php _e('Published','mdocs'); ?></option>
								<option value="private" ><?php _e('Private','mdocs');  ?></option>
								<option value="pending"  ><?php _e('Pending Review','mdocs');  ?></option>
								<option value="draft" ><?php _e('Draft','mdocs');  ?></option>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-2 control-label" for="mdocs-social"><?php _e('Show Social Apps','mdocs'); ?></label>
						<div class="col-sm-1">
							<input class="form-control" type="checkbox" name="mdocs-social" checked />
						</div>
						<label class="col-sm-3 control-label" for="mdocs-non-members"><?php _e('Downloadable by Non Members','mdocs'); ?></label>
						<div class="col-sm-1">
							<input class="form-control" type="checkbox" name="mdocs-non-members" checked />
						</div>
					</div>
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label" for="mdocs-social"><?php _e('Contributors','mdocs'); ?></label>
						<div class="col-sm-10">
							<div id="mdocs-contributors-container">
								<span class="label label-primary mdocs-contributors" id="mdocs-current-owner"></span>
							</div>
							<input autocomplete="off" class="form-control" type="text" name="mdocs-add-contributors" id="mdocs-add-contributors" placeholder="<?php _e('Add contributor, users and roles types are allowed.'); ?>"/>
							<div class="mdocs-user-search-list hidden"></div>
						</div>
					</div>
					<div class="form-group form-group-lg">
						<label class="col-sm-2 control-label" for="mdocs-tags"><?php _e('Tags','mdocs'); ?></label>
						<div class="col-sm-10">
							<input class="form-control" type="text" name="mdocs-tags" id="mdocs-tags" placeholder="<?php _e('Comma Separated List', 'mdocs'); ?>" />
						</div>
					</div>
					<div class="form-group">
						<div class="page-header">
							<h2><?php _e('Description','mdocs'); ?></h2>
							<br>
							<div>
							<?php
							//$wp_edit_settings = array('quicktags' => false);
							wp_editor('', "mdocs-desc");
							?>
							</div>
						</div>
					</div>
				</div>
				
				<input type="submit" class="button button-primary" id="mdocs-save-doc-btn" value="" />
				
			</form>
		</div>
	</div>
</div>
	
<?php

}
?>