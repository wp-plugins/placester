<?php 

PLS_Style::init();

class PLS_Style {
	

    static $styles = array();

    /**
     *  grabs the option list and makes it available
     */
    static function init()
    {

        // hooks create_css into head. create_css generates all
        // the needed css for these options.
        add_filter('wp_head', array(__CLASS__, 'create_css') );		
        
        // bundles all the options to the class so they can 
        // have styles generated for theme. 
        self::get_options();

    }

    static function get_options()
    {
        // Cache options
        $cache = new PLS_Cache('Theme PLS Options');
        $cache_args = array();
        if ($options = $cache->get($cache_args)) {
            self::$styles = array_merge(self::$styles, $options);
            return;
        }

        require(PLS_Route::locate_blueprint_option('init.php'));
        require_if_theme_supports("pls-user-options", PLS_Route::locate_blueprint_option('user.php'));
        require_if_theme_supports("pls-search-options", PLS_Route::locate_blueprint_option('search.php'));    
        require_if_theme_supports("pls-color-options", PLS_Route::locate_blueprint_option('colors.php'));    
        require_if_theme_supports("pls-slideshow-options", PLS_Route::locate_blueprint_option('slideshow.php'));    
        require_if_theme_supports("pls-typography-options", PLS_Route::locate_blueprint_option('typography.php'));
        require_if_theme_supports("pls-header-options", PLS_Route::locate_blueprint_option('header.php'));
        require_if_theme_supports("pls-navigation-options", PLS_Route::locate_blueprint_option('navigation.php'));   
        require_if_theme_supports("pls-listing-options", PLS_Route::locate_blueprint_option('listings.php'));
        require_if_theme_supports("pls-post-options", PLS_Route::locate_blueprint_option('post.php'));
        require_if_theme_supports("pls-widget-options", PLS_Route::locate_blueprint_option('widget.php'));
        require_if_theme_supports("pls-footer-options", PLS_Route::locate_blueprint_option('footer.php'));
        require_if_theme_supports("pls-css-options", PLS_Route::locate_blueprint_option('css.php'));

        // Cache options
        $cache->save(self::$styles);
    }

    public static function add ($options = false)
    {
        if ($options) {
            self::$styles[] =$options;
        }
    }

	public static function create_css () {
			// error_log('Styles being created');

			// Groups all the styles by selector so they can be combined into a string, which is echo'd out 
			$sorted_selector_array = self::sort_by_selector(self::$styles);

			if ( empty($sorted_selector_array) ) {
				return false;
			}

			$styles = '';

			foreach ( $sorted_selector_array as $selector => $options) {

				$styles .= apply_filters($selector, $selector) . ' {' . "\n";

				foreach ($options as $index => $option) {

					$defaults = array(
						"name" => "",
						"desc" => "",
						"id" => "",
						"std" => "",
						"selector" => "body",
						"style" => "",
						"type" => "",
						"important" => true,
						"default" => ""
					);

					/** Merge the arguments with the defaults. */
					$option = wp_parse_args( $option, $defaults );

					if (!empty($option['style']) || self::is_special_case($option['type'])) {

						//if we have a style, then let's try to generate a stlye.
						$styles .= self::handle_style($option['style'], $option['id'], $option['default'], $option['type'], $option['important']);

          // } elseif (!empty($id)) {
            // $id doesn't exist... not sure how this was meant to be executed. 
            // you can't check for ID because all options require IDs
            
						//try to use the id as the style... saves time for power devs.
            // $styles .= self::handle_style($option['style'], $option['id'], $option['default'], $option['type'], $option['important']);

					} else {
					}
				}
				$styles .= '}' . "\n";
			}

			// error_log('<pre>' . $styles . '</pre>');

			$styles = '<style type="text/css">' . $styles . '</style>';

			echo $styles;

	}

	//for quick styling
	private static function handle_style ($style, $id, $default, $type, $important) 
	{

        if ($value = pls_get_option($id, $default)) {
            
            $css_style = '';
            
            // check for special cases
            // sometimes the options framework saves certain options
            // in unique ways which can't be directly translated into styles
            if (self::is_special_case($type)) {
                
                //handles edge cases, returns a property formatted string
                
                return self::handle_special_case($value, $id, $default, $type, $important);
            } else {
                $css_style = self::make_style($style, $value, $important);
                return $css_style;                    
            }
                        
        } else {
            return '';
        }
	}

    private static function handle_special_case($value, $id, $default, $type, $important)
    {
        switch ($type) {
            case 'bg_gradient':
              return self::handle_bg_gradient($value, $id, $default, $type, $important);
              break;
            case 'typography':
                return self::handle_typography($value, $id, $default, $type, $important);
                break;
            case 'background':
                return self::handle_background($value, $id, $default, $type, $important);
                break;
            case 'border':
                return self::handle_border($value, $id, $default, $type, $important);
                break;
            case 'box_shadow':
            	return self::handle_box_shadow($value, $id, $default, $type, $important);
            	break;
            case 'border_shadow':
                $border_shadow_style = self::handle_border($value, $id, $default, $type, $important);
                $border_shadow_style .= self::handle_box_shadow($value, $id, $default, $type, $important);
				
                return $border_shadow_style;
                break;
        }
    }

