<?php

PLS_Taxonomy::init();
class PLS_Taxonomy {

	static $custom_meta = array();
	static $tax_loc_map = array('state'=>'region', 'zip'=>'postal', 'city'=>'locality', 'neighborhood'=>'neighborhood', 'street'=>'address');

	public static function init () {
		add_action('init', array(__CLASS__, 'metadata_customizations'));
	}

	/**
	 * Get array of location items of specified locality type
	 */
	public static function get_available_locations($type = 'neighborhood') {
		$locations = array();
		$api_locations = PLS_Plugin_API::get_location_list();
		if (!empty($api_locations[$type])) {
			$locations = $api_locations[$type];
			sort($locations);
		}
		return $locations;
	}

	/**
	 * Get array of location items of specified locality type, optionally using values from an option
	 * If option is empty then create a list using any available locations of the type.
	 * At minimum returns name and url for each item, set $get_meta to get all available data
	 */
	public static function get_featured_locations ($type = 'neighborhood', $limit = 10, $option = 'pls-featured-neighborhood', $get_meta = false) {
		$locations = array();
		$terms = array();
		$featured_locations = pls_get_option($option);

		if (empty($featured_locations)) {
			// Ensure the 'type' passed is a supported taxonomy...
			if (isset(self::$tax_loc_map[$type])) {
				$loc_type = self::$tax_loc_map[$type];
			}

			$locations = PLS_Plugin_API::get_location_list($loc_type);

			// Sanity check...
			if (is_null($locations) || !is_array($locations)) { $locations = array(); }
    		
    		// Check for erroneous first element...
			if (isset($locations['false'])) { unset($locations['false']); }
    		
    		$locations = array_slice($locations, 0, $limit);
    		// sort($locations);
		}
		else {
			// Get from theme option if it is set...
			$locations = explode(',', $featured_locations);
		}

		foreach ($locations as $location) {
			if (!empty($location)) {
				$location = trim($location);
				
				// Fetch actual term or virtual term...
				$term = self::get_location_by('name', $location, $type);

				// edge cases where, e.g. a user-specified value no longer exists
				if (empty($term)) {
					continue;
				}

				// TODO: Does this actually make sense here?  (NOT called by default...)
				if ($get_meta) {
					$term = self::_add_meta($term);
				}

				if (is_array($term)) {
				 	$terms[$location] = (object)$term;
				}
			}
		}
		
		return $terms;
	}

	/**
	 * Get basic location item using its name or slug (as 'field' param) and taxonomy name
	 * At minimum returns name and url for item, set $get_meta if additional data
	 */
	public static function get_location_by ($field, $value, $taxonomy, $get_meta = false, $get_count = false) {
		// Fetch term...
		$term = PLS_Plugin_API::get_term(array('field'=>$field, 'value'=>$value, 'taxonomy'=>$taxonomy));
		
		if ($term) {
			$term = (array)$term;
			$url_templates = PLS_Plugin_API::get_permalink_templates();
			$term['url'] = str_replace("%{$taxonomy}%", $term['slug'], $url_templates[$taxonomy]);
			if ($get_meta) {
				$term = self::_add_meta($term, $get_count);
			}
		}

		return $term;
	}

	/**
	 * Merge meta data into the item
	 */
	private static function _add_meta($item, $get_count = false) {
		$meta = array();
		if ( $item['term_id'] > 0) {
			$m = get_option('tax_meta_'.$item['term_id']);
			if (is_array($m)) {
				$meta = $m;
			}
		}
		foreach(self::$custom_meta as $m) {
			if (!isset($meta[$m['id']])) {
				$meta[$m['id']] = false;
			}
		}
		$item += $meta;

		if ($get_count) {
			$type = self::$tax_loc_map[$item['taxonomy']];
			$result = PLS_Plugin_API::get_locations_counts(array('locations'=>array($item['name']), 'type'=>$type));
			$item['count'] = empty($result[$item['name']]) ? 0 : $result[$item['name']];
		}

		return $item;
	}

