<?php
function mdocs_find_lost_files() {
	mdocs_list_header();
?>
<h2><?php _e('Find Lost Files','mdocs'); ?></h2>
<h4><?php _e('Sometime files may get lost, this process will allow you to find some of those lost files.','mdocs'); ?></h4>
<p><?php _e('Click the button below to run a scan of your Memphis Documents file system.  This scan will find files that may have been lost or moved to unknown locations.','mdocs'); ?></p>
<p><?php _e('Once the system scan is complete you will see a list of lost files,  select the folder you would like them to be transferred to and click the save button.  Once complete all the lost files will be visible again.','mdocs'); ?></p>
<h5><?php _e('This process may take awhile so please be patient.','mdocs'); ?></h5>
<button class="button button-primary" id="mdocs-find-lost-file-start"><?php _e('Start File Search'); ?></button>
<br><br>
<div id="mdocs-find-lost-files-results"></div>
<div id="mdocs-find-lost-files-saved"></div>
<?php
}
function find_lost_files() {
	global $found_cat;
	$docs = get_option('mdocs-list');
	$docs = mdocs_array_sort();
	$cats = get_option('mdocs-cats');
	echo '<form action="#" method="POST" id="mdocs-find-lost-files-save">';
	$id = 0;
	foreach($docs as $index => $doc) {
		$found_cat = false;
		mdocs_find_cat($doc['cat']);
		if($found_cat === false) {
			$found_at_least_one = true;
			$filesize_mb = number_format(round($doc['size']/1024,0));
			?>
			<div class="mdocs-find-lost-files" >
				<input type="hidden" name="mdocs-find-lost-files[<?php echo $id; ?>][index]" value="<?php echo $index; ?>"
				<label><?php _e('File Name','mdocs'); ?>:</label>
				<input class="disabled" type="text" name="mdocs-find-lost-files[<?php echo $id; ?>][name]" value="<?php echo $doc['name']; ?>" /> <?php echo $filesize_mb.' '.__('KB','mdocs'); ?>
				<br><br>
				<label><?php _e('Category','mdocs'); ?>:</label>
				<select name="mdocs-find-lost-files[<?php echo $id; ?>][cat]">
					<?php mdocs_get_cats($cats); ?>
				</select>
			
			</div>
			<?php
			$id++;
		}
	}
	?>
		<?php if($found_at_least_one == true) { ?>
		<br><br>
		<input type="submit" class="button button-primary" id="mdocs-find-lost-files-save-btn"  value="<?php _e('Save Files'); ?>" />
		<?php } ?>
	</form>
	<?php
	if($found_at_least_one == false) echo '<br><br><div class="alert alert-success" role="alert" > '.__('Congratulations, you have no lost files.', 'mdocs').' </div>';
	
}
function save_lost_files() {
	$docs = get_option('mdocs-list');
	$docs = mdocs_array_sort();
	parse_str($_POST['form-data'], $form_data);
	foreach($form_data['mdocs-find-lost-files'] as $doc) {
		$form_doc_name = str_replace('\\','',$doc['name']);
		$list_doc_name = str_replace('\\','',$docs[$doc['index']]['name']);
		if( $form_doc_name == $list_doc_name) {
			$docs[$doc['index']]['name'] = $list_doc_name;
			$docs[$doc['index']]['cat'] = $doc['cat'];
		} else {
			echo '<div class="alert alert-danger" role="alert" > '.__('A name conflict has occurred with the following file', 'mdocs').': '.$doc['name'].' => '.$docs[$doc['index']]['name'].' </div>';
		}
	}
	update_option('mdocs-list',$docs);
	echo '<br><br><div class="alert alert-success" role="alert" > '.__('Find Lost Files Process Complete.', 'mdocs').' </div>';
}
$found_cat = false;
function mdocs_find_cat($find_cat, $cats=null) {
	global $found_cat;
	$found_cat = false;
	if($cats == null) $cats = get_option('mdocs-cats');
	foreach($cats as $cat) {
		if($find_cat == $cat['slug']) {
			$found_cat = true;
		} else {
			if(count($cat['children']) > 0 && $found_cat == false) {
				mdocs_find_cat($find_cat, $cat['children'], $found_cat );
			}
		}
	}
	return $found_cat;
}
?>
