/*
Plugin Name: Memphis Documents Library
Plugin URI: http://www.kingofnothing.net/memphis-documents-library/
Description: A documents repository for WordPress. 
Version: 3.1.1
Author URI: http://www.kingofnothing.net/

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA 
 */
var toggle_share = false;

// INITIALIZE THE ADMIN JAVASCRIPT
function mdocs_wp() {
    // ADDS A MEMPHIS DOCUMENTS TAG TO THE BODY FOR STYLE ISSUE FIXES
    jQuery('body').addClass('mdocs');
    // INITALIZE BOOTSTRAP POPOVER FUNCTIONALITY
    //jQuery('[data-toggle="popover"]').popover();
    // ADD UPDATE DOCUMENT
    mdocs_add_update_documents();
    // TOOGLE DESCRIPTION/PREVIEW
    mdocs_toogle_description_preview();
    // DESRIPTION PREVIEW
    mdocs_description_preview();
    // FILE PREVIEW
    mdocs_file_preview();
    // IMAGE PREVIEW
    mdocs_image_preview();
    // RATING SYSTEM
    mdocs_ratings();
    // SORT OPTIONS
    mdocs_sort_files();
    // RATINGS SUBMIT
    mdocs_submit_rating('small');
    // SHARING MODAL
    mdocs_share_modal();
    // CHECK WITH OF DOCUMENTS CONTAINER
    //mdocs_check_width();
    //jQuery(window).resize(function() { mdocs_check_width(); });
    // MODAL TOOGLE
    mdocs_toogle_modals();
}
// INITIALIZE THE ADMIN JAVASCRIPT
function mdocs_admin() {
    tinyMCE.init({ remove_linebreaks: false, });  
    //FIX FOCUS ISSUE WITH TINYMCE TEXTBOXES
    jQuery(document).on('focusin', function(e) { e.stopImmediatePropagation(); });
    // MODAL CLOSE EVENT
    mdocs_modal_close();
    // INITALIZE BOOTSTRAP POPOVER FUNCTIONALITY
    jQuery('[data-toggle="popover"]').popover();
    mdocs_color_pickers();
    // INITALIZE DRAGGABLE
    //jQuery(".draggable").draggable();
    // DISABLED SETTINGS
    mdocs_toogle_disable_setting('#mdocs-hide-all-files','#mdocs-hide-all-files-non-members');
    mdocs_toogle_disable_setting('#mdocs-hide-all-files-non-members','#mdocs-hide-all-files');
    mdocs_toogle_disable_setting('#mdocs-hide-all-posts','#mdocs-hide-all-posts-non-members');
    mdocs_toogle_disable_setting('#mdocs-hide-all-posts-non-members','#mdocs-hide-all-posts');
     // ADD UPDATE DOCUMENT
    mdocs_add_update_documents();
    // DESRIPTION PREVIEW
    mdocs_description_preview();
    // FILE PREVIEW
    mdocs_file_preview();
    // IMAGE PREVIEW
    mdocs_image_preview();
    // ADD MIME TYPE
    mdocs_add_mime_type();
    // REMOVE MIME TYPE
    mdocs_remove_mime_type();
    // RESTORE DEFAULT FILE TYPES
    mdocs_restore_default_mimes();
    // SORT OPTIONS
    mdocs_sort_files();
    // RATING SYSTEM
    mdocs_ratings();
    // SHARING MODAL
    mdocs_share_modal();
    // RUN 3.0 PATCH ON CLICK
    jQuery('#mdosc-3-0-patch-btn').click(function(event) {
	event.preventDefault();
	var numfiles = Number(jQuery(this).data('num-docs'));
	mdocs_v3_0_patch(numfiles);
    });
    // SOCIAL CLICKED
    jQuery('.mdocs-show-social').click(function() {
	    if (jQuery(this).hasClass('fa fa-plus-sign-alt')) {
		    jQuery(this).removeClass('fa fa-plus-sign-alt');
		    jQuery(this).addClass('fa fa-minus-sign-alt');
		    var raw_id = jQuery(this).prop('id');
	    raw_id = raw_id.split("-");
	    var id = raw_id[raw_id.length-1];
	    jQuery('#mdocs-social-index-'+id).show();
	    } else {
		    jQuery(this).removeClass('fa fa-minus-sign-alt');
		    jQuery(this).addClass('icon-plus-sign-alt');
		    var raw_id = jQuery(this).prop('id');
	    raw_id = raw_id.split("-");
	    var id = raw_id[raw_id.length-1];
	    jQuery('#mdocs-social-index-'+id).hide();
	    }

    });
   // ADD ROOT CATEGORY
    jQuery('#mdocs-add-cat').click(function(event) { event.preventDefault(); });
    jQuery('input[id="mdocs-cat-remove"]').click(function() {
	var confirm = window.confirm(mdocs_js.category_delete);
	var cat = jQuery(this).prop('name');
	if (confirm) {
		jQuery('[name="mdocs-cats['+cat+'][remove]"]').prop('value',1);
		jQuery('#mdocs-cats').submit();
	}
    });
    /*
    jQuery('#mdocs-file-status').change(function() {
	    if (jQuery(this).val() == 'hidden') jQuery('#mdocs-post-status').prop('disabled','true');
	    else if (jQuery(this).val() == 'public') jQuery('#mdocs-post-status').removeAttr('disabled');
    });
    */
    // BOX VIEW REFRESH
    mdocs_box_view_refresh();
    // CHECK WITH OF DOCUMENTS CONTAINER
    //mdocs_check_width();
    //jQuery(window).resize(function() { mdocs_check_width(); });
    // FIND LOST FILES
    mdocs_find_lost_files();
    // MODAL TOOGLE
    mdocs_toogle_modals();
}
 // ADD / UPDATE DOCUMENTS
