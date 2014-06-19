
jQuery(window).load( function ( $ ) { 
	

	jQuery('#button_email_forwarding').on('click', function () {

		jQuery('.pls-loading-overlay').show();
		
		var email_addresses = jQuery('#forwarding_email_addresses').val();

		var invalid_email_address = !are_any_email_addresses_invalid( email_addresses );

		if (  !invalid_email_address ) {
			//success
			var data = {};
			data.action = 'set_forwarding_addresses';
			data.email_addresses = email_addresses;

			jQuery.post(ajaxurl, data, function(response, textStatus, xhr) {
				//optional stuff to do after success
				if (response == 1) {
					forwarding_email_forwarding_validation('Forwarding email addresses successfully updated');
				} else {
					forwarding_email_forwarding_validation('There was an error while trying to save your request. Please wait a moment and try again.');
				}
			});
		} else {
			forwarding_email_forwarding_validation(invalid_email_address + ' is not a valid email. Please correct it and resubmit.');
		}
	});

	//
	function are_any_email_addresses_invalid ( email_addresses ) {

		var email_array = email_addresses.split(',');
		var invalid_email_address = false;

		if (email_array.length > 0) {
			for (var i = 0; i < email_array.length; i++) {
				if (validate_email_address(email_array[i]) ) {
					invalid_email_address = email_array[i];
				}
			}

			if (!invalid_email_address) {
				return false;
			} else {
				return invalid_email_address;
			}
		}
	}

	function forwarding_email_forwarding_validation (message, type) {

		jQuery('.pls-loading-overlay').hide();
		
		var color = 'green';
		if (type == 'error') { color = 'red' };

		jQuery('#email_validation').html('')
		jQuery('#email_validation').html(message).css('color', color).show();

		setTimeout(function () {
			jQuery('#email_validation').hide();			
		}, 3000)
	}



});
