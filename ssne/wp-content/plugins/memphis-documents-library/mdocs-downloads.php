<?php
if(isset($_GET['mdocs-file'])) mdocs_download_file();
if(isset($_GET['mdocs-version'])) mdocs_download_file($_GET['mdocs-version']);
if(isset($_GET['mdocs-export-file'])) mdocs_download_export_file($_GET['mdocs-export-file']);
if(isset($_GET['mdocs-img-preview'])) mdocs_img_preview();

function mdocs_download_file() { add_action( 'plugins_loaded', 'mdocs_plugin_loaded' ); }
function mdocs_plugin_loaded() {
	global $current_user;
	$upload_dir = wp_upload_dir();
	$mdocs = get_option('mdocs-list');
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$is_logged_in = is_user_logged_in();
	$login_denied = false;
	$non_member = '';
	$file_status = '';
	$send_bot_alert = false;
	if(isset($_GET['is-box-view']) && $_GET['is-box-view'] == 'true') $box_view = true;
	else $box_view = false;
	if(!empty($_GET['mdocs-export-file']) ) { $filename = $_GET['mdocs-export-file']; }
	elseif(isset($_GET['mdocs-version']) ) { $filename = isset($_GET['mdocs-version']); }
	else {
		$serialized_file = unserialize(str_replace('\'','"',$_GET['mdocs-file']));
		if($serialized_file != false) {
			$mdocs_file = $serialized_file['id'];
			$is_google = true;
		}
		if($box_view === true) $send_bot_alert = false;
		else $send_bot_alert = true;
		if($serialized_file == false) $mdocs_file = $_GET["mdocs-file"];
		foreach($mdocs as $index => $value) {
			if($value['id'] == $mdocs_file ) {
				$filename = $value['filename'];
				$non_member = $value['non_members'];
				$file_status = $value['file_status'];
				$contributors = $value['contributors'];
				$owner = $value['owner'];
				break;
			} //else $filename = 'mdocs-empty';
		}
	}
	if($non_member == '' && $is_logged_in == false || $file_status == 'hidden' && !is_admin() || $mdocs_hide_all_files  || $mdocs_hide_all_files_non_members && is_user_logged_in() == false) $login_denied = true;
	else $login_denied = false;
	foreach($contributors as $user) {
		$login_denied = true;
		if($current_user->user_login == $user) $login_denied = false;
	}
	if($current_user->user_login == $owner) $login_denied = false;
	if($current_user->roles[0] == 'administrator') $login_denied = false;
	
	$mdocs_is_bot = mdocs_is_bot();
	if($mdocs_is_bot === false && $login_denied == false && !isset($_GET['mdocs-export-file']) &&  $box_view === false && !isset($_GET['mdocs-version']) && $is_google == false) {
		$mdocs[$index]['downloads'] = (string)(intval($mdocs[$index]['downloads'])+1);
		mdocs_save_list($mdocs);
	}
	
	//if(isset($_GET['mdocs-export-file'])) mdocs_export_zip();
	$file = $upload_dir['basedir']."/mdocs/".$filename;
	if(isset($_GET['mdocs-version'])) $filename = substr($filename, 0, strrpos($filename, '-'));
	$filetype = wp_check_filetype($file );
	if($login_denied == false  || $box_view || $is_google) {
		if (file_exists($file) && $mdocs_is_bot === false  ) {		
			header('Content-Description: File Transfer');
			header('Content-Type: '.$filetype['type']);
			header('Content-Disposition: attachment; filename='.$filename);
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false); 
			header('Pragma: public');
			header('Content-Length: ' . filesize($file));
			ob_clean();
			flush();
			readfile($file);
			if($send_bot_alert) mdocs_send_bot_alert($mdocs_is_bot);
			exit;
		} else if(!file_exists($file) && $box_view == true) die(__('Memphis Documents Error','mdocs').': '.basename($file).' '.__('was not found, no preview created for this file.', 'mdocs'));
		else {
			die(__('Memphis Documents Error','mdocs').': <b>'.basename($file).'</b> '.__('was not found, please contact the owner for assistance.', 'mdocs'));
		}
	} else die(__('Sorry you are unauthorized to download this file.','mdocs'));
	
}

function mdocs_img_preview() {
	require_once(ABSPATH . 'wp-includes/pluggable.php');
	$upload_dir = wp_upload_dir();
	$image = $upload_dir['basedir'].MDOCS_DIR.$_GET['mdocs-img-preview']; 
	$content = file_get_contents($image);
	header('Content-Type: image/jpeg');
	echo $content; exit();
}
?>