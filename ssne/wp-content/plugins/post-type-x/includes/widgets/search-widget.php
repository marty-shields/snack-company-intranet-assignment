<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Manages item search widget
 *
 * Here item search widget is defined.
 *
 * @version		1.4.0
 * @package		post-type-x/includes
 * @author 		Norbert Dreszer
 */
class product_widget_search extends WP_Widget {

	function __construct() {
		$widget_ops = array( 'classname' => 'product_search search widget_search', 'description' => __( 'A search form for your items.', 'post-type-x' ) );
		parent::__construct( 'product_search', __( 'Item Search', 'post-type-x' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		if ( get_integration_type() != 'simple' ) {
			$title = apply_filters( 'widget_title', empty( $instance[ 'title' ] ) ? '' : $instance[ 'title' ], $instance, $this->id_base );

			echo $args[ 'before_widget' ];
			if ( $title )
				echo $args[ 'before_title' ] . $title . $args[ 'after_title' ];

			// Use current theme search form if it exists
			$search_button_text = apply_filters( 'product_search_button_text', '' );
			echo '<form role="search" class="' . design_schemes( 'box', 0 ) . '" method="get" id="product_search_form" action="' . esc_url( home_url( '/' ) ) . '">
<input type="hidden" name="post_type" value="' . get_current_screen_post_type() . '" />
<input class="product-search-box" type="search" value="' . get_search_query() . '" id="s" name="s" placeholder="' . __( 'Item Search', 'post-type-x' ) . '" />
<input class="product-search-submit" type="submit" id="searchsubmit" value="' . $search_button_text . '" />
</form>';

			echo $args[ 'after_widget' ];
		}
	}

	function form( $instance ) {
		if ( get_integration_type() != 'simple' ) {
			$instance	 = wp_parse_args( (array) $instance, array( 'title' => '' ) );
			$title		 = $instance[ 'title' ];
			?>
			<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'post-type-x' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p><?php
		} else {
			if ( is_integration_mode_selected() ) {
				implecode_warning( sprintf( __( 'Search widget is disabled with simple theme integration. Please see <a href="%s">Theme Integration Guide</a> to enable item search widget.', 'post-type-x' ), 'https://implecode.com/wordpress/product-catalog/theme-integration-guide/#cam=simple-mode&key=search-widget' ) );
			} else {
				implecode_warning( sprintf( __( 'Search widget is disabled due to a lack of theme integration.%s', 'post-type-x' ), sample_product_button( 'p' ) ) );
			}
		}
	}

	function update( $new_instance, $old_instance ) {
		$instance			 = $old_instance;
		$new_instance		 = wp_parse_args( (array) $new_instance, array( 'title' => '' ) );
		$instance[ 'title' ] = strip_tags( $new_instance[ 'title' ] );
		return $instance;
	}

}
