<?php
/**
 * @package Martin Shields Favicon
 * @version 1.0
 */
/*
Plugin Name: Martin Shields Favicon
Plugin URI: http://domain.com/myexample
Description: A simple plugin which changes the small favicon to a custom one
Author: Martin Shields
Version: 1.0
Author URI: http://domain.com
*/

add_action( 'wp_head', 'my_favicon' );
add_action('admin_head', 'my_favicon');
add_action('login_head', 'my_favicon');

function my_favicon() { ?>

<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/login-icon.png" />

<?php }

?>