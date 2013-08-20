// finish styling filters

//lifestyle polygon map

// only trigger map reload after 70% move in a direction
// only trigger map reload after a zoom out.
// show the number of total results on the map
// infowindow alternative?

function Map () {}

Map.prototype.init = function ( params ) {

	$=jQuery;

	if (!params)
		alert('Your map object must have some options defined, specifically a map type');


	//where ever you go, know who you are.
	var that = this;
	
	this.map = false;
	this.type = params.type || alert('You must define a map type for the method to work properly');
	this.infowindows = [];
	this.markers = params.markers ||[];
	this.markers_hash = {};
	this.bounds = [];
	this.list = params.list || false;
	this.dom_id = params.dom_id || 'map_canvas';
	this.center_map_on_polygons = params.center_map_on_polygons === false ? false : true;

	//map states
	this.is_loaded = false;
	this.is_idle = false;
	this.was_marker_click = false;

	//other objects
	this.listings = params.listings || alert('Map: You must attach a lisitngs object. Every arm needs a head. Some notes: \n if you are creating a map on a property details page then pass your single listing to the listing object.');
	this.polygons = params.polygons || [];
	this.status_window = params.status_window || false;

	//polygon settings
	this.polygons_verticies = [];
	this.polygons_exclude_center = false;
	this.selected_polygon = params.selected_polygon || false;
	this.allow_polygons_to_clear = params.allow_polygons_to_clear || false;
	
	// map settings
	this.lat = params.lat || '42.37';
	this.lng = params.lng || '-71.03';
	this.zoom = params.zoom || 15;
	this.always_center = params.always_center || true;
	this.filter_by_bounds = params.filter_by_bounds === false ? false : true;
	this.full_callback = params.full_callback || false;
    this.disable_info_window = params.disable_info_window === true ? true : false;
    this.infotemplate = params.infotemplate || false;

	//marker settings
	this.marker = {};
	this.marker.icon = params.marker || false;
	this.marker.icon_hover = params.marker_hover || 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=|FF0000|000000'

	this.map_options = params.map_options || { zoom: this.zoom, mapTypeId: google.maps.MapTypeId.ROADMAP, mapTypeControl: false, streetViewControl: false, zoomControl: true, zoomControlOptions: { style: google.maps.ZoomControlStyle.SMALL, position: google.maps.ControlPosition.TOP_LEFT } };

	//polygon settings
	if ( this.type == 'neighborhood' ) {
		//filter by bounds forces the datatable to render after the map is idle
		//thats not needed here.
		this.filter_by_bounds = false;
		this.neighborhood = params.neighborhood || alert('If this is to be a neighborhood map, you\'ll need to give it a neighborhood object');	
	} else if ( this.type == 'lifestyle' ) {
		this.lifestyle = params.lifestyle || alert('If this is to be a lifestyle map, you\'ll need to give it a lifestyle object');	
	} else if ( this.type == 'lifestyle_polygon' ) {
		this.lifestyle_polygon = params.lifestyle_polygon || alert('If this is to be a lifestyle_polygon map, you\'ll need to give it a lifestyle_polygon object');	
	} 

	// map/list interaction
	Map.prototype.marker_click = params.marker_click || function ( listing_id ) {
		that.was_marker_click = true;
		// console.log(that);
	}
	Map.prototype.marker_mouseover = params.marker_mouseover || function ( listing_id ) {
		if (listing_id) {
			var marker = this.markers_hash[listing_id];
			marker.setIcon(this.marker.icon_hover);	
		}
		
	}
	Map.prototype.marker_mouseout = params.marker_mouseout || function ( listing_id ) {
		if (listing_id) {
			var marker = this.markers_hash[listing_id];
			marker.setIcon(null);
		}
	}

	Map.prototype.responsive_map = params.responsive_map || function () {
		map = this;
		map_id = "#" + map.dom_id;

		$(window).resize(function() {
			// get height/width of the map's container
			map_height = $(map_id).parent().height();
			map_width = $(map_id).parent().width();
            // map's height/width responds to container
            $(map_id).height(map_height);
            $(map_id).width(map_width);
            // re-center map on intended point
            map.center();
		});
	}

	this.init = function() {
		// map options are defined in init
		that.map_options.center = new google.maps.LatLng(that.lat, that.lng);
		that.map = new google.maps.Map(document.getElementById(that.dom_id), that.map_options);
		
		google.maps.event.addDomListenerOnce(that.map, 'idle', function () {
			that.is_idle = true;
			that.once_idle();
		});

		if ( that.type == 'neighborhood' ) {
			//all neighborhoods shown
			that.neighborhood.init();
		} else if ( that.type == 'lifestyle' ) {
			//show points of interests on the map.
			that.lifestyle.init();
		} else if ( that.type == 'lifestyle_polygon' ) {
			//show points of interests on the map, then do listings searches with them.
			that.lifestyle_polygon.init();
		}

		if (that.responsive_map) {
			that.responsive_map();
		};
	}

	this.once_idle = function () {

		if (this.status_window) {
			this.status_window.init();
			this.status_window.add_control_container();
			if( this.status_window.on_load !== false) {
				this.status_window.on_load();	
			}
		}
	}

	google.maps.event.addDomListener(window, 'load', function () {
		that.is_loaded = true;
	});

	//build map
	if (this.is_loaded) {
		this.init();
	} else {
		google.maps.event.addDomListener(window, 'load', that.init);	
	};
	
}

