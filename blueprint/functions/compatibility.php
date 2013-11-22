<?php
/**
 * This class acts as a buffer between the theme and the plugin. All calls to 
 * functions from the plugin must be done using this class. If new functions 
 * that need to be available to theme developers are added to the plugin, they 
 * must be added to this class.
 *
 * @package PlacesterBlueprint
 * @subpackage Functions
 * @since 0.0.1
 */
class PLS_Plugin_API {

    static public $default_company_details = array(
        'description' => '',
        'logo_url' => '',
        'name' => '',
        'phone' => '',
        'email' => '',
        'location' => array(
            'address' => '',
            'unit' => '',
            'locality' => '',
            'region' => '',
            'postal' => '',
            'neighborhood' => '',
            'country' => '',
            'latitude' => '',
            'longitude' => ''
        )
    );

    /**
     * Verify if calling a plugin function throws any exceptions. If it throws 
     * a timeout exception, set the theme global error flag.
     * 
     * @static
     * @access private
     * @return mixed The result of the execution of the function if the plugin 
     * didn't throw any exceptions, false otherwise.
     * @since 0.0.1
     */
    private static function _try_for_exceptions () {
        // Don't proceed if there's an issue with the plugin...
        if (pls_has_plugin_error()) {
            return false;
        }
            
        $parameters = func_get_args();
        $function_name = array_shift($parameters);

        try {
            // Call the function with its parameters...
            $return = call_user_func_array($function_name, $parameters);
        } 
        catch (Exception $e) {
            // Assumes an exception with a private message is a timeout
            if ( !isset($e->message) ) {
                pls_has_plugin_error('timeout');
            }
            $return = false;
        }
        
        return $return;
    }

    private static function try_call_func ($callback, $params = array(), $if_fail = null) {
        // Call the function and test for any exceptions...
        $return = self::_try_for_exceptions($callback, $params);
        
        // If invalid, set to value specified by 'if_fail'
        if (!$return) {
            $return = $if_fail;
        }
        
        return $return;
    }

    /*
     * Taxonomies
     */

    public static function get_polygon_listings ($params = array()) {
        return self::try_call_func( array("PL_Taxonomy_Helper","get_listings_polygon_name"), $params, array("listings" => array()) );
    }

    public static function get_polygon_links ($params = array()) {
        return self::try_call_func( array("PL_Taxonomy_Helper","get_polygon_links"), $params, array() );
    }

    public static function get_taxonomies_by_type ($params = array()) {
        return self::try_call_func( array("PL_Taxonomy_Helper","get_polygons_by_type"), $params, array() );
    }

    public static function get_taxonomies_by_slug ($params = array()) {
        return self::try_call_func( array("PL_Taxonomy_Helper","get_polygons_by_slug"), $params, array() );
    }

    public static function get_polygon_detail ($params = array()) {
        return self::try_call_func( array("PL_Taxonomy_Helper","get_polygon_detail"), $params, array() );
    }

    /*
     * Leads (Membership + People) Funcs
     */

    public static function placester_lead_control_panel ($args) {
        return self::try_call_func( array("PL_Membership", "placester_lead_control_panel"), $args, false );
    }

    public static function placester_favorite_link_toggle ($args) {
        return self::try_call_func( array("PL_People_Helper", "placester_favorite_link_toggle"), $args, false );
    }

    public static function get_person_details () {
        return self::try_call_func( array("PL_People_Helper", "person_details"), array(), array() );
    }

    public static function update_person_details ($person_details) {
        return self::try_call_func( array("PL_People_Helper", "update_person_details"), $person_details, false );
    }

    public static function create_person ($person_details) {
        return self::try_call_func( array("PL_People_Helper", "add_person"), $person_details, false );
    }

    public static function get_listings_fav_ids () {
        return self::try_call_func( array("PL_People_Helper", "get_favorite_ids"), array(), false );
    }
    
    public static function merge_bcc_forwarding_addresses_for_sending ($headers) {
        return self::try_call_func( array("PL_Lead_Capture_Helper", "merge_bcc_forwarding_addresses_for_sending"), $headers);   
    }

    /*
     * Saved Search Funcs
     */
    
    public static function save_search ($search_id, $search_filters) {
        return self::_try_for_exceptions( array("PL_Saved_Search", "save_search"), $search_id, $search_filters );
    }

    public static function get_user_saved_searches () {
        return self::try_call_func( array("PL_Saved_Search", "get_user_saved_searches"), false, array() );
    }

    public static function get_saved_search_registration_form () {
        return self::try_call_func( array("PL_Saved_Search", "get_saved_search_registration_form") );   
    }

    public static function get_saved_search_button () {
        return self::try_call_func( array("PL_Saved_Search", "get_saved_search_button") ); 
    }

    public static function get_saved_search_filters ($search_id) {
        return self::try_call_func( array("PL_Saved_Search", "get_saved_search_filters"), $search_id, false );
    }

