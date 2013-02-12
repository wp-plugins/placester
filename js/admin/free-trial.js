jQuery(document).ready(function($) {
	$( "#premium_wizard" ).dialog({
		autoOpen: false,
		draggable: false,
		modal: true,
		width: 700,
		zIndex: 600000
	});
});

function prompt_free_trial (title, success_callback, cancel_callback) {
	$ = jQuery;
	$( "#premium_wizard" ).dialog({title : '<h3>' + title + '</h3>'} );
	$( "#premium_wizard" ).dialog({close : cancel_callback} );
	$( "#premium_wizard" ).dialog( "open" );

	$('#premium_wizard_start').live('click', function(event) {
		event.preventDefault();
		var content_div = $(this).parents('.ui-dialog-content');
		$(content_div).html('<div class="ajax_message">Confirming Name and Phone have been entered....</div>');
		$.post(ajaxurl, {action: 'whoami'}, function(data, textStatus, xhr) {
			// console.log(data);
			if (data) {
				var collect_info_form = '';
				var needed_fields = '';
				if (data.user && data.user.first_name == '') {
					needed_fields += '<div class="prem_form"><label for="first_name">First Name</label><input id="first_name" type="text" name="first_name" value=""></div>';
				};
				if (data.user && data.user.last_name == '') {
					needed_fields += '<div class="prem_form"><label for="last_name">Last Name</label><input id="last_name" type="text" name="last_name" value=""></div>';
				};
				if (data.user && data.user.phone == '') {
					var entered_phone = ( $('#pls_integration_form #phone').length == 0 ) ? '' : $('#pls_integration_form #phone').val();
					needed_fields += '<div class="prem_form"><label for="phone">Phone Number</label><input id="phone" type="text" name="phone" value="' + entered_phone + '"></div>';
				};
				if (needed_fields != '') {
            $("#premium_wizard").dialog({
              buttons: [
                {
                  text: "Save & Get Started",
                  class: "green-btn right-btn",
                  click: function () { check_info_for_prem_trial(this, success_callback, cancel_callback); }
                }
              ]
            });
            collect_info_form += '<h2>Almost done!</h2>';
            collect_info_form += '<p class="modal-subtitle">Before we get started we need to collect the following information:</p>';
            collect_info_form += '<div id="prem_form_messages"></div><form id="premium_name_phone">' + needed_fields + '</form>';
            $(content_div).html(collect_info_form);
        } else {
          $(content_div).html('<div class="ajax_message">Name and Phone confirmed. Starting 15 day free trial...</div>');
          start_free_trial(success_callback, cancel_callback);
          // console.log("Would have started free trial!!! #1");
          // cancel_callback();
				};
			} else {
				$(content_div).html('<div><p>There was an error. That stinks because we\'re excited to work together. Give us a ring at (800) 728-8391 or shoot us an email at <a mailto="support@placester.com">support@placester.com</a> and we\'ll get you set up.</p></div>');
			};
		}, 'json');
	});
}

function check_info_for_prem_trial(that, success_callback, cancel_callback) {
  $ = jQuery;
  var form_values = {};
  $.each($('#premium_name_phone').serializeArray(), function(i, field) {
    form_values[field.name] = field.value;
  });
    
    $('#prem_form_messages').html('Updating account....');
    $('#prem_form_messages').removeClass('red');
    $("#premium_name_phone").children("input").removeClass("red");

    var error_messages = '';

    if (form_values['first_name'] == '' ) {
      error_messages += '<li class="first-name">First Name must be set.</li>';
      $("#premium_name_phone input#first_name").addClass("red");
    } else if (form_values['first_name'] && form_values['first_name'].length < 3) {
      $("#premium_name_phone input#first_name").addClass("red");
      error_messages += '<li class="first-name">First Name must be longer then 3 characters.</li>';
    };

    if (form_values['last_name'] == '' ) {
      error_messages += '<li class="last-name">Last Name must be set!</li>';
      $("#premium_name_phone input#last_name").addClass("red");
    } else if (form_values['last_name'] && form_values['last_name'].length < 3) {
      $("#premium_name_phone input#last_name").addClass("red");
      error_messages += '<li class="last-name">Last Name must be longer then 3 characters.</li>';
    };

    if (form_values['phone'] == '' ) {
      error_messages += '<li class="phone">Phone must be set!</li>';
      $("#premium_name_phone input#phone").addClass("red");
    } else if (form_values['phone'] && form_values['phone'].length < 6) {
      error_messages += '<li class="phone">Phone must be 7 or more characters.</li>';
    };

    
    if (error_messages != '') {
        error_messages = '<ul id="error-messages">' + error_messages;
        error_messages += '</ul>';
        $('#prem_form_messages').html(error_messages);
        $('#prem_form_messages').addClass('red');
    } else {
        form_values['action'] = 'update_user';
        $.post(ajaxurl, form_values, function(data, textStatus, xhr) {
          if (data && data.result) {
            // console.log(data);
            $('#prem_form_messages').html(data.message);
            start_free_trial(success_callback, cancel_callback);
            // console.log("Would have started free trial!!! #2");
            // cancel_callback();
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
            $('#prem_form_messages').html('<div id="message" class="error"><h3>'+ data['message'] + '</h3><ul>' + item_messages.join(' ') + '</ul></div>');	
          };
        }, 'json');

    };

}

function start_free_trial (success_callback, cancel_callback) {
	$.post(ajaxurl, {action: "start_subscription_trial"}, function(data, textStatus, xhr) {
		if (data && data["plan"] && data["plan"] == 'pro') {
			$( "#premium_wizard" ).dialog( "close" );
			success_callback();
		} else {
			$( "#premium_wizard" ).dialog( "close" );
			cancel_callback();
		};
	}, "json");
}