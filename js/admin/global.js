var modal_state = {
	integration_launch: function () {
		jQuery('#signup_wizard').dialog("close");
		prompt_integration();								
	},
	demo_data_launch: function () {
		jQuery('#integration_wizard').dialog( "close" );
		prompt_demo_data();
	}	
};

function parse_validation (response) {
	$ = jQuery; //we're in no conflict land. 
	if (response && response['validations']) {
		var item_messages = [];
		for(var key in response['validations']) {
			var item = response['validations'][key];
			if (typeof item == 'object') {
				for( var k in item) {
					if (typeof item[k] == 'string') {
						var message = '<span class="red">' + response['human_names'][key] + ' ' + item[k] + '</span>';
					} else {
						var message = '<span class="red">' + response['human_names'][k] + ' ' + item[k].join(',') + '</span>';
					}
					item_messages.push(message);
				}
			} else {
				var message = '<span class="red">'+item[key].join(',') + '</span>';
				item_messages.push(message);
			}
		}
		return item_messages;	
	}
}



function check_api_key (api_key) {
	$ = jQuery; //we're in no conflict land. 
	$('#api_key_message').hide();

	var data = {action : "set_placester_api_key",api_key: api_key};
	$('#api_key_message').removeClass('red');
	$('#api_key_message').html('Checking....').show().addClass('green');

	$.ajax({
		url: ajaxurl, //wordpress thing
		type: "POST",
		data: data,
		dataType: "json",
		success: function (response) {
			if (response && response.message) {
				if (response.result) {
					$('#api_key_message').html("You've successfully changed your Placester API Key.").show().removeClass('red').addClass('green');
					$('#api-key-message-icon').show().addClass('green');
          $('#api_key_form #existing_placester_modal_api_key').addClass('green');
          setTimeout(function () {
           window.location.href = window.location.href;
          }, 2000);
				} else {
					$('#api_key_message').html(response.message).show().removeClass('green').addClass('red');
					$('#api-key-message-icon').show().removeClass('green').addClass('red');
          $('#existing_placester_modal_api_key').removeClass('green').addClass('red');
				};
			};		
		}
	});
}

function new_sign_up(success_callback) {
	$ = jQuery; //we're in no conflict land. 
	var email = $('input#email').val();
	$('#api_key_success').html('Checking...').show();
	$('#api_key_validation').html('');
  $('#confirm_email input#email').removeClass('green').removeClass('red');

	$.post(ajaxurl, {action: 'create_account', email: email}, function(data, textStatus, xhr) {
		if (data) {	
      // console.log(data);
			if (data['validations']) {
				mixpanel.track("SignUp: Validation issue on signup");
				var message = parse_validation(data);
				$('#api_key_success').html('');
				$('#api_key_validation').html(message.join(', ')).show();
				$('#confirm_email input#email').removeClass('green').addClass('red');

			} else if(data['api_key']) {
				$('#api_key_success').html('Success! Setting up plugin.');
				mixpanel.track("SignUp: Successful Signup");
				$('#confirm_email input#email').removeClass('red').addClass('green');
        $.post(ajaxurl, {action: 'set_placester_api_key', api_key: data['api_key']}, function(response, textStatus, xhr) {
          if (response['result']) {
            var standard_success_message = "You've successfully changed your Placester API Key. This page will reload in momentarily.";
            if (response['message'] == standard_success_message) {
              $('#api_key_success').html("You've successfully changed your Placester API Key.").show();
            } else {
              $('#api_key_success').html(response['message']).show();
            }
            mixpanel.track("SignUp: API key installed");
            // reload screen
            setTimeout(function () {
             window.location.href = window.location.href;
            }, 2000);
            
           // API key was successfully created AND set, ok to move-on to the integration dialog...
           // if (success_callback) { success_callback(); }
         }
        },'json');
			};
		};
	},'json');
}

