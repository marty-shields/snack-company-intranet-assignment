<?php
function mdocs_contributors_check($contrib) {
	if(!is_array($contrib))  {
		return array();
	} else return $contrib;
}
function mdocs_add_update_rights($the_mdoc, $current_cat) {
	global $current_user;
	$is_allowed = mdocs_check_role_rights($the_mdoc);
	$the_mdoc['contributors'] = mdocs_contributors_check($the_mdoc['contributors']);
	if($current_user->user_login === $the_mdoc['owner'] || current_user_can( 'manage_options' ) || in_array($current_user->user_login, $the_mdoc['contributors']) || $is_allowed) {
	?>
	<li role="presentation">
		<a class="add-update-btn" role="menuitem" tabindex="-1" data-toggle="mdocs-modal" data-target="#mdocs-add-update" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" data-is-admin="<?php echo is_admin(); ?>" data-action-type="update-doc"  data-current-cat="<?php echo $current_cat; ?>" href="">
			<i class="fa fa-file-o" ></i> <?php _e('Manage File','mdocs'); ?>
		</a>
	</li>
	<?php
	}
}
function mdocs_check_role_rights($the_mdoc) {
	global $current_user;
	$is_allowed = false;
	if(is_array($the_mdoc['contributors'])) {
		foreach($the_mdoc['contributors'] as $index => $role) {
			if(in_array($role, $current_user->roles)) { $is_allowed = true; break; }
		}
	}
	return $is_allowed;
}
function mdocs_goto_post_rights($the_mdoc, $the_mdoc_permalink) {
	global $current_user;
	$hide_all_post = get_option('mdocs-hide-all-posts');
	$hide_all_post_non_members = get_option('mdocs-hide-all-posts-non-members');
	$mdocs_view_private = get_option('mdocs-view-private');
	foreach($mdocs_view_private as $index => $role) {
		$private_viewable = false;
		if($current_user->role[0] == $index) $show_private = $private_viewable = true;
	}
	if($hide_all_post == false && $hide_all_post_non_members == false || is_user_logged_in() == true && $hide_all_post_non_members == true) {
		if($private_viewable == true || $current_user->user_login == $the_mdoc['owner'] || $current_user->role[0] == 'administrator') {
	?>
	<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo $the_mdoc_permalink; ?>" target="_blank"><i class="fa fa-arrow-circle-o-right"></i> <?php _e('Goto Post','mdocs'); ?></a></li>
	<?php
		}
	}
}
function mdocs_manage_versions_rights($the_mdoc, $index, $current_cat) {
	global $current_user;
	$is_allowed = mdocs_check_role_rights($the_mdoc);
	$the_mdoc['contributors'] = mdocs_contributors_check($the_mdoc['contributors']);
	if($current_user->user_login === $the_mdoc['owner'] || current_user_can( 'manage_options' ) || in_array($current_user->user_login, $the_mdoc['contributors']) || $is_allowed) {
	?>
	<li role="presentation"><a role="menuitem" tabindex="-1" href="?page=memphis-documents.php&mdocs-cat=<?php echo $current_cat; ?>&action=mdocs-versions&mdocs-index=<?php echo $index; ?>"><i class="fa fa-road"></i> <?php _e('Manage Versions','mdocs'); ?></a></li>
	<?php
	}
}
function mdocs_download_rights($the_mdoc) {
	global $post, $current_user;
	$the_mdoc_permalink = htmlspecialchars(get_permalink($the_mdoc['parent']));
	$mdocs_show_non_members = $the_mdoc['non_members'];
	if($the_mdoc['contributors'] != null) {
		foreach($the_mdoc['contributors'] as $user) {
			$contributor = false;
			if($current_user->user_login == $user) $contributor = true;
		}
	} else $contributor = false;
	if($the_mdoc['file_status'] != 'hidden' || $contributor == true || $the_mdoc['owner'] == $current_user->user_login || $current_user->roles[0] == 'administrator') {
		if($mdocs_show_non_members  == 'off' && is_user_logged_in() == false && is_admin() == false) { ?>
			<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo wp_login_url($the_mdoc_permalink); ?>"><i class="fa fa-cloud-download"></i> <?php _e('Download','mdocs'); ?></a></li><?php
		} elseif($the_mdoc['non_members'] == 'on' || is_user_logged_in() || is_admin()) { ?>
			<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo site_url().'/?mdocs-file='.$the_mdoc['id'].'&mdocs-url=false'; ?>"><i class="fa fa-cloud-download"></i> <?php _e('Download','mdocs'); ?></a></li><?php
		} else { ?>
		<li role="presentation"><a role="menuitem" tabindex="-1" href="<?php echo wp_login_url($the_mdoc_permalink); ?>"><i class="fa fa-cloud-download"></i> <?php _e('Download','mdocs'); ?></a></li>
		<?php }
	}
}
function mdocs_preview_rights($the_mdoc) {
	global $mdocs_img_types, $current_user;
	$mdocs_show_preview = get_option('mdocs-show-preview');
	$mdocs_show_description = get_option('mdocs-show-description');
	$mdocs_show_preview = get_option('mdocs-show-preview');
	$mdocs_hide_all_files = get_option( 'mdocs-hide-all-files' );
	$mdocs_show_non_members = $the_mdoc['non_members'];
	$preview_type = 'file-preview';
	
	if(!in_array($the_mdoc['type'], $mdocs_img_types) ) $preview_type = 'file-preview';
	else $preview_type = 'img-preview';
	if($the_mdoc['contributors'] != null) {
		foreach($the_mdoc['contributors'] as $user) {
			$contributor = false;
			if($current_user->user_login == $user) $contributor = true;
		}
	} else $contributor = false; 
	if($contributor == true || $the_mdoc['owner'] == $current_user->user_login || $current_user->roles[0] == 'administrator') {
		?>
	<li role="presentation"><a class="<?php echo $preview_type; ?>" role="menuitem" tabindex="-1" data-toggle="mdocs-modal" data-target="#mdocs-file-preview" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" data-is-admin="<?php echo is_admin(); ?>" href=""><i class="fa fa-search mdocs-preview-icon" ></i> <?php _e('Preview','mdocs'); ?></a></li>
	<?php
	} elseif($mdocs_hide_all_files) {
		//fail
	} else if($mdocs_show_preview == false) {
		//fail
	} else if( is_user_logged_in() == false && $mdocs_hide_all_files_non_members) {
		//fail
	} elseif ( $the_mdoc['file_status'] == 'hidden') {
		//fail
	} else {
		?>
	<li role="presentation"><a class="<?php echo $preview_type; ?>" role="menuitem" tabindex="-1" data-toggle="mdocs-modal" data-target="#mdocs-file-preview" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" data-is-admin="<?php echo is_admin(); ?>" href=""><i class="fa fa-search mdocs-preview-icon" ></i> <?php _e('Preview','mdocs'); ?></a></li>
	<?php
	}
}
function mdocs_desciption_rights($the_mdocs) {
	$mdocs_show_description = get_option('mdocs-show-description');
	if($mdocs_show_description == true) {
	?>
	<li role="presentation"><a class="description-preview" role="menuitem" tabindex="-1" href="#" data-toggle="mdocs-modal" data-target="#mdocs-description-preview" data-mdocs-id="<?php echo $the_mdocs['id']; ?>" data-is-admin="<?php echo is_admin(); ?>" ><i class="fa fa-leaf"></i> <?php _e('Description','mdocs'); ?></a></li>
	<?php
	}
}
function mdocs_share_rights($index, $permalink, $download) {
	$mdocs_show_show = get_option('mdocs-show-share');
	if($mdocs_show_show) {
	?>
	<li role="presentation"><a class="sharing-button" role="menuitem" tabindex="-1" href="#" data-toggle="mdocs-modal" data-doc-index="<?php echo $index; ?>" data-target="#mdocs-share" data-permalink="<?php echo $permalink;?>" data-download="<?php echo $download; ?>" ><i class="fa fa-share"></i> <?php _e('Share','mdocs'); ?></a></li>
	<?php
	}
}
function mdocs_rating_rights($the_mdoc) {
	if(get_option( 'mdocs-show-ratings' )) {
	?>
	<li role="presentation"><a class="ratings-button" role="menuitem" tabindex="-1" href="" data-toggle="mdocs-modal" data-target="#mdocs-rating" data-mdocs-id="<?php echo $the_mdoc['id']; ?>" data-is-admin="<?php echo is_admin(); ?>"><i class="fa fa-star"></i> <?php _e('Rate','mdocs'); ?></a></li>
	<?php
	}
}
function mdocs_delete_file_rights($the_mdoc, $index, $current_cat) {
	global $current_user;
	$is_allowed = mdocs_check_role_rights($the_mdoc);
	$the_mdoc['contributors'] = mdocs_contributors_check($the_mdoc['contributors']);
	if($current_user->user_login === $the_mdoc['owner'] || current_user_can( 'manage_options' ) || in_array($current_user->user_login, $the_mdoc['contributors']) || $is_allowed) {
	?>
	<li role="presentation">
		<a onclick="mdocs_delete_file('<?php echo $index; ?>','<?php echo $current_cat; ?>','<?php echo $_SESSION['mdocs-nonce']; ?>');" role="menuitem" tabindex="-1" href="#"><i class="fa fa-times-circle"></i> <?php _e('Delete File','mdocs'); ?></a>
	</li>
	<?php
	}
}
function mdocs_refresh_box_view($the_mdoc, $index) {
	if($current_user->user_login === $the_mdoc['owner'] || current_user_can( 'manage_options' ) || in_array($current_user->user_login, $the_mdoc['contributors']) || $is_allowed) {
		?>
		<li role="presentation"><a class="box-view-refresh-button" role="menuitem" tabindex="-1" href="#" data-toggle="mdocs-modal" data-index="<?php echo $index; ?>" data-filename="<?php echo $the_mdoc['filename']; ?>" ><i class="fa fa-refresh"></i> <?php _e('Refresh Preview and Thumbnail','mdocs'); ?></a></li>
		<?php
	}
}
?>