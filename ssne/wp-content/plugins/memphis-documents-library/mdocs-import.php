<?php
function mdocs_import() {
	$upload_dir = wp_upload_dir();
	mdocs_list_header();
?>
<div class="mdocs-container">
<h2><?php _e('Import Files','mdocs'); ?></h2>
<p><?php _e('There are two type of imports you can choose from.','mdocs'); ?></p>
<p>
	<ol>
		<li><b><?php _e('Keep Existing Files','mdocs'); ?></b>
			<blockquote><?php _e('Is the safest way to import.  This option keeps all your current files and only imports new ones. 
			<br>If a file that is being imported matches one on the current system, the one on the current system will be left untouched,
			<br>and you will have to manually import these files.','mdocs'); ?></blockquote>
		</li>
		<li><b><?php _e('Delete Old Files','mdocs'); ?></b>
			<blockquote><?php _e('Is a good when you have a empty documents library or you at looking to refresh your current library.'); ?>   
			<br><?php _e('This method deletes all files, posted and version on the current system. After the method has completed you will
			<br>get a list of all the conflicts that have occured make note of them.','mdocs'); ?>
			<i><?php _e('Please take great care in using this method as there is little to no return.','mdocs'); ?></i></blockquote>
		</li>
	</ol>
</p>
<form  id="mdocs-import" method="post" action="admin.php?page=memphis-documents.php&mdocs-cat=import" enctype="multipart/form-data">
	<h3><?php _e('Add the Zip File','mdocs'); ?>:</h3>
	<p><b><i><?php _e('Remember to always export any valuable data before doing an import.','mdocs'); ?></i></b></p>
	<!--<input type="hidden" value="memphis-documents.php" id="page" name="page"/>-->
	<input type="hidden" value="mdocs-import" id="action" name="action"/>
	<input type="radio" value="keep" name="radio1" disabled> <?php _e('Keep Existing Files','mdocs'); ?>
	<input type="radio" value="overwrite" name="radio1" checked> <?php _e('Delete Old Files','mdocs'); ?><br><br>
	<input type="file" name="mdocs-import-file" id="mdocs-import-file"/>
	<input type="submit" class="button button-primary" id="mdocs-import-submit" value="<?php _e('Import Memphis Documents Library','mdocs') ?>" />
</form><br>
</div>
<?php
	//if($_GET['mdocs-cat'] == 'export' || $_GET['mdocs-cat'] == 'import') mdocs_export_file_status();
}

function mdocs_import_zip() {
	$_FILES['mdocs-import-file']['name'] = mdocs_filenames_to_latin($_FILES['mdocs-import-file']['name']);
	if($_FILES['mdocs-import-file']['name'] != '' ) {
		$error = false;
		$upload_dir = wp_upload_dir();
		$mdocs = get_option('mdocs-list');
		$mdocs_zip_file = sys_get_temp_dir().'/'.$_FILES['mdocs-import-file']['name'];
		//Backup Current Memphis Documents
		if(!file_exists($upload_dir['basedir'].'/mdocs-backup/')) mkdir($upload_dir['basedir'].'/mdocs-backup/');
		$files = glob($upload_dir['basedir'].'/mdocs/*'); 
		foreach($files as $file){ 
		  if(is_file($file))
			$explode = explode('/',$file);
			$filename = $explode[count($explode)-1];
			@rename($file, $upload_dir['basedir'].'/mdocs-backup/'.$filename);
		}
		if(file_exists($mdocs_zip_file)) unlink($mdocs_zip_file);
		move_uploaded_file($_FILES['mdocs-import-file']['tmp_name'], $mdocs_zip_file);
		$zip_result = mdocs_unzip($mdocs_zip_file, sys_get_temp_dir());
		if(is_array($zip_result)) {
			if(file_exists(sys_get_temp_dir().'/mdocs/mdocs-list.txt')) {
				$mdocs_list_file = unserialize(file_get_contents(sys_get_temp_dir().'/mdocs/mdocs-list.txt'));
			} else $error = true;
			if(file_exists(sys_get_temp_dir().'/mdocs/mdocs-cats.txt')) {
				$mdocs_cats_file = unserialize(file_get_contents(sys_get_temp_dir().'/mdocs/mdocs-cats.txt'));
				if(!is_array($mdocs_cats_file[0])) {
					$new_mdocs_cats = array();
					foreach($mdocs_cats_file as $index => $cat) {
						array_push($new_mdocs_cats, array('slug' => $index,'name' => $cat, 'parent' => '', 'children' => array(), 'depth' => 0));
					}
					$mdocs_cats_file = $new_mdocs_cats;
					mdocs_errors(__('Old folder structure found, updated to the new folder structure.  It is recommened that you re-export you files again.  The process did finish.','mdocs'), 'error');
				}
			} else $error = true;
			if($mdocs_cats_file === false || $mdocs_list_file === false || $error ) {
				if(file_exists($upload_dir['basedir'].'/mdocs/mdocs-list.txt')) unlink(sys_get_temp_dir().'/mdocs/mdocs-cats.txt');
				if(file_exists($upload_dir['basedir'].'/mdocs/mdocs-cats.txt')) unlink(sys_get_temp_dir().'/mdocs/mdocs-list.txt');
				if(file_exists(sys_get_temp_dir().'/mdocs/'.$_FILES['mdocs-import-file']['name'])) unlink(sys_get_temp_dir().'/mdocs/'.$_FILES['mdocs-import-file']['name']);
				foreach($zip_result['file'] as $file) { 
					if(is_file($file)) unlink($file);
				}
				if(isset($zip_result['dir'])) {
				foreach($zip_result['dir'] as $dir) { 
					rmdir($dir);
				}
				}
				$files = glob($upload_dir['basedir'].'/mdocs-backup/*'); 
				foreach($files as $file) { 
					if(is_file($file))
						$explode = explode('/',$file);
						$filename = $explode[count($explode)-1];
						rename($file, $upload_dir['basedir'].'/mdocs/'.$filename);
				}
				rmdir($upload_dir['basedir'].'/mdocs-backup/');
				mdocs_errors('There was an error processing your saved variables file.  Sorry the import process can not continue.','error');
			} else {
				update_option('mdocs-zip',$_FILES['mdocs-import-file']['name']);
				unlink(sys_get_temp_dir().'/'.$_FILES['mdocs-import-file']['name']);
				$mdocs_list_conflicts = array();
				$mdocs_cats_conflicts = array();
				if($_POST['radio1'] == 'overwrite') {
					foreach($mdocs as $key => $value) {
						wp_delete_attachment( intval($mdocs[$key]['id']), true );
						wp_delete_post( intval($mdocs[$key]['parent']), true );
					}
					mdocs_save_list($mdocs_list_file);
					update_option('mdocs-cats', $mdocs_cats_file, '' , 'no');
					$cats = get_option('mdocs-cats');
					$mdocs = array();
				} else {
					$mdocs_cats = get_option('mdocs-cats');
					$modocs_list_return = array();
					foreach($mdocs as $key => $value) {
						$found = false;
						foreach($mdocs_list_file as $k => $v) {
							if($mdocs_list_file[$k]['filename'] == $mdocs[$key]['filename']) {
								$explode = explode('.',$v['filename']);
								$ext = $explode[count($explode)-1];
								$_150x150 = substr_replace($v['filename'],'-150x150',-4);
								$name = $_150x150.'.'.$ext;
								array_push($mdocs_list_conflicts, $mdocs_list_file[$k]['filename']);
								$found = true;
								unset($mdocs_list_file[$k]);
								$thumbnails = glob($upload_dir['basedir'].'/mdocs-backup/'.$name.'-150x55*');
								$versions = glob($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'].'-v*');
								foreach($thumbnails as $t) copy($t, str_replace('mdocs-backup','mdocs',$t));
								foreach($versions as $v) copy($v, str_replace('mdocs-backup','mdocs',$v));
								@copy($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'], $upload_dir['basedir'].'/mdocs/'.$value['filename']);
								break;
							}
						}
						if($found == false) {
							$explode = explode('.',$value['filename']);
							array_pop($explode);
							$name = implode('',$explode);
							$thumbnails = glob($upload_dir['basedir'].'/mdocs-backup/'.$name.'-150x55*');
							$versions = glob($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'].'-v*');
							foreach($thumbnails as $t) copy($t, str_replace('mdocs-backup','mdocs',$t));
							foreach($versions as $v) copy($v, str_replace('mdocs-backup','mdocs',$v));
							copy($upload_dir['basedir'].'/mdocs-backup/'.$value['filename'], $upload_dir['basedir'].'/mdocs/'.$value['filename']);
						}
					}
					foreach($mdocs_cats_file as $key => $value) {
						$found = false;
						if(!empty($cats)) {
							foreach($mdocs_cats as $k => $v) {
								if($key == $k) {
									array_push($mdocs_cats_conflicts, $mdocs_cats[$k]);
									$found = true;
									break;
								}
							}
						}
						if($found == false) $mdocs_cats[$key] = $value;
					}
					
					update_option('mdocs-cats',$mdocs_cats, '' , 'no');
				}
				foreach($mdocs_list_file as $key => $value) {
					if(file_exists(sys_get_temp_dir().'/mdocs/'.$value['filename'])) $move = rename(sys_get_temp_dir().'/mdocs/'.$value['filename'], $upload_dir['basedir'].'/mdocs/'.strtolower($value['filename']));
					elseif(file_exists(sys_get_temp_dir().'/mdocs/'.strtolower($value['filename']))) $move = rename(sys_get_temp_dir().'/mdocs/'.strtolower($value['filename']), $upload_dir['basedir'].'/mdocs/'.strtolower($value['filename']));
					else $file_not_found = true;
					
					if(is_array($value['archived']) && count($value['archived']) > 0){
						foreach($value['archived'] as $archive) {
							if(file_exists(sys_get_temp_dir().'/mdocs/'.$archive)) rename(sys_get_temp_dir().'/mdocs/'.$archive, $upload_dir['basedir'].'/mdocs/'.strtolower($archive));
							elseif(file_exists(sys_get_temp_dir().'/mdocs/'.strtolower($archive))) rename(sys_get_temp_dir().'/mdocs/'.strtolower($archive), $upload_dir['basedir'].'/mdocs/'.strtolower($archive));
						}
					}
					
					$hide_all_posts = get_option('mdocs-hide-all-posts');
					$hide_all_posts_non_members = get_option('mdocs-hide-all-posts-non-members');
					if($mdocs_list_file[$key]['post_status'] == '' && $hide_all_posts == '' && $hide_all_posts_non_members == '') $the_post_stauts = 'publish';
					elseif($hide_all_posts == '1') $the_post_stauts = 'draft';
					elseif($hide_all_posts_non_members == '1') $the_post_stauts = 'private';
					else $the_post_stauts = $mdocs_list_file[$key]['post_status'];
					if($mdocs_list_file[$key]['post_status_sys'] == '' && $hide_all_posts == '') $the_post_stauts_sys = 'publish';
					elseif($hide_all_posts == '1') $the_post_stauts_sys = 'draft';
					else $the_post_stauts_sys = $mdocs_list_file[$key]['post_status_sys'];
					$file = array(
						'type'=>'null',
						'tmp_name'=>'null',
						'error'=> 0,
						'size' => 0,
						'filename'=>strtolower($value['filename']),
						'name'=>$mdocs_list_file[$key]['name'],
						'desc'=>$mdocs_list_file[$key]['desc'],
						'post-status'=>$the_post_stauts,
						'modifed'=>$mdocs_list_file[$key]['modified']);
					$upload = mdocs_process_file($file, true);
					if(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '') {
						$boxview = new mdocs_box_view();
						$boxview_file = $boxview->uploadFile(get_site_url().'/?mdocs-file='.$upload['attachment_id'].'&mdocs-url=false&is-box-view=true', $upload['filename']);
					} else $boxview_file['id'] = 0;
					$owner = get_user_by('login', $mdocs_list_file[$key]['owner'])->user_login;
					if($owner == '') {
						$current_user = wp_get_current_user();
						$owner = $current_user->user_login;
					}
					if($mdocs_list_file[$key]['contributors'] == null) $mdocs_list_file[$key]['contributors'] = array();
					array_push($mdocs, array(
						'id'=>(string)$upload['attachment_id'],
						'parent'=>(string)$upload['parent_id'],
						'filename'=>(string)$upload['filename'],
						'name'=>(string)$upload['name'],
						'desc'=>(string)$upload['desc'],
						'type'=>$mdocs_list_file[$key]['type'],
						'cat'=>$mdocs_list_file[$key]['cat'],
						'owner'=>$owner,
						'contributors'=>$mdocs_list_file[$key]['contributors'],
						'size'=>(string)$mdocs_list_file[$key]['size'],
						'modified'=>(string)$mdocs_list_file[$key]['modified'],
						'version'=>(string)$mdocs_list_file[$key]['version'],
						'downloads'=>(string)$mdocs_list_file[$key]['downloads'],
						'archived'=>$mdocs_list_file[$key]['archived'],
						'show_social'=>$mdocs_list_file[$key]['show_social'],
						'non_members'=>$mdocs_list_file[$key]['non_members'],
						'file_status'=>$mdocs_list_file[$key]['file_status'],
						'post_status'=>$the_post_stauts,
						'post_status_sys'=>$the_post_stauts_sys,
						'ratings'=>$mdocs_list_file[$key]['ratings'],
						'rating'=>$mdocs_list_file[$key]['rating'],
						'doc_preview'=>$mdocs_list_file[$key]['doc_preview'],
						'box-view-id' => $boxview_file['id'],
					));
					mdocs_save_list($mdocs);
				}
				$files = glob($upload_dir['basedir'].'/mdocs-backup/*'); 
				foreach($files as $file){ 
					if(is_file($file)) unlink($file);
				}
				rmdir($upload_dir['basedir'].'/mdocs-backup/');
				$files = glob(sys_get_temp_dir().'/mdocs/*');
				if(file_exists(sys_get_temp_dir().'/mdocs/.htaccess')) unlink(sys_get_temp_dir().'/mdocs/.htaccess');
				foreach($files as $file){
					if(is_file($file)) unlink($file);
					if(is_dir($file)) rmdir($file);
				}
				@rmdir(sys_get_temp_dir().'/mdocs/');
				if(count($mdocs_list_conflicts) > 0) mdocs_errors('The following files where not added to the Documents Library. You will have to upload these files manually:<ul><li><b>' .implode('</li><li>',$mdocs_list_conflicts).'</b></li></ul>', 'error');
				if(count($mdocs_cats_conflicts) > 0) mdocs_errors('The following categories where not added to the Documents Library. You will have to add these categories manually:<ul><li><b>' .implode('</li><li>',$mdocs_cats_conflicts).'</b></li></ul>', 'error');
			}
		} else {
			unlink($upload_dir['basedir'].'/mdocs/'.$_FILES['mdocs-import-file']['name']);
			$files = glob($upload_dir['basedir'].'/mdocs-backup/*');
			if(file_exists($upload_dir['basedir'].'/mdocs-backup/.htaccess')) unlink(sys_get_temp_dir().'/mdocs/.htaccess');
			foreach($files as $file){ 
			  if(is_file($file))
				$explode = explode('/',$file);
				$filename = $explode[count($explode)-1];
				rename($file, $upload_dir['basedir'].'/mdocs/'.$filename);
			}
			rmdir($upload_dir['basedir'].'/mdocs-backup/');
			if(file_exists($upload_dir['basedir'].'/mdocs/mdocs-list.txt')) unlink($upload_dir['basedir'].'/mdocs/mdocs-cats.txt');
			if(file_exists($upload_dir['basedir'].'/mdocs/mdocs-cats.txt')) unlink($upload_dir['basedir'].'/mdocs/mdocs-list.txt');
		}
		mdocs_hide_show_toogle();
	} else mdocs_errors('The file you are trying to upload is not the correct file.  Please try again.','error');
}

function mdocs_unzip($zip_file, $output_path) {
	$zip = new ZipArchive;
	$zip_result = false;
	if ($zip->open($zip_file) === TRUE) {
		$zip_result = array();
		for($i = 0; $i < $zip->numFiles; $i++)  {
			$output_dir = false;
			$file = $zip->getNameIndex($i);
			if(!file_exists($output_path.'/mdocs/')) mkdir($output_path.'/mdocs/');
			if(preg_match('/.DS_Store/',$file)) $output_file = false;
			elseif(preg_match('/__MACOSX/',$file)) $output_file = false;
			elseif(preg_match('/mdocs\//',$file)) $output_file = $output_path.'/'.$file; 
			elseif(strrpos($file, '/') == strlen($file)-1 || strrchr($file, '\\') != false ) {
				@mkdir($output_path.'/mdocs/'.mdocs_filenames_to_latin($file));
				$output_file = false;
				$output_dir = $output_path.'/mdocs/'.$file;
			} elseif(!preg_match('/mdocs\//',$file)) $output_file = $output_path.'/mdocs/'.$file;
			elseif(preg_match('/mdocs\//',$file)) $output_file = $output_path;
			else $output_file = false;
			$output_file = mdocs_filenames_to_latin($output_file);
			//var_dump($output_file);
			if($output_file) {
				@copy('zip://'. $zip_file .'#'. $file , $output_file );
				$zip_result['file'][$i] = $output_file;
			}
			if($output_dir) {
				$zip_result['dir'][$i] = $output_dir;
			}
		}
		$zip_result['file'] = array_values($zip_result['file']);
		if(isset($zip_result['dir']))  $zip_result['dir'] = array_values($zip_result['dir']);
		$zip->close();
		return $zip_result;
	} else mdocs_errors('The Documents zip file has failed to imported.','error');	
	return $zip_result;
}

?>