Map.prototype.create_polygon = function ( polygon_options ) {
	var that = this;
	var polygon = new google.maps.Polygon( polygon_options );
	//faster to travers native arrays then using google's getters. We'll risk the collision
	polygon.vertices = polygon_options.vertices;
	
	if ( polygon_options.label && polygon_options.label_center ) {
		new TxtOverlay( polygon_options.label_center, polygon_options.label, "polygon_text_area", this.map );	
	}
	
	polygon.setMap(this.map);
	this.polygons.push(polygon);
	
	google.maps.event.addListener(polygon, 'click', function() {
		that[that.type].polygon_click( polygon );
	});

	google.maps.event.addListener(polygon,"mouseover",function(){
		that[that.type].polygon_mouseover( polygon );
	}); 

	google.maps.event.addListener(polygon,"mouseout",function(){
		that[that.type].polygon_mouseout( polygon );
	});

	return polygon;
}

Map.prototype.update = function ( ajax_response ) {

	if (ajax_response && ajax_response.aaData.length > 0) {
		if ( this.status_window)
			this.status_window.some_results();

		if (this.markers.length > 0 )
			this.clear();

		if (ajax_response.aaData.length > 0 ) {
			for (var i = ajax_response.aaData.length - 1; i >= 0; i--) {
				this.create_listing_marker( ajax_response.aaData[i][1] );
			}
		}
		
		// if filter by bounds, don't move the map, it's confusing
		if ( this.always_center && this.type == 'neighborhood' && this.selected_polygon ) {
			this.center_on_selected_polygon();
		} else if ( this.always_center && this.markers.length > 0 ) {
			this.center();	
		}

		//show full overlay so users knows to zoom if they want.
		var limit = this.list.limit_default || 50;
		if ( ajax_response.iTotalRecords >= limit) {
			this.show_full();
			if ( this.full_callback ) {
				this.full_callback();
			}
		}

		//displaying map status bars
		if ( this.status_window && this.listings.active_filters && this.map ) {
			this.status_window.update();
		}
			

	} else {
		this.show_empty();
	}
	this.hide_loading();
}

Map.prototype.clear = function () {
	if (this.markers) {
		for (var i = this.markers.length - 1; i >= 0; i--) {
			this.markers[i].setMap( null )
		}
        this.markers = [];
	}

	if ( this.allow_polygons_to_clear && this.polygons ) {
		for (var i = this.polygons.length - 1; i >= 0; i--) {
			this.polygons[i].setMap( null );
		}
		this.polygons = [];
	}
}

Map.prototype.create_listing_marker = function ( listing ) {
	var marker_options = {};
	//bind the listing data to the marker so it can be used later
	marker_options.listing = listing;
	marker_options.animation = google.maps.Animation.DROP
	marker_options.position = new google.maps.LatLng(listing['location']['coords'][0], listing['location']['coords'][1]);

	if (listing['images'] && listing['images'][0] && listing['images'][0]['url']) {
    	var image_url = listing['images'][0]['url'];
    };

    //If infotemplate is set
    if( false !== this.infotemplate ) {
	    // Process the template with Underscore.js templating
	    var data = { listing: listing };
	    var parsedTemplate = _.template(this.infotemplate, data );
	    marker_options.content = parsedTemplate;
    } else {
    // Default infowindow markup
	    marker_options.content = '<div id="content">'+
			    	'<div id="siteNotice">'+'</div>'+
				    '<h2 id="firstHeading" class="firstHeading"><a href="'+ listing['cur_data']['url'] + '">' + listing['location']['full_address'] +'</a></h2>'+
				    '<div id="bodyContent">'+
					    '<img width="80px" height="80px" style="float: left" src="'+image_url+'" />' +
					    '<ul style="float: right; width: 130px">' +
						    '<li> Beds: '+ listing['cur_data']['beds'] +'</li>' +
						    '<li> Baths: '+ listing['cur_data']['baths'] +'</li>' +
						    '<li> Price: '+ listing['cur_data']['price'] +'</li>' +
					    '</ul>' +
				    '</div>' +
					'<div class="viewListing" style="margin: 15px 70px; float: left; font-size: 16px; font-weight: bold;"><a href="'+listing['cur_data']['url']+'">View Details</a></div>' +
					'<div class="clear"></div>' +
				  '</div>'+
			    '</div>';
    }

    var marker = this.create_marker( marker_options );
	this.markers_hash[marker.listing.id] = marker;

}