    public static function translate_key ($key) {
        return self::try_call_func( array("PL_Saved_Search", "translate_key"), $key, $key );
    }
    
    /*
     * Miscellaneous
     */

    public static function get_api_key () {
        $api_key = null;

        if (function_exists('placester_get_api_key')) {
            $api_key = placester_get_api_key();
            
            if (!$api_key) { 
                $api_key = false; 
            }
        }

        return $api_key;
    }

    public static function get_schools ($params = array()) {
        return self::try_call_func( array("PL_Education_Helper","get_schools"), $params, array() );
    }

    public static function get_walkscore ($params = array()) {
        return self::try_call_func( array("PL_Walkscore","get_score"), $params, array() );
    }

    public static function get_translations () {
        return self::try_call_func( array("PL_Custom_Attribute_Helper", "get_translations"), array(), array() );
    }

    public static function create_page ($page_list) {
        return self::try_call_func( array("PL_Pages", "create_once"), $page_list, false );
    }    

    public static function get_user_details () {
        return self::try_call_func( array("PL_Helper_User", "whoami"), array(), false );
    }

    /**
     * Return public-safe info for company
     * @return array Public company info. Any fields not populated by user will be an empty string.
     */
    public static function get_company_details () {
        $details = self::get_user_details();
        $company_info = self::$default_company_details;

        if (is_array($details)) {
            $r = array_replace_recursive($company_info, $details);
            $company_info = array_intersect_key($r, $company_info);
        }

        return $company_info;
    }

    public static function mls_message ($context) {
        return self::try_call_func( array("PL_Compliance", "mls_message"), $context, false );
    }

    public static function log_snippet_js ($event, $attributes) {
        return self::_try_for_exceptions( array("PL_Analytics", "log_snippet_js"), $event, $attributes );
    }

    /*
     * Listings
     */

    public static function get_listing_in_loop () {
        return self::try_call_func( array("PL_Listing_Helper", "get_listing_in_loop") );
    }

    public static function get_listing_aggregates ($keys) {
        return self::try_call_func( array("PL_Listing_Helper", "basic_aggregates"), $keys, array() );
    }
    
    public static function get_property_url ($id = false) {
        // Make sure $id is set...
        if (!$id) { return false; }

        $cache = new PLS_Cache("Property URL");
        if ($url = $cache->get($id)) {
            return $url;
        }

        // Test the function for any exceptions
        $return = self::_try_for_exceptions( array("PL_Page_Helper", "get_url"), $id );

        // If no exceptions were detected, return the result
        if ($return) {
            $cache->save($return, PLS_Cache::TTL_LOW);
            return $return;
        }

        if (pls_has_plugin_error()) {
            $page = get_page_by_title('Sample Listing', 'ARRAY_A');
            if ($page && isset($page['guid'])) {
                return $page['guid'];        
            }
        }

        return false;
    }

    /**
     * Returns a list of property_types valid for current site for use in search dropdown.
     * 
     * @static
     * @return array The property_type(s) used on current site
     */
    public static function get_type_list () {
        return self::try_call_func( array("PL_Listing_Helper","types_for_options"), array(), false );
    }
    
    /**
     * Gets an object containing the list of cities, zip codes and states of 
     * the available properties.
     *
     * The object looks like this: 
     * <code>
     * object(stdClass)#59 (3) {
     *   ["city"]=> array(8) { [0]=> string(7) "City 1", ... }
     *   ["zip"]=> array(8) { [0]=> string(7) "Zip Code 1", ... }
     *   ["state"]=> array(6) { [0]=> string(2) "State Code 1", ... }
     * }
     * </code>
     * 
     * @return mixed The object containing the data if the plugin is active and 
     * has a API key, FALSE otherwise.
     * @since 0.0.1
     */
    public static function get_location_list ($return_only = false) {
        return self::try_call_func( array("PL_Listing_Helper","locations_for_options"), $return_only, false );
    }

    public static function get_location_list_polygons ($return_only = false) {
        return self::try_call_func( array("PL_Listing_Helper","polygon_locations"), $return_only, array() );
    }

    /**
     * Prints a standalone list of properties.
     *
     * @return list of property details
     * @since 0.0.1
     */
    public static function get_listings ($args, $global_filters = true, $caching_on = false) {
        return self::_try_for_exceptions(array("PL_Listing_Helper", "results"), $args, $global_filters);
    }

    public static function get_listing_details ($args) {
        return self::_try_for_exceptions(array("PL_Listing_Helper", "details"), $args);
    }

