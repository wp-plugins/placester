<?php 

PLS_Map::init();
class PLS_Map {
	
	// we'll build our response in here so we can
	// build html as the data requires it rather 
	// then the needed order for google maps. 
	static $response;

	static $map_js_var;

	static $markers = array();

	function init() {
		
		add_action('wp_head', array(__CLASS__,'add_marker_js'));

	}

	static function dynamic($listings = array(), $map_args = array(), $marker_args = array())
	{
		self::make_markers($listings, $marker_args);
		
		return self::assemble_map($map_args);
	}

	public function add_marker_js() {
		
		ob_start()
		?>
			<script type="text/javascript">
				
				var markers = [];

				function pls_js_add_marker(row) {
                    
                    if (typeof google != 'undefined') {
	                    markers.push(new google.maps.Marker({
	                        position: new google.maps.LatLng(row.location.coords.latitude, row.location.coords.longitude)
	                    }));	
                    };
				}

			</script>
		<?php
		echo ob_get_clean();

		
	}

	private static function make_markers($listings, $args) {
		
		if (is_array($listings) && isset($listings[0])) {
			foreach ($listings as $listing) {
				self::make_marker($listing);
			}
		} else {
			if (!empty($listings)) {
				self::make_marker($listings);
			}
		}
	}

	private static function make_marker($listing = '', $args ='') {
		
		extract(self::process_marker_defaults($listing, $args), EXTR_SKIP);
		
		ob_start();
		?>
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>),
			map: pls_google_map,
		});
		<?php
		self::$markers[] = trim(ob_get_clean());
	}

	private static function assemble_map($args) {
		
		extract(self::process_defaults($args), EXTR_SKIP);

		// make id available to everyone
		self::$map_js_var = $map_js_var;
		
		ob_start();
		?>
		
		<script src="http://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript" charset="utf-8"></script>
		
		<script type="text/javascript">
		  
		  var <?php echo $map_js_var; ?>;

		  jQuery(function() { 

		  	var latlng = new google.maps.LatLng(<?php echo $lat; ?>, <?php echo $lng; ?>);
		    var myOptions = {
		      zoom: <?php echo $zoom; ?>,
		      center: latlng,
		      mapTypeId: google.maps.MapTypeId.ROADMAP
		    };
		    <?php echo $map_js_var ?> = new google.maps.Map(document.getElementById("<?php echo $id ?>"),
		        myOptions);
		
			<?php foreach (self::$markers as $marker): ?>
				<?php echo $marker; ?>
			<?php endforeach ?>	
			
		});	  
		


		function pls_js_render_markers () {
				
				var bounds = new google.maps.LatLngBounds();

				if (markers) {
					for (var i = markers.length - 1; i >= 0; i--) {
						markers[i].setMap(<?php echo $map_js_var ?>);
						bounds.extend(markers[i].getPosition());
					};
					<?php echo $map_js_var ?>.fitBounds(bounds);
				};

			}
		</script>
		
		<div class="<?php echo $class ?>" id="<?php echo $id ?>" style="width:<?php echo $width; ?>px; height:<?php echo $height; ?>px"></div>
		<?php
		
		return ob_get_clean();

	}

	private static function process_defaults ($args) {
		
		/** Define the default argument array. */
		$defaults = array(
        	'lat' => '42.37',
        	'lng' => '-71.03',
        	'zoom' => '14',
        	'width' => 300,
        	'height' => 300,
        	'id' => 'map_canvas',
        	'class' => 'custom_google_map',
        	'map_js_var' => 'pls_google_map'
        );

		/** Merge the arguments with the defaults. */
        $args = wp_parse_args( $args, $defaults );

        return $args;

	}

	private static function process_marker_defaults ($listing, $args) {

		// pls_dump($listing);
		if (isset($listing) && is_array($listing) && isset($listing['location'])) {
			if (isset($listing['location']['coords']['latitude'])) {
				$coords = $listing['location']['coords'];
				$args['lat'] = $coords['latitude'];
				$args['lng'] = $coords['longitude'];	
			} elseif (is_array($listing['location']['coords'])) {
				$coords = $listing['location']['coords'];
				$args['lat'] = $coords[0];
				$args['lng'] = $coords[1];	
			}
		}

		/** Define the default argument array. */
		$defaults = array(
        	'lat' => '42.37',
        	'lng' => '71.03',
        );

		/** Merge the arguments with the defaults. */
        $args = wp_parse_args( $args, $defaults );

        return $args;		

	}
}