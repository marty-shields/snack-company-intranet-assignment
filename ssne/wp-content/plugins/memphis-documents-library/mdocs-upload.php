<?php
function mdocs_file_upload() {
	global $current_user, $wp_filetype;
	$mdocs = mdocs_array_sort();
	$mdocs_cats = get_option('mdocs-cats');
	foreach($mdocs as $index => $doc) {
		if($_POST['mdocs-index'] == $doc['id']) {
			$mdocs_index = $index; break;
		}
	}
	$_FILES['mdocs']['name'] = mdocs_filenames_to_latin($_FILES['mdocs']['name']);
	$mdocs_filename = $_FILES['mdocs']['name'];
	$mdocs_name = $_POST['mdocs-name'];
	$mdocs_fle_type = substr(strrchr($mdocs_filename, '.'), 1 );
	$mdocs_fle_size = $_FILES["mdocs"]["size"];
	$mdocs_type = $_POST['mdocs-type'];
	$mdocs_cat = $_POST['mdocs-cat'];
	$mdocs_desc = $_POST['mdocs-desc'];
	$mdocs_version = $_POST['mdocs-version'];
	$mdocs_social = $_POST['mdocs-social'];
	$mdocs_non_members = @$_POST['mdocs-non-members'];
	$mdocs_file_status = $_POST['mdocs-file-status'];
	$mdocs_doc_preview = @$_POST['mdocs-doc-preview'];
	if(!isset($_POST['mdocs-contributors']))  $_POST['mdocs-contributors'] = array();
	if(isset($_POST['mdocs-post-status'])) $mdocs_post_status = $_POST['mdocs-post-status'];
	else $mdocs_post_status = $_POST['mdocs-post-status-sys'];
	$date_format = get_option('mdocs-date-format');
	
	if(method_exists('DateTime', 'createFromFormat')) {
		$dtime = DateTime::createFromFormat($date_format, $_POST['mdocs-last-modified']);
		if($dtime != false) {
			$mdocs_last_modified = $dtime->getTimestamp();
		} else $mdocs_last_modified =time()+MDOCS_TIME_OFFSET;
	} else $mdocs_last_modified =time()+MDOCS_TIME_OFFSET;
	$upload_dir = wp_upload_dir();	
	$mdocs_user = $current_user->user_login;
	if($mdocs_file_status == 'hidden') $mdocs_post_status_sys = 'draft';
	else $mdocs_post_status_sys = $mdocs_post_status;
	$the_post_status = $mdocs_post_status_sys;
	$_FILES['mdocs']['name'] = preg_replace('/[^A-Za-z0-9\-._]/', '', $_FILES['mdocs']['name']);
	$_FILES['mdocs']['name'] = str_replace(' ','', $_FILES['mdocs']['name']);
	$_FILES['mdocs']['post_status'] = $the_post_status;
	//MDOCS FILE TYPE VERIFICATION	
	$mimes = get_allowed_mime_types();
	$valid_mime_type = false;
	foreach ($mimes as $type => $mime) {
		$file_type = wp_check_filetype($_FILES['mdocs']['name']);
		$found_ext = strpos($type,$file_type['ext']);
		if($found_ext !== false) {
			$valid_mime_type = true;
			break;
		}
	}
	//MDOCS NONCE VERIFICATION
	if ($_REQUEST['mdocs-nonce'] == MDOCS_NONCE ) {
		if(!empty($mdocs_cats)) {
			if($mdocs_type == 'mdocs-add') {
				if($valid_mime_type) {
		$_FILES['mdocs']['post-status'] = $mdocs_post_status;
		$upload = mdocs_process_file($_FILES['mdocs']);
		if($mdocs_version == '') $mdocs_version = '1.0';
		//elseif(!is_numeric($mdocs_version)) $mdocs_version = '1.0';
		if(!isset($upload['error'])) {
			if(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '') {
				$boxview = new mdocs_box_view();
				$boxview_file = $boxview->uploadFile(get_site_url().'/?mdocs-file='.$upload['attachment_id'].'&mdocs-url=false&is-box-view=true', $upload['filename']);
			} else $boxview_file['id'] = 0;
			//if(!is_array($_POST['mdocs-contributors']))  $_POST['mdocs-contributors'] = array();
			array_push($mdocs, array(
				'id'=>(string)$upload['attachment_id'],
				'parent'=>(string)$upload['parent_id'],
				'filename'=>$upload['filename'],
				'name'=>$upload['name'],
				'desc'=>$upload['desc'],
				'type'=>$mdocs_fle_type,
				'cat'=>$mdocs_cat,
				'owner'=>$mdocs_user,
				'contributors'=>$_POST['mdocs-contributors'],
				'size'=>(string)$mdocs_fle_size,
				'modified'=>(string)$mdocs_last_modified,
				'version'=>(string)$mdocs_version,
				'show_social'=>(string)$mdocs_social,
				'non_members'=> (string)$mdocs_non_members,
				'file_status'=>(string)$mdocs_file_status,
				'post_status'=> (string)$mdocs_post_status,
				'post_status_sys'=> (string)$mdocs_post_status_sys,
				'doc_preview'=>(string)$mdocs_doc_preview,
				'downloads'=>(string)0,
				'archived'=>array(),
				'ratings'=>array(),
				'rating'=>0,
				'box-view-id' => $boxview_file['id'],
			));
			$mdocs = mdocs_array_sort($mdocs);
			mdocs_save_list($mdocs);
		} else mdocs_errors($upload['error'],'error');
	} else mdocs_errors(MDOCS_ERROR_2 , 'error');
			} elseif($mdocs_type == 'mdocs-update') {
				if($_FILES['mdocs']['name'] != '') {
					if($valid_mime_type) {
						$old_doc = $mdocs[$mdocs_index];
						$old_doc_name = $old_doc['filename'].'-v'.preg_replace('/ /', '',$old_doc['version']);
						@rename($upload_dir['basedir'].'/mdocs/'.$old_doc['filename'],$upload_dir['basedir'].'/mdocs/'.$old_doc_name);
						$name = substr($old_doc['filename'], 0, strrpos($old_doc['filename'], '.') );
						$filename = $name.'.'.$mdocs_fle_type;
						$_FILES['mdocs']['name'] = $filename;
						$_FILES['mdocs']['parent'] = $old_doc['parent'];
						$_FILES['mdocs']['id'] = $old_doc['id'];
						$_FILES['mdocs']['post-status'] = $mdocs_post_status;
						$upload = mdocs_process_file($_FILES['mdocs']);
						if(!isset($upload['error'])) {
							if(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '') {
								$boxview = new mdocs_box_view();
								$boxview_file = $boxview->uploadFile(get_site_url().'/?mdocs-file='.$old_doc['id'].'&mdocs-url=false&is-box-view=true', $filename);
							} else $boxview_file['id'] = 0;
							if($mdocs_version == '') $mdocs_version = '1.0';
							else if($mdocs_version == $mdocs[$mdocs_index]['version']) $mdocs_version = $mdocs[$mdocs_index]['version'].'.1';
							$mdocs[$mdocs_index]['filename'] = $upload['filename'];
							$mdocs[$mdocs_index]['name'] = $upload['name'];
							$mdocs[$mdocs_index]['desc'] = $upload['desc'];
							$mdocs[$mdocs_index]['version'] = (string)$mdocs_version;
							$mdocs[$mdocs_index]['type'] = (string)$mdocs_fle_type;
							$mdocs[$mdocs_index]['cat'] = $mdocs_cat;
							$mdocs[$mdocs_index]['owner'] = $mdocs[$mdocs_index]['owner'];
							$mdocs[$mdocs_index]['contributors'] = $_POST['mdocs-contributors'];
							$mdocs[$mdocs_index]['size'] = (string)$mdocs_fle_size;
							$mdocs[$mdocs_index]['modified'] = (string)$mdocs_last_modified;
							$mdocs[$mdocs_index]['show_social'] =(string)$mdocs_social;
							$mdocs[$mdocs_index]['non_members'] =(string)$mdocs_non_members;
							$mdocs[$mdocs_index]['file_status'] =(string)$mdocs_file_status;
							$mdocs[$mdocs_index]['post_status'] =(string)$mdocs_post_status;
							$mdocs[$mdocs_index]['post_status_sys'] =(string)$mdocs_post_status_sys;
							$mdocs[$mdocs_index]['doc_preview'] =(string)$mdocs_doc_preview;
							$mdocs[$mdocs_index]['box-view-id'] = $boxview_file['id'];
							array_push($mdocs[$mdocs_index]['archived'], $old_doc_name);
							$mdocs = mdocs_array_sort($mdocs);
							mdocs_save_list($mdocs);
						} else mdocs_errors($upload['error'],'error');
					} else mdocs_errors(MDOCS_ERROR_2 , 'error');
				} else {
					//if($mdocs_desc == '') $desc = MDOCS_DEFAULT_DESC;
					//else
					$desc = $mdocs_desc;
					if($mdocs_name == '') $mdocs[$mdocs_index]['name'] = $_POST['mdocs-pname'];
					else $mdocs[$mdocs_index]['name'] = $mdocs_name;
					if($mdocs_version == '') $mdocs_version = $mdocs[$mdocs_index]['version'];
					$mdocs[$mdocs_index]['desc'] = $desc;
					$mdocs[$mdocs_index]['version'] = (string)$mdocs_version;
					$mdocs[$mdocs_index]['cat'] = $mdocs_cat;
					$mdocs[$mdocs_index]['owner'] = $mdocs[$mdocs_index]['owner'];
					$mdocs[$mdocs_index]['contributors'] = $_POST['mdocs-contributors'];
					$mdocs[$mdocs_index]['modified'] = (string)$mdocs_last_modified;
					$mdocs[$mdocs_index]['show_social'] =(string)$mdocs_social;
					$mdocs[$mdocs_index]['non_members'] =(string)$mdocs_non_members;
					$mdocs[$mdocs_index]['file_status'] =(string)$mdocs_file_status;
					$mdocs[$mdocs_index]['post_status'] =(string)$mdocs_post_status;
					$mdocs[$mdocs_index]['post_status_sys'] =(string)$mdocs_post_status_sys;
					$mdocs[$mdocs_index]['doc_preview'] =(string)$mdocs_doc_preview;
					$mdocs_post = array(
						'ID' => $mdocs[$mdocs_index]['parent'],
						'post_title' => $mdocs[$mdocs_index]['name'],
						'post_content' => '[mdocs_post_page]',
						'post_status' => $the_post_status,
						'post_excerpt' => $desc,
						'post_date' => gmdate('Y-m-d H:i:s',$mdocs_last_modified)
					);
					$mdocs_post_id = wp_update_post( $mdocs_post );
					//wp_set_post_tags( $mdocs_post_id, $mdocs_name.', '.$mdocs_cat.', memphis documents library, '.$wp_filetype['type'] );
					wp_set_post_tags($mdocs_post_id, $_POST['mdocs-tags']);
					$mdocs_attachment = array(
						'ID' => $mdocs[$mdocs_index]['id'],
						'post_title' => $mdocs_name
					);
					wp_update_post( $mdocs_attachment );
					$mdocs = mdocs_array_sort($mdocs);
					mdocs_save_list($mdocs);
				}
			}
		} else mdocs_errors(MDOCS_ERROR_3,'error');
	} else mdocs_errors(MDOCS_ERROR_4,'error');
}

function mdocs_create_document($valid_mime_type) {
	
}
?>