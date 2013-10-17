function Filters () {}

Filters.prototype.init = function (params) {
	this.filters = {};
	this.dom_id = params.dom_id || false;
	this.list = params.list || false;
	this.map = params.map || false;
	this.listings = params.listings || false;
	this.custom_update_callback = params.custom_update_callback || false;
	this.className = params['class'] || '.pls_search_form_listings';
	
	if (params.listener) {
		this.listener = {};
		this.listener.elements = params.listener.elements || this.className + ', #sort_by, #sort_dir'
		this.listener.events = params.listener.events || 'change submit';	
	} else {
		this.listener = {};
		this.listener.elements = this.className + ', #sort_by, #sort_dir'
		this.listener.events = 'change submit';	
	}
}

Filters.prototype.listeners = function (callback) {
	var that = this;
	jQuery(this.listener.elements).on(this.listener.events, function(event) {
        event.preventDefault();

        // Checks to see if a meaningful change to search criteria (i.e., not sort or pagination) triggered this call...
        var search_criteria_changed = (this.className === that.className.replace('.', ''));
        
        that.listings.get(search_criteria_changed);
    });	
}

Filters.prototype.get_values = function () {
	var result = [];
	jQuery.each(jQuery(this.listener.elements).serializeArray(), function(i, field) {
		result.push({'name': field.name, 'value': field.value});
    });

	return result;
}

Filters.prototype.set_values = function (search_id) {
	var that = this;
	jQuery.post(info.ajaxurl, {action: 'get_saved_search_filters', search_id: search_id}, function (data, textStatus, xhr) {
		jQuery(that.listener.elements).find('input, select').each(function (i) {
			if (this.name == 'purchase_types[]') {
				// Special case for purchase types
				if (data['purchase_types[0]']) {
					jQuery(this).val(data['purchase_types[0]']);
					jQuery(this).trigger("liszt:updated");	
				};
			} else if (this.name == 'zoning_types[]') {
				// Special case for zoning types
				if (data['zoning_types[0]']) {
					jQuery(this).val(data['zoning_types[0]']);
					jQuery(this).trigger("liszt:updated");
				}
			} else if (data[this.name]) {
				jQuery(this).val(data[this.name]);
				jQuery(this).trigger("liszt:updated");
			}
	    });

	    if (typeof that.custom_update_callback == 'function') {
			that.custom_update_callback(data);
	    }
	}, 'json');
}