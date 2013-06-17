jQuery(document).ready(function($) {

	var new_buttons = {
        1 : {
            text: "Don't activate yet",
            class: "linkify-button",
            click: function() {
                $(this).dialog("close");
            }
        },  
        2 : {
            text: "I already have an account",
            class: "linkify-button",
            click: function() {
             	construct_modal(existing_acct_args);
            }
        },
        3 : {
            text: "Confirm Email",
            class: "green-btn right-btn",
            click: function() {
            	// Instrument...
            	mixpanel.track("Registration - Submitted");

                new_sign_up(function () { 
                	construct_modal(idx_args); 
                	$(this).dialog("close");
                });
            }
        }
    };

	var existing_buttons = {
		1 : {
			text: "Don't Activate Yet",
			class: "linkify-button",
			click: function() {
				 $(this).dialog("close");
			}
		},
		2 : {
			text: "Create a New Account",
			class: "linkify-button",
			classes: "left",
			click: function() {
				 construct_modal(new_acct_args);
			}
		},
		3 : {
			text: "Confirm",
			class: "green-btn right-btn",
			click: function() {
				var api_key = $('#existing_placester_modal_api_key').val();
				check_api_key(api_key);
			}
		}
	};

	var idx_buttons = {
        1 : {
            text: "No thanks",
            class: "linkify-button no-thanks-idx-btn",
            click: function() {
				// Remove current dialog area, add "Add Listings Manually" dialog area.
				$('#idx-add-inner').addClass('hide');
				$('#idx-none-inner').removeClass('hide');
				
				// Hide buttons, show new buttons
				$('.yes-idx-btn, .no-thanks-idx-btn').addClass('hide');
				$('.add-listings-manually-btn').removeClass('hide');

				// Change title
				$('.ui-dialog-title h3').html("Add Listings to your Website Manually");
				$(".ui-dialog-title").parent().parent().css("top", 90);
            }
        },
        2 : {
            text: "Yes",
            class: "yes-idx-btn right-btn green-btn",
            click: function() {
				// Remove current dialog area, add phone # dialog area.
				$('#idx-add-inner').addClass('hide');
				$('#idx-contact-inner').removeClass('hide');
				
				// Hide buttons, show new buttons
				$('.yes-idx-btn, .no-thanks-idx-btn').addClass('hide');
				$('.i-prefer-email-btn, .call-me-btn').removeClass('hide');
				
				// Instrument...
				mixpanel.track("Registration - Integration Requested");

				// Start free trial...
				$.post(ajaxurl, {action: "start_subscription_trial", source: "wi"}, function (result) {
					// Instrument...
					mixpanel.track("Registration - Trial Started",  {'source' : 'Activation Modal'});
				}, "json");
            }            
        },  
        3 : {
            text: "All set!",
            class: "linkify-button hide add-listings-manually-btn right-btn",
            click: function() {
				// Direct to Add Listings page
				$(this).dialog("close");
				
				// Reload page to reflect the addition of an API key...
				setTimeout(function () { window.location.href = window.location.href; }, 1000);
            }
        }, 
        4 : {
            text: "I prefer email",
            class: "linkify-button hide i-prefer-email-btn",
            click: function() {
            	// Instrument...
            	mixpanel.track("Registration - MLS through Email");

				// remove current dialog
				$('#idx-contact-inner').addClass('hide');
				$('.ui-dialog-title h3').html("Congratulations! IDX / MLS Request Submitted");

				// Show email dialog
				$('#idx-success-inner span#action').text("email");
				$('#idx-success-inner').removeClass('hide');

				// Hide buttons, show new buttons
				$('.i-prefer-email-btn, .call-me-btn').addClass('hide');
				$('.request-done-btn').removeClass('hide');

				$(".ui-dialog-title").parent().parent().css("top", 90);
            }
        },
        5 : {
            text: "Please Call Me",
            class: "hide call-me-btn right-btn green-button green-btn",
            click: function() {
				// Check if number entered is valid...
				var phone_number = $("#callme-idx-phone").val();
				var valid = validate_phone_number(phone_number);

				if (valid) {
					// Instrument...
					mixpanel.track("Registration - Phone Number");

					$('.ui-dialog-title h3').html("Congratulations! IDX / MLS Request Submitted");

					// Valid Phone Number
					$('#idx-contact-inner').prepend("YEP!");

					// remove current dialog
					$('#idx-contact-inner').addClass('hide');
					
					// Show email dialog
					$('#idx-success-inner span#action').text("call");
					$('#idx-success-inner').removeClass('hide');

					// Hide buttons, show new buttons
					$('.i-prefer-email-btn, .call-me-btn').addClass('hide');
					$('.request-done-btn').removeClass('hide');
					$("#phone-validation-message").html('');
					
					// Move to top of the screen
					$(".ui-dialog-title").parent().parent().css("top", 90);

					// Update user's account with phone number in Rails...
					$.post(ajaxurl, {action: 'update_user', phone: phone_number}, function (result) {
						// console.log(result);
					}, "json");
				} 
				else {
					// Invalid Phone Number
					$("#callme-idx-phone").addClass('red');
					$("#phone-validation-message").html("Phone number is not valid");
				}
            }            
        },
        6 : {
            text: "All set!",
            class: "linkify-button hide request-done-btn right-btn",
            click: function() {
            	// Instrument...
            	mixpanel.track("Registration - Complete");

				// Marks flag that prevents similar IDX prompts elsewhere in the app from firing in the future...
				$.post(ajaxurl, {action: 'idx_prompt_completed', mark_completed: true}, function (result) { }, "json");

				// Reload page to reflect the addition of an API key...
				setTimeout(function () { window.location.href = window.location.href; }, 2000);            
			}            
		}
    };

	// Dialog config args...
	var new_acct_args = { ajax: 'new_api_key_view', title: 'Activate Your Plugin', buttons: new_buttons, width: 500 };
	var existing_acct_args = { ajax: 'existing_api_key_view', title: 'Use an Existing Placester Account', buttons: existing_buttons, width: 700 };
	var idx_args = { ajax: 'idx_prompt_view', title: 'Add IDX / MLS To My Website', buttons: idx_buttons, width: 500 };

	function construct_modal(args) {
		$.post(ajaxurl, {action: args.ajax}, function (result) {
			if (result) {
				$('#signup_wizard').html(result);
				$("#signup_wizard").dialog({
					autoOpen: true,
					draggable: false,
					modal: true,
					position: 'center',
					title: args.title,
					width: args.width,
					buttons: args.buttons
				});
			};
		});
	}

	// Create the sign-up wizard dialog container on initial page load...
	$('body').append('<div id="signup_wizard"></div>');
	construct_modal(new_acct_args);

	// Instrument...
	mixpanel.track("Registration - Opportunity", {'type' : 'Activation Modal'});

	// Prevent any clicks...
	$('.wrapper').on('click', function() {
		$("#signup_wizard").dialog("open");
	});
	
});