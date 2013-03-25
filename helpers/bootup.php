<?php 

PL_Bootup::init();
class PL_Bootup {

  static $switching = false;
  static $items_that_can_be_created = array(
      'pages' => array(),
      'menus' => array(),
      'posts' => array(),
      'agents' => array(),
      'testimonials' => array(),
      'settings' => array()
  );

	public function init () {
    // Make sure this action is NOT added if the theme is changed via an AJAX call...
    if ( !defined('DOING_AJAX') ) {
      add_action('after_switch_theme', array( __CLASS__, 'theme_switch_user_prompt' ));
    }
    
    add_action('wp_ajax_add_dummy_data', array( __CLASS__, 'add_dummy_data') );
  }

  public function is_theme_switched () {
    return self::$switching;
  }

  public function add_dummy_data () {
    // Retrieve default and theme manifests
    $manifest = wp_parse_args( self::parse_manifest_to_array(), self::$items_that_can_be_created );
    extract($manifest);

    // Start creating dummy data here...
    if ( !empty($pages) )  {
      self::create_pages( $pages );
    }
    if ( !empty($menus) ) {
      self::create_menus( $menus );
    }
    if ( !empty($posts) ) {
      self::create_posts( $posts, 'post', $settings );
    }

    // Add CPTs here
    $all_cpts = array(
      'agent' => $agents,
      'testimonial' => $testimonials,
      'service' => $services
    );

    // create CPT posts
    foreach ($all_cpts as $post_type => $custom_posts) {
      if (post_type_exists($post_type)) {
        self::create_posts( $custom_posts, $post_type, $settings );
      }
      // delete Hello World post
      wp_delete_post( 1, true );
    }

    echo json_encode(true);
    die();
  }


  private function create_pages ( $pages ) {
    PL_Pages::create_once( $pages, $force_template = false );
  }

  private function create_menus ( $menus ) {
    PL_Menus::create( $menus );
  }

  private function create_posts ( $posts, $post_type, $settings ) {
    PL_Posts::create( $posts, $post_type, $settings );
  }

  public function theme_switch_user_prompt () {
    self::$switching = true;
    
    PL_Router::load_builder_partial('theme-switch.php');
    PL_Router::load_builder_partial('dummy-data-confirmation.php');
  }

	private function parse_manifest_to_array () {
		return json_decode( file_get_contents( self::get_current_theme_manifest_location() ), true );
	}

	private function get_current_theme_manifest_location () {	
		$template = trailingslashit( get_template_directory() );
		if (file_exists( $template . 'manifest.json' )) {
			return $template . 'manifest.json';
		}
		return trailingslashit( PL_PARENT_URL ) . 'config/default-manifest.json';
	}

	private function is_placester_theme () {
		global $i_am_a_placester_theme;
		if ($i_am_a_placester_theme) {
			return true;
		}
		return false;
	}

}