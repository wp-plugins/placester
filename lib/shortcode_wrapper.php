<?php


PL_Shortcode_Wrapper::init();

class PL_Shortcode_Wrapper {
	
	public function init() {
		// do init if needed
	}
	
	public static function create( $shortcode, $content = '' ) {
		ob_start();		
		do_action( $shortcode . '_pre_header' );
		// do some real shortcode work
		echo $content;
		do_action( $shortcode . '_post_footer' );
		return ob_get_clean();
	}
	
}