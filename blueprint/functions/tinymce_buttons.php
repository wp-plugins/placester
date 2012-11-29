<?php

class PLS_Shortcode_Buttons {
	
	function __construct() {
		add_action( 'init', array( $this, 'add_placester_button' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_init', array( $this, 'featured_listings_popup' ), 15 );
		add_action( 'admin_init', array( $this, 'static_listings_popup' ), 15 );

		add_action( 'admin_print_styles', array( $this, 'tinymce_dialog_touches' ), 99 );
		add_filter( 'tiny_mce_version', array( $this, 'my_refresh_mce' ) );
	}
	
	function enqueue_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-dialog');
		wp_enqueue_style('jquery-ui');
		wp_enqueue_style('wp-jquery-ui-dialog');
		//wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
	}
	
	/* Add buttons */
	
	function add_placester_button() {
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
			return;
		if ( get_user_option('rich_editing') == 'true') {
			add_filter('mce_external_plugins', array( $this, 'add_placester_tinymce_plugin' ) );
			add_filter('mce_buttons', array( $this, 'register_placester_button' ) );
		}
	}
	
	/* Add button callbacks */
	
	function register_placester_button($buttons) {
		array_push($buttons, "|", "pls_placester");
		return $buttons;
	}
	
	function add_placester_tinymce_plugin($plugin_array) {
		$plugin_array['pls_placester'] = trailingslashit( PLS_JS_URL ) . 'scripts/tinymce_buttons.js';
		return $plugin_array;
	}
	
	/**
	 * Hacky for refreshing the TinyMCE cache
	 * @param inr $ver
	 * @return number version
	 */
	function my_refresh_mce($ver) {
		$ver += 3;
		return $ver;
	}
	
	function featured_listings_popup() {
		global $pagenow;
		if( $pagenow == 'post-new.php' || $pagenow == 'post.php' ):
				$listings_query = new WP_Query( array( 
							'post_type' => 'featured_listing',
						)		
					);
			?>
				<div id="dialog-featured-listings" class="wp-dialog ui-dialog" style="display:none;">
					<div id="dialog-featured-wrapper" style="padding: 40px; background-color: white; z-index:99999; position: relative;">
						<form id="dialog-featured-form" method="POST">
							<h2>Pick a featured listing</h2>
							<select id="featured-listings-select" name="featured-listings-select">
								<?php 
								if( $listings_query->have_posts() ):
									while( $listings_query->have_posts() ):
										$listings_query->the_post();
								 
										$meta = get_post_meta(get_the_ID(), 'pl_listing_type'); 
										if( ! empty( $meta ) && $meta[0] == 'featured' ):
								?>
								<option value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
								<?php endif; endwhile; endif; ?>
							</select>
							<input id="featured-listings-submit" type="submit" />
						</form>
						<a href="#" class="ui-dialog-titlebar-close ui-corner-all ui-icon-closethick" role="button"></a>
					</div>
				</div>
			<?php
			wp_reset_postdata();
		endif;
	}
	
	function static_listings_popup() {
		global $pagenow;
		if( $pagenow == 'post-new.php' || $pagenow == 'post.php' ):
			$listings_query = new WP_Query( array(
						'post_type' => 'featured_listing',
						)
				);
			?>
				<div id="dialog-static-listings" class="wp-dialog ui-dialog" style="display:none;">
					<div id="dialog-featured-wrapper" style="padding: 40px; background-color: white; z-index:99999; position: relative;">
						<form id="dialog-static-form" method="POST">
							<h2>Pick a static listing</h2>
							<select id="static-listings-select" name="static-listings-select">
								<?php 
								if( $listings_query->have_posts() ):
									while( $listings_query->have_posts() ):
										$listings_query->the_post();
								 
										$meta = get_post_meta(get_the_ID(), 'pl_listing_type'); 
										if( ! empty( $meta ) && $meta[0] == 'static' ):
								?>
								<option value="<?php echo get_the_ID(); ?>"><?php echo get_the_title(); ?></option>
								<?php endif; endwhile; endif; ?>
							</select>
							<input id="static-listings-submit" type="submit" />
						</form>
						<a href="#" class="ui-dialog-titlebar-close ui-corner-all ui-icon-closethick" role="button"></a>
					</div>
				</div>
			<?php
			wp_reset_postdata();
		endif;
	}
	
	function tinymce_dialog_touches() {
	?>
		<style type="text/css">
			div.ui-dialog {
				border: 0px;
			}
			div.ui-dialog div.ui-dialog-titlebar {
				display: none;
			}
			
		</style>
	<?php 
	}
}

if ( defined( 'PL_PLUGIN_VERSION' ) ) {
	new PLS_Shortcode_Buttons();
}