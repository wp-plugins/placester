function Listings ( params ) {
	this.map = params.map || false;
	this.list = params.list || false;
	this.filter = params.filter || false;
	this.poi = params.poi || false;
	this.hook = params.hook || 'pls_listings_ajax';
	this.sSource = params.sSource || info.ajaxurl;
	this.aoData = params.aoData || [];
	this.active_filters = [];
	this.single_listing = params.single_listing || false;
	this.property_ids = params.property_ids || false;
	this.default_filters = params.default_filters || [];
	this.filter_override = params.filter_override || false;
	this.is_new_serch = false;
	this.from_back_button = false;
	this.search_hash = false;

}

Listings.prototype.pending = false;

Listings.prototype.init = function () {
	var that = this;

	if (this.filter) {
		this.filter.listeners(function () {
			that.update();
		});
	}

	if (this.map) {
		this.map.listeners();
	}

	if (this.list) {
		this.list.listeners();

		jQuery.address.change(function(event) {
			if (!that.is_new_serch) {
				that.from_back_button = true;
				that.get();
			}
		});

		//boot up the datatable
		if ( this.map.filter_by_bounds ) {
			google.maps.event.addDomListenerOnce(window, 'load', function() {
				google.maps.event.addDomListenerOnce(that.map.map, 'idle', function() {
					that.list.datatable = jQuery(that.list.dom_id).dataTable(that.list.settings);			
				});
			});
		} else {
			this.list.datatable = jQuery(this.list.dom_id).dataTable(this.list.settings);	
		}
		
	}

	if ( this.single_listing ) {
		this.get();
	}
}

Listings.prototype.get = function ( success ) {
	this.is_new_serch = true;
	var that = this;

	//if there's a pending request, do nothing.
	if ( Listings.prototype.pending ) {
		return;
	};

	//if there's a single listing, always return that
	if ( this.map.type == 'single_listing' ) {
		google.maps.event.addDomListenerOnce(window, 'load', function() {
			that.map.update( {'aaData' : [['', that.single_listing]], 'iDisplayLength': 0, 'iDisplayStart': 0, 'sEcho': 0} );	
		});
		return false;
	}

	//or, if we're dealing with a polygon map and there's not a selected polygon
	if ( ( this.map.type == 'neighborhood' && !this.map.selected_polygon && !this.map.neighborhood.neighborhood_override ) ) {
		if ( this.list )
			this.list.update( {'aaData' : [], 'iDisplayLength': 0, 'iDisplayStart': 0, 'sEcho': this.list.sEcho} )

		if ( this.map )
			this.map.update( {'aaData' : [], 'iDisplayLength': 0, 'iDisplayStart': 0, 'sEcho': this.list.sEcho} )

		return false;
	}
	this.pending = true;

	var that = this;

	if (that.default_filters.length > 0) {
		that.active_filters = that.default_filters;
	}

	//allows the dev to pass in one or many property ids
	if (this.property_ids) {
		that.active_filters.push( { "name": "property_ids", "value" :  this.property_ids} );	
	}

	//get pagination and sorting information
	if (this.list && this.list.datatable) {
		this.list.show_loading();
		var fnSettings = this.list.datatable.fnSettings();
		that.active_filters.push( { "name": "iDisplayLength", "value" : fnSettings._iDisplayLength } );
		that.active_filters.push( { "name": "iDisplayStart", "value" : fnSettings._iDisplayStart } );
		this.list.sEcho++
		that.active_filters.push( { "name": "sEcho", "value" :  this.list.sEcho} );			
		// aoData;
	} else if ( this.list ) {
		this.list.show_loading();
		that.active_filters.push( { "name": "iDisplayLength", "value" : this.list.limit_default} );
		that.active_filters.push( { "name": "iDisplayStart", "value" : 0} );
		that.active_filters.push( { "name": "sEcho", "value" : 1} );	
	}

	if (this.list && this.list.context) {
		that.active_filters.push( { "name": "context", "value" : this.list.context} );
	}
  
	//get get current state of search filtes. 
	if (this.filter) {
		that.active_filters = that.active_filters.concat(this.filter.get_values());
	}

	//get bounding box or polygon information
	//there should be a map
	//also, we either need a type of neighborhood or filter bounds to be enabled
	if (this.map && (this.map.type == 'neighborhood' || this.map.filter_by_bounds ) ) {
		this.map.show_loading();
		that.active_filters = that.active_filters.concat(this.map.get_bounds());
	}

	if (that.filter_override) {
		for (var i = that.filter_override.length - 1; i >= 0; i--) {
			that.active_filters.push(that.filter_override[i]);
		};
	};
	that.active_filters.push( { "name": "action", "value" : this.hook} );

	//saved search functionality
	var hash = that.generate_search_hash()
	var current_hash = jQuery.address.value();

	// Don't display in preview screens, nor in widget pages
	if( window.location.href.indexOf( 'post_type=pl_general_widget' ) === -1 && 
			window.location.href.indexOf( 'post.php?post=' ) === -1 &&
			window.location.href.indexOf( '/pl_general_widget/' ) === -1) {
		//the hash has never been set, and it's not empty, or its different from the previous hash. Go look it up.
		if (current_hash !== '/' && ( that.search_hash === hash || that.search_hash === false || that.from_back_button) ) {
			that.active_filters.push( { "name": "saved_search_lookup", "value" : current_hash } );	
			that.search_hash = current_hash;
	
			//if there are filters active, set them too
			if (that.filter) {
				that.filter.set_values( current_hash );
			}
	
		} else {
			that.active_filters.push( { "name": "saved_search_lookup", "value" : '/' + hash } );	
			jQuery.address.value(hash);
			that.search_hash = '/' + hash;
		}
	}
		
	jQuery.ajax({
	    "dataType" : 'json',
	    "type" : "POST",
	    "url" : this.sSource,
	    "data" : that.active_filters,
	    "success" : function ( ajax_response ) {
			that.pending = false; 
			that.is_new_serch = false;		
			that.from_back_button = false;		
			that.ajax_response = ajax_response;
			if (that.map && that.map.map)
				that.map.update( ajax_response );

			if ( that.list )
				that.list.update( ajax_response );

			if (that.poi )
				that.poi.update();
			

			that.active_filters = [];
	    }
	});
}

Listings.prototype.generate_search_hash = function () {

	var joined = '';
	for (var i = this.active_filters.length - 1; i >= 0; i--) {
		if ( this.active_filters[i] ) {
			if (this.active_filters[i]['name'] == "saved_search_lookup" || this.active_filters[i]['name'] == "sEcho")
				continue;

			joined += this.active_filters[i]['name'];
			joined += this.active_filters[i]['value'];
		}
	}	
	return this.fast_hasher(joined);
	
}

Listings.prototype.fast_hasher = function(str){
    var hash = 0;
    if (str.length == 0) return hash;
    for (i = 0; i < str.length; i++) {
        char = str.charCodeAt(i);
        hash = ((hash<<5)-hash)+char;
        hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
}