	private static function handle_bg_gradient( $value, $id, $default, $type, $important ) {
		// only value should be color
		$css_style = '';
		if( isset( $value['color'] ) ) {
			// proceed
			$css_style .= self::make_style('bg_gradient', $value, $important);
		}
		return $css_style;
	}

    private static function handle_background($value, $id, $default, $type, $important) {

        if (is_array($value)) {
            
            $css_style = '';
            // find out if we need to build a gradient or not
            $do_gradient = false;
            if( isset( $value['gradation'] ) ) {
              if( "1" == $value['gradation'] ) {
                $do_gradient = true;
              }
            }
            
            foreach ($value as $key => $value) {
                switch ($key) {
                    case 'color':
                        // check for gradient and act accordingly
                        if( $do_gradient ) {
                          $css_style .= self::make_style('bg_gradient', $value, $important );
                        } else {
                          $css_style .= self::make_style('background', $value, $important);
                        }
                        break;

                    case 'image':
                        $css_style .= self::make_style('background-image', $value, $important);
                        break;
                    
                    case 'repeat':
                        $css_style .= self::make_style('background-repeat', $value, $important);
                        break;
                    
                    case 'position':
                        $css_style .= self::make_style('background-position', $value, $important);
                        break;

                    case 'attachment':
                        $css_style .= self::make_style('background-attachment', $value, $important);
                        break;    
                }    
            }
            return $css_style;
        }
    }

    private static function handle_typography ($value, $id, $default, $type, $important)
    {
        
        if (is_array($value)) {
            
            $css_style = '';
            
            foreach ($value as $key => $value) {
                switch ($key) {
                        case 'size':
                            if ($value != "9px") {
                                $css_style .= self::make_style('font-size', $value, $important);
                            }
                            break;

                        case 'face':
                            $css_style .= self::make_style('font-family', $value, $important);
                            break;
                        
                        case 'style':
                            $css_style .= self::make_style('font-weight', $value, $important);
                            break;
                        
                        case 'color':
                            $css_style .= self::make_style('color', $value, $important);
                            break;
                    }    
            }
            // return the new styles.
            return $css_style;

        } else {
            //something strange happened, typography should always return an array.
            return '';
        }
    }

		private static function handle_border ($value, $id, $default, $type, $important) {

			if (is_array($value)) {

				$css_style = "border: ";

				foreach ($value as $key => $value) {
					if ($key == "size") {
						$value = $value . 'px';
						$css_style .= $value . ' ';
					}
					if($key == "style") {
						$css_style .= $value . ' ';
					}
					if($key == "color") {
						$css_style .= $value . ' !important;';
					}
				}
				return $css_style . "\n";

			} else {
				return '';
			}
    }
    
    private static function handle_box_shadow ($value, $id, $default, $type, $important) {
    
    	if (is_array($value)) {
    
    		$shadow_style = "box-shadow: ";
    		$webkit_style = "-webkit-box-shadow: ";
    
    		foreach ($value as $key => $value) {
    			if($key == "color") {
    				$shadow_style .= $value . ' ';
    				$webkit_style .= $value . ' ';
    			}
    			else if($key == "size") {
    				$shadow_style .= "{$value}px {$value}px {$value}px {$value}px ";
    				$webkit_style .= "{$value}px {$value}px {$value}px {$value}px ";
    			}
    		}
    		
    		$shadow_style .= "!important; ";
    		$webkit_style .= "!important; ";
    		
    		return $shadow_style . "\n" . $webkit_style . "\n";

    	} else {
    		return '';
    	}
    }


    //given a syle, and a value, it returns a propertly formated styles
    private static function make_style($style, $value, $important = false)
    {

        if (empty($value) || $value == 'default') {
            return '';
        } else {

					switch ($style) {
						// adding case for gradient? -pek
						// note: this should probably become an OPTION of the background attribute...
						case 'bg_gradient':
							// we got here from bg_gradient, but all we asked the user to specify was a base color
							// we'll build a background gradient here for the style, 30% darker than the user color
							$specified_color = $value;
							$tinted_color = self::tint_hex_color( $specified_color, 70 );
							
							$item = 'background: -webkit-gradient(linear, left top, left bottom, from(' . $specified_color . '), to(' . $tinted_color . '))' . ($important ? ' !important;' : '') . "\n";
							$item .= 'background: -moz-linear-gradient(top, ' . $specified_color . ', ' . $tinted_color . ')' . ($important ? ' !important;' : '') . "\n";
							$item .= "filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='" . $specified_color . "', endColorstr='" . $tinted_color . "')" . ($important ? ' !important;' : '') . "\n";
							
							return $item;
							break;
						case 'radius':
							$item = 'border-radius:'. $value . "px " . ($important ? ' !important;' : '') . "\n";
							$item .= '-moz-border-radius:' . $value . "px " . ($important ? ' !important;' : '') . "\n";
							$item .= '-webkit-border-radius:' . $value . "px" . ($important ? ' !important;' : '') . "\n";
							return $item;
							break;
                
						case 'background-image':
							return 'background-image: url(\'' . $value . "') " . ($important ? ' !important;' : '') . "\n";
							break;

            case 'background':
              return $style . ': ' . $value . ($important ? ' !important;' : '') . "\n" . 'filter: none !important;' . "\n";
						default:
							return $style . ': ' . $value . ($important ? ' !important;' : '') . "\n";
							break;
						}
					}

    }

