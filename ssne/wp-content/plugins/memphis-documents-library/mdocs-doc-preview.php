<?php
function mdocs_load_preview() {
	if(isset($_POST['type'])) {
		global $mdocs_img_types, $current_user;
		$mdocs = get_option('mdocs-list');
		$mdocs_show_preview = get_option('mdocs-show-preview');
		$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
		$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
		$found = false;
		$is_admin = $_POST['is_admin'];
		foreach($mdocs as $index => $the_mdoc) {
			$mdocs_show_non_members = $the_mdoc['non_members'];
			if(intval($the_mdoc['id']) == intval($_POST['mdocs_file_id']) && $found == false) {
				if(is_user_logged_in() || $mdocs_show_non_members == 'on' && $mdocs_show_preview = '1' ) {
					if($_POST['type'] == 'file') {
						$upload_dir = wp_upload_dir();
						$file_url = get_site_url().'/?mdocs-file='.$the_mdoc['id'].'|'.is_user_logged_in();
						foreach($the_mdoc['contributors'] as $user) {
							$contributor = false;
							if($current_user->user_login == $user) $contributor = true;
						}
						if($mdocs_hide_all_files && $contributor == false && $the_mdoc['owner'] != $current_user->user_login && $current_user->roles[0] != 'administrator') {
							echo '<div class="alert alert-warning" role="alert">'.__('Preview is unavailable for this file.','mdocs').'</div>';
						} elseif($the_mdoc['file_status'] == 'hidden' && $contributor == false && $the_mdoc['owner'] != $current_user->user_login && $current_user->roles[0] != 'administrator') {
							echo '<div class="alert alert-warning" role="alert">'.__('Preview is unavailable for this file.','mdocs').'</div>';
						} else if( is_user_logged_in() == false && $mdocs_hide_all_files_non_members) {
							echo '<div class="alert alert-warning" role="alert">'.__('Please login to view this file preview.','mdocs').'</div>';
						} else {
							echo '<h4>'.$the_mdoc['filename'].'</h4>';
							mdocs_doc_preview($the_mdoc);
						}
					} elseif($_POST['type'] == 'img') {
						foreach($the_mdoc['contributors'] as $user) {
							$contributor = false;
							if($current_user->user_login == $user) $contributor = true;
						}
						if($mdocs_hide_all_files && $contributor == false && $the_mdoc['owner'] != $current_user->user_login && $current_user->roles[0] != 'administrator') {
							echo '<div class="alert alert-warning" role="alert">'.__('Preview is unavailable for this file.','mdocs').'</div>';
						} elseif($the_mdoc['file_status'] == 'hidden' && $contributor == false && $the_mdoc['owner'] != $current_user->user_login && $current_user->roles[0] != 'administrator') {
							echo '<div class="alert alert-warning" role="alert">'.__('Preview is unavailable for this file.','mdocs').'</div>';
						} else if( is_user_logged_in() == false && $mdocs_hide_all_files_non_members) {
							echo '<div class="alert alert-warning" role="alert">'.__('Please login to view this file preview.','mdocs').'</div>';
						} else {
							echo '<h4>'.$the_mdoc['filename'].'</h4>';
							mdocs_show_image_preview($the_mdoc);
						}
					} elseif($_POST['type'] == 'show') {
						if($_POST['show_type'] == 'preview') {
							$upload_dir = wp_upload_dir();
							$file_url = get_site_url().'/?mdocs-file='.$the_mdoc['id'].'|'.is_user_logged_in();
							if($mdocs_hide_all_files || $the_mdoc['file_status'] == 'hidden') {
								echo '<div class="alert alert-warning" role="alert">'.__('Preview is unavailable for this file.','mdocs').'</div></div>';
							} else if( is_user_logged_in() == false && $mdocs_hide_all_files_non_members) {
								echo '<div class="alert alert-warning" role="alert">'.__('Please login to view this file preview.','mdocs').'</div>';
							} else {
								echo '<h4>'.$the_mdoc['filename'].'</h4>';
								if(in_array($the_mdoc['type'], $mdocs_img_types)) mdocs_show_image_preview($the_mdoc);
								else mdocs_doc_preview($the_mdoc);
							}
						} else {
							$mdocs_desc = apply_filters('the_content', $the_mdoc['desc']);
							$mdocs_desc = str_replace('\\','',$mdocs_desc);
							?>
							<div class="mdoc-desc">
								
								<?php mdocs_show_description($the_mdoc['id']); ?>
							</div>
							<?php
						}
					}
				}  else {
					?><div class="alert alert-warning" role="alert"><h1><?php _e('Sorry you are unauthorized to preview this file.','mdocs'); ?></h1></div><?php
				}
				$found = true;
				break;
			}
		}
	}
}