Map.prototype.create_marker = function ( marker_options ) {
	
	var that = this;
	if (this.marker.icon) {
		marker_options.icon = this.marker.icon;
	}
	// console.log(marker_options);
	var marker = new google.maps.Marker(marker_options);

	if (marker_options.listing) {
		marker.listing = marker_options.listing;	
	}
	
	if ( (this.disable_info_window === false) && (this.type != 'single_listing') ) {
	  var infowindow = new google.maps.InfoWindow({content: marker_options.content});
  	this.infowindows.push(infowindow);

  	google.maps.event.addListener(marker, 'click', marker_options.click || function() {
  		if ( marker.listing ) {
  			that.marker_click( marker.listing.id );	
  		}
		
  		for (var i = that.infowindows.length - 1; i >= 0; i--) {
  			that.infowindows[i].setMap(null)
  		}
  		infowindow.open( that.map, marker );
  	});

	};
	
	google.maps.event.addListener(marker,"mouseover",function(){
		if (marker.listing) {
			that.marker_mouseover( marker.listing.id );
			if ( that.list ) {
				that.list.row_mouseover( marker.listing.id );	
			}	
		}
	}); 

	google.maps.event.addListener(marker,"mouseout",function(){
		if (marker.listing) {
			that.marker_mouseout( marker.listing.id );
			if ( that.list ) {
				that.list.row_mouseleave( marker.listing.id );
			}
		}
		
	});

	marker.setMap(this.map);
	that.markers.push(marker);
	return marker;
}

Map.prototype.center = function () {
	var that = this;
	var listener = false;
	
	if ( !that.center_map_on_polygons || that.type == 'single_listing' ) {
		// center map based on lat/lng, not on polygons
		var bounds = new google.maps.LatLng(this.lat, this.lng);
		this.map.setCenter(bounds);

	} else if ( !this.filter_by_bounds || !this.bounds || this.selected_polygon ) {
		//only reposition the map if it's not the first load (this.bounds) and the dev wants (this.filter_by_bounds)
		clearTimeout(listener);

		var bounds = new google.maps.LatLngBounds();

		if ( this.markers.length > 0 ) {
			for (var i = this.markers.length - 1; i >= 0; i--) {
				bounds.extend(this.markers[i].getPosition());
			}
		}
		
		if ( !this.polygons_exclude_center && this.polygons_verticies.length > 0 ) {
			for (var i = this.polygons_verticies.length - 1; i >= 0; i--) {
				bounds.extend(this.polygons_verticies[i]);
			}
		}
		
        if ( this.map ) {
        	this.map.fitBounds(bounds);
            listener = setTimeout( function () {
				google.maps.event.addListener(that.map, 'bounds_changed', function( event ) {
				    if ( that.map.getZoom() > 15 ) {
				    	that.map.setZoom( 15 );
				    }
				});
            }, 750 );
        }
	}
}


Map.prototype.center_on_polygons = function () {
	
	var bounds = new google.maps.LatLngBounds();
	for (var i = this.polygons_verticies.length - 1; i >= 0; i--) {
		bounds.extend(this.polygons_verticies[i]);
	}
	this.map.fitBounds(bounds);
}

Map.prototype.center_on_selected_polygon = function () {
	
	var bounds = new google.maps.LatLngBounds();
	for (var i = this.selected_polygon.vertices.length - 1; i >= 0; i--) {
		var vertice = this.selected_polygon.vertices[i];
		var gpoint = new google.maps.LatLng(vertice.lat, vertice.lng);
		bounds.extend(gpoint);
	}
	this.map.fitBounds(bounds);	
}

