<?php
function mdocs_settings($cat) {
	$upload_dir = wp_upload_dir();
	$mdocs_list_type = get_option( 'mdocs-list-type' );
	$mdocs = get_option('mdocs-list');
	$mdocs_list_type_dashboard = get_option( 'mdocs-list-type-dashboard' );
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_show_post_menu = get_option('mdocs-show-post-menu');
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$mdocs_hide_all_posts = get_option( 'mdocs-hide-all-posts' );
	$mdocs_hide_all_posts_default = get_option( 'mdocs-hide-all-posts-default' );
	$mdocs_hide_all_posts_non_members = get_option( 'mdocs-hide-all-posts-non-members' );
	$mdocs_hide_all_posts_non_members_default = get_option( 'mdocs-hide-all-posts-non-members-default' );
	$mdocs_show_downloads = get_option( 'mdocs-show-downloads' );
	$mdocs_show_author = get_option( 'mdocs-show-author' );
	$mdocs_show_version = get_option( 'mdocs-show-version' );
	$mdocs_show_update = get_option( 'mdocs-show-update' );
	$mdocs_show_ratings = get_option( 'mdocs-show-ratings' );
	$mdocs_show_social = get_option( 'mdocs-show-social' );
	$mdocs_show_share = get_option('mdocs-show-share');
	$mdocs_show_new_banners = get_option('mdocs-show-new-banners');
	$mdocs_time_to_display_banners = strval(get_option('mdocs-time-to-display-banners'));
	$mdocs_sort_type = get_option('mdocs-sort-type');
	$mdocs_sort_style = get_option('mdocs-sort-style');
	$mdocs_default_content = get_option('mdocs-default-content');
	$mdocs_show_description = get_option('mdocs-show-description');
	$mdocs_show_preview = get_option('mdocs-show-preview');
	$mdocs_htaccess = get_option('mdocs-htaccess');
	$mdocs_font_size = get_option('mdocs-font-size');
	$mdocs_hide_subfolders = get_option('mdocs-hide-subfolders');
	$mdocs_disable_bootstrap = get_option('mdocs-disable-bootstrap');
	$mdocs_disable_jquery = get_option('mdocs-disable-jquery');
	$mdocs_disable_fontawesome = get_option('mdocs-disable-fontawesome');
	$mdocs_show_no_file_found = get_option('mdocs-show-no-file-found');
	$mdocs_preview_type = get_option('mdocs-preview-type');
	mdocs_hide_show_toogle();
	mdocs_list_header();
	/* FOR TESTING PROPOSES */
	/*
	$cats = json_decode(file_get_contents(dirname(__FILE__).'/cats.txt'), true);
	$list = json_decode(file_get_contents(dirname(__FILE__).'/list.txt'), true);
	update_option('mdocs-list',$list);
	update_option('mdocs-cats', $cats);
	*/
?>
<!-- COLOR PICKER 
<input type="text" value="#bada55" class="mdocs-color-picker" />
<input type="text" value="#bada55" class="mdocs-color-picker" data-default-color="#effeff" />
-->
<div class="mdocs-donate-btn">
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input name="cmd" type="hidden" value="_s-xclick" />
		<input name="encrypted" type="hidden" value="-----BEGIN PKCS7-----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-----END PKCS7----- " />
		<input alt="PayPal - The safer, easier way to pay online!" name="submit" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" type="image" />
		<img src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" alt="" width="1" height="1" border="0" />
	</form>
</div>
<h2><?php _e('Library Settings','mdocs'); ?></h2>
<form enctype="multipart/form-data" method="post" action="options.php" class="mdocs-setting-form">
	<?php settings_fields( 'mdocs-global-settings' ); ?>
	<input type="hidden" name="mdocs-view-private[administrator]" value="1" />
	<input type="hidden" name="mdocs-download-color-normal" value="<?php echo get_option( 'mdocs-download-color-normal' ); ?>" />
	<input type="hidden" name="mdocs-download-color-hover" value="<?php echo get_option( 'mdocs-download-color-hover' ); ?>" />
	<input type="hidden" name="mdocs-hide-all-posts-default" value="<?php echo get_option( 'mdocs-hide-all-posts-default' ); ?>" />
	<input type="hidden" name="mdocs-hide-all-posts-non-members-default" value="<?php echo get_option( 'mdocs-hide-all-posts-non-members-default' ); ?>" />

<table class="table form-table mdocs-settings-table">
	<tr>
		<th><?php _e('Allowed to Upload'); ?></th>
		<td>
			<?php
			$wp_roles = get_editable_roles();
			if(!is_array(get_option('mdocs-allow-upload'))) update_option('mdocs-allow-upload' , array());
			$mdocs_allow_upload = get_option('mdocs-allow-upload');
			foreach($wp_roles as $index => $role) {
				$checked = false;
				if($index != 'administrator') {
					$role_object = get_role($index);
					if(array_key_exists($index, $mdocs_allow_upload)) { $checked = true; $role_object->add_cap( 'mdocs-dashboard'); }
					else $role_object->remove_cap( 'mdocs-dashboard');
			?>
			<input type="checkbox" name="mdocs-allow-upload[<?php echo $index; ?>]" value="1"  <?php checked($checked , 1) ?> /> <span><?php echo ucfirst($index); ?></span><br>
			<?php
				} 
			} 
			?>
		</td>
		<th><?php _e('Disable Thrid Party Includes'); ?>:</th>
		<td>
			<input type="checkbox" name="mdocs-disable-bootstrap" value="1"  <?php checked(1,$mdocs_disable_bootstrap) ?> /> <span><?php _e('Bootstrap'); ?></span><br>
			<input type="checkbox" name="mdocs-disable-jquery" value="1"  <?php checked(1,$mdocs_disable_jquery) ?> /> <span><?php _e('jQuery'); ?></span><br>
			<input type="checkbox" name="mdocs-disable-fontawesome" value="1"  <?php checked(1,$mdocs_disable_fontawesome) ?> /> <span><?php _e('Fontawesome'); ?></span><br>
		</td>
	</tr>
	<tr>
		<th><?php _e('Document Preview Settings', 'mdocs'); ?></th>
		<td>
			<input type="radio" name="mdocs-preview-type" value="google" <?php checked('google', $mdocs_preview_type) ?>> <?php _e('Use Google Docuement Preview', 'mdocs'); ?><br>			
			<input type="radio" name="mdocs-preview-type" value="box" <?php checked('box', $mdocs_preview_type) ?>> <?php _e('Use Box Document Preview', 'mdocs'); ?><br>
			<label><?php _e('Box View Key','mdocs'); ?></label><br>
			<input style="width: 100%;" type="text" value="<?php echo get_option('mdocs-box-view-key'); ?>" name="mdocs-box-view-key"  placeholder="<?php _e('Enter your key here', 'mdocs'); ?>"/><br>
			<a href="https://developers.box.com" target="_blank" alt="<?php _e('Sign up for Box Developer site to get your key.', 'mdocs'); ?>"><?php _e('Sign up for Box Developer site to get your key.', 'mdocs'); ?></a>
			<p><?php _e('A Box View Key is needed to use Box Document Preview,', 'mdocs'); ?><br><?php _e('just click on the link above and sign up to create you own key.', 'mdocs'); ?></p>
			
		</td>
		<th><?php _e('Document List Font Size'); ?>:</th>
		<td>
			<select name="mdocs-font-size" id="mdocs-font-size" >
				<option value="3" <?php if($mdocs_font_size == '3') echo 'selected'; ?>><?php _e('3px','mdocs'); ?></option>
				<option value="4" <?php if($mdocs_font_size == '4') echo 'selected'; ?>><?php _e('4px','mdocs'); ?></option>
				<option value="5" <?php if($mdocs_font_size == '5') echo 'selected'; ?>><?php _e('5px','mdocs'); ?></option>
				<option value="6" <?php if($mdocs_font_size == '6') echo 'selected'; ?>><?php _e('6px','mdocs'); ?></option>
				<option value="7" <?php if($mdocs_font_size == '7') echo 'selected'; ?>><?php _e('7px','mdocs'); ?></option>
				<option value="8" <?php if($mdocs_font_size == '8') echo 'selected'; ?>><?php _e('8px','mdocs'); ?></option>
				<option value="9" <?php if($mdocs_font_size == '9') echo 'selected'; ?>><?php _e('9px','mdocs'); ?></option>
				<option value="10" <?php if($mdocs_font_size == '10') echo 'selected'; ?>><?php _e('10px','mdocs'); ?></option>
				<option value="11" <?php if($mdocs_font_size == '11') echo 'selected'; ?>><?php _e('11px','mdocs'); ?></option>
				<option value="12" <?php if($mdocs_font_size == '12') echo 'selected'; ?>><?php _e('12px','mdocs'); ?></option>
				<option value="13" <?php if($mdocs_font_size == '13') echo 'selected'; ?>><?php _e('13px','mdocs'); ?></option>
				<option value="14" <?php if($mdocs_font_size == '14') echo 'selected'; ?>><?php _e('14px','mdocs'); ?></option>
				<option value="15" <?php if($mdocs_font_size == '15') echo 'selected'; ?>><?php _e('15px','mdocs'); ?></option>
				<option value="16" <?php if($mdocs_font_size == '16') echo 'selected'; ?>><?php _e('16px','mdocs'); ?></option>
				<option value="17" <?php if($mdocs_font_size == '17') echo 'selected'; ?>><?php _e('17px','mdocs'); ?></option>
				<option value="18" <?php if($mdocs_font_size == '18') echo 'selected'; ?>><?php _e('18px','mdocs'); ?></option>
				<option value="19" <?php if($mdocs_font_size == '19') echo 'selected'; ?>><?php _e('19px','mdocs'); ?></option>
				<option value="20" <?php if($mdocs_font_size == '20') echo 'selected'; ?>><?php _e('20px','mdocs'); ?></option>
				<option value="21" <?php if($mdocs_font_size == '21') echo 'selected'; ?>><?php _e('21px','mdocs'); ?></option>
				<option value="22" <?php if($mdocs_font_size == '22') echo 'selected'; ?>><?php _e('22px','mdocs'); ?></option>
				<option value="23" <?php if($mdocs_font_size == '23') echo 'selected'; ?>><?php _e('23px','mdocs'); ?></option>
				<option value="24" <?php if($mdocs_font_size == '24') echo 'selected'; ?>><?php _e('24px','mdocs'); ?></option>
				<option value="25" <?php if($mdocs_font_size == '25') echo 'selected'; ?>><?php _e('25px','mdocs'); ?></option>
				<option value="26" <?php if($mdocs_font_size == '26') echo 'selected'; ?>><?php _e('26px','mdocs'); ?></option>
				<option value="27" <?php if($mdocs_font_size == '27') echo 'selected'; ?>><?php _e('27px','mdocs'); ?></option>
				<option value="28" <?php if($mdocs_font_size == '28') echo 'selected'; ?>><?php _e('28px','mdocs'); ?></option>
				<option value="29" <?php if($mdocs_font_size == '29') echo 'selected'; ?>><?php _e('29px','mdocs'); ?></option>
				<option value="30" <?php if($mdocs_font_size == '30') echo 'selected'; ?>><?php _e('30px','mdocs'); ?></option>
				<option value="31" <?php if($mdocs_font_size == '31') echo 'selected'; ?>><?php _e('31px','mdocs'); ?></option>
				<option value="32" <?php if($mdocs_font_size == '32') echo 'selected'; ?>><?php _e('32px','mdocs'); ?></option>
			</select>
		</td>
	</tr>
	<tr>
		<th><?php _e('Private File Post Viewing','mdocs'); ?></th>
		<td>
			<?php
			$wp_roles = get_editable_roles(); 
			$mdocs_view_private = get_option('mdocs-view-private');
			foreach($wp_roles as $index => $role) {
				if($index != 'administrator') {
					$checked = false;
					if(array_key_exists($index, $mdocs_view_private)) $checked = true;
					else {
						$role_object = get_role($index);
						$role_object->remove_cap('read_private_posts');
						$role_object->remove_cap('read_private_pages');
					}
			?>
			<input type="checkbox" name="mdocs-view-private[<?php echo $index; ?>]" value="1"  <?php checked($checked , 1) ?> /> <span><?php echo ucfirst($index); ?></span><br>
			<?php
				}
			}
			?>
		</td>
		<th><?php _e('Style Settings','mdocs'); ?></th>
		<td>
			<h2><?php _e('Download Button Options','mdocs'); ?></h2>
			<h4><?php _e('Background Options','mdocs'); ?></h4>
			<label><?php _e('Background Color','mdocs'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-color-normal'); ?>" name="mdocs-download-color-normal" id="bg-color-mdocs-picker" data-default-color="#d14836" /><br>
			<label><?php _e('Background Hover Color','mdocs'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-color-hover'); ?>" name="mdocs-download-color-hover" id="bg-hover-color-mdocs-picker" data-default-color="#c34131" /><br>
			<label><?php _e('Text Color','mdocs'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-text-color-normal'); ?>" name="mdocs-download-text-color-normal" id="bg-text-color-mdocs-picker" data-default-color="#ffffff" /><br>
			<label><?php _e('Text Hover Color','mdocs'); ?></label>
			<input type="text" value="<?php echo get_option('mdocs-download-text-color-hover'); ?>" name="mdocs-download-text-color-hover" id="bg-text-hover-color-mdocs-picker" data-default-color="#ffffff" /><br>
			<h4><?php _e('Download Button Preview','mdocs'); ?></h4>
			<button class="btn btn-primary mdocs-download-btn-config"><?php echo __('Download','mdocs');?></button>
		</td>
	</tr>
	<tr>
		<th><?php _e('Displayed File Information','mdocs'); ?></th>
		<td>
			<input type="checkbox" name="mdocs-show-downloads" value="1"  <?php checked(1,$mdocs_show_downloads) ?>/> <?php _e('Downloads','mdocs'); ?><br>
			<input type="checkbox" name="mdocs-show-author" value="1"  <?php checked( $mdocs_show_author, 1) ?>/> <?php _e('Author','mdocs'); ?><br>
			<input type="checkbox" name="mdocs-show-version" value="1"  <?php checked( $mdocs_show_version, 1) ?>/> <?php _e('Version','mdocs'); ?><br>
			<input type="checkbox" name="mdocs-show-update" value="1"  <?php checked( $mdocs_show_update, 1) ?>/> <?php _e('Updated','mdocs'); ?><br>
			<input type="checkbox" name="mdocs-show-ratings" value="1"  <?php checked( $mdocs_show_ratings, 1) ?>/> <?php _e('Ratings','mdocs'); ?><br>
			<input type="checkbox" name="mdocs-show-social" value="1"  <?php checked( $mdocs_show_social, 1) ?>/> <?php _e('Social','mdocs'); ?><br>
		</td>
	
		<th><?php _e('Hide Things','mdocs'); ?></th>
		<td>
			<input type="checkbox" name="mdocs-show-no-file-found" value="1"  <?php checked(1,$mdocs_show_no_file_found) ?> /> <span><?php _e('Show No Files Found'); ?></span><br>
			<input type="checkbox" id="mdocs-show-post-menu" name="mdocs-show-post-menu" value="1"  <?php checked(1,$mdocs_show_post_menu) ?>/> <?php _e('Show Memphis Posts Menu Item','mdocs'); ?><br>
			<input type="checkbox" name="mdocs-hide-subfolders" value="1"  <?php checked(1,$mdocs_hide_subfolders) ?>/> <span><?php _e('Hide Sub Folders'); ?></span><br>
			<input type="checkbox" id="mdocs-hide-all-files" name="mdocs-hide-all-files" value="1"  <?php checked(1,$mdocs_hide_all_files) ?>/> <?php _e('All Files','mdocs'); ?><br>
			<input type="checkbox" id="mdocs-hide-all-posts" name="mdocs-hide-all-posts" value="1"  <?php checked(1,$mdocs_hide_all_posts) ?>/> <?php _e('All Posts','mdocs'); ?><br>
			<input type="checkbox" id="mdocs-hide-all-files-non-members" name="mdocs-hide-all-files-non-members" value="1"  <?php checked(1,$mdocs_hide_all_files_non_members) ?>/> <?php _e('All Files: (Non Members)','mdocs'); ?><br>
			<input type="checkbox" id="mdocs-hide-all-posts-non-members" name="mdocs-hide-all-posts-non-members" value="1"  <?php checked(1,$mdocs_hide_all_posts_non_members) ?>/> <?php _e('All Posts: (Non Members)','mdocs'); ?><br>
			
		</td>
	</tr>
	<tr>
		<th><?php _e('Show Sharing Button/Link','mdocs'); ?></th>
		<td>
			<input type="checkbox" name="mdocs-show-share" value="1"  <?php checked( $mdocs_show_share, 1) ?>/> 
		</td>
		<th><?php _e('Date/Time Format'); ?></th>
		<td>
			<input type="text" name="mdocs-date-format" value="<?php echo get_option('mdocs-date-format');?>"  /><br>
			<a href="http://php.net/manual/en/function.date.php" target="_blank" alt="<?php _e('Date/Time Format Reference'); ?>"><?php _e('Date/Time Format Reference'); ?></a>
		</td>
	</tr>
	<tr>
		<th><?php _e('New & Updated Banner','mdocs'); ?></th>
		<td>
			<input type="checkbox" id="mdocs-show-new-banners" name="mdocs-show-new-banners" value="1"  <?php checked(1,$mdocs_show_new_banners) ?>/> <?php _e('Show New & Updated Banner','mdocs'); ?><br>
			<input class="width-30" type="text" id="mdocs-time-to-display-banners" name="mdocs-time-to-display-banners" value="<?php echo $mdocs_time_to_display_banners; ?>"/> <?php _e('days - Time to Displayed','mdocs'); ?><br>
		</td>
		<th><?php _e('Default Sort Options','mdocs'); ?></th>
		<td>
			<label><?php _e('Order Types:','mdocs'); ?>
				<select name="mdocs-sort-type" id="mdocs-sort-type" >
					<option value="name" <?php if($mdocs_sort_type == 'name') echo 'selected'; ?>><?php _e('File Name','mdocs'); ?></option>
					<option value="downloads" <?php if($mdocs_sort_type == 'downloads') echo 'selected'; ?>><?php _e('Number of Downloads','mdocs'); ?></option>
					<option value="version" <?php if($mdocs_sort_type == 'version') echo 'selected'; ?>><?php _e('Version','mdocs'); ?></option>
					<option value="owner" <?php if($mdocs_sort_type == 'owner') echo 'selected'; ?>><?php _e('Author','mdocs'); ?></option>
					<option value="modified" <?php if($mdocs_sort_type == 'modified') echo 'selected'; ?>><?php _e('Last Updated','mdocs'); ?></option>
					<option value="rating" <?php if($mdocs_sort_type == 'rating') echo 'selected'; ?>><?php _e('Rating','mdocs'); ?></option>
				</select>
			</label><br><br>
			<label><?php _e('Order Style:','mdocs'); ?>
				<select name="mdocs-sort-style" id="mdocs-sort-style" >
					<option value="desc" <?php if($mdocs_sort_style == 'desc') echo 'selected'; ?>><?php _e('Sort Descending','mdocs'); ?></option>
					<option value="asc" <?php if($mdocs_sort_style == 'asc') echo 'selected'; ?>><?php _e('Sort Ascending','mdocs'); ?></option>
				</select>
			</label><br><br>
			<label><?php _e('Disable User Sort','mdocs'); ?>
				<input type="checkbox" id="mdocs-disable-user-sort" name="mdocs-disable-user-sort" value="1"  <?php checked(1,get_option('mdocs-disable-user-sort')) ?>/>
			</label>
		</td>
	</tr>
	<tr>
		<th><?php _e('Document Page Settings','mdocs'); ?></th>
		<td>
			<label><?php _e('Default Content:','mdocs'); ?>
				<select name="mdocs-default-content" id="mdocs-default-content" >
					<option value="description" <?php if($mdocs_default_content == 'description') echo 'selected'; ?>><?php _e('Description','mdocs'); ?></option>
					<option value="preview" <?php if($mdocs_default_content == 'preview') echo 'selected'; ?>><?php _e('Preview','mdocs'); ?></option>
				</select>
			</label><br><br>
			<input type="checkbox" id="mdocs-show-description" name="mdocs-show-description" value="1"  <?php checked(1,$mdocs_show_description) ?>/> <?php _e('Show Description','mdocs'); ?><br>
			<input type="checkbox" id="mdocs-show-preview" name="mdocs-show-preview" value="1"  <?php checked(1,$mdocs_show_preview) ?>/> <?php _e('Show Preview','mdocs'); ?><br>
		</td>
		<th><?php _e('.htaccess File Editor','mdocs'); ?></th>
		<?php
		
		if(isset($_GET['settings-updated']) && $_GET['page'] == 'memphis-documents.php') {
			$upload_dir = wp_upload_dir();
			$htaccess = file_put_contents($upload_dir['basedir'].MDOCS_DIR.'.htaccess', $mdocs_htaccess);
		}
		?>
		<td>
				<textarea cols="30" rows="10" name="mdocs-htaccess"><?php echo $mdocs_htaccess; ?></textarea>
		</td>
	</tr>
	<!-- ROLES AND PERMISSION SETTINGS 
	<tr>
		<td colspan="3">
			<h2><?php _e('Roles & Permissions','mdocs'); ?></h2>
			<p>Roles and permissions are cascading meaning if you choose author everyone above and including author will have access to the documents library.</p>
			<label>Add Documents</label>
			<select>
			<?php
				global $wp_roles;
				$roles = $wp_roles->get_names();
				foreach($roles as $key => $name) {
					echo '<option>'.$name.'</option>';
				}
			?>
			</select><br>
			<label>Edit Categories</label>
			<select>
			<?php
				global $wp_roles;
				$roles = $wp_roles->get_names();
				foreach($roles as $key => $name) {
					echo '<option>'.$name.'</option>';
				}
			?>
			</select>
		</td>		
	</tr>
	-->
</table>

<input style="margin:15px;" type="submit" class="button-primary" value="<?php _e('Save Changes','mdocs') ?>" />
</form>
<?php
}






?>