    // lighten or darken a hex color
    /*
    	pass in the hex string
    	pass in the tint_amount, tinted color will be tint_amount% of hexcolor
    	returns tinted hex color string or #ffffff on failure
    */
    private static function tint_hex_color( $hexcolor = false, $tint_amount = 70 ) {
    	// make sure we have a valid hex color string
    	if( ! self::is_hex_color( $hexcolor ) ) {
    		// return white
    		return "#FFFFFF";
    	}
    	// convert to rgb
    	$rgb_colors = self::hex2rgb( $hexcolor );
    	// tint away
    	$potential_tint = abs( intval( $tint_amount ) );
    	// if we're out of range, default tint to +30%
    	if( $potential_tint < 2 OR $potential_tint > 98 ) {
    		$potential_tint = 70;
    	}
    	$potential_tint = $potential_tint / 100;
    	
    	// probably a quick php array function can step through all these
    	$rgb_colors[0] = $rgb_colors[0] * $potential_tint;
    	$rgb_colors[1] = $rgb_colors[1] * $potential_tint;
    	$rgb_colors[2] = $rgb_colors[2] * $potential_tint;
    	// convert back to hex
    	$tinted_hex = self::rgb2hex( $rgb_colors );
    	if( !$tinted_hex ) {
    		// something went wrong, return white
    		return "#FFFFFF";
    	}
    	// else
    	return $tinted_hex;
    }
    // In order to automatically make gradients, like for background gradients, we need to go to and from hex / rgb colors
    // This is so we can ask the user to specify a base color for the gradient and then build the second gradient color ourselves.
    /*
		Usage:
		$rgb = hex2rgb("#cc0");
		// returns:
		Array ( [0] => red [1] => gree [2] => blue )

	*/
	private static function hex2rgb($hex) {
		$hex = str_replace("#", "", $hex);
		
		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		} else {
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);
		return $rgb; // returns an array with the rgb values
	}
	
	/*
		Usage:
		$rgb = array( 255, 255, 255 );
		$hex = rgb2hex($rgb);
		echo $hex;
		// outputs a hex string full
	*/
	private static function rgb2hex($rgb) {
		$hex = "#";
		$hex .= str_pad(dechex($rgb[0]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[1]), 2, "0", STR_PAD_LEFT);
		$hex .= str_pad(dechex($rgb[2]), 2, "0", STR_PAD_LEFT);
		return $hex;
	}

	// verify a string is valid hex color
	// note that in current implementation this returns a hex color string if it validates, but we don't actually use the return value
	private static function is_hex_color( $color ) {
		// Check for a hex color string
		if(preg_match('/^#[a-f0-9]{6}$/i', $color)) {
			// great, color with # in front
			return $color;
		} else if(preg_match('/^[a-f0-9]{6}$/i', $color)) {
			// okay, color had no # in front so we add it
			return "#" . $color;
			// $fix_color = '#' . $color;
		} else {
			// sorry, that string won't work
			return false;
		}
	}

    // Takes an array with options that have various seelctors
    // and merges all the otpions with the same selector under
    // a new array attribute so it can be easily used to generate
    // css
    private static function sort_by_selector ($options)
    {
        $selector_array = array();

        foreach ($options as $item => $option) {
            //if we don't have a selector, try to generate one
            if ($option['type'] == 'heading' || $option['type'] == 'info') {
                continue;
            }

            if ((is_array($option) && !isset($option['selector'])) || empty($option['selector'])) {

                // user can set selector in front of id
                $selector_id_array = explode('.', $option['id']);
                
                if ( isset($selector_id_array[1])) {
                    $option['selector'] = $selector_id_array[0];    
                } else {
                    $option['selector'] = 'body';    
                }
            } 

            // yank out all the styles that apply to specific selectors into
            // an array that is 'selector'[0] => style, [1] => style
            if (array_key_exists($option['selector'], $selector_array)) {
                $selector_array[$option['selector']][] = $option;
            } else {
                $selector_array[$option['selector']] = array();
                $selector_array[$option['selector']][] = $option;
            }
        }

        return $selector_array;
    }

    private static function is_special_case($option_type)
    {
    	// adding special case for bg-gradient
        $special_id_cases = array('typography', 'background', 'border', 'box_shadow', 'border_shadow', 'bg_gradient');
        if ( in_array($option_type, $special_id_cases) ) {
            return true;
        } 

        return false;
    }
}

// needed for the options framework. 
// TODO: integrate this into the style class
function optionsframework_options() {
    return PLS_Style::$styles;
}