Map.prototype.center_on_markers = function () {
	var bounds = new google.maps.LatLngBounds();
	for (var i = this.markers.length - 1; i >= 0; i--) {
		bounds.extend(this.markers[i].getPosition());
	}	
	this.map.fitBounds(bounds);
}

Map.prototype.get_bounds =  function () {
	if ( !this.map ) {
		return this.bounds;
	}
	this.bounds = [];

	if (this.type == 'neighborhood' && this.selected_polygon) {
		for (var i = this.selected_polygon.vertices.length - 1; i >= 0; i--) {
			var point = this.selected_polygon.vertices[i];
			this.bounds.push({'name' : 'polygon[' + i + '][lat]', 'value': point['lat'] });
			this.bounds.push({'name' : 'polygon[' + i + '][lng]', 'value': point['lng'] });
		}
	} else if (this.type == 'neighborhood' && this.neighborhood.neighborhood_override === true ) {
		//if the neighborhood object is attempting to recover, let it.
		return;
	} else {
		var map_bounds = this.map.getBounds();
		if ( typeof map_bounds == 'undefined' ) {
			return this.bounds;
		}

		this.bounds.push({'name' : 'polygon[0][lat]', 'value': map_bounds.getNorthEast().lat() });
		this.bounds.push({'name' : 'polygon[0][lng]', 'value': map_bounds.getNorthEast().lng() });

		this.bounds.push({'name' : 'polygon[1][lat]', 'value': map_bounds.getNorthEast().lat() });
		this.bounds.push({'name' : 'polygon[1][lng]', 'value': map_bounds.getSouthWest().lng() });

		this.bounds.push({'name' : 'polygon[2][lat]', 'value': map_bounds.getSouthWest().lat() });
		this.bounds.push({'name' : 'polygon[2][lng]', 'value': map_bounds.getSouthWest().lng() });

		this.bounds.push({'name' : 'polygon[3][lat]', 'value': map_bounds.getSouthWest().lat() });
		this.bounds.push({'name' : 'polygon[3][lng]', 'value': map_bounds.getNorthEast().lng() });	
	}
	return this.bounds;
}

Map.prototype.listeners = function ( ) {
	var that = this;
	var timeout = false;

	if (this.filter_by_bounds) {
		google.maps.event.addDomListener(window, 'load', function() {
			if (that.type == 'listings') {
				//trigger a reload on any movement
				google.maps.event.addListener(that.map, 'dragend', function() {
					
					// console.log(that.status_window.update_map_on_drag);
					if ( that.status_window && !that.status_window.update_map_on_drag ) {
						that.status_window.on_load();
						return;
					}

					if ( timeout === false ) {
						that.listings.get();
					} 
					//only reload the map once since bounds_changed is a little trigger happy
					clearTimeout(timeout);
					timeout = setTimeout(function () {
						that.listings.get();	
					}, 750);
				});

				google.maps.event.addListener(that.map, 'zoom_changed', function() {
					//only reload the map once since bounds_changed is a little trigger happy
					clearTimeout(timeout);
					timeout = setTimeout(function () {
						that.listings.get();
					}, 750);	
				});
			}

			google.maps.event.addListener(that.map, 'drag', function () {
				that.drag();
			});
			
		});	
	}

}

Map.prototype.geocode = function (address, callback ) {
	var geocoder = new google.maps.Geocoder();
	var bounds = this.map.getBounds();
    geocoder.geocode( { 'address': address, bounds: bounds}, function(results, status) {
      callback( results, status);
    });
}

Map.prototype.show_empty = function () {

	jQuery('.map_wrapper #empty_overlay').show();

	setTimeout(function () {
		jQuery('.map_wrapper #empty_overlay').fadeOut();		
	}, 750);

	if ( this.status_window && this.status_window.empty)
		this.status_window.empty();
}
Map.prototype.hide_empty = function () {
	jQuery('.map_wrapper #empty_overlay').hide();
}
Map.prototype.show_loading = function () {
	jQuery('.map_wrapper #loading_overlay').show();
	if ( this.status_window && this.status_window.loading )
		this.status_window.loading();
}
Map.prototype.hide_loading = function () {
	jQuery('.map_wrapper #loading_overlay').hide();
}
Map.prototype.show_full = function () {
	if ( this.status_window )
		this.status_window.full();	
}
Map.prototype.drag = function () {
	if ( this.status_window )
		this.status_window.dragging();		
}