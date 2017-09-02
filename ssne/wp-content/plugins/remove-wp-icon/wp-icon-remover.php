<?php
/**
 * @package Martin S Remove WP Icon
 * @version 1.0
 */
/*
Plugin Name: Remove Plugin Icon
Plugin URI: http://domain.com/myexample
Description: A simple plugin which removes the WP icon in the tool bar at the top of the page
Author: Martin Shields
Version: 1.0
Author URI: http://domain.com
*/
  
add_action( 'admin_bar_menu', 'remove_wp_logo', 999 );

function remove_wp_logo( $wp_admin_bar ) {
    
	$wp_admin_bar->remove_node( 'wp-logo' );

}
    
?>