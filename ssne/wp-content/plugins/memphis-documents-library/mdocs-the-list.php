<?php
function mdocs_the_list($att=null) {
	global $current_user, $post;
	ob_start();
	$is_read_write = mdocs_check_read_write();
	if($is_read_write) {
		$site_url = site_url();
		$upload_dir = wp_upload_dir();	
		$mdocs = get_option('mdocs-list');
		$cats =  get_option('mdocs-cats');
		$current_cat_array = mdocs_get_current_cat_array($att);
		$current_cat = $current_cat_array['slug'];
		if(isset($att['cat']) && $att['cat'] == 'All Files') { $current_cat = 'all'; mdocs_list_header(false); }
		else if(!isset($att['cat'])) mdocs_list_header(true);
		else mdocs_list_header(false);
		if(is_array($post)) $permalink = get_permalink($post->ID);
		else $permalink = '';
		if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
			$mdocs_get = $permalink.'&mdocs-cat=';
		} else $mdocs_get = $permalink.'?mdocs-cat=';
		$mdocs_sort_type = get_option('mdocs-sort-type');
		$mdocs_sort_style = get_option('mdocs-sort-style');
		$disable_user_sort = get_option('mdocs-disable-user-sort');
		if(isset($_COOKIE['mdocs-sort-type']) && $disable_user_sort == false) $mdocs_sort_type = $_COOKIE['mdocs-sort-type'];
		if(isset($_COOKIE['mdocs-sort-range']) && $disable_user_sort == false) $mdocs_sort_style = $_COOKIE['mdocs-sort-range'];
		if($mdocs_sort_style == 'desc') $mdocs_sort_style_icon = ' <i class="fa fa-chevron-down"></i>';		
		else $mdocs_sort_style_icon = ' <i class="fa fa-chevron-up"></i>';
	?>
	<div class="mdocs-container">	
		<?php if(isset($att['header'])) echo '<p>'.__($att['header']).'</p>'; ?>
		<?php
		//mdocs_load_modals();
		$mdocs = mdocs_array_sort();
		$count = 0;
		$num_tds = 1;
		if(get_option('mdocs-list-type') == 'small') echo '<table class="table table-hover table-condensed mdocs-list-table">';
		?>
		<tr class="hidden-sm hidden-xs">
		<th class="mdocs-sort-option" data-disable-user-sort="<?php echo $disable_user_sort; ?>" data-sort-type="name" data-current-cat="<?php echo $current_cat; ?>" data-permalink="<?php echo $permalink; ?>"><?php _e('Name','mdocs'); ?><?php if($mdocs_sort_type == 'name') echo $mdocs_sort_style_icon; ?></th>
		<?php if(get_option('mdocs-show-downloads')) { $num_tds++; ?>
		<th class="mdocs-sort-option" data-disable-user-sort="<?php echo $disable_user_sort; ?>" data-sort-type="downloads" data-current-cat="<?php echo $current_cat; ?>" data-permalink="<?php echo $permalink; ?>"><?php _e('Downloads','mdocs'); ?><?php if($mdocs_sort_type == 'downloads') echo $mdocs_sort_style_icon; ?></th><?php } ?>
		<?php if(get_option('mdocs-show-version')) { $num_tds++; ?>
		<th class="mdocs-sort-option" data-disable-user-sort="<?php echo $disable_user_sort; ?>" data-sort-type="version" data-current-cat="<?php echo $current_cat; ?>" data-permalink="<?php echo $permalink; ?>"><?php _e('Version','mdocs'); ?><?php if($mdocs_sort_type == 'version') echo $mdocs_sort_style_icon; ?></th><?php } ?>
		<?php if(get_option('mdocs-show-author')) { $num_tds++; ?>
		<th class="mdocs-sort-option" data-disable-user-sort="<?php echo $disable_user_sort; ?>" data-sort-type="owner" data-current-cat="<?php echo $current_cat; ?>" data-permalink="<?php echo $permalink; ?>"><?php _e('Owner','mdocs'); ?><?php if($mdocs_sort_type == 'owner') echo $mdocs_sort_style_icon; ?></th><?php } ?>
		<?php if(get_option('mdocs-show-update')) { $num_tds++; ?>
		<th class="mdocs-sort-option" data-disable-user-sort="<?php echo $disable_user_sort; ?>" data-sort-type="modified" data-current-cat="<?php echo $current_cat; ?>" data-permalink="<?php echo $permalink; ?>"><?php _e('Last Modified','mdocs'); ?><?php if($mdocs_sort_type == 'modified') echo $mdocs_sort_style_icon; ?></th><?php } ?>
		<?php if(get_option('mdocs-show-ratings')) { $num_tds++; ?>
		<th class="mdocs-sort-option" data-disable-user-sort="<?php echo $disable_user_sort; ?>" data-sort-type="rating" data-current-cat="<?php echo $current_cat; ?>" data-permalink="<?php echo $permalink; ?>"><?php _e('Rating','mdocs'); ?><?php if($mdocs_sort_type == 'rating') echo $mdocs_sort_style_icon; ?></th><?php } ?>
		</tr>
		<?php
		// SUB CATEGORIES
		//$current_cat_array = mdocs_get_the_cat($current_cat);
		//$parent_cat_array = mdocs_get_the_cat($current_cat_array['parent']);
		$hide_sub_folders = get_option('mdocs-hide-subfolders');
		if(!isset($att['cat'])) $num_cols = mdocs_get_subcats($current_cat_array, 'null');
		elseif(isset($current_cat_array['children']) && $hide_sub_folders == false && isset($att['cat'])) $num_cols = mdocs_get_subcats($current_cat_array, $att['cat']);
		foreach($mdocs as $index => $the_mdoc) {
			if($the_mdoc['cat'] == $current_cat || $current_cat == 'all') {
				$is_allowed = mdocs_check_role_rights($the_mdoc);
				if($the_mdoc['file_status'] == 'public' || is_admin() && $the_mdoc['owner'] == $current_user->user_login ||  in_array($current_user->user_login, $the_mdoc['contributors']) || $is_allowed || $current_user->roles[0] == 'administrator') {
					$count ++;
					$mdocs_post = get_post($the_mdoc['parent']);
					//$mdocs_desc = apply_filters('the_content', $mdocs_post->post_excerpt);
					
					if(get_option('mdocs-list-type') == 'small') {
						mdocs_file_info_small($the_mdoc, $index, $current_cat); 
					} else {
						$user_logged_in = is_user_logged_in();
						$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
						$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
						if($mdocs_hide_all_files_non_members && $user_logged_in == false) $show_files = false;
						elseif($mdocs_hide_all_files == false ) $show_files = true;
						else $show_files = false;
						if( $show_files) {
							?>
							<div class="mdocs-post">
								<?php mdocs_file_info_large($the_mdoc, $index, $current_cat); ?>
								<div class="mdocs-clear-both"></div>
								<?php mdocs_social($the_mdoc); ?>
							</div>
							<div class="mdocs-clear-both"></div>
							<?php mdocs_des_preview_tabs($the_mdoc); ?>
							<div class="mdocs-clear-both"></div>
							</div>
							<?php
						}
					}
				}
			} 
		}
		if($count == 0 && get_option('mdocs-show-no-file-found')) {
			?><tr><td colspan="<?php echo $num_tds; ?>"><p class="mdocs-nofiles" ><?php _e('No files found in this folder.','mdocs'); ?></p></td></tr><?php
		}
		if(get_option('mdocs-list-type') == 'small') echo '</table></div>';
	} else mdocs_errors(__('Unable to create the directory "mdocs" which is needed by Memphis Documents Library. Its parent directory is not writable by the server?','mdocs'),'error');
	//echo '</div>';
	$the_list = ob_get_clean();
	return $the_list;
}
?>