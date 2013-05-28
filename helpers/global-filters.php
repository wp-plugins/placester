<?php 

PL_Global_Filters::init();
class PL_Global_Filters {
	
	public static function init() {
		add_action('wp_ajax_user_save_global_filters', array(__CLASS__, 'set_global_filters'));
		add_action('wp_ajax_user_remove_all_global_filters', array(__CLASS__, 'remove_all_global_filters'));
		add_action('wp_ajax_filter_options', array(__CLASS__, 'filter_options'));
	}


	//
	// Parses and merged incoming args with the current global filters
	//
	public static function merge_global_filters ($args) {
		// Comes back as an associative array -- false if empty.
		$global_filters = self::get_global_filters();

		//will be "false" if empty
	    if (is_array($global_filters)) {

	  		foreach ($global_filters as $attribute => $value) {

	  			if (strpos($attribute, 'property_type') !== false ) {
	  				//to be honest, not really sure why this is here. 
	  				// I don't believe we currently support "property_type"
	  				$args['property_type'] = self::handle_property_type_filter($value);
	  			} elseif ($attribute === 'zoning_types' || $attribute === 'purchase_types' ) {
	  				// zoning and purchase types come in as an array
	  				// which is fine since that's what the rails app expects
	  				$args[$attribute] = $value;
	  			} elseif ( $attribute === 'location' || $attribute === 'metadata' ) {
	  				// error_log(var_export($value, true));
	  				$args = self::handle_group_filters($args, $attribute, $value);
	  			} else {
	  				//since the rails api doesn't like the strings "true" and "false"
	  				//convert it into a 1 or 0
	  				$args[$attribute] = self::handle_generic_values($value);
	  			}

	  		}
	    }
	    return $args;
	}

	// method that handles all "other" values.
	// designed to handle some oddities in a catch all style.
	private static function handle_generic_values ($value) {
		//we'll still get random arrays in here. Like non_import, etc..
		if ( is_array($value) ) {
			$value = implode('', $value);
		} 
		return self::handle_boolean_values($value);
	}

	private static function handle_property_type_filter ($property_type_value) {
		if (is_array($property_type_value)) {
			$property_type_value = implode('', $property_type_value);
		} 
		return $property_type_value;
	}

	private static function handle_group_filters ($args, $attribute, $value ) {
		//when an array in a location or metadata group has 
		//more then 1 item, then we need to collect all values
		//so they can be sentout as:
		// metadata[$attribute][] = $value[0]
		// metadata[$attribute][] = $value[1]
		// etc..
		if ( is_array($value) && count($value) > 1 ) {
			$args[$attribute] = $value;	
		} else {
			// if there's only a single value for an attribute, then we need to 
			// prepend it as a non-array value. The easiest way to do this is to
			// iterate through.
			foreach ($value as $attribute_key => $attribute_value_as_array) {
				$args[$attribute][$attribute_key] = implode('',$attribute_value_as_array);
			}
		}
		return $args;
	}

	/* Updates boolean values so they are properly respected by Rails */
	private static function handle_boolean_values ($value) {
		$val = $value;
		if ($value === 'true') {
			$val = 1;
		} elseif($value === false ) {
			$val = 0;
		}
		return $val;
	}



	public static function display_global_filters () {
		$filters = self::get_global_filters();
		// pls_dump($filters);
		$html = '';
		if (!empty($filters)) {
			foreach ($filters as $key => $filter) {
				if (is_array($filter)) {
					foreach ($filter as $subkey => $item) {
						if (!is_array($item)) {
							if ($item == 'in') { continue; }
							$label = is_int($subkey) ? $key : $key . '-' . $subkey;
							$value = $item;
							$name = $key . '['.$subkey.']=';
							ob_start();
							?>
								<span id="active_filter_item">
									<a href="#"  id="remove_filter"></a>
									<span class="global_dark_label"><?php echo $label ?></span> : <?php echo $value ?>
									<input test="true" type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>">	
								</span>
							<?php
							$html .= ob_get_clean();
						} else {
							foreach ($item as $k => $value) {
								if ($value == 'in') { continue; }
								$label = is_int($subkey) ? $key : $key . '-' . $subkey;
								$value = $value;
								$name = $key . '['.$subkey.'][]=';
								ob_start();
								?>
									<span id="active_filter_item">
										<a href="#"  id="remove_filter"></a>
										<span class="global_dark_label"><?php echo $label ?></span> : <?php echo $value ?>
										<input type="hidden" name="<?php echo $name ?>" value="<?php echo $value ?>">	
									</span>
								<?php
								$html .= ob_get_clean();
							}
						}
					}
				}
			}
		}
		echo $html;
	}