    /**
     * Processes a list of arguments and selects only the valid ones that can 
     * be used to make a request to the API.
     * 
     * @static
     * @param array $args The argument array.
     * @uses PLS_Plugin_API::get_property_list_fields();
     * @since 0.0.1
     */
    public static function get_valid_property_list_fields (&$args) {

        /** Get the list of arguments accepted by the api function. */
        $api_valid_args = self::get_property_list_fields();

        /** Process arguments that need to be sent to the API. */
        $request_params = array();
        foreach( $args as $key => $value ) {
            /** If the argument is meant for the API request. */
            if ( array_key_exists( $key, $api_valid_args ) ) {

                /** The field valid type. */
                $api_valid_args_type = $api_valid_args[$key];

                /** Verify if the argument value is valid. */
                $has_valid_value = empty( $api_valid_args_type ) ||
                    ( 
                        is_array( $api_valid_args_type ) && 
                        array_key_exists( $value, $api_valid_args_type )
                    ) ||
                    ( 
                        is_string( $api_valid_args_type ) && 
                        function_exists( "is_{$api_valid_args_type}" ) && 
                        call_user_func( "is_{$api_valid_args_type}", $value )
                    );

                /** If it's valid, add the argument to the request parameters. */ 
                if ( $has_valid_value ) {
                    $request_params[$key] = $value;
                    unset( $args[$key] );
                }

            }
        }

        return $request_params;
    }

    /**
     * The value of the array contains the allowed type of the argument, the 
     * subset of allowed values if it's an array, or anything if empty.
     * 
     * @static
     * @return array The allowed arguments array>
     */
    public static function get_property_list_fields ($field = '') {

        $return = array(
            'only_verified' => '',
            'include_disabled' => '',
            'property_ids' => 'array',
            'property_type' => array(
                'apartment' => true,
                'penthouse' => true,
                'townhouse' => true,
                'brownstone' => true,
                'family_home' => true,
                'multi_fam_home' => true,
                'flat' => true,
                'loft' => true,
                'cottage' => true,
                'villa' => true,
                'mansion' => true,
                'ranch' => true,
                'island' => true,
                'log_cabin' => true,
                'tent' => true,
            ) ,
            'listing_types' => 'array', 
            'zoning_types' => 'array', 
            'purchase_types' => 'array', 
            'bedrooms' => 'numeric', 
            'bathrooms' => 'numeric', 
            'half_baths' => 'numeric', 
            'min_price' => 'float', 
            'max_price' => 'float', 
            'price' => 'float', 
            'available_on' => '', 
            'location[zip]' => 'string', 
            'location[state]' => 'string', 
            'location[city]' => 'string', 
            /** Country not supported by the API. */
            'box[min_latitude]' => 'numeric',
            'box[max_latitude]' => 'numeric',
            'box[min_longitude]' => 'numeric',
            'box[max_longitude]' => 'numeric',
            'address_mode' => array( 'polygon' => true, 'exact' => true ),
            'limit' => 'numeric',
            'skip' => 'numeric',
            'is_featured' => '',
            'is_new' => '',
            /** The commented ones are not supported by the list of listings. */
            'sort_by' => array( 
                'price' => 'Price',
                // 'sqft' => 'Square Feet',
                // 'description' => 'Description', 
                // 'bedrooms' => 'Bedroom',
                // 'half_baths' => 'Half Baths',
                // 'available_on' => 'Available On',
                'location.address' => 'Address',
                'location.city' => 'City',
                'location.state' => 'State',
                'location.zip' => 'Zip',
                // 'location.neighborhood' => 'Neighborhood',
                // 'location.country' => 'Country',
            ),
            'sort_type' => array( 'asc' => true, 'desc' => true )
        );

        if ( ! empty( $field ) && array_key_exists( $field, $return ) )
            return $return[$field];

        return $return;
    }

    public static function get_type_values ($type) {
        // Define the supported types
        $supported_types = array( 
            'property' => array(
                'apartment' => 'Apartment',
                'penthouse' => 'Penthouse',
                'townhouse' => 'Townhouse',
                'brownstone' => 'Brownstone',
                'Single Family Home' => 'Single Family Home',
                'Multi Family Home' => 'Multi Family Home',
                'flat' => 'Flat',
                'loft' => 'Loft',
                'cottage' => 'Cottage',
                'mansion' => 'Mansion',
                'ranch' => 'Ranch',
                'duplex' => 'Duplex',
                'condo' => 'Condominium'
            ), 
            'listing' => array(
                // 'storage' => 'Storage',
                'housing' => 'Housing',
                'parking' => 'Parking',
                'sublet' => 'Sublet',
                'vacation' => 'Vacation',
                'land' => 'Land',
                // 'other' => 'Other',
            ), 
            'zoning' => array(
                'residential' => 'Residential',
                'commercial' => 'Commercial',
            ), 
            'purchase' => array(
                'rental' => 'Rent',
                'sale' => 'Buy',
            )
        );

        // If not a valid type, return empty handed
        if ( empty($type) || !array_key_exists($type, $supported_types) ) { return; }

        return $supported_types[$type];
    }

    public static function resize_image($image_args) {
    	return self::try_call_func(array("PL_Dragonfly", "resize"), $image_args, $image_args['old_image']);
    }
}
// end of class
?>