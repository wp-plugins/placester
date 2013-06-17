widget_autosave = function() {
	
	var post_id = jQuery("#post_ID").val();
	var post_type = jQuery('#pl_post_type').val() || "";
	var shortcode_type = pls_get_shortcode_by_post_type( post_type );
	var widget_class = jQuery('#widget_class').val() || '';
	
	//autosave();
	var featured = {};
	jQuery("input[name^='pl_featured_listing_meta']").map(function() {
			var element_name = jQuery(this).attr('name');
			var open_bracket = element_name.lastIndexOf('[');
			var close_bracket = element_name.lastIndexOf(']');
			var element_key = element_name.substring( open_bracket + 1, close_bracket );
			
			featured[element_key] = jQuery(this).val(); 
		}
	);
	
	var static_listings = {};
	static_listings.location = {};
	static_listings.metadata = {};
	
	// manage static listings form params
	if( post_type === 'static_listings' || post_type === 'search_listings'
		|| post_type === 'pl_static_listings' || post_type === 'pl_search_listings' ) {
		jQuery('#pl_static_listing_block .form_group input, #pl_static_listing_block .form_group select').each(function() {
			// omit blank values and not filled ones
			var value = jQuery(this).val();
			if( value !== undefined && value !== false && value !== '' ) {
				var id = this.id;

				if( id.indexOf('location-') !== -1 ) {
					// get the part after location
					if( this.value != 'false' ) {
						var field = id.substring( 9 );
						static_listings.location[field] = value;
					}
				} else if( id.indexOf('metadata-') !== -1 ) {
						// don't mark checkboxes as true in filters
						if( ( this.type == 'checkbox' && this.checked ) ) {
							// get the part after metadata
							var field = id.substring( 9 );
							static_listings.metadata[field] = value;
							// input checkbox is with value true in the search filters
						} else if( this.type != 'checkbox' && this.value != 'false' && this.value != "0" ) {
							var field = id.substring( 9 );
							static_listings.metadata[field] = value;
						}
				} else {
					static_listings[id] = value;
				}
				
			}
		});
	}
	
	// debugger;
	
	var radio_type = jQuery("input[name='radio-type']:checked").val();
	var neighborhood_type = 'nb-id-select-' + radio_type; 
	var neighborhood_value = jQuery('#' + neighborhood_type).val();

	// the selector to fetch the template from
	var tpl_selector =  '#' + shortcode_type + '_template_block input.shortcode[value="' + shortcode_type + '"]';
	
	var post_data = {
					'post_id': post_id,
	                'action': 'autosave_widget',
	                'pl_post_type': post_type,
	                'pl_cpt_template': jQuery(tpl_selector).parent().find('option:selected').val(),
	                'pl_template_before_block': jQuery('pl_template_before_block').val(),
	                'pl_template_after_block': jQuery('pl_template_after_block').val(),
	                'width': jQuery('#widget-meta-wrapper input#width').val() || "250",
	                'height': jQuery('#widget-meta-wrapper input#height').val() || "250",
	                'pl_featured_listing_meta': JSON.stringify(featured),
	                'radio-type': radio_type,
	                'meta_box_nonce': jQuery('#meta_box_nonce').val(),
	                'listing_types': static_listings['listing_types'] || 'false',
//	                'zoning_types': static_listings['zoning_types'] || 'false',
//	                'purchase_types': static_listings['purchase_types'] || 'false',
	                'location': JSON.stringify( static_listings.location ),
	                'metadata': JSON.stringify( static_listings.metadata ),
	                'hide_sort_by': jQuery('#hide_sort_by').is(':checked'),
	                'hide_sort_direction': jQuery('#hide_sort_direction').is(':checked'),
	                'hide_num_results': jQuery('#hide_num_results').is(':checked'),
	                'form_action_url': jQuery('#form_action_url').val(),
	                'widget_class': widget_class
	};
	
	post_data[neighborhood_type] = neighborhood_value;
	post_data[radio_type] = neighborhood_value;
	 
	var num_results_shown = jQuery('#num_results_shown').val();
	if( num_results_shown  !== '' ) {
		if( /^[0-9]+$/.test(num_results_shown)
			&& num_results_shown >= 0 
			&& num_results_shown <= 50) {
			post_data['num_results_shown'] = num_results_shown;
		}
	} 
	
	jQuery.ajax({
		data: post_data,
		// beforeSend: doAutoSave ? autosave_loading : null,
		type: "POST",
		url: ajaxurl,
		success: function( response ) {
			setTimeout(function() {
				// breaks the overall layout
				// var frame_width = post_data['width'];
				var frame_width = '250';
				var frame_height = '250';
				var post_id = jQuery("#post_ID").val();
				
				var widget_class = jQuery('#widget_class').val() || '';
				if( widget_class !== '' ) {
					widget_class = 'class="' + widget_class + '"';
				}
				
				jQuery('#preview-meta-widget').html("<iframe src='" + siteurl + "/?p=" + post_id + "&preview=true' width='" + frame_width + "px' height='" + frame_height + "px' " + widget_class + "></iframe>");
				// jQuery('#preview-meta-widget').css('height', post_data['height']);
				jQuery('#pl-review-link').show();
			}, 800);
		}
	});
};

function pls_get_shortcode_by_post_type( post_type ) {
	switch( post_type ) {
		case 'pl_search_listings':		return 'search_listings';
		case 'pl_map':					return 'search_map';
		case 'pl_form':					return 'search_form';
		case 'pl_slideshow':			return 'listing_slideshow';
		case 'pl_static_listings':		return 'static_listings';
			
		default:
			return post_type;
	}	
}