	public static function filter_options () {
		$option_name = 'pl_my_listings_filters';
		$options = get_option($option_name);
		if (isset($_POST['filter']) && isset($_POST['value']) && $options) {
			$options[$_POST['filter']] = $_POST['value'];
			update_option($option_name, $options);
		} 
		elseif (isset($_POST['filter']) && isset($_POST['value']) && !$options) {
			$options = array($_POST['filter'] => $_POST['value']);
			add_option($option_name, $options);
		}
		echo json_encode($options);
		die();
	}

	public static function get_listing_attributes() {
		$options = array();

		$attributes = PL_Config::PL_API_LISTINGS('get', 'args');

		$form_types = PL_Config::PL_API_CUST_ATTR('get');
		$form_types = $form_types['args']['attr_type']['options'];

		if (isset($attributes['custom']) && is_array($attributes['custom'])) {
			$custom_attributes = call_user_func( array($attributes['custom']['bound']['class'], $attributes['custom']['bound']['method'] ) );
							
			foreach ($custom_attributes as $key => $option) {
				$attributes[$option['cat']][] = array('label' => $option['name'], 'type' => $form_types[$option['attr_type']] );
			} 

			unset($attributes['custom']);
			// pls_dump('custom_attributes',$response);
		}
		// pls_dump($attributes);
		foreach ($attributes as $key => $attribute) {
			if ( isset($attribute['label']) ) {
				$options['basic'][$key] = $attribute['label'];
			} else {
				foreach ($attribute as $k => $v) {
					if (isset( $v['label'])) {
						if (is_int($k)) {
							$options[$key][self::generate_global_filter_key_from_value($v['label'])] = $v['label'];
						} else {
							$options[$key][$k] = $v['label'];
						}
						
					}
				}
			}
		}
		// pls_dump($attributes);
		// pls_dump($options);
		$option_html = '';
		foreach ($options as $group => $value) {
			ob_start();
			?>
				<optgroup label="<?php echo ucwords($group) ?>">
					<?php foreach ($value as $value => $label): ?>
						<option value="<?php echo $value ?>"><?php echo $label ?></option>
					<?php endforeach ?>
				</optgroup>
			<?php
			$option_html .= ob_get_clean();
		}

		$option_html = '<select id="selected_global_filter">' . $option_html . '</select>';
		echo $option_html;
	}

	/*
	 * Functionality for Global Filters
	 */

	public static function remove_all_global_filters() {
		$response = PL_Option_Helper::set_global_filters(array('filters' => array()));
		if ($response) {
			echo json_encode(array('result' => true, 'message' => 'You successfully removed all global search filters'));
		} else {
			echo json_encode(array('result' => false, 'message' => 'Change not saved or no change detected. Please try again.'));
		}
		die();
	}

	public static function get_global_filters() {
		$response = PL_Option_Helper::get_global_filters();
		return $response;
	}

	public static function set_global_filters ($args = array()) {
		if (empty($args) ) {
			unset($_POST['action']);
			$args = $_POST;
		}
		
		$global_search_filters = PL_Validate::request($args, PL_Config::PL_API_LISTINGS('get', 'args'));
		foreach ($global_search_filters as $key => $filter) {
			foreach ($filter as $subkey => $subfilter) {
				if (!is_array($subfilter) && (count($filter) > 1) ) {
					$global_search_filters[$key . '_match'] = 'in';
				} 
				elseif (count($subfilter) > 1) {
					$global_search_filters[$key][$subkey . '_match'] = 'in';
				}
			}
		}
		$response = PL_Option_Helper::set_global_filters(array('filters' => $global_search_filters));
		if ($response) {
			echo json_encode(array('result' => true, 'message' => 'You successfully updated the global search filters'));
		} else {
			echo json_encode(array('result' => false, 'message' => 'Change not saved or no change detected. Please try again.'));
		}
		echo json_encode(self::report_filters());
		die();
	}

	private static function report_filters () {
		$response = PL_WordPress::set(array_merge(self::get_global_filters(), array('url' => site_url())));
		return $response;
	}

	private static function generate_global_filter_key_from_value ($value) {
		$value = str_replace(' ', '_', $value);
		$value = str_replace('.', '', $value);
		$value = str_replace('-', '', $value);
		$value = strtolower($value);
		return $value;
	}
}
?>
