<?php 

class PLS_Partial_Get_Listings {
    /**
     * Returns a list of properties listed formated in a default html.
     *
     * This function takes the raw properties data returned by the plugin and
     * formats wrapps it in html. The returned html is filterable in multiple
     * ways.
     *
     * The defaults are as follows:
     *     'width' - Default 100. The listing image width. If set to 0,
     *          width is not added.
     *     'height' - Default false. The listing image height. If set to 0,
     *          width is not added.
     *     'placeholder_img' - Defaults to placeholder image. The path to the
     *          listing image that should be use if the listing has no images.
     *     'context' - An execution context for the function. Used when the
     *          filters are created.
     *     'context_var' - Any variable that needs to be passed to the filters
     *          when function is executed.
     *     'limit' - Default is 5. Total number of listings to retrieve. Maximum
     *          set to 50.
     * Defines the following filters:
     * pls_listings_request[_context] - Filters the request parameters.
     * pls_listing[_context] - Filters the individual listing html.
     * pls_listings[_context] - Filters the complete listings list html.
     *
     * @static
     * @param array|string $args Optional. Overrides defaults.
     * @return string The html with the list of properties.
     * @since 0.0.1
     */
    function init ($args = '') {
       
        /** Define the default argument array. */
        $defaults = array(
            'width' => 100,
            'height' => 0,
            'placeholder_img' => PLS_IMG_URL . "/null/listing-300x180.jpg",
            'context' => '',
            'context_var' => false,
            'featured_option_id' => false,
            /** Placester API arguments. */
            'limit' => 5,
            'sort_type' => 'asc',
        );

        /** Merge the arguments with the defaults. */
        $args = wp_parse_args( $args, $defaults );

        /** Process arguments that need to be sent to the API. */
        $request_params = PLS_Plugin_API::get_valid_property_list_fields( $args );

        /** Extract the arguments after they merged with the defaults. */
        extract( $args, EXTR_SKIP );
        
        /** Sanitize the width. */
        if ( $width ) 
            $width = absint( $width );
            
        /** Sanitize the height. */
        if ( $height ) 
            $height = absint( $height );

        /** Filter the request parameters. */
        $request_params = apply_filters( pls_get_merged_strings( array( 'pls_listings_request', $context ), '_', 'pre', false ), $request_params, $context_var );

        /** Display a placeholder if the plugin is not active or there is no API key. */
        if ( pls_has_plugin_error() && current_user_can( 'administrator' ) ) {
            global $PLS_API_DEFAULT_LISTING;
            $listings_raw = $PLS_API_DEFAULT_LISTING;
        } elseif (pls_has_plugin_error()) {
            global $PLS_API_DEFAULT_LISTING;
            $listings_raw = $PLS_API_DEFAULT_LISTING;
        } else {
            /** Request the list of properties. */
            if ($featured_option_id) {
                $listings_raw = PLS_Listing_Helper::get_featured($featured_option_id);
            }

            if (!$featured_option_id || empty($listings_raw['listings'])) {
                $listings_raw = PLS_Plugin_API::get_property_list($request_params);    
            }
        }
        
        // pls_dump($listings_raw);

        /** Define variable which will contain the html string with the listings. */
        $return = '';

        /** Set the listing image attributes. */
        $listing_img_attr = array();
        if ( $width )
            $listing_img_attr['width'] = $width;
        if ( $height )
            $listing_img_attr['height'] = $height;

        /** Collect the html for each listing. */
        $listings_html = array();
        foreach ( $listings_raw['listings'] as $listing_data ) {
            // pls_dump($listing_data);
            /**
             * Curate the listing_data.
             */

            /** Overwrite the placester url with the local url. */
            // $listing_data->url = PLS_Plugin_API::get_property_url( $listing_data->id );

            /** Use the placeholder image if the property has no photo. */
            if ( !$listing_data['images'] ) {
                $listing_data['images'][0]['url'] = $placeholder_img;
                $listing_data['images'][0]['order'] = 0;
            }

            /** Remove the ID for each image (not needed by theme developers) and add the image html. */
            foreach ( $listing_data['images'] as $image ) {
                unset( $image['id'] );
                $image['html'] = pls_h_img( $image['url'], $listing_data['location']['address'], $listing_img_attr );
            }
                $location = $listing_data['location'];
                $full_address = $location['address'] . ' ' . $location['region'] . ', ' . $location['locality'] . ' ' . $location['postal'];
             ob_start();
             ?>

        <div class="listing-item grid_8 alpha" id="post-<?php the_ID(); ?>">
            <header class="grid_8 alpha">
                <p><a href="<?php echo PLS_Plugin_API::get_property_url($listing_data['id']); ?>" rel="bookmark" title="<?php echo $full_address ?>"><?php echo $full_address ?></a></p>
            </header>
            <div class="listing-item-content grid_8 alpha">
                <div class="grid_8 alpha">
                    <!-- If we have a picture, show it -->
                    <?php if (is_array($listing_data['images'])): ?>
                        <div class="listing-thumbnail">
                            <div class="outline">
                                <a href="<?php echo @$listing_data['cur_data']['url']; ?>"><?php echo PLS_Image::load($listing_data['images'][0]['url'], array('resize' => array('w' => 250, 'h' => 150), 'fancybox' => true, 'as_html' => true, 'html' => array('alt' => $full_address))); ?></a>
                            </div>
                        </div>
                    <?php endif ?>
						<?php if (!empty($listing_data['cur_data']['beds'])) { ?>
							<p><span>Beds:</span> <?php echo @$listing_data['cur_data']['beds']; ?></p>
						<?php } ?>

						<?php if (!empty($listing_data['cur_data']['baths'])) { ?>
							<p><span>Baths:</span> <?php echo @$listing_data['cur_data']['baths']; ?></p>
						<?php } ?>

						<?php if (!empty($listing_data['cur_data']['half_baths'])) { ?>
							<p><span>Half Baths:</span> <?php echo @$listing_data['cur_data']['half_baths']; ?></p>
						<?php } ?>

						<?php if (!empty($listing_data['cur_data']['price'])) { ?>
							<p><span>Price:</span> <?php echo @$listing_data['cur_data']['price']; ?></p>
						<?php } ?>

						<?php if (!empty($listing_data['cur_data']['avail_on'])) { ?>
							<p><span>Available On:</span> <?php echo @$listing_data['cur_data']['avail_on']; ?></p>
						<?php } ?>

          	<?php if (!empty($listing_data['rets']['mls_id'])) { ?>
          		<p class="mls"><span>MLS #:</span> <?php echo @$listing_data['rets']['mls_id'] ?></p>
          	<?php } ?>

						<?php if (!empty($listing_data['cur_data']['desc'])): ?>
							<p class="listing-description" class="grid_8 omega">
								<?php echo substr($listing_data['cur_data']['desc'], 0, 300); ?>
							</p>
						<?php endif; ?>
                    <div class="actions">
                        <a class="more-link" href="<?php echo PLS_Plugin_API::get_property_url($listing_data['id']); ?>">View Property Details</a>
												<?php echo PLS_Plugin_API::placester_favorite_link_toggle(array('property_id' => $listing_data['id'])); ?>
                    </div>
                </div>
            </div><!-- .entry-summary -->
        </div>


             <?php
             $listing_html = ob_get_clean();

            /** Filter (pls_listing[_context]) the resulting html for a single listing. */
            $listing_html = apply_filters( pls_get_merged_strings( array( 'pls_listing', $context ), '_', 'pre', false ), $listing_html, $listing_data, $request_params, $context_var );

            /** Append the html to an array. This will be passed to the final filter. */
            $listings_html[] = $listing_html;

            /** Merge all the listings html. */
            $return .= $listing_html;

        }

        /** Wrap the listings html. */
        $return = pls_h(
            'section',
            array( 'class' => "pls-listings pls-listings " . pls_get_merged_strings( array( 'pls-listing', $context ), '-', 'pre', false ) ),
            $return
        );

        /** Filter (pls_listings[_context]) the resulting html that contains the collection of listings.  */
        return apply_filters( pls_get_merged_strings( array( 'pls_listings', $context ), '_', 'pre', false ), $return, $listings_raw, $listings_html, $request_params, $context_var );
    }


}
