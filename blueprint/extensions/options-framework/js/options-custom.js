/**
 * Prints out the inline javascript needed for the colorpicker and choosing
 * the tabs in the panel.
 */

jQuery(document).ready(function($) {
	
	// Fade out the save message
	$('.fade').delay(1000).fadeOut(1000);
	
	// Color Picker
	$('.colorSelector').each(function(){
		var Othis = this; //cache a copy of the this variable for use inside nested function
		var initialColor = $(Othis).next('input').attr('value');
		$(this).ColorPicker({
		color: initialColor,
		onShow: function (colpkr) {
		$(colpkr).fadeIn(500);
		return false;
		},
		onHide: function (colpkr) {
		$(colpkr).fadeOut(500);
		return false;
		},
		onChange: function (hsb, hex, rgb) {
		$(Othis).children('div').css('backgroundColor', '#' + hex);
		$(Othis).next('input').attr('value','#' + hex);
	}
	});
	}); //end color picker
	
	// Switches option sections
	$('.group').hide();
	var activetab = '';
	if (typeof(localStorage) != 'undefined' ) {
		activetab = localStorage.getItem("activetab");
	}
	if (activetab != '' && $(activetab).length ) {
		$(activetab).fadeIn();
	} else {
		$('.group:first').fadeIn();
	}
	$('.group .collapsed').each(function(){
		$(this).find('input:checked').parent().parent().parent().nextAll().each( 
			function(){
				if ($(this).hasClass('last')) {
					$(this).removeClass('hidden');
						return false;
					}
				$(this).filter('.hidden').removeClass('hidden');
			});
	});
	
	if (activetab != '' && $(activetab + '-tab').length ) {
		$(activetab + '-tab').addClass('nav-tab-active');
	}
	else {
		$('.nav-tab-wrapper a:first').addClass('nav-tab-active');
	}
	$('.nav-tab-wrapper a').click(function(evt) {
		$('.nav-tab-wrapper a').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active').blur();
		var clicked_group = $(this).attr('href');
		if (typeof(localStorage) != 'undefined' ) {
			localStorage.setItem("activetab", $(this).attr('href'));
		}
		$('.group').hide();
		$(clicked_group).fadeIn();
		evt.preventDefault();
	});
           					
	$('.group .collapsed input:checkbox').click(unhideHidden);
				
	function unhideHidden(){
		if ($(this).attr('checked')) {
			$(this).parent().parent().parent().nextAll().removeClass('hidden');
		}
		else {
			$(this).parent().parent().parent().nextAll().each( 
			function(){
				if ($(this).filter('.last').length) {
					$(this).addClass('hidden');
					return false;		
					}
				$(this).addClass('hidden');
			});
           					
		}
	}
	
	// Image Options
	$('.of-radio-img-img').click(function(){
		$(this).parent().parent().find('.of-radio-img-img').removeClass('of-radio-img-selected');
		$(this).addClass('of-radio-img-selected');		
	});
		
	$('.of-radio-img-label').hide();
	$('.of-radio-img-img').show();
	$('.of-radio-img-radio').hide();

	$.each($('.featured_listings_options'), function(index, val) {
		load_listings(this);  
	});
	
	$('.featured_listings_options').live('change', function(event) {
		event.preventDefault();
		var that = this;
		load_listings(that);
	});

	$('.fls-add-listing').live('click', function (event) {
		event.preventDefault();
		var key = $(this).parent().children('select').val();
		var iterator = $(this).parent().children('select').attr('ref');
		var text = $(this).parent().find('option:selected').text();
		$(this).parent().find('option:selected').remove();
		var option_name = $(this).parent().find('#option-name').val();
		var listing_box = $(this).parent().parent().find('.fls-option');
		if ($(this).hasClass('for_slideshow')) {
			var html = "<li style='float:left; list-style-type: none;'><div id='pls-featured-text' style='width: 200px; float: left;'>" + text + "</div><a style='float:left;' href='#' id='pls-option-remove-listing'>Remove</a><input type='hidden' name='" +option_name+ "["+iterator+"]["+key+"]=' value='"+text+"' /></li>";	
		} else {
			var html = "<li style='float:left; list-style-type: none;'><div id='pls-featured-text' style='width: 200px; float: left;'>" + text + "</div><a style='float:left;' href='#' id='pls-option-remove-listing'>Remove</a><input type='hidden' name='" +option_name+ "["+key+"]=' value='"+text+"' /></li>";	
		};
		
		listing_box.append(html);
	});

	$('#pls-option-remove-listing').live('click', function(event) {
		event.preventDefault();
		var key = $(this).parent().children('input').val();
		var text = $(this).parent().children('#pls-featured-text').text();
		var select = $(this).parent().parent().parent().find('select').prepend('<option value="'+key+'">'+text+'</option>');
		$(this).parent().remove();

	});

	function load_listings(that) {
		var data = {};
		var parent = $(that).parent();
		parent.find('#search_message').show();
		data['action'] = 'pls_listings_for_options';
		$.each(parent.find('.featured_listings_options select'), function(i, field) {
		 	data[field.name] = field.value;
		});
		$.post(ajaxurl, data, function(data) {
			if (data) {
				parent.find('#fls-select-address').html(data);	
				parent.find('#search_message').hide();
			};
		}, 'json');
	}


	$('.slideshow_type').live('change', function(event) {
		$(this).parent().parent().children('.type_controls').hide();
		$(this).parent().parent().children('.type_controls#' + $(this).val() + '_type').show();
		
	});
	$('.slideshow_type').trigger('change');
	
	if( window.location.hash !== undefined && window.location.hash !== '' ) {
		var window_hash = window.location.hash;

		$('.side-nav.nav-tab-wrapper li a').each(function() {
			if( $(this).attr('href') === window_hash ) {
				$(this).trigger('click');
			}
		});
	}


	/*
	 * Handles exporting and asynchronous importing of theme options files + loading of defaults
	 */

	$('#theme_option_file').live('change', function(event) {
		event.preventDefault();

		if (!confirm('Are you sure you want to overwrite your existing Theme Options?'))
		{ return; }

		//Retrieve the first (and only!) File from the FileList object
	    var file = event.target.files[0]; 

	    console.log(file);
	    //return;

	    if (file) {
	      var r = new FileReader();
	      r.onload = function(e) { 
		     var contents = e.target.result;
	         // alert( "Got the file.\n" 
	         //      + "name: " + file.name + "\n"
	         //      + "type: " + file.type + "\n"
	         //      + "size: " + file.size + " bytes\n"
	         //      + "starts with: " + contents.substr(1, 50)
	         // );  

	        var data = { action: 'import_theme_options', 
	        			 options_raw: contents };
	        console.log(data);

			$.post(ajaxurl, data, function(response) {
			  if (response) {
			  	// console.log(response);
			  }

		      // Refresh theme options to reflect newly imported settings...
		      location.reload()  
			});
	      }
	      r.readAsText(file);
	    } 
	    else { 
	      alert("Failed to load file");
	    }
	});

	$('#btn_def_opts').live('click', function(event) {
		event.preventDefault();

		if (!confirm('Are you sure you want to overwrite your existing Theme Options?'))
		{ return; }

		var data = { action: 'import_default_options',
					 name: $('#def_theme_opts option:selected').val() }
		// console.log(data);

		$.post(ajaxurl, data, function(response) {
		  if (response) {
		  	// console.log(response);
		  }

	      // Refresh theme options to reflect newly imported settings...
	      window.location.reload(true);
		});
	});

	$('#export_link').live('click', function() {
		var filename = $('#export_txt').val();

		if (filename) {
			var new_href = $(this).attr('href') + '&filename=' + filename;
			$(this).attr('href', new_href);
		}
	});
		 		
});	