function mdocs_add_update_documents() {
    jQuery('.add-update-btn').click(function(event) {
	var action_type = jQuery(this).data('action-type');
	var mdocs_id = jQuery(this).data('mdocs-id');
	var current_cat = jQuery(this).data('current-cat');
	var nonce = jQuery(this).data('nonce');
	var is_admin = jQuery(this).data('is-admin');
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', 'type': action_type,'mdocs-id': mdocs_id, 'current-cat': current_cat, 'is-admin': is_admin},function(data) {
	    var action_text = 'Add Document';
	    var doc_data = JSON.parse(data);
	    console.debug(doc_data['error']);
	    if (doc_data['error'] != null) {
		alert(doc_data['error']);
	    } else {
		if (action_type == 'update-doc') {
		    jQuery('#mdocs-add-update-form').prop('action', 'admin.php?page=memphis-documents.php&mdocs-cat='+current_cat);
		    jQuery('input[name="mdocs-type"]').prop('value', 'mdocs-update');
		    jQuery('input[name="mdocs-index"]').prop('value', mdocs_id);
		    jQuery('input[name="mdocs-pname"]').prop('value', doc_data['name']);
		    jQuery('input[name="mdocs-post-status-sys"]').prop('value', doc_data['post_status']);
		    action_text = mdocs_js.update_doc+' <small>'+doc_data['filename']+'</small>';
    
    
		    jQuery('#mdocs-current-doc').html(mdocs_js.current_file+': '+doc_data['filename']);
		    jQuery('input[name="mdocs-name"]').prop('value',doc_data['name']);
		    jQuery('option[value="'+doc_data['cat']+'"]').prop('selected',true);
		    jQuery('input[name="mdocs-version"]').prop('value', doc_data['version']);
		    jQuery('input[name="mdocs-tags"]').prop('value', doc_data['post-tags']);
		    jQuery('input[name="mdocs-last-modified"]').prop('value', doc_data['timestamp']);
		    if (doc_data['show_social'] == 'on') jQuery('input[name="mdocs-social"]').prop('checked',true);
		    else jQuery('input[name="mdocs-social"]').prop('checked',false);
		    if (doc_data['non_members'] == 'on') jQuery('input[name="mdocs-non-members"]').prop('checked',true);
		    else jQuery('input[name="mdocs-non-members"]').prop('checked',false);
		    //if(doc_data['file_status'] == 'hidden') jQuery("#mdocs-post-status").prop('disabled', true);
		    jQuery("#mdocs-file-status option").each(function() {
			if(doc_data['file_status'] == jQuery(this).val()) jQuery(this).prop('selected',true);
		    });
		    jQuery("#mdocs-post-status option").each(function() {
			if(doc_data['post_status'] == jQuery(this).val()) jQuery(this).prop('selected',true);
		    });
		    jQuery('#mdocs-current-owner').html('<i class="fa fa-user"></i> '+doc_data['owner']);
		    jQuery('#mdocs-contributors-container').append('<input type="hidden" value="'+doc_data['owner']+'" name="mdocs-owner-value"/>');
		    mdocs_contributor_editor(doc_data);
		    //console.debug(doc_data['desc']);
		    //doc_data['desc'] = doc_data['desc'].replace(/(?:\r\n|\r|\n)/g, '<br />');
		    //console.debug(doc_data['desc']);
		    //doc_data['desc'] = doc_data['desc'].replace(/(?:\r\n|\r|\n|&nbsp;)/g, '');
		    //doc_data['desc'] = doc_data['desc'].replace(/&nbsp;/g, '<br />');
		    doc_data['desc'] = doc_data['desc'].replace(/\\/g, '');
		    doc_data['desc'] = doc_data['desc'].replace(/&quot;/g, '');
		    doc_data['desc'] = doc_data['desc'];
		    tinyMCE.activeEditor.setContent(doc_data['desc']);
		    jQuery('#mdocs-save-doc-btn').prop('value', mdocs_js.update_doc_btn);
		} else {
		    jQuery('input[name="mdocs-name"]').val('');
		    jQuery('option[value="'+current_cat+'"]').prop('selected', true);
		    jQuery('input[name="mdocs-version"]').val('1.0');
		    jQuery('#mdocs-current-doc').html('');
		    jQuery('#mdocs-current-owner').html('<i class="fa fa-user"></i> '+jQuery('input[name="mdocs-current-user"]').val());
		    jQuery('#mdocs-contributors-container').append('<input type="hidden" value="'+jQuery('input[name="mdocs-current-user"]').val()+'" id="mdocs-owner-value"/>');
		    jQuery('input[name="mdocs-type"]').prop('value', 'mdocs-add');
		    jQuery('input[name="mdocs-last-modified"]').prop('value', doc_data['timestamp']);
		    mdocs_contributor_editor(doc_data);
		    action_text = mdocs_js.add_doc;
		    jQuery('#mdocs-save-doc-btn').prop('value', mdocs_js.add_doc_btn);
		}
	       jQuery('#mdocs-add-update-header').html(action_text);
	    }
	});
	

    });


}
// CONTRIBUTOR EDITOR
function mdocs_contributor_editor(doc_data) {
    jQuery('span[id^="mdocs-contributors["]').each(function() { jQuery(this).remove(); });
    jQuery('input[name^="mdocs-contributors["]').each(function() { jQuery(this).remove(); });
    if (doc_data['contributors'] == null) doc_data['contributors'] = {};
    for(var i = 0; i < doc_data['contributors'].length; i++) {
	jQuery('#mdocs-contributors-container').append('<span class="label label-success mdocs-contributors" id="mdocs-contributors['+i+']" data-index="'+i+'"><i class="fa fa-user"></i> '+doc_data['contributors'][i]+' <i class="fa fa-times mdocs-contributors-delete-btn" id="mdocs-contributors-delete[mdocs-contributors['+i+']]"></i></span>');
	jQuery('#mdocs-contributors-container').append('<input type="hidden" value="'+doc_data['contributors'][i]+'" name="mdocs-contributors['+i+']"/>');
    }
    activate_contributors_delete_btn();
    jQuery('#mdocs-add-contributors').keyup(function() {
	var search_string_length = jQuery(this).val().length;
	if (search_string_length > 1) {
	    
	    var contributors = []
	    jQuery('input[name^="mdocs-contributors"]').each(function() {
		contributors.push(jQuery(this).val()); 
	     });
	    
	    jQuery('.mdocs-user-search-list').removeClass('hidden');
	    jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', 'type': 'search-users', 'user-search-string': jQuery('#mdocs-add-contributors').val(), 'owner': jQuery('input[name="mdocs-owner-value"]').val(), 'contributors':contributors},function(data) {
		jQuery('.mdocs-user-search-list').html(data);
		jQuery('.mdocs-search-results-roles').click(function(event) {
		    event.preventDefault();
		    //jQuery(this).remove();
		    jQuery('#mdocs-contributors-container').append('<span class="label label-success mdocs-contributors" id="mdocs-contributors['+i+']" data-index="'+i+'"><i class="fa fa-user"></i> '+jQuery(this).data('value')+' <i class="fa fa-times mdocs-contributors-delete-btn" id="mdocs-contributors-delete[mdocs-contributors['+i+']]"></i></span>');
		    jQuery('#mdocs-contributors-container').append('<input type="hidden" value="'+jQuery(this).data('value')+'" name="mdocs-contributors['+i+']"/>');
		    i++;
		    jQuery('#mdocs-add-contributors').val('');
		    jQuery('.mdocs-user-search-list').html('');
		    jQuery('#mdocs-add-contributors').focus();
		    jQuery('.mdocs-user-search-list').addClass('hidden');
		    activate_contributors_delete_btn();
		});
		 jQuery('.mdocs-search-results-users').click(function(event) {
		    event.preventDefault();
		    //jQuery(this).remove();
		    jQuery('#mdocs-contributors-container').append('<span class="label label-success mdocs-contributors" id="mdocs-contributors['+i+']" data-index="'+i+'"><i class="fa fa-user"></i> '+jQuery(this).data('value')+' <i class="fa fa-times mdocs-contributors-delete-btn" id="mdocs-contributors-delete[mdocs-contributors['+i+']]"></i></span>');
		    jQuery('#mdocs-contributors-container').append('<input type="hidden" value="'+jQuery(this).data('value')+'" name="mdocs-contributors['+i+']"/>');
		    i++;
		    jQuery('#mdocs-add-contributors').val('');
		    jQuery('.mdocs-user-search-list').html('');
		    jQuery('#mdocs-add-contributors').focus();
		    jQuery('.mdocs-user-search-list').addClass('hidden');
		    activate_contributors_delete_btn();
		});	
	    });
	} else  {
	    jQuery('.mdocs-user-search-list').addClass('hidden');
	    jQuery('.mdocs-user-search-list').html();
	}
    });
}
//ACTIVATE CONTRIBUTOR DELETE BUTTON
function activate_contributors_delete_btn() {
    jQuery('.mdocs-contributors-delete-btn').click(function() {
	var index = jQuery(this).parent().data('index');
	jQuery(this).parent().remove();
	jQuery('input[name="mdocs-contributors['+index+']"]').remove();
    });
}
// ADD ROOT CATEGORY
function add_main_category(total_cats) {
    mdocs_add_sub_cat(total_cats, '', 0, jQuery('#the-list'), true);
}
// === COLOR PICKERS === //
//INITIALIZE IRIS COLOR PICKER
function mdocs_color_pickers() {
    var button_bg_color_normal = jQuery('#bg-color-mdocs-picker').prop('value');
    var button_bg_color_hover = jQuery('#bg-hover-color-mdocs-picker').prop('value');
    var button_text_color_normal = jQuery('#bg-text-color-mdocs-picker').prop('value');
    var button_text_color_hover =  jQuery('#bg-text-hover-color-mdocs-picker').prop('value');
    var color_options = {
	change: function(event, ui) {
	    var element = jQuery(this).prop('id');
	    if (element == 'bg-color-mdocs-picker') {
		button_bg_color_normal = ui.color.toString();
		jQuery('.mdocs-download-btn-config').css('background', button_bg_color_normal);
	    } else if (element == 'bg-hover-color-mdocs-picker') button_bg_color_hover = ui.color.toString();
	    if (element == 'bg-text-color-mdocs-picker') {
		button_text_color_normal = ui.color.toString();
		jQuery('.mdocs-download-btn-config').css('color', button_text_color_normal);
	    } else if (element == 'bg-text-hover-color-mdocs-picker') button_text_color_hover = ui.color.toString();
	}
    }
    jQuery('[id$="mdocs-picker"]').wpColorPicker(color_options);
    // HOVER ADMIN DOWNLOAD BUTTON PREVIEW
     jQuery('.mdocs-download-btn-config').hover(
	function() {
	    jQuery(this).css('background', button_bg_color_hover);
	    jQuery(this).css('color', button_text_color_hover);
	}, function() {
	    jQuery(this).css('background', button_bg_color_normal);
	    jQuery(this).css('color', button_text_color_normal);
	}
    );
}
// === ALLOWED FILE TYPES === //
// RESTORE MIME TYPES
function mdocs_restore_default_mimes() {
    jQuery('#mdocs-restore-default-file-types').click(function(event) {
	event.preventDefault();
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'restore-mime', is_admin: true},function(data) {
	    jQuery('.mdocs-mime-table').html(data);
	    mdocs_remove_mime_type();
	    mdocs_add_mime_type();
	});
    });
}
// ADD MIME TYPE
function mdocs_add_mime_type() {
    jQuery('#mdocs-add-mime').click(function(event) {
	event.preventDefault();
	var file_extension = jQuery('input[name="mdocs-file-extension"]').val();
	var mime_type = jQuery('input[name="mdocs-mime-type"]').val();
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'add-mime', file_extension: file_extension, mime_type: mime_type, is_admin: true},function(data) {
	    jQuery(data).insertBefore('.mdocs-mime-submit');
	    mdocs_remove_mime_type();
	    jQuery('input[name="mdocs-file-extension"]').val('');
	    jQuery('input[name="mdocs-mime-type"]').val('');
	});
    });
}
// REMOVE MIME TYPE
function mdocs_remove_mime_type() {
    jQuery('.mdocs-remove-mime').click(function(event) {
	event.preventDefault();
	var file_extension = jQuery(this).parent().parent().data('file-type');
	jQuery(this).parent().parent().remove();
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'remove-mime', file_extension: file_extension, is_admin: true},function(data) { });
    });
}
// ========================== //
// ADD SUB CATEGORY
//var subcat_index = 0;
var add_button_clicks = 1;
function mdocs_add_sub_cat(total_cats, parent, parent_depth, object, is_parent) {
    //mdocs_set_onleave();
    
    jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'mdocs-cat-index', 'check-index': jQuery('#mdocs-cats').data('check-index')},function(data) {
	var subcat_index = parseInt(data);
	var check_index = subcat_index+1;
	jQuery('#mdocs-cats').data('cat-index', subcat_index);
	jQuery('#mdocs-cats').data('check-index', check_index);
	var child_depth = parseInt(parent_depth)+1;
	var parent_id = 'class="mdocs-cats-tr"';
	if (child_depth <= mdocs_js.levels) {
	    jQuery('input[name="mdocs-update-cat-index"]').val(add_button_clicks++);
	    var padding = 'style="padding-left: '+(40*child_depth)+'px; "';
	    if (subcat_index == 0) subcat_index = parseInt(total_cats)+1;
	    else subcat_index++;
	    var order = parseInt(jQuery('input[name="mdocs-cats['+parent+'][num_children]"]').val())+1;
	    var disabled = '';
	    jQuery('input[name="mdocs-cats['+parent+'][num_children]"]').val(order);
	    if (is_parent) {
		padding = 0;
		order = jQuery('.wp-list-table > tbody > tr').size()+1;
		disabled = '';
		child_depth = 0;
		parent_id = 'class="mdocs-cats-tr parent-cat"';
	    }
	    if (jQuery('input[name="mdocs-cats['+parent+'][index]"]').val() != undefined) {
		var parent_index = jQuery('input[name="mdocs-cats['+parent+'][index]"]').val();
	    } else var parent_index = 0;
	    var subcat_index = jQuery('#mdocs-cats').data('cat-index');
	    var html = '\
		<tr '+parent_id+'>\
		    <td  id="name" '+padding+' >\
		       <input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][index]" value="'+subcat_index+'"/>\
			<input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][parent_index]" value="'+parent_index+'"/>\
			<input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][num_children]" value="0" />\
			<input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][depth]" value="'+child_depth+'"/>\
			<input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][parent]" value="'+parent+'"/>\
			<input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][slug]" value="mdocs-cat-'+subcat_index+'" />\
			<input type="text" name="mdocs-cats[mdocs-cat-'+subcat_index+'][name]"  value="'+mdocs_js.new_category+'"  />\
		    </td>\
		    <td id="order">\
			<input  type="text" name="mdocs-cats[mdocs-cat-'+subcat_index+'][order]"  value="'+order+'" '+disabled+' />\
		    </td>\
		    <td id="remove">\
			    <input type="hidden" name="mdocs-cats[mdocs-cat-'+subcat_index+'][remove]" value="0"/>\
			    <input type="button" id="mdocs-sub-cats-remove-new-'+subcat_index+'" class="button button-primary" value="Remove"  />\
		    </td>\
		    <td id="add-cat">\
			<input type="button" class="mdocs-add-sub-cat button button-primary" id="mdocs-sub-cats-add-new-'+subcat_index+'" value="'+mdocs_js.add_folder+'""   />\
		    </td>\
		</tr>\
		';
	    if (jQuery(object).prop('id') == 'the-list') jQuery(object).append(html);
	    else jQuery(html).insertAfter(jQuery(object).parent().parent());
	    jQuery('input[id="mdocs-sub-cats-remove-new-'+subcat_index+'"]').click(function() {
		jQuery('input[name="mdocs-cats['+parent+'][num_children]"]').val(order-1);
		jQuery(this).parent().parent().remove();
	    });
	    jQuery('input[id="mdocs-sub-cats-add-new-'+subcat_index+'"]').click(function() {
		var id = jQuery(this).prop('id').split('-');
		id = id[id.length-1];
		var parent = jQuery('input[name="mdocs-cats[mdocs-cat-'+id+'][parent]"]').val();
		var slug = jQuery('input[name="mdocs-cats[mdocs-cat-'+id+'][slug]"]').val();
		var depths = jQuery('input[name="mdocs-cats[mdocs-cat-'+id+'][depth]"]').val();
		//console.debug(jQuery('input[name="mdocs-cats[mdocs-cat-'+id+'][depth]"]').val());
		//alert(jQuery('input[name="mdocs-cats[mdocs-cat-3][depth]"]').val());
		mdocs_add_sub_cat(subcat_index,slug, depths, this);
	    });
	    
	} else alert(mdocs_js.category_support);
	//jQuery('.mdocs-add-sub-cat').unbind('click');
    });

}
// FUNCTIONS
function mdocs_set_onleave() { window.onbeforeunload = function() { return mdocs_js.leave_page;}; }
function mdocs_reset_onleave() { window.onbeforeunload = null; }
function mdocs_download_zip(zip_file) {
    jQuery('#mdocs-export-container').append('<h5 id="mdocs-export-cog"><i class="fa fa-cog fa-1x fa-spin"></i> '+mdocs_js.create_export_file+'</h5>');
    jQuery('#mdocs-export-submit').addClass('disabled');
    jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'mdocs-export', 'zip-file': zip_file},function(data) {
	jQuery('#mdocs-export-cog').html(mdocs_js.export_creation_complete_starting_download);
	jQuery('#mdocs-export-cog').delay(3000).fadeOut(1000, function() { jQuery('#mdocs-export-cog').remove(); jQuery('#mdocs-export-submit').removeClass('disabled'); });
	window.location.href = '?mdocs-export-file='+zip_file;
    });
}
function mdocs_download_current_version(version_id) {window.location.href = '?mdocs-file='+version_id; }
//function mdocs_download_version(version_id) { window.location.href = '?mdocs-file='+version_id; }
function mdocs_download_version(version_file) { window.location.href = '?mdocs-version='+version_file; }
function mdocs_delete_version(version_file, index, category, nonce) {
    var confirm = window.confirm(mdocs_js.version_delete);
    if (confirm) {
	    window.location.href = 'admin.php?page=memphis-documents.php&mdocs-cat='+category+'&action=delete-version&version-file='+version_file+'&mdocs-index='+index+'&mdocs-nonce='+nonce;
    }
}
function mdocs_delete_file(index, category, nonce) {
    var confirm = window.confirm(mdocs_js.version_file);
    if (confirm) {
	window.location.href = 'admin.php?page=memphis-documents.php&action=delete-doc&mdocs-index='+index+'&mdocs-cat='+category+'&mdocs-nonce='+nonce;
    }
}
function mdocs_download_file(mdocs_file, mdocs_url) { window.location.href = '?mdocs-file='+mdocs_file+'&mdocs-url=false'; }
function mdocs_share(mdocs_link,mdocs_direct,the_id) {
	if (toggle_share == false) {
		jQuery('#'+the_id+' .mdocs-share-link').remove();
		jQuery('#'+the_id).append('<div class="mdocs-share-link"><br>');
		jQuery('.mdocs-share-link').append('<div class="well well-sm"><h3><i class="fa fa-arrow-circle-o-right"></i> Download Page</h3><p>'+mdocs_link+'</p></div>');
		jQuery('.mdocs-share-link').append('<div class="well well-sm"><h3><i class="fa fa-download"></i> Direct Download</h3><p>'+mdocs_direct+'</p></div>');
		jQuery('.mdocs-share-link').append('</div>');
		toggle_share = true;
	} else {
		jQuery('#'+the_id+' .mdocs-share-link').remove();
		toggle_share = false;
	}
}
function mdocs_toogle_disable_setting(main, disable) {
	var checked = jQuery(main).prop('checked');
	if (checked) jQuery(disable).prop('disabled', true);
	else jQuery(disable).prop('disabled', false);
	jQuery(main).click(function() {
		var checked = jQuery(this).prop('checked');
		if (checked) jQuery(disable).prop('disabled', true);
		else jQuery(disable).prop('disabled', false);
	});
}
// RATINGS
function mdocs_ratings() {
    // DISPLAY RATING WIDGET
    jQuery('.ratings-button' ).click(function(event) {
	jQuery('.mdocs-ratings-body').empty();
	var mdocs_file_id = jQuery(this).data('mdocs-id');
	var mdocs_is_admin = jQuery(this).data('is-admin');
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'rating',mdocs_file_id: mdocs_file_id, wp_root: mdocs_js.wp_root},function(data) {
		jQuery('.mdocs-ratings-body').html(data);
		mdocs_submit_rating('large',mdocs_file_id);
	});
    });
}
function mdocs_submit_rating(size,file_id) {
    var my_rating = jQuery('.mdocs-ratings-stars').data('my-rating');
    if (size == 'large') {
	size = 'fa-5x';
	for (index = 1; index < 6; ++index) {
	if (my_rating >= index) jQuery('#'+index).prop('class', 'fa fa-star '+size+'  mdocs-gold mdocs-my-rating');
	    else  jQuery('#'+index).prop('class', 'fa fa-star-o '+size+' mdocs-my-rating');
	}
    } else size = 'fa-1x';

    jQuery('.mdocs-my-rating').click(function(event) {
	if (size == 'fa-1x') file_id = jQuery('.mdocs-post-header').data('mdocs-id');
	my_rating = jQuery(this).prop('id');
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'rating-submit',mdocs_file_id: file_id, 'mdocs-rating': my_rating},function(data) {
	    //jQuery('.mdocs-post').append(data);
	    window.location.href = '';
	});
    });
    jQuery('.mdocs-my-rating').mouseover(function() {
	for (index = 1; index < 6; ++index) {
	    if (this.id >= index) jQuery('#'+index).prop('class', 'fa fa-star '+size+' mdocs-gold mdocs-my-rating');
	    else  jQuery('#'+index).prop('class', 'fa fa-star-o '+size+' mdocs-my-rating');
	}
    });
    jQuery('.mdocs-rating-container-small, .mdocs-rating-container').mouseout(function() {
	my_rating = jQuery('.mdocs-ratings-stars').data('my-rating');
	for (index = 1; index < 6; ++index) {
	    if (my_rating >= index) jQuery('#'+index).prop('class', 'fa fa-star '+size+'  mdocs-gold mdocs-my-rating');
	    else  jQuery('#'+index).prop('class', 'fa fa-star-o '+size+' mdocs-my-rating');
	}
    });
}
// RESTORE DEFAULT
function mdocs_restore_default() {
   if (confirm(mdocs_js.restore_warning)) {
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type:'restore', blog_id: mdocs_js.blog_id, is_admin: true},function(data) {
	    window.location.href = "admin.php?page=memphis-documents.php&mdocs-cat=mdocuments&restore-default=true";
	});
    }

}
// DESRIPTION PREVIEW
function mdocs_description_preview() {
    jQuery('.description-preview' ).click(function(event) {
	jQuery('.mdocs-description-preview-body').empty();
	var mdocs_file_id = jQuery(this).data('mdocs-id');
	var mdocs_is_admin = jQuery(this).data('is-admin');
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'show-desc',mdocs_file_id: mdocs_file_id, is_admin: mdocs_is_admin},function(data) {
		jQuery('.mdocs-description-preview-body').html(data);
	});
    });
}
// FILE PREVIEW
function mdocs_file_preview() {
    jQuery('.file-preview' ).click(function(event) {
	jQuery('.mdocs-file-preview-body').empty();
	var mdocs_file_id = jQuery(this).data('mdocs-id');
	var mdocs_is_admin = jQuery(this).data('is-admin');
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'file',mdocs_file_id: mdocs_file_id, is_admin: mdocs_is_admin},function(data) {
	    jQuery('.mdocs-file-preview-body').html(data);
	});
    });
}
// IMAGE PREVIEW
function mdocs_image_preview() {
     jQuery('.img-preview' ).click(function() {
	jQuery('.mdocs-file-preview-body').empty();
	var mdocs_file_id = jQuery(this).data('mdocs-id');
	var mdocs_is_admin = jQuery(this).data('is-admin');
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'img',mdocs_file_id: mdocs_file_id, is_admin: mdocs_is_admin},function(data) {
	    jQuery('.mdocs-file-preview-body').html(data);
	});
    });
}
// TOOGLE DESCRIPTION/PREVIEW
function mdocs_toogle_description_preview() {
    jQuery('.mdocs-nav-tab' ).click(function(event) {
	event.preventDefault();
	jQuery('.mdocs-nav-tab').each(function() { jQuery(this).removeClass('mdocs-nav-tab-active'); });
	jQuery(this).addClass('mdocs-nav-tab-active');
	jQuery('#mdocs-show-container-'+mdocs_file_id).empty();
	var mdocs_file_id = jQuery(this).data('mdocs-id');
	var show_type = jQuery(this).data('mdocs-show-type');
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type:'show', show_type:show_type,mdocs_file_id: mdocs_file_id, wp_root: mdocs_js.wp_root},function(data) {
	    jQuery('#mdocs-show-container-'+mdocs_file_id).html(data);
	});
    });
}
// SORT FUNCTIONALITY
function mdocs_sort_files() {
    jQuery('.mdocs-sort-option').click(function() {
	if (jQuery(this).data('disable-user-sort') == false) {
	    var permalink = jQuery(this).data('permalink');
	    var current_cat = jQuery(this).data('current-cat');
	    var sort_type = jQuery(this).data('sort-type');
	    var sort_range = jQuery(this).children(':first').prop('class');
	    if (sort_range == 'fa fa-chevron-down') {
		sort_range = 'asc';
	    } else sort_range = 'desc';
	    jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'sort', sort_type: sort_type, sort_range: sort_range  },function(data) {
		jQuery('.mdocs-container').append(data);
		window.location.href = permalink;
	    });
	}
    });
}
// CHECK WIDTH OF DOCUMENTS AREA
is_collapsed = null;
function mdocs_check_width() {

    if(jQuery('#mdocs-navbar').width() < 600 && is_collapsed == false || jQuery('#mdocs-navbar').width() < 600 && is_collapsed == null) {
	is_collapsed = true;
	jQuery('#mdocs-nav-expand').remove();
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'nav-collaspse'  },function(data) {
	    jQuery('head').append(data);
	});
    } else if(jQuery('#mdocs-navbar').width() > 600 && is_collapsed == true || jQuery('#mdocs-navbar').width() > 600 &&  is_collapsed == null) {
	is_collapsed = false;
	jQuery('#mdocs-nav-collapse').remove();
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'nav-expand'  },function(data) {
	    jQuery('head').append(data);
	});
    }
}
// SHARING MODAL
function mdocs_share_modal() {
    jQuery('.sharing-button').click(function() {
	jQuery('.mdocs-share-body').empty();
	jQuery('.mdocs-share-body').html('<h1>Sharing</h1>');
	jQuery('.mdocs-share-body').append('<div class="well well-sm"><h3><i class="fa fa-arrow-circle-o-right"></i> Download Page</h3><p>'+jQuery(this).data('permalink')+'</p></div>');
	jQuery('.mdocs-share-body').append('<div class="well well-sm"><h3><i class="fa fa-download"></i> Direct Download</h3><p>'+jQuery(this).data('download')+'</p></div>');
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'show-social', 'doc-index': jQuery(this).data('doc-index')  },function(data) {
	    jQuery('.mdocs-share-body').append(data);
	    //TWITTER
	    !function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');
	    jQuery.getScript('http://platform.twitter.com/widgets.js');
	    //twttr.widgets.load();
	    //GOOGLE +1
	    (function() {
	      var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
	      po.src = '//apis.google.com/js/plusone.js';
	      var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	    })();
	    //FACEBOOK LIKE
	    window.fbAsyncInit = function() {
		FB.init({ xfbml: true, version: 'v2.4' });
	    };
	    (function(d, s, id){
		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) { return; }
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/sdk.js";
		fjs.parentNode.insertBefore(js, fjs);
	    }(document, 'script', 'facebook-jssdk'));
	    FB.XFBML.parse();
	    jQuery('.mdocs-share-body').append('<div id="fb-root"></div>');
	    // LINKEDIN
	    var din ='<script id="holderLink" type="IN/Share" data-url="" data-counter="right"></script>';
	    jQuery(".linkedinDetail").html(din);
	    IN.parse();
	});
    });
}
// VERSION 3.0 JAVASCRIPT PATCH
function mdocs_v3_0_patch(_numfiles) {

    if (typeof mdocs_js == 'object') var mdocs_vars = mdocs_js;
    else var mdocs_vars = mdocs_patch_js;

    jQuery.post(mdocs_vars.ajaxurl,{action: 'myajax-submit', type: 'mdocs-v3-0-patch'  },function(data) {
	jQuery('body').append(data);
	jQuery('#run-updater-3-0').click(function() {
	    jQuery('.container-3-0').html('\
		<div class="btn-container-3-0">\
		    <h3>'+mdocs_vars.patch_text_3_0_1+'</h3>\
		    <h1><i class="fa fa-spinner fa-pulse fa-3x"></i></h1>\
		    <h2>'+mdocs_vars.patch_text_3_0_2+'</h2>\
		</div>\
		');
	    jQuery.post(mdocs_vars.ajaxurl,{action: 'myajax-submit', type: 'mdocs-v3-0-patch-run-updater'  },function(data) {
		window.location.href = '';
	    });
	});
	jQuery('#not-now-3-0').click(function() {
	    jQuery.post(mdocs_vars.ajaxurl,{action: 'myajax-submit', type: 'mdocs-v3-0-patch-cancel-updater'  },function(data) {
		jQuery('html, body').css('overflowY', 'auto');
		jQuery('.bg-3-0').remove();
		jQuery('.container-3-0').remove();
	    });
	});
    });
}
// FIND LOST FILES
function mdocs_find_lost_files() {
    jQuery('#mdocs-find-lost-file-start').click(function() {
	jQuery('#mdocs-find-lost-file-start').addClass('disabled');
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'lost-file-search-start'  },function(data) {
	    jQuery('#mdocs-find-lost-file-start').removeClass('disabled');
	    jQuery('#mdocs-find-lost-files-results').html(data);
	    mdocs_find_lost_files_save();
	});
    });
    
}
function mdocs_find_lost_files_save() {
    jQuery('#mdocs-find-lost-files-save').submit(function(event) {
	jQuery('#mdocs-find-lost-files-save-btn').addClass('disabled');
	event.preventDefault();
	var form_data = jQuery(this).serialize();
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'lost-file-save', 'form-data': form_data  },function(data) {
	     jQuery('#mdocs-find-lost-files-results').html(data);
	});
    });
}
// BOX VIEW REFRESH
function mdocs_box_view_refresh() {
    jQuery('.box-view-refresh-button').click(function() {
	var filename = jQuery(this).data('filename');
	var index = jQuery(this).data('index');
	jQuery.post(mdocs_js.ajaxurl,{action: 'myajax-submit', type: 'box-view-refresh', 'filename': filename, 'index': index  },function(data) {
	    jQuery('.mdocs-container').append(data);
	    jQuery('#box-view-updated').delay(3000).fadeOut(400, function() { jQuery('#box-view-updated').remove()});
	});
    });
}
// CLOSE ALL MODALS
function mdocs_modal_close() {
    jQuery('.modal').on('hidden.bs.modal', function () {
	jQuery('.mdocs-modal-body').empty();
    })
}
// MODAL TOOGLE
function mdocs_toogle_modals() {
     jQuery('body').on('click.collapse-next.data-api', '[data-toggle=mdocs-modal]', function(event) {
	event.preventDefault();
	var options = {};
	var target = jQuery(this).data('target');
	jQuery(target).modal(options)   
    });
}