<?php
function mdocs_export() {
	$upload_dir = wp_upload_dir();
	$path = $upload_dir['basedir'];
	$mdocs = get_option('mdocs-list');
	$mdocs = htmlspecialchars(serialize($mdocs));
	$cats = htmlspecialchars(serialize(get_option('mdocs-cats')));
	mdocs_list_header();
?>
<div id="mdocs-export-container"> 
<h2><?php _e('Export Files','mdocs'); ?></h2>
<p>When you click the buttons below the document repository will create a ZIP files for you to save to your computer.</p>
<p>This compressed data, will contain your documents, saved variables, and media posts tied to each document.</p>
<p>Once you've saved the download file, you can use the Import function in another WordPress installation to import the content from this site.</p>
<h3>Click the Button to Export Memphis Documents</h3>
<form action="" method="post" id="mdocs-export">
	<input type="button" onclick="mdocs_download_zip('<?php echo get_option('mdocs-zip'); ?>');" id="mdocs-export-submit" class="button button-primary" value="<?php _e('Export Memphis Documents Library','mdocs'); ?>">
</form><br>
</div>
<?php
	//if($_GET['mdocs-cat'] == 'export' || $_GET['mdocs-cat'] == 'import') mdocs_export_file_status();
}
function mdocs_export_zip() {
	$mdocs_zip = get_option('mdocs-zip');
	$mdocs_list = get_option('mdocs-list');
	if(empty($mdocs_list)) $mdocs_list = array();
	$mdocs_cats = get_option('mdocs-cats');
	if(is_string($mdocs_cats)) $mdocs_cats = array();
	$upload_dir = wp_upload_dir();
	$mdocs_zip_file = sys_get_temp_dir().'/'.$mdocs_zip;
	if(is_dir(sys_get_temp_dir().'/mdocs/')) {
		$files = glob(sys_get_temp_dir().'/mdocs/*');
		foreach($files as $file) if(is_file($file)) unlink($file);
		if(is_file(sys_get_temp_dir().'/mdocs/.htaccess')) unlink(sys_get_temp_dir().'/mdocs/.htaccess');
		rmdir(sys_get_temp_dir().'/mdocs/');
	}
	mkdir(sys_get_temp_dir().'/mdocs/');
	$mdocs_cats_file = sys_get_temp_dir().'/mdocs/'.MDOCS_CATS;
	$mdocs_list_file = sys_get_temp_dir().'/mdocs/'.MDOCS_LIST;
	file_put_contents($mdocs_cats_file, serialize($mdocs_cats));
	file_put_contents($mdocs_list_file, serialize($mdocs_list));
	mdocs_zip_dir($upload_dir['basedir'].'/mdocs',$mdocs_zip_file,true);
	if(file_exists($mdocs_cats_file)) unlink($mdocs_cats_file);
	if(file_exists($mdocs_list_file)) unlink($mdocs_list_file);
	
	if(is_dir(sys_get_temp_dir().'/mdocs/')) {
		$files = glob(sys_get_temp_dir().'/mdocs/*');
		if(file_exists(sys_get_temp_dir().'/mdocs/.htaccess')) unlink(sys_get_temp_dir().'/mdocs/.htaccess');
		foreach($files as $file) if(is_file($file)) unlink($file);
		rmdir(sys_get_temp_dir().'/mdocs/');
	}
	
}
function mdocs_zip_dir($sourcePath, $outZipPath)  { 
    @unlink($outZipPath);
	$pathInfo = pathInfo($sourcePath); 
    $parentPath = $pathInfo['dirname']; 
    $dirName = $pathInfo['basename'];
	if(class_exists('ZipArchive')) {
		$z = new ZipArchive(); 
		$z->open($outZipPath, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE); 
		$z->addEmptyDir($dirName); 
		mdocs_folder_zip($sourcePath, $z, strlen("$parentPath/")); 
		$z->close();
	} else die('ZipArchive Not Installed.');
    
}
function mdocs_folder_zip($folder, &$zipFile, $exclusiveLength) { 
	$handle = opendir($folder);
	$zipFile->addFile(sys_get_temp_dir().'/mdocs/'.MDOCS_CATS, 'mdocs/'.MDOCS_CATS); 
	$zipFile->addFile(sys_get_temp_dir().'/mdocs/'.MDOCS_LIST, 'mdocs/'.MDOCS_LIST); 
    while (false !== $f = readdir($handle)) { 
      if ($f != '.' && $f != '..' && $f != 'mdocs-files.bak') { 
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip. 
        $localPath = substr($filePath, $exclusiveLength); 
        if (is_file($filePath)) { 
          $zipFile->addFile($filePath, $localPath); 
        } elseif (is_dir($filePath)) { 
          // Add sub-directory. 
          $zipFile->addEmptyDir($localPath); 
          mdocs_folder_zip($filePath, $zipFile, $exclusiveLength); 
        } 
      } 
    } 
    closedir($handle); 
}
function mdocs_download_export_file($file) {
	$filename = $file;
	$file = sys_get_temp_dir()."/".$file;
	if (file_exists($file)) {		
			header('Content-Description: File Transfer');
			header('Content-Type: application/zip');
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
			exit;
	} else mdocs_errors(__('Memphis Documents Error','mdocs').': '.basename($file).' '.__('was not found, file not exported.', 'mdocs'), 'error');
}
?>