jQuery(document).ready(function($) {

	function validate_phone (number) {
		// Check for blank input...
		if (!number) { 
			return false;
		}

		// All tests passed...
		return true;
	}

	$('#pls_integration_form').live('submit', function(event) {
		event.preventDefault();

		var refresh_page = function () {
			setTimeout(function () {
				window.location.href = window.location.href;
			}, 500);
		}

		submit_handler(refresh_page);	
	});

	$('#customize_integration_submit').live('click', function() {
		var clear_form = function () {
			// In case this is visible...
			$('#message.error').remove();

			// Clear form values... 
			$.each($('#pls_integration_form').find('input, select'), function (i, elem) {
				$(elem).val('');
			});	
		}

		submit_handler(clear_form);
	});

	function submit_handler (success_callback) {
		$('#rets_form_message').removeClass('red');
		$('#message.error').remove();

		$.each($('#pls_integration_form .invalid'), function(i, elem) {
			$(elem).removeClass('invalid');
		});

		$('#rets_form_message').html('Checking Account Status...');

		// Check to see if phone number input exists--if it exists and has invalid input, act accordingly...
		if ( $('#phone').length != 0 && !validate_phone($('#phone').val()) ) {
			$('#phone').addClass('invalid');
			$('#phone').closest('div .row').find('h3').first().addClass('invalid');

			$('#rets_form_message').html('');
			$('#pls_integration_form').prepend('<div id="message" class="error"><h3>Please enter a valid phone number</h3></div>');
			return;
		}

		$.post(ajaxurl, {action: 'subscriptions'}, function(data, textStatus, xhr) {
		  // console.log(data);
		  if (data && data.plan && data.plan == 'pro') {
		  	check_mls_credentials(success_callback);
		  } else if (data && data.eligible_for_trial) {
		  	// console.log('prompt free trial');
		  	var success_handler = function () { check_mls_credentials(success_callback); }
		  	prompt_free_trial('Start your 15 day free trial to complete the MLS integration', success_handler, display_cancel_message);
		  } else {
		  	// console.log('not eligible');
		  	var msg = '<h3>Sorry, your account isn\'t eligible to link with an MLS.</h3>';
		  	msg += '<h3>Please <a href="https://placester.com/subscription">Upgrade Your Account</a> or call us with any questions at (800) 728-8391.</h3>';
		  	$('#rets_form_message').html('');
			$('#pls_integration_form').prepend('<div id="message" class="error">' + msg + '</div>');
		  };
		},'json');	
	}

	function check_mls_credentials (success_callback) {
		$('#rets_form_message').html('Checking RETS information...');
		
		var form_values = {action: 'create_integration'};
		var form_serialized = $('#pls_integration_form').serializeArray();
		
		if (form_serialized.length > 0) {
			$.each(form_serialized, function (i, field) {
	    		form_values[field.name] = field.value;
	        });
		}
		else { // Submitted from customizer...
			$.each($('#pls_integration_form').find('input, select'), function (i, elem) {
				form_values[$(elem).attr('name')] = $(elem).val();
			});		
		}

        // console.log(form_values);

		$.post(ajaxurl, form_values, function(data, textStatus, xhr) {
		  	// console.log(data);
		  	var form = $('#pls_integration_form');
			if (data && data.result) {
				$('#rets_form_message').html(data.message);
				if (success_callback) { success_callback(); }
			} else {
				var item_messages = [];
				for(var key in data['validations']) {
					var item = data['validations'][key];
					if (typeof item == 'object') {
						for( var k in item) {
							if (typeof item[k] == 'string') {
								var message = '<li class="red">' + data['human_names'][key] + ' ' + item[k] + '</li>';
							} else {
								var message = '<li class="red">' + data['human_names'][k] + ' ' + item[k].join(',') + '</li>';
							}
							$("#" + key + '-' + k).prepend(message);
							item_messages.push(message);
						}
					} else {
						var message = '<li class="red">'+item[key].join(',') + '</li>';
						$("#" + key).prepend(message);
						item_messages.push(message);
					}
				} 
				$(form).prepend('<div id="message" class="error"><h3>'+ data['message'] + '</h3><ul>' + item_messages.join(' ') + '</ul></div>');
				$('#rets_form_message').html('');
			};

		}, 'json');
	}

	function display_cancel_message () {
		$('#rets_form_message').html('');
		$('#pls_integration_form').prepend('<div id="message" class="error"><h3>Sorry, this feature requires a premium subscription</h3><p>However, you can test the MLS integration feature for free by creating a website at <a href="https://placester.com" target="_blank">placester.com</a></p></div>');
	}

	var integration_buttons = {
		1 : {
			text: "Skip Integration Set Up",
			click: function() {
				 $(this).dialog( "close" );
				 prompt_demo_data();
			}
		},
		2 : {
			text: "Submit",
			id: 'submit_integration_button',
			click: function() {
				 submit_handler(modal_state.demo_data_launch);
			}
		}
	}

	$( "#integration_wizard" ).dialog({
		autoOpen: false,
		draggable: false,
		modal: true,
		title: '<h3>Set Up an MLS Integration for your Website</h3>',
		width: 810,
		minHeight: 500,
		buttons: integration_buttons
	});
});

function prompt_integration () {
  jQuery(document).ready(function($) {
  	$('#integration_wizard').dialog( "open" );
  	// TODO: Add spinner/loading prompt...
	$.post(ajaxurl, {action:"new_integration_view"}, function (result) {
	  if (result) {
		// console.log(result);
		$('#integration_wizard').html(result);
	  };
	});
  });
}