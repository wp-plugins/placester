<?php

pls_register_post_types();

/* Register Placester post types. */
function pls_register_post_types() {
	if ( current_theme_supports( 'pls-post-type-agent' ) ) {
		require_once( trailingslashit( PLS_POST_TYPES_DIR ) . 'agent.php' );
	}
	
	if ( current_theme_supports( 'pls-post-type-service' ) ) {
		require_once( trailingslashit( PLS_POST_TYPES_DIR ) . 'service.php' );
	}
	
	if ( current_theme_supports( 'pls-post-type-testimonial' ) ) {
		require_once( trailingslashit( PLS_POST_TYPES_DIR ) . 'testimonial.php' );
	}

	if ( current_theme_supports( 'pls-post-type-ad' ) ) {
		require_once( trailingslashit( PLS_POST_TYPES_DIR ) . 'ad.php' );
	}
}