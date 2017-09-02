<?php
function mdocs_file_info_small($the_mdoc, $index=0, $current_cat) {
	global $post, $mdocs_img_types;
	$upload_dir = wp_upload_dir();
	$the_mdoc_permalink = htmlspecialchars(get_permalink($the_mdoc['parent']));
	$the_post = get_post($the_mdoc['parent']);
	$is_new = preg_match('/new=true/',$the_post->post_content);
	$post_date = strtotime($the_post->post_date);
	$last_modified = gmdate(get_option('mdocs-date-format'),$the_mdoc['modified']);
	$user_logged_in = is_user_logged_in();
	$mdocs_show_non_members = $the_mdoc['non_members'];
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_hide_all_posts = get_option( 'mdocs-hide-all-posts' );
	$mdocs_hide_all_files_non_members = get_option( 'mdocs-hide-all-files-non-members' );
	$mdocs_show_downloads = get_option( 'mdocs-show-downloads' );
	$mdocs_show_author = get_option( 'mdocs-show-author' );
	$mdocs_show_version = get_option( 'mdocs-show-version' );
	$mdocs_show_update = get_option( 'mdocs-show-update' );
	$mdocs_show_ratings = get_option( 'mdocs-show-ratings' );
	$mdocs_show_new_banners = get_option('mdocs-show-new-banners');
	$mdocs_time_to_display_banners = get_option('mdocs-time-to-display-banners');
	$mdocs_default_content = get_option('mdocs-default-content');
	$mdocs_show_description = get_option('mdocs-show-description');
	$mdocs_show_preview = get_option('mdocs-show-preview');
	if(isset($post)) $permalink = get_permalink($post->ID);
	else $permalink = '';
	
	if(preg_match('/\?page_id=/',$permalink) || preg_match('/\?p=/',$permalink)) {
		$mdocs_get = $permalink.'&mdocs-cat=';
	} else $mdocs_get = $permalink.'?mdocs-cat=';
		$the_rating = mdocs_get_rating($the_mdoc);
		$file_type = wp_check_filetype($the_mdoc['filename']);
		if(file_exists(plugin_dir_path( __FILE__ ).'assets/imgs/filetype-icons/'.$file_type['ext'].'.png'))  $file_icon = '<img src="'.plugins_url().'/memphis-documents-library/assets/imgs/filetype-icons/'.$file_type['ext'].'.png" class="hidden-xs hidden-sm"/>';
		else $file_icon = '<img src="'.plugins_url().'/memphis-documents-library/assets/imgs/filetype-icons/unknow.png" />';
		if($mdocs_show_new_banners) {
			$modified = floor($the_mdoc['modified']/86400)*86400;
			$today = floor(time()/86400)*86400;
			$days = (($today-$modified)/86400);
			if($mdocs_time_to_display_banners > $days) {
				if($is_new == true) $status_tag = '<span class="mdocs-new-updated-small badge pull-left alert-success ">'.__('New','mdocs').'</span>';
				else $status_tag = '<span class="mdocs-new-updated-small badge pull-left alert-info ">'.__('Updated','mdocs').'</span>';
			} else $status_tag = '';
		} else $status_tag = '';
		if ( current_user_can('read_private_posts') ) $read_private_posts = true;
		else $read_private_posts = false;
	?>
		<tr>
			<td id="title" class="mdocs-tooltip">
					<div class="btn-group">
						<a class="mdocs-title-href" data-toggle="dropdown" href="#" ><?php echo $file_icon.' '.str_replace('\\','',$the_mdoc['name']).$status_tag; ?></a>
						
						<ul class="dropdown-menu mdocs-dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
							<li role="presentation" class="dropdown-header"><i class="fa fa-medium"></i> &#187; <?php echo $the_mdoc['name']; ?></li>
							<li role="presentation" class="divider"></li>
							<li role="presentation" class="dropdown-header"><?php _e('File Options'); ?></li>
							<?php
								mdocs_download_rights($the_mdoc);
								mdocs_desciption_rights($the_mdoc);
								mdocs_preview_rights($the_mdoc);
								mdocs_rating_rights($the_mdoc);
								mdocs_goto_post_rights($the_mdoc, $the_mdoc_permalink);
								mdocs_share_rights($index, $the_mdoc_permalink, get_site_url().'/?mdocs-file='.$the_mdoc['id'].'&mdocs-url=false');
								if(is_admin()) { ?>
							<li role="presentation" class="divider"></li>
							<li role="presentation" class="dropdown-header"><?php _e('Admin Options'); ?></li>
							<?php
								mdocs_add_update_rights($the_mdoc, $current_cat);
								mdocs_manage_versions_rights($the_mdoc, $index, $current_cat);
								mdocs_delete_file_rights($the_mdoc, $index, $current_cat);
								if(get_option('mdocs-preview-type') == 'box' && get_option('mdocs-box-view-key') != '') {
									mdocs_refresh_box_view($the_mdoc, $index);
								}
							?>
							<li role="presentation" class="divider"></li>
							<li role="presentation" class="dropdown-header"><i class="fa fa-laptop"></i> <?php _e('File Status:'.' '.ucfirst($the_mdoc['file_status'])); ?></li>
							<li role="presentation" class="dropdown-header"><i class="fa fa-bullhorn"></i> <?php _e('Post Status:'.' '.ucfirst($the_mdoc['post_status'])); ?></li>
							<?php } ?>
						  </ul>
					</div>
			</td>
			<?php if($mdocs_show_downloads) { ?><td id="downloads"><i class="fa fa-cloud-download"></i> <b class="mdocs-orange"><?php echo $the_mdoc['downloads'].' '.__('Downloads','mdocs'); ?></b></td><?php } ?>
			<?php if($mdocs_show_version) { ?><td id="version"><i class="fa fa-power-off"></i><b class="mdocs-blue"> <?php echo $the_mdoc['version']; ?></b></td><?php } ?>
			<?php if($mdocs_show_author) { ?><td id="owner"><i class="fa fa-pencil"></i> <i class="mdocs-green"><?php echo get_user_by('login', $the_mdoc['owner'])->display_name; ?></i></td><?php } ?>
			<?php if($mdocs_show_update) { ?><td id="update"><i class="fa fa-calendar"></i> <b class="mdocs-red"><?php echo $last_modified; ?></b></td><?php } ?>
			<?php
				if($mdocs_show_ratings) {
					echo '<td id="rating">';
					for($i=1;$i<=5;$i++) {
						if($the_rating['average'] >= $i) echo '<i class="fa fa-star mdocs-gold" id="'.$i.'"></i>';
						elseif(ceil($the_rating['average']) == $i ) echo '<i class="fa fa-star-half-full mdocs-gold" id="'.$i.'"></i>';
						else echo '<i class="fa fa-star-o" id="'.$i.'"></i>';
					}
					echo '</td>';
				} ?>
		</tr>
		<tr>
<?php
	//}
}
?>