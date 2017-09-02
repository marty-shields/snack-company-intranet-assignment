<?php

if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Manages externa compatibility functions folder
 *
 *
 * @version		1.0.0
 * @package		post-type-x/functions
 * @author 		Norbert Dreszer
 */
add_action( 'plugins_loaded', 'run_ext_comp_files', 20 );

function run_ext_comp_files() {
	if ( function_exists( 'pll_get_post' ) || function_exists( 'icl_object_id' ) ) {
		require_once(AL_BASE_PATH . '/ext-comp/multilingual.php');
	}

	if ( defined( 'WPSEO_VERSION' ) ) {
		require_once(AL_BASE_PATH . '/ext-comp/wpseo.php');
	}

	if ( defined( 'QTS_VERSION' ) ) {
		require_once(AL_BASE_PATH . '/ext-comp/qtranslate-slug.php');
	}
}
