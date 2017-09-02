<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages item functions folder
 *
 * Here all plugin functions folder is defined and managed.
 *
 * @version        1.0.0
 * @package        post-type-x/functions
 * @author        Norbert Dreszer
 */
require_once(AL_BASE_PATH . '/functions/content-functions.php');

require_once(AL_BASE_PATH . '/functions/conditionals.php');
require_once(AL_BASE_PATH . '/functions/compatibility.php');

function start_admin_only_functions() {
	if ( !is_admin() && is_user_logged_in() ) {
		require_once(AL_BASE_PATH . '/functions/catalog-admin.php');
	}
}

add_action( 'wp', 'start_admin_only_functions' );
