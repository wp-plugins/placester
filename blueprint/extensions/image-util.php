<?php 

/*
Based heavily on Wes Edling's chaching/scaling script, modified to work properly in our context.
Modifications include:
	 - fixing the way urls are handled to remove get vars in the image name
	 - rewrote to use GD for image manipulation rather then ImageMagic

TODO: 
	- break this out into reusable functions so the logic is more obvious
	- performance testing / optimization.

Here's Wes' requested attribution for the modified "resize" function:

function by Wes Edling .. http://joedesigns.com
feel free to use this in any project, i just ask for a credit in the source code.
a link back to my site would be nice too.

** Wes' resizing was removed because WP Theme Submission didn't allow file_get_contents();

*/

// Include the GD image manipulation library. 
include(trailingslashit ( PLS_EXT_DIR ) . 'image-util/image-resize-writer.php');

PLS_Image::init();
class PLS_Image {

	public static function init() {

		if (!is_admin()) {
			add_action('init', array(__CLASS__,'enqueue'));
		}
	}

    public static function enqueue() {

        $image_util_support = get_theme_support( 'pls-image-util' );

		if ( !wp_script_is('pls-image-util-fancybox' , 'registered') ) {
	        wp_register_script( 'pls-image-util-fancybox', trailingslashit( PLS_EXT_URL ) . 'image-util/fancybox/jquery.fancybox-1.3.4.pack.js' , array( 'jquery' ), NULL, true );
		}

		if ( !wp_script_is('pls-image-util-fancybox-default-settings' , 'registered') ) {
        	wp_register_script( 'pls-image-util-fancybox-default-settings', trailingslashit( PLS_EXT_URL ) . 'image-util/fancybox/default-settings.js' , array( 'jquery' ), NULL, true );
		}
		
		if ( !wp_style_is('pls-image-util-fancybox-style' , 'registered') ) {
        	wp_register_style( 'pls-image-util-fancybox-style', trailingslashit( PLS_EXT_URL ) . 'image-util/fancybox/jquery.fancybox-1.3.4.css' );
		}

        if ( is_array( $image_util_support ) ) {
            if ( in_array( 'fancybox', $image_util_support[0] ) ) {
              	if ( !wp_script_is('pls-image-util-fancybox' , 'queue') ) {
	  				wp_enqueue_script( 'pls-image-util-fancybox' );
              	}

				if ( !wp_script_is('pls-image-util-fancybox-default-settings' , 'queue') ) {
	                wp_enqueue_script( 'pls-image-util-fancybox-default-settings' );
				}

				if ( !wp_style_is('pls-image-util-fancybox-style' , 'queue') ) {
	                wp_enqueue_style( 'pls-image-util-fancybox-style' );
				}
            }
            return;
        }
    }

	public static function load ($old_image = '', $args = null) {
		$new_image = false;

		if (isset($args['fancybox']) && $args['fancybox']) {
			unset($args['fancybox']);
		}

	    $args = self::process_defaults($args);
	    $disable_dragonfly = pls_get_option('pls-disable-dragonfly');
	    
	    // use standard default image
		if ( $old_image === '' || empty($old_image)) {
			if ( !empty($args['null_image']) ) {
				$old_image = $args['null_image'];
			} 
			else {
				$old_image = PLS_IMG_URL . "/null/listing-1200x720.jpg";
			}
		} 
		elseif ( $args['allow_resize'] && $args['resize']['w'] && $args['resize']['h'] && get_theme_support('pls-dragonfly') && ($disable_dragonfly != true)) {

			extract(wp_parse_args(parse_url($old_image), array('query' => '') ));

			//finds the extension, "jpeg" in this case
			$pathinfo = pathinfo($path);
			$ext = $pathinfo['extension'];
			$host = 'd2frgvzmtkrf4d.cloudfront.net';
			$size = $args['resize']['w'] . 'x' . $args['resize']['h'] . '#';
			$action = 'thumb';
			// $action = 'resize';
			// $action = 'crop';

			//corrects image path to remove starting "/" included in $path
			$path = ltrim($path, '/');

			$request_tabs_newlines = "f\t" . $path . "\np" . "\t". $action . "\t". $size . "\ne" . "\t" . $ext;
			$request_clean = 'f' . $path . 'p' . $action . $size . 'e' . $ext;
			$job = base64_encode($request_tabs_newlines);
			$secret = substr(sha1($request_clean . PLACESTER_DF_SECRET), 0, 16);
			$new_image = $scheme . '://' . $host . '/' . $secret . '/' . rtrim($job, '=') . '.' . $ext . '?' . $query;
		}

		if ( $args['fancybox'] || $args['as_html']) {
			if ($new_image) {
				$new_image = self::as_html($old_image, $new_image, $args);
			} else {
				$new_image = self::as_html($old_image, null, $args);
			}
		}

		// return the new image if we've managed to create one
		if ($new_image) {
			return $new_image;
		} 
		else {
			return $old_image;
		}

	}
	
	private static function as_html ($old_image, $new_image = false, $args )
	{
		extract( $args, EXTR_SKIP );
		// echo 'here in html';
		// pls_dump($html);
		if ($fancybox && !$as_html) {
			// echo 'fancybox';
			ob_start();
			// our basic fancybox html
			?>
				<a ref="#" rel="<?php echo @$html['rel']; ?>" class="<?php echo isset( $fancybox['trigger_class'] ) ? $fancybox['trigger_class'] : '' . ' ' . ( isset( $html['classes'] ) ? $html['classes'] : '' )  ?>" href="<?php echo @$old_image; ?>" >
					<img alt="<?php echo @$html['alt']; ?>" title="<?php echo @$html['title'] ? $html['title'] : ''; ?>" class="<?php echo @$html['img_classes']; ?>" style="width: <?php echo @$resize['w']; ?>px; height: <?php echo @$resize['h']; ?>px; overflow: hidden;" src="<?php echo $new_image ? $new_image : $old_image; ?>" />
				</a>
			<?php
			
			return trim( ob_get_clean() );
			
			
		} else {
			ob_start();
			?>
			<img class="<?php echo @$html['img_classes']; ?>" style="width: <?php echo @$resize['w']; ?>px; height: <?php echo @$resize['h']; ?>px; overflow: hidden;" src="<?php echo $new_image ? $new_image : $old_image; ?>" alt="<?php echo @$html['alt']; ?>" title="<?php echo $html['title'] ?>" itemprop="image" />
			<?php
		
			return trim(ob_get_clean());
		}
	}
	

	private static function process_defaults ($args) {

		/** Define the default argument array. */
		$defaults = array(
			'resize' => array(
				'w' => false,
				'h' => false
			),
			'allow_resize' => true,
			'html' => array(
				'ref' => '',
				'rel' => 'gallery',
				'a_classes' => '',
				'img_classes' => '',
				'alt' => '',
				'title' => ''
			),
			'as_html' => false,
			'as_url' => true,
			'fancybox' => array(
			'trigger_class' => 'pls_use_fancy',
			'classes' => false,
			'null_image' => false,
			'allow_dragonfly' => true
			),
		);

        /** Merge the arguments with the defaults. */
        $args = wp_parse_args( $args, $defaults );
        $args['resize'] = wp_parse_args( $args['resize'], $defaults['resize']);
        $args['html'] = wp_parse_args( $args['html'], $defaults['html']);

        return $args;
				
	}
}// end class 

?>