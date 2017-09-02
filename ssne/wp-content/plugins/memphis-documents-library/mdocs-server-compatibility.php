<?php
function mdocs_server_compatibility() {
	mdocs_list_header();
	$success = '<i class="fa fa-check fa-2x text-success"></i>';
	$fail = '<i class="fa fa-times fa-2x text-danger"></i>';
	if(function_exists('imagecreatefromjpeg'))  $php_gd = $success;
	else $php_gd = $fail.' ( '.__('Document thumbnails will not work.').')';
	if(intval(ini_get("max_input_vars")) >= 1000) $input_vars = $success.' ( '.__('The recommended value is 1000, if you have lots of folders we recommend increasing this value.').')';
	else $input_vars = $fail.' ( '.__('The recommended value is 1000, if you have lots of folders you may need to increasing this value.').')';
	if(!method_exists('DateTime', 'createFromFormat')) $datetime_method = $fail.' '.__('( PHP version 5.3 or greater is required to be able to modify dates )', 'mdocs');
	else $datetime_method = $success;
	if(!class_exists('ZipArchive')) $zip_archive_method = $fail.' '.__('( ZipArchive is used in import, export and batch upload, it needs to be install in order for these to function. )', 'mdocs');
	else $zip_archive_method = $success;
	if(!class_exists('imagick')) $imagick_method = $fail.' '.__('( Imagick is used to create pdf thumbnails. )', 'mdocs');
	else $imagick_method = $success;
	if(mdocs_check_read_write() == false) $upload_dir_read_write = $fail.' '.__('( The WordPress upload directory is not read/writeable.  Memphis Documents Library will not work with this access. )', 'mdocs');
	else $upload_dir_read_write = $success;
?>
<div class="alert alert-info">
	<h2><?php _e('Memphis Documents Server Compatiability Check', 'mdocs'); ?></h2>
	<h5><?php _e('Wordpres upload direcotry read/write access', 'mdocs');?> <?php echo $upload_dir_read_write; ?></h5>
	<h5><?php _e('PHP Image Processing and GD', 'mdocs');?> <?php echo $php_gd; ?></h5>
	<h5><?php _e('Recommended Maxim PHP Input Vars', 'mdocs'); ?> = <?php echo  ini_get("max_input_vars"); ?> <?php echo $input_vars; ?></h5>
	<h5><?php _e('Date Time Method Avaiable', 'mdocs');?> <?php echo $datetime_method; ?></h5>
	<h5><?php _e('ZipArchive Installed', 'mdocs');?> <?php echo $zip_archive_method; ?></h5>
	<h5><?php _e('Imagick Installed', 'mdocs');?> <?php echo $imagick_method; ?></h5>
</div>
<?php
}
?>