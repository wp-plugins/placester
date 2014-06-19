<?php

class PL_Customizer 
{
	private static $def_setting_opts = array(
			                          'default'   => '',
			                          'type'      => 'option',
			                          'transport' => 'refresh' 
			                        );

	private static $priority = 0;


	private function get_setting_opts( $args = array() )
	{
		$merged_opts = wp_parse_args($args, self::$def_setting_opts);
		
		return $merged_opts;
	}

	private function get_priority( $section = '' ) 
	{
  		$new_priority = 10; // Default priority in case no other can be found...
  		global $PL_CUSTOMIZER_ONBOARD_SECTIONS;

  		if ( is_array($PL_CUSTOMIZER_ONBOARD_SECTIONS) && !empty($section) ) {
  			$new_priority = $PL_CUSTOMIZER_ONBOARD_SECTIONS[$section];
  		}
  			
	  	return $new_priority;
	}

	private function get_control_opts( $id, $opts, $sect_id, $is_custom = false )
	{
		$args = array(
                        'settings' => $id,
                        'label'    => $opts['name'],
                        'section'  => $sect_id
                     );

		if ( !$is_custom ) {
			$args['type'] = $opts['type'];
		}

		return $args;
	}

	public function register_components( $wp_customize, $excluded = null ) 
	{
		$theme_opts_key = $wp_customize->get_stylesheet();
	    $last_section_id = '';

	    global $PL_CUSTOMIZER_ONBOARD_OPTS;
	    $options = $PL_CUSTOMIZER_ONBOARD_OPTS;

	    foreach ($options as $opt) 
	    {
	    	// Skip over excluded options...
	    	if ( $excluded && $opt['name'] && in_array($opt['name'], $excluded) ) {
	    		continue;
	    	}

	    	// Take care of defining some common vars used many of the cases below...
	    	if ( isset($opt['id']) ) {
	    		$setting_id = "{$theme_opts_key}[{$opt['id']}]";
	    		$control_id = "{$opt['id']}_ctrl";
	    	}

	    	$custom_args = array();
	    	if ( isset($opt['transport']) ) {
	    		$custom_args['transport'] = $opt['transport'];
	    	}

	        switch ( $opt['type'] ) 
	        {
	            case 'heading':
	                $args_section = array( 'title' => __($opt['name'],''), 'description' => $opt['name'] ); 
	                $args_section['priority'] = self::get_priority( $opt['id'] );
            		// Check for optional keys...
                	if ( isset($opt['desc']) ) {
                		$args_section['subtitle'] = $opt['desc'];	
                	}
                	if ( isset($opt['class']) ) {
                		$args_section['class'] = $opt['class'];
                	}

	                $id_base = isset($opt['id']) ? $opt['id'] : $opt['name'];
	                $section_id = strtolower( str_replace( ' ', '_', $id_base ) );
	                // $wp_customize->add_section( $section_id, $args_section );
	                $wp_customize->add_section( new PL_Customize_Section( $wp_customize, $section_id, $args_section ) );

	                // Add dummy control to certain sections so that they will appear...
	                if ( isset($opt['class']) && $opt['class'] == 'no-pane' ) {
	                	$wp_customize->add_setting( 'dummy_setting', array() );
	                	$wp_customize->add_control( "dummy_ctrl_{$section_id}", array('settings' => 'dummy_setting', 'section' => $section_id, 'type' => 'none') );
	            	}

	                $last_section_id = $section_id;
	                break;

	            // Handle the standard (i.e., 'built-in') controls...
	            case 'text':
	            case 'checkbox':
	            	$wp_customize->add_setting( $setting_id, self::get_setting_opts($custom_args) );
	                
	                $args_control = self::get_control_opts( $setting_id, $opt, $last_section_id );
	                $wp_customize->add_control( $control_id, $args_control);
	                break;

	            case 'textarea':
		            $wp_customize->add_setting( $setting_id, self::get_setting_opts($custom_args) );

	                $args_control = self::get_control_opts( $setting_id, $opt, $last_section_id, true );
	                $wp_customize->add_control( new PL_Customize_TextArea_Control($wp_customize, $control_id, $args_control) );
	                break;

	            case 'typography':
	            	$typo_setting_keys = array('size', 'face', 'style', 'color');
	            	$typo_setting_ids = array();
	            	
	            	foreach ($typo_setting_keys as $key) {
	            		$wp_customize->add_setting( "{$setting_id}[{$key}]", self::get_setting_opts() );
	            		$typo_setting_ids[$key] = "{$setting_id}[{$key}]";
	            	}
	            	
	            	$args_control = self::get_control_opts( $typo_setting_ids, $opt, $last_section_id, true );
	                $wp_customize->add_control( new PL_Customize_Typography_Control($wp_customize, $control_id, $args_control) );
	            	break;

	            case 'upload':
	            	$wp_customize->add_setting( $setting_id, self::get_setting_opts() );

	            	$args_control = self::get_control_opts( $setting_id, $opt, $last_section_id, true );
	                $wp_customize->add_control( new WP_Customize_Upload_Control($wp_customize, $control_id, $args_control) );
	            	break;

	            case 'background':
	            	$setting_id .= '[color]';
	            	$wp_customize->add_setting( $setting_id, self::get_setting_opts() );

	            	$args_control = self::get_control_opts( $setting_id, $opt, $last_section_id, true );
	            	$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, $control_id, $args_control) );
	            	break;	

	            case 'custom':
	            	// Register PL component...
	            	self::register_pl_control( $wp_customize, $opt['name'], $last_section_id );
	            	break;

	            default:
	                break;
	        } 
	    }

	}

	public function register_pl_control( $wp_customize, $name, $section_id ) 
	{
		// Dummy setting must be associated with non-option sections in order for them to appear/function properly...
	    $dummy_setting_id = 'dummy_setting';
	    $wp_customize->add_setting( 'dummy_setting', array() );

		switch ( $name ) 
		{
			case 'theme-select':
				$switch_theme_ctrl_id = 'switch_theme_ctrl';
	    		$switch_theme_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $section_id, 'type' => 'none');
	    		$wp_customize->add_control( new PL_Customize_Switch_Theme_Control($wp_customize, $switch_theme_ctrl_id, $switch_theme_args_ctrl) );
				break;
			
			case 'integration':
				if ( PL_Option_Helper::api_key() && !PL_Integration_Helper::idx_prompt_completed() ) {
			        $int_ctrl_id = 'integration_ctrl';
			        $int_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $section_id, 'type' => 'none');
			        $wp_customize->add_control( new PL_Customize_Integration_Control($wp_customize, $int_ctrl_id, $int_args_ctrl) );
				}
				break;
			
			case 'demo-data':
				$demo_setting_id = 'pls_demo_data_flag';
			    $wp_customize->add_setting( $demo_setting_id, self::get_setting_opts() );
				
				$demo_ctrl_id = 'demo_data_ctrl';                
			    $demo_args_control = self::get_control_opts( $demo_setting_id, array('name'=>'Use Demo Listing Data', 'type'=>'checkbox'), $section_id );
			    $wp_customize->add_control( $demo_ctrl_id, $demo_args_control);
				break;
			
			case 'theme-opt-defaults':
				if ( class_exists('PLS_Options_Manager') ) {
				    $load_opts_ctrl_id = 'load_opts_ctrl';
				    $load_opts_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $set_section_id, 'type' => 'none');
				    $wp_customize->add_control( new PL_Customize_Load_Theme_Opts_Control($wp_customize, $load_opts_ctrl_id, $load_opts_args_ctrl) );
				}
				break;

			case 'post-listing':
				$listing_ctrl_id = 'listing_ctrl';
				$listing_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $section_id, 'type' => 'none');
				$wp_customize->add_control( new PL_Customize_Listing_Control($wp_customize, $listing_ctrl_id, $listing_args_ctrl) );
				break;

			case 'blog-post':
				$blog_post_ctrl_id = 'blog_post_ctrl';
				$blog_post_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $section_id, 'type' => 'none');
				$wp_customize->add_control( new PL_Customize_Blog_Post_Control($wp_customize, $blog_post_ctrl_id, $blog_post_args_ctrl) );
				break;

			case 'color-scheme':
			 	$color_scheme_ctrl_id = 'color_scheme_ctrl';
			 	$color_scheme_args_ctrl = array('settings' => $dummy_setting_id, 'section' => $section_id, 'type' => 'none');
			 	$wp_customize->add_control( new PL_Customize_Color_Scheme_Control($wp_customize, $color_scheme_ctrl_id, $color_scheme_args_ctrl) );
			 	break;
				
			default:
				# code...
				break;
		}
	    
	}
	    
}
?>