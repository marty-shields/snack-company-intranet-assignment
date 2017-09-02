<?php
/**
 * @package Login Logo
 * @version 1.0
 */
/*
Plugin Name: Login Logo Changer
Plugin URI: http://domain.com/myexample
Description: A simple plugin which changes the login wordpress logo to a logo of your own
Author: Martin
Version: 1.0
Author URI: http://domain.com
*/

function my_login_logo() { ?>
    <style type="text/css">
        .login h1 a {
            background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/login-icon.png);
            padding-bottom: 30px;
        }
    </style>
<?php }
remove_action( 'login_enqueue_scripts', 'my_login_logo' );
add_action( 'login_enqueue_scripts', 'my_login_logo' );

function my_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'my_login_logo_url' );

function my_login_logo_url_title() {
    return 'Your Site Name and Info';
}
add_filter( 'login_headertitle', 'my_login_logo_url_title' );

?>