	/**
	 * Get complete location object based on value passed in for taxonomy key
	 */
	public static function get ($args = array()) {
		$cache = new PLS_Cache('nbh');
		if ($result = $cache->get($args)) {
			return $result;
		}

		extract(self::process_args($args), EXTR_SKIP);
		$subject = array('get_polygon'=>true);
		if ($street) {
			$subject += array('taxonomy' => 'street', 'value' => $street);
		} elseif ($neighborhood) {
			$subject += array('taxonomy' => 'neighborhood', 'value' => $neighborhood);
		} elseif ($zip) {
			$subject += array('taxonomy' => 'zip', 'value' => $zip);
		} elseif ($city) {
			$subject += array('taxonomy' => 'city', 'value' => $city);
		} elseif ($state) {
			$subject += array('taxonomy' => 'state', 'value' => $state);
		}
		$subject['field'] = 'slug';
		$term = PLS_Plugin_API::get_term($subject);
		if (empty($term)) {
			return false;
		}
		$term = (array)$term;
		if ($term['term_id']>0) {
			$meta = get_option('tax_meta_'.$term['term_id']);
			if (is_array($meta)) {
				$term += $meta;
			}
		}
		foreach(self::$custom_meta as $meta) {
			if (!isset($term[$meta['id']])) {
				$term[$meta['id']] = false;
			}
		}

		if (!empty($term['polygon'])) {
			$polygon = $term['polygon'];
			$polygon['neighborhood_polygons'] = $polygon['name'];
			$listings_raw = PLS_Plugin_API::get_polygon_listings($polygon);
			$term['listings'] = PLS_Partials::get_listings( "limit=5&context=home&neighborhood_polygons=" . $polygon['name'] );
		} else {
			$listings_raw = PLS_Plugin_API::get_listings("location[" . $term['api_field'] . "]=" . $term['api_term']);
			$term['listings'] = PLS_Partials::get_listings( "limit=5&context=home&request_params=location[" . $term['api_field'] . "]=" . $term['api_term'] );
		}

		$term['areas'] = array('locality' => array(), 'postal' => array(), 'neighborhood' => array(), 'address' => array());
		$locality_tree = array('city' => array('postal', 'neighborhood'), 'zip' => array('neighborhood'));

		$term['listings_raw'] = $listings_raw['listings'];

		//assemble all the photos
		$api_translations = array('locality' => 'city', 'neighborhood' => 'neighborhood', 'postal' => 'zip');
		$term['listing_photos'] = array();
		$count = 0;
		if (isset($listings_raw['listings'])) {
			foreach ($listings_raw['listings'] as $key => $listing) {
				if (!empty($listing['images'])) {
					foreach ($listing['images'] as $image) {
						if ($count > $image_limit) {
							break;
						}
						$term['listing_photos'][] = array('full_address' => $listing['location']['full_address'], 'image_url' => $image['url'], 'listing_url' => $listing['cur_data']['url']);
						$count++;
					}
				}
				// TODO: Unused?
				$link_templates = PLS_Plugin_API::get_permalink_templates();
				if (isset($locality_tree[$subject['taxonomy']])) {
					foreach ($locality_tree[$subject['taxonomy']] as $locality) {
						$tax = $api_translations[$locality];
						$permalink = empty($link_templates[$tax]) ? '' : str_replace("%$tax%", sanitize_title_with_dashes($listing['location'][$locality]), $link_templates[$tax]);
						$link = array('name' => $listing['location'][$locality], 'permalink' => $permalink);
						if (is_string($link['permalink'])) {
							$term['areas'][$locality][] = $link;
						}
					}
				}
			}
		}
		$cache->save($term);
		return $term;
	}

	// TODO: Unused?
	public static function get_links ($location) {
		$response = array();
		$neighborhoods = array('state' => false, 'city' => false, 'neighborhood' => false, 'zip' => false, 'street' => false);
		$api_translations = array('state' => 'region', 'city' => 'locality', 'neighborhood' => 'neighborhood', 'zip' => 'postal', 'street' => 'address');
		global $query_string;
		$args = wp_parse_args($query_string, $neighborhoods);
		$link_templates = PLS_Plugin_API::get_permalink_templates();
		foreach ($neighborhoods as $neighborhood => $value) {
			if (isset($args[$neighborhood]) && isset($location[$api_translations[$neighborhood]]) && isset($link_templates[$neighborhood])) {
				$term_link = str_replace("%$neighborhood%", sanitize_title($location[$api_translations[$neighborhood]]), $link_templates[$neighborhood]);
				$response[ $location[$api_translations[$neighborhood]] ] = $term_link;
			}
		}
		return $response;
	}

	public static function add_meta ($type, $id, $label) {
		if (in_array($type, array('text', 'textarea', 'checkbox', 'image', 'file', 'wysiwyg'))) {
			self::$custom_meta[] = array('type' => $type, 'id' => $id, 'label' => $label);
		} else {
			return false;
		}
	}

	public static function metadata_customizations () {
        include_once(PLS_Route::locate_blueprint_option('meta.php'));

		//throws random errors if you aren't an admin, can't be loaded with admin_init...
        if (!is_admin() || !class_exists('Tax_Meta_Class')) {
        	return;
        }

		$config = array('id' => 'demo_meta_box', 'title' => 'Demo Meta Box', 'pages' => array('state', 'city', 'zip', 'street', 'neighborhood'), 'context' => 'normal', 'fields' => array(), 'local_images' => false, 'use_with_theme' => false );
		$my_meta = new Tax_Meta_Class($config);
		foreach (self::$custom_meta as $meta) {
			switch ($meta['type']) {
				case 'text':
					$my_meta->addText($meta['id'],array('name'=> $meta['label']));
					break;
				case 'textarea':
					$my_meta->addTextarea($meta['id'],array('name'=> $meta['label']));
					break;
				case 'wysiwyg':
					$my_meta->addCheckbox($meta['id'],array('name'=> $meta['label']));
					break;
				case 'image':
					$my_meta->addImage($meta['id'],array('name'=> $meta['label']));
					break;
				case 'file':
					$my_meta->addFile($meta['id'],array('name'=> $meta['label']));
					break;
				case 'checkbox':
					$my_meta->addCheckbox($meta['id'],array('name'=> $meta['label']));
					break;
			}
		}
		$my_meta->Finish();
	}

	public static function process_args ($args) {
		$defaults = array(

        );
        $args = wp_parse_args( $args, $defaults );
        return $args;
	}

//end of class
}