jQuery(document).ready(function($) {

	set_property_type();
	
	//if we're editing, disable listing type.
	if ($('#add_listing_form').attr('method') == 'PUT') {
		$('select#compound_type').prop('disabled', true);
	} else {
		$('select#compound_type').bind('change', function () {
			set_property_type();
		});
	};

	$('.show_advanced').live('click', function () {
		$(this).removeClass('show_advanced');
		$(this).addClass('hide_advanced');
		$(this).text('Hide Advanced');
		var id = '#' + $(this).attr('id') + '_details_admin_ui_advanced'
		$(id).show();
	})

	$('.hide_advanced').live('click', function () {
		$(this).removeClass('hide_advanced');
		$(this).addClass('show_advanced');
		$(this).text('Show Advanced');
		var id = '#' + $(this).attr('id') + '_details_admin_ui_advanced';
		$(id).hide();
	})

	function set_property_type() {
		//res_sale
		$('div#res_sale_details_admin_ui_basic').hide().find('input, select').prop('disabled', true);
		$('div#res_sale_details_admin_ui_advanced').hide().find('input, select').prop('disabled', true);

		//res_rental
		$('div#res_rental_details_admin_ui_basic').hide().find('input, select').prop('disabled', true);
		$('div#res_rental_details_admin_ui_advanced').hide().find('input, select').prop('disabled', true);

		//vac rental
		$('div#vac_rental_details_admin_ui_basic').hide().find('input, select').prop('disabled', true);
		$('div#vac_rental_details_admin_ui_advanced').hide().find('input, select').prop('disabled', true);

		//sublet
		$('div#sublet_details_admin_ui_basic').hide().find('input, select').prop('disabled', true);
		$('div#sublet_details_admin_ui_advanced').hide().find('input, select').prop('disabled', true);

		//Com Rentals
		$('div#comm_rental_details_admin_ui_basic').hide().find('input, select').prop('disabled', true);
		$('div#comm_rental_details_admin_ui_advanced').hide().find('input, select').prop('disabled', true);
		
		//Com Sales
		$('div#comm_sale_details_admin_ui_basic').hide().find('input, select').prop('disabled', true);
		$('div#comm_sale_details_admin_ui_advanced').hide().find('input, select').prop('disabled', true);

		//Parking
		$('div#park_rental_details_admin_ui_basic').hide().find('input, select').prop('disabled', true);
		$('div#park_rental_details_admin_ui_ad').hide().find('input, select').prop('disabled', true);

		//show the right boxes
		// console.log('#' + $('select#compound_type').val() + '_details_admin_ui_basic');
		mixpanel.track('Add Property - Type Selected', {'type' : $('select#compound_type').val() });
		$('#' + $('select#compound_type').val() + '_details_admin_ui_basic' ).show().find('input, select').prop('disabled', false);
		$('#' + $('select#compound_type').val() + '_details_admin_ui_advanced' ).find('input, select').prop('disabled', false);
	}

	// Initialize the jQuery File Upload widget:
    $('#add_listing_form').fileupload({
        formData: {action: 'add_temp_image'},
        sequentialUploads: true,
        submit: function (e, data) {
        	$.each(data.files, function (index, file) {
        		var id = file.name.replace(/( )|(\.)|(\))|(\()/g,'');
                $('#fileupload-holder-message').append('<div id="image_container_remove"><li class="image_container"><div class="image_upload_bg"><div class="wpspinner" id="'+id+'"></div><a id="remove_image">Loading...</a></div></li></div>');
            });	
        },
        done: function (e, data) {
        	if (data.result.message) {
        		alert(data.result.message);
        		return false;
        	}
            $.each(data.result, function (index, file) {
            	//The server returns a properly formed array with no url. 
            	//forcing us to do error handling on the js side.
            	if (!file.url) {
            		mixpanel.track('Add Property - Image - Upload Error');
            		alert('Error - Upload Failed. Your image needs to be smaller then 1MB and gif, jpg, or png.');
            		var id = '#' + file.orig_name.replace(/( )|(\.)|(\))|(\()/g,'');
	            	$(id).parentsUntil('#image_container_remove').remove();
            		return false;
            	} else {
	            	mixpanel.track('Add Property - Image - Upload Complete');
	            	var count = $('.image_container div input').length;
	            	var id = '#' + file.orig_name.replace(/( )|(\.)|(\))|(\()/g,'');
	            	$(id).parentsUntil('#image_container_remove').remove();
	                $('#fileupload-holder-message').append('<li class="image_container"><div><img width="100px" height="100px" src="'+file.url+'" ><a id="remove_image">Remove</a><input id="hidden_images" type="hidden" name="images['+count+'][filename]" value="'+file.name+'"></div></li>');	
            	}
            	
            });
        },
        failed: function (e, data) {
        	mixpanel.track('Add Property - Image - Upload Error');
        	alert('error');
        }
    });

	$('#remove_image').live('click', function (event) {
		event.preventDefault();
		$(this).closest('.image_container').remove();
	});	

	
	$("input#metadata-avail_on_picker").datepicker({
        showOtherMonths: true,
        numberOfMonths: 2,
        selectOtherMonths: true,
        dateFormat: "yy-mm-dd"
	});

	// duplicates the custom attribute form.
	// $('button#custom_data').live('click', function (event) {
	// 	event.preventDefault();
	// 	var html = $(this).closest('section').clone();
	// 	$(this).html('Remove').attr('id', 'custom_data_remove');
	// 	$(this).closest(".form_group").append(html);
	// });	

	// $('button#custom_data_remove').live('click', function (event) {
	// 	event.preventDefault();
	// 	$(this).closest('section').remove();
	// });	

	
	    
	//create listing
	$('#add_listing_publish').live('click', function(event) {
		$('#loading_overlay').show();
        event.preventDefault();
       	//hide all previous validation issues
       	$('.red').remove();
       	$('#message').remove();
       	//set default values required for the form to work. 
        var form_values = {}
        form_values['request_url'] = $(this).attr('url');
        if ($('#add_listing_form').attr('method') == 'POST') {
        	form_values['action'] = 'add_listing';	
        } else {
        	form_values['action'] = 'update_listing';	
        };
        
        //get each of the form values, set key/values in array based off name attribute
        $.each($('#add_listing_form :input[value]').serializeArray(), function(i, field) {
    		form_values[field.name] = field.value;
        });
        //set context of the form.
       var form = $('#add_listing_form');
       //ajax request for creating the listing.
       // console.log(form_values); 
        $.ajax({
			url: ajaxurl, //wordpress thing
			type: "POST",
			data: form_values,
			dataType: "json",
			success: function (response) {
				$('#loading_overlay').hide();
				if (response && response['validations']) {
					var item_messages = [];
					for(var key in response['validations']) {
						var item = response['validations'][key];
						if (typeof item == 'object') {
							for( var k in item) {
								if (typeof item[k] == 'string') {
									var message = '<p class="red">' + response['human_names'][key] + ' ' + item[k] + '</p>';
								} else {
									var message = '<p class="red">' + response['human_names'][k] + ' ' + item[k].join(',') + '</p>';
								}
								$("#" + key + '-' + k).prepend(message);
								item_messages.push(message);
							}
						} else {
							var message = '<p class="red">'+item[key].join(',') + '</p>';
							$("#" + key).prepend(message);
							item_messages.push(message);
						}
					} 
					$(form).prepend('<div id="message" class="error"><h3>'+ response['message'] + '</h3>' + item_messages.join(' ') + '</div>');
				} else if (response && response['id']) {
					if (form_values['action'] == 'add_listing') {
						$('#manage_listing_message').html('<div id="message" class="updated below-h2"><p> Listing successfully created! You may <a href="'+siteurl+'/properties/'+response['id']+'" class="button-secondary">View</a> or <a href="'+adminurl+'?page=placester_property_add&id='+response['id']+'" class="button-secondary">Edit</a></p></div>')
					} else {
						$('#manage_listing_message').html('<div id="message" class="updated below-h2"><p> Listing successfully updated! You may <a href="'+siteurl+'/properties/'+response['id']+'" class="button-secondary">View</a> or <a href="'+adminurl+'?page=placester_property_add&id='+response['id']+'" class="button-secondary">Edit</a></p></div>')
					}
				}
			}
		}, 'json');
    });
});