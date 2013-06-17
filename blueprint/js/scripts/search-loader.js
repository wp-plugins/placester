function SearchLoader ( params ) { 
	this.is_inited = false;
	this.params = {};

	if (!params) {
		return this;
	}
	
	this.params = params;

	this.init();
}

SearchLoader.prototype.init = function () {
	this.is_inited = true;

	this.listings = {};
	this.map = false;
	this.list = false;
	this.filter = false;
	this.poi = false;
	
	// Some type checking to make sure we handle defaults correctly
	this.init_params();
	
	// Just out of kindness for new devs
	if ( this.params.map || this.params.filter || this.params.list || this.params.poi ) {
		this.create_objects();
		this.init_objects();
		this.listings.init();
	} else {
		alert('You didn\'t ask for filters, a list, or a map - why are you using the bootloader? ');
	}
}

SearchLoader.prototype.init_params = function () {
	var that = this;

	this.map_params = function () {
		if (that.params.map && typeof Map !== 'function')
			return false;

		if (that.params.map instanceof Object) {
			that.params.map.type = that.params.map.type || 'listings';
			return that.params.map;
		}
		
		return {type: 'listings'};
	}();

	this.list_params = function () {
		if (that.params.list && typeof List !== 'function')
			return false;

		if (that.params.list instanceof Object) 
			return that.params.list;

		return {};	
	}();

	this.filter_params = function () {
		if (that.params.filter && typeof Filters !== 'function')
			return false;

		if (that.params.filter instanceof Object)
			return that.params.filter;

		return {};	
	}();

	this.poi_params = function () {
		if (that.params.poi && typeof POI !== 'function')
			return false;

		if (that.params.poi instanceof Object)
			return that.params.poi;

		return {};	
	}();
}

SearchLoader.prototype.create_objects = function () {
	this.listings_params = {};

	if (this.params.map) {
		this.map = new Map();
		this.listings_params.map = this.map;
		this.filter_params.map = this.map;
		this.list_params.map = this.map;
		this.poi_params.map = this.map;
	}
	if (this.params.filter) {
		this.filter = new Filters();
		this.listings_params.filter = this.filter;
		this.list_params.filter = this.filter;
		this.map_params.filter = this.filter;
	}
	if (this.params.list) {
		this.list = new List();
		this.listings_params.list = this.list;
		this.filter_params.list = this.list;
		this.map_params.list = this.list;
	}
	if (this.params.poi) {
		this.poi = new POI();
		this.map_params.poi = this.poi;
	}

	this.listings = new Listings (this.listings_params);

	if (this.params.map) 
		this.map_params.listings = this.listings;

	if (this.params.filter)
		this.filter_params.listings = this.listings;

	if (this.params.list)
		this.list_params.listings = this.listings;

	if (this.params.poi)
		this.poi_params.listings = this.listings;
}

SearchLoader.prototype.init_objects = function () {
	if (this.params.map) {
		if (this.params.map.type === 'neighborhood') {
			var neighborhood_params = {map: this.map};
			if (this.params.map.neighborhood instanceof Object ) {
				neighborhood_params = this.params.map.neighborhood;
				neighborhood_params.map = this.map;
			}
			this.params.map.neighborhood = new Neighborhood (neighborhood_params);
		}
		this.map.init( this.map_params );
	}

	if (this.params.filter) {
		this.filter.init( this.filter_params );
	}

	if (this.params.list) {
		this.list.init( this.list_params );
	}

	if (this.params.poi) {
		this.poi.init( this.poi_params );
	}
}

SearchLoader.prototype.add_param = function (new_params) { 
	for (var property in new_params) {
        if (!this.params.hasOwnProperty(property)) {
            this.params[property] = new_params[property];
        }
    }
    // console.log(this.params);
}

