/*
 * Global Definitions
 */

// Usually defined by WordPress, but not in the customizer...
var ajaxurl = ( window.location.origin ) ? window.location.origin : ( window.location.protocol + "//" + window.location.host );
ajaxurl += '/wp-admin/admin-ajax.php';



// This global variable must be defined in order to conditionally prevent iframes from being
// automatically "busted" when in the hosted environment... (see hosted-modifications plugin)
var customizer_global = {
	refreshing: true,
	previewLoaded: function () {
		// alert('Preview finished loading...');
		// jQuery('#customize-preview').removeClass('preview-load-indicator');
		jQuery('#customize-preview').fadeTo(1000, 1);
		jQuery('#preview_load_spinner').fadeTo(700, 0);

		// Set to let other components know that refresh has been completed...
		this.refreshing = false;
	},
	stateAltered: false
};

// The main form/sidebar is initially hidden so that the mangled-mess that exists before
// the DOM manipulation is completed is NOT shown to the user...
jQuery(window).load( function () {
	jQuery('#customize-controls').css('display', 'block');

	// If there's a theme arg in the query string, user just switched themes so make
	// sure to have the theme selection pane appear upon page load...
	if ( window.location.href.indexOf('theme_change=true') != -1 ) {
		jQuery('li#theme').trigger('click');
	}  
});

window.onbeforeunload = function () {
	if ( customizer_global.stateAltered ) {
		mixpanel.track("Customizer - Leaving with unsaved changes");
		return 'You have unsaved changes that will be lost!';
	}
}

// Generate AJAX spinner...
function newSpinner (id) {
	var attrID = id ? id : 'spinner';
	var spinnerElem = '<div id="' + attrID + '" class="spinningBars">'
					  + '<div class="bar1"></div>'
					  + '<div class="bar2"></div>'
					  + '<div class="bar3"></div>'
					  + '<div class="bar4"></div>' 
					  + '<div class="bar5"></div>'
					  + '<div class="bar6"></div>'
					  + '<div class="bar7"></div>'
					  + '<div class="bar8"></div>'
					  + '</div>';

	return spinnerElem;				   
}

/*
 * Main JS
 */

jQuery(document).ready(function($) {

 /*
  * Using JS, give the customizer a "facelift" and structural re-org to create
  * the "Placester" version...
  */

	// Hide the "You are Previewing" div + header & footer--no hook to prevent these from
	// rendering, so this is the only way to hide w/out altering core...
	$('#customize-info').remove();
	$('#customize-header-actions').remove();
	$('#customize-footer-actions').remove();

	$('div.wp-full-overlay').attr('id', 'full-overlay');
	$('div.wp-full-overlay-sidebar-content').removeClass('wp-full-overlay-sidebar-content').attr('id', 'sidebar');
	
	$('#customize-theme-controls').first().attr('id', 'menu-nav');
	$('#menu-nav > ul').first().attr('id', 'navlist');
	$('#menu-nav').after('<section id="pane"></section>');

	var controlDivs = $('.control-container').detach();
	controlDivs.appendTo('#pane');

	$('#customize-controls').append('<input type="submit" name="save" id="save" style="display:none">');


 /*
  * Trigger preview re-load + display loading overlay for input changes...
  */

	function setPreviewLoading() {
		if ( !customizer_global.refreshing ) {
		  	$('#customize-preview').fadeTo(800, 0.3);
			$('#preview_load_spinner').fadeTo(700, 1);

			customizer_global.refreshing = true;
		}  
	}

	function refreshPreview() {
		var ctrl = $('#customize-control-pls-google-analytics_ctrl input[type=text]');
		var currVal = ctrl.val()
		var newVal = currVal + '3';
		
		// We need to change the control value AND trigger the keyup even in succession...
		ctrl.val(newVal);
		ctrl.trigger('keyup');

		// Change back to existing value...
		ctrl.val(currVal);
		ctrl.trigger('keyup');

		setPreviewLoading();
	}

	// NOTE: Uncomment this for testing purposes...
	// refPrev = refreshPreview;

	$('[data-customize-setting-link]').on('keyup change', function (event) { 
		if ( !customizer_global.stateAltered ) {
			var conf = $('#confirm');
			conf.fadeTo(600, 1, function() {
				conf.fadeTo(600, 0.3, function() {
						conf.fadeTo(600, 1);
				});
			});

			customizer_global.stateAltered = true;
		}
	});


 /*
  * Bind customizer menu actions...
  */

	$('#hide_pane, #logo').on('click', function (event) {
		event.preventDefault();
		
		$('#pane').css('display', 'none');
		$('.control-container').css('display', 'none');

		// Remove active class from any existing elements...
		var activeLi = $('#navlist li.active');
		if ( activeLi.length > 0 ) {
			activeLi.each( function() { $(this).toggleClass('active'); } );

			//record the user closing the pane.
			mixpanel.track("Customizer - Pane Closed", {'theme' : $('#theme_choices').val() });
		}
	});

	$('#navlist li:not(.no-pane)').on('click', function (event) {
		event.preventDefault();

		// Pass pane opened event to mixpanel
		mixpanel.track("Customizer - Pane Opened", {'type' : $(this).attr('id'), 'theme' : $('#theme_choices').val() });

		// If activated menu section is clicked OR preview is refreshing/loading, do nothing...
		if ( $(this).hasClass('active') || customizer_global.refreshing ) { return; }

		// Remove active class from any existing elements...
		var activeLi = $('#navlist li.active');
		if ( activeLi.length > 0 ) {
			activeLi.each( function() { $(this).toggleClass('active'); } );
		}

		// Set the current menu item to 'active'
		$(this).toggleClass('active');

		// Make sure pane is visible, then hide any visible control-container(s)...
		$('#pane').css('display', 'block');
		$('.control-container').css('display', 'none');
		
		// Construct the associated control-container's id and show it...
		var containerId = '#' + $(this).attr('id') + '_content';
		$(containerId).css('display', 'block');

		// $(containerId).show('slide', { direction: 'left'}, 1000);
		// $('#pane').show("slide", { direction: "left" }, 1000);
	});

	$('#confirm').on('click', function (event) {
		event.preventDefault();
		if ( !customizer_global.stateAltered ) { return; }

		setPreviewLoading();
		$('#save').trigger('click');
		
		// Set this back to false so that user won't be prompted about "losing changes" 
		// when re-directing back to homepage...
		customizer_global.stateAltered = false;

		mixpanel.track("Customizer - Saved");
		
		var home_url = ( window.location.origin ) ? window.location.origin : ( window.location.protocol + "//" + window.location.host );

		setTimeout( function () { window.location.href = home_url; }, 1200 ); 
	});

	$('.control-container label').on('click', function (event) {
		$(this).next('input, textarea').focus();
	});


 /*
  * Handles integration pane...
  */

  	$('#customize_integration_no').on('click', function() { 
  		$('#logo').trigger('click');
  	});

	$('#customize_integration_submit').on('click', function() {

		$.post(ajaxurl, {action: "start_subscription_trial", source: "wci"}, function (result) {
			// Instrument...
			mixpanel.track("Registration - Trial Started",  {'source' : 'Customizer'});
		}, "json");

		// Show the phone number section.
		$('#customizer_mls_phone_section').show();
		$('#customizer_mls_request_buttons').hide();
	});

	$('#customize_integration_phone_submit').on('click', function () {

		// In case this is visible...
		$('#message.error').remove();

		var phone_number = $('#pls_integration_form #phone').val();
		var valid = validate_phone_number(phone_number);
		var is_blank = (phone_number.length == 0);

		// Functionality specifically for when the user enters a valid phone number...
		if (valid) {
			// Instrument...
			mixpanel.track("Customizer - Phone - Submitted");

			// Update user's account with phone number in Rails...
			$.post(ajaxurl, {action: 'update_user', phone: phone_number}, function (result) { phone_success(); }, "json");
		} else {
			// Entered number is invalid!
			var msg = "Please enter a valid phone number (or click 'No Thanks')";
			$('#custmizer_mls_phone_validation').prepend('<div id="message" class="error"><h3>' + msg + '</h3></div>');
			$('#pls_integration_form #phone').addClass('invalid');
		}

	});

	function phone_success() {
		mixpanel.track("Customizer - Phone - Saved");
		// Show integration video + hide the form...
		
		$('#mls_submitted').show();
		$('#pls_integration_form').hide();
		$('#mls_content h1').html('Congratulations!');
		$('#mls_content h3').html('IDX / MLS Request Submitted');
		$(this).hide();

		// Set completion flag so this screen doesn't appear again...
		$.post(ajaxurl, {action: 'idx_prompt_completed', mark_completed: true}, function (result) { }, "json");
	}


 /*
  * Handles theme selection...
  */

  	// Logic to determine whether to hide or show pagination buttons based on change...
	function paginationHideShow(oldIdx, newIdx, maxIdx) {
		var prev = $('#pagination a.prev');
		var next = $('#pagination a.next');
		
		// Handle previous...
		if ( oldIdx == 0) { prev.css('visibility', 'visible'); } 
		else if ( newIdx == 0 ) { prev.css('visibility', 'hidden'); }
		else { /* No action necessary...*/ }

		// Handle next...
		if ( oldIdx == maxIdx ) { next.css('visibility', 'visible'); }
		else if ( newIdx == maxIdx ) { next.css('visibility', 'hidden'); }
		else { /* No action necessary...*/ }				
	}

	function initPagination() {
		var themeSelect = $('#theme_choices');
		if ( themeSelect.length > 0 ) {
			var newInd = themeSelect.get(0).selectedIndex; // Current index is "new" index when initially setting this...
			var maxInd = ( themeSelect.get(0).options.length - 1 );
			paginationHideShow( -1, newInd, maxInd ); // "old" index is set to -1 so it's value won't cause any changes...
		}
	}

	function activateTheme() {
		var data = { action: 'change_theme', new_theme: $('#theme_choices').val() };

		// Show spinner to indicate theme activation is in progress...
		var infoElem = $('#theme_info');
		infoElem.prepend(newSpinner());
		infoElem.css('opacity', '0.7');

		var submitElem = $('#submit_theme');
		submitElem.attr('disabled', 'disabled');
		submitElem.addClass('bt-disabled');

		//pass pane opened event to mixpanel
		mixpanel.track("Customizer - Theme Changed", {'theme' : $('#theme_choices').val() });

		$.post(ajaxurl, data, function (response) {
	        if ( response && response.success ) {
	            // Reload customizer to display new theme...
	            var curr_href = window.location.href;
        
	           	if ( curr_href.indexOf('onboard=true') != -1 && curr_href.indexOf('theme_changed=true') == -1 ) {
	            	window.location.href = curr_href + '&theme_changed=true';
	            }
	            else {
	            	window.location.reload(true);
	            }
	        }
	        else {
	        	// If theme switch fails, hide progress so user can try again...
	        	var infoElem = $('#theme_info');
	        	infoElem.find('#spinner').remove();
	        	infoElem.css('opacity', '1');

				submitElem.removeAttr('disabled');
				submitElem.removeClass('bt-disabled');	        	
	        }
	    },'json');
	}

	function valPremTheme(container) {
		// Show spinner to indicate theme premium theme validation is in progress...
		var infoElem = $('#theme_info');
		infoElem.prepend(newSpinner());
		infoElem.css('opacity', '0.7');

		// Set success and failure callbacks...
		var success_callback = function () { activateTheme(); }
		var failure_callback = function () {
			// Construct error message...
			var msg = '<h3>Sorry, your account isn\'t eligible to use Premium themes.</h3>';
		  	msg += '<h3>Please <a href="https://placester.com/subscription">Upgrade Your Account</a> or call us with any questions at (800) 728-8391.</h3>';

			container.prepend('<div id="message" class="error">' + msg + '</div>');
		}

		// Check user's subscription status and act accordingly...
		$.post(ajaxurl, {action: 'subscriptions'}, function (response) {
		  // console.log(response);

		  // Regardless of the response, remove loading bar...
		  var infoElem = $('#theme_info');
    	  infoElem.find('#spinner').remove();
	      infoElem.css('opacity', '1');

		  if (response && response.plan && response.plan == 'pro') {
		  	success_callback();
		  } 
		  else if (response && response.eligible_for_trial) {
		  	// console.log('prompt free trial');
		  	prompt_free_trial('Start your 15 day Free Trial to Activate a Premium Theme', success_callback, failure_callback, 'wc');
		  } 
		  else {
		  	failure_callback();
		  };
		},'json');	
	}

	// On initial page load, hide/show the pagination buttons accordingly...
	initPagination();

	$('#theme_choices').on('change', function (event) {
		// Remove any latent error messages if they exist...
		$('#theme_content ul.control-list').find('#message.error').remove();

		// If theme selected is set to current one, set the submit button to disabled, otherwise enable it
		var submitElem = $('#submit_theme');
		if ( _wpCustomizeSettings && _wpCustomizeSettings.theme.stylesheet == $(this).val() ) {
			submitElem.attr('disabled', 'disabled');
			submitElem.addClass('bt-disabled');
		}
		else {
		// Might not be necessary--done to handle all cases properly
			submitElem.removeAttr('disabled');
			submitElem.removeClass('bt-disabled');
		}

		var infoElem = $('#theme_info');
		infoElem.prepend(newSpinner());
		infoElem.css('opacity', '0.7');

		data = { action: 'load_theme_info', theme: $(this).val() };
		
		// console.log(data);
		// return;

		$.post(ajaxurl, data, function (response) {
	        if ( response && response.theme_info ) {
	            // Populate theme info with new html...
	            infoElem.html(response.theme_info);
	            infoElem.css('opacity', '1');
	        
	            // Reset pagination button(s) to match newly selected theme...
			    $('#pagination a').css('visibility', 'visible');
			    initPagination();
	        }
	    },'json');

	});

	$('#submit_theme').on('click', function (event) {
		var container = $('#theme_content ul.control-list');

		// Remove any latent error messages if they exist...
		container.find('#message.error').remove();

		// Check if user is trying to activate a Premium theme, and act accordingly...
		var type = $('option:selected').parent().attr('label');
		if ( type === 'Premium' ) { 
			valPremTheme(container); 
		}
		else {
			activateTheme();
		}
	});

	// Handles "Previous" and "Next" pagination buttons...
	$('#pagination a').on('click', function (event) {
		event.preventDefault();

		var type = $(this).attr('class');
		var selectElem = $('#theme_choices').get(0);
		var maxIndex = (selectElem.options.length - 1);
		var currIndex = selectElem.selectedIndex;
		var newIndex;

		// Handle each type accordingly
		if ( type === 'prev' ) {
			newIndex = (currIndex - 1);
		}
		else if ( type == 'next' ) {
			newIndex = (currIndex + 1);
		}
		else {
			console.log('Pagination button of type "' + type + '"not handled');
			return;
		}

		// Validate new index
		if ( newIndex < 0 || newIndex > maxIndex ) { 
			console.log('Index out of bounds...reverting'); 
			return;
		}

		// Set selected theme to new index... 
		selectElem.selectedIndex = newIndex;
		$('#theme_choices').trigger('change');
	});

	// Ensures that saving a new theme in the customizer does NOT cause a redirect...
	if (_wpCustomizeSettings) {
		var boolSuccess = delete _wpCustomizeSettings.url.activated;
		// console.log('redirect deleted: ' + boolSuccess);
	}


 /*
  * Handle custom controls...
  */	

  	/* --- Blog Post --- */
/*
	function toggleInvalid (item, invalid) {
        if (invalid) {
		  	item.addClass('invalid');
		  	item.prev().addClass('invalid');
		}
		else {
			item.removeClass('invalid');
			item.prev().removeClass('invalid');
		}
	}

  	$('#submit_blogpost').on('click', function (event) {
  		var title = $('#blogpost_title');
  		var content = $('#blogpost_content');
  		var tags = $('#blogpost_tags');

  		if ( !title.val() || !content.val()  ) {
  			$('#blogpost_message').show();

  			if ( !title.val() ) { toggleInvalid(title, true); }
			if ( !content.val() ) { toggleInvalid(content, true); }  			

  			return;
  		}
  		else {
  			$('#blogpost_message').hide();

  			toggleInvalid(title, false);
  			toggleInvalid(content, false);
  		}

  		var data = {
    	  	action: 'publish_post',
	        title: title.val(),
	        content: content.val(),
	        tags: tags.val()
	    };

	    // console.log(data);
	    // return;

	    $.post(ajaxurl, data, function (response) {
	        if ( response && response.new_post_id ) {
	        	alert('Post created successfully!');
	            // console.log(response.new_post_id);
	            setTimeout( function () { refreshPreview(); }, 300 );

	            // Reset blog post form fields...
	            title.val('');
	            content.val('');
	            tags.val('');
	        }
	    },'json');
  	});
*/
	
	/* --- Create a Listing --- */

/*
  	$('#submit_listing').on('click', function (event) {
		// $('#loading_overlay').show();

       	// Hide all previous validation issues
       	$('#listing_message').hide();

       	// Prep form values for submission
        var form_values = {}
        form_values['action'] = 'add_listing';
        
        // Get each of the form values, set key/values in array based off name attribute
        $.each($('#create_listing :input').serializeArray(), function (i, field) {
    		form_values[field.name] = field.value;
        });
       

        // console.log(form_values); 
        // return;
        
        $.post(ajaxurl, form_values, function (response) {
			// $('#loading_overlay').hide();
			if (response && response['validations']) {
				var item_messages = [];

				for (var key in response['validations']) 
				{
					var item = response['validations'][key];

					if (typeof item == 'object') {
						for (var k in item) {
							if (typeof item[k] == 'string') {
								var message = '<p class="red">' + response['human_names'][key] + ' ' + item[k] + '</p>';
							} else {
								var message = '<p class="red">' + response['human_names'][k] + ' ' + item[k].join(',') + '</p>';
							}
							// $("#" + key + '-' + k).prepend(message);
							item_messages.push(message);
						}
					} 
					else {
						var message = '<p class="red">'+item[key].join(',') + '</p>';
						// $("#" + key).prepend(message);
						item_messages.push(message);
					}
				} 

				// Populate and show error messages...
				$('#listing_message').html( '<h3>' + response['message'] + '</h3>' + item_messages.join(' ') );
				$('#listing_message').show();
			} 
			else if (response && response['id']) {
				alert('Listing successfully created!');
				setTimeout( function () { refreshPreview(); }, 1200 ); 
				// $('#manage_listing_message').html('<div id="message" class="updated below-h2"><p>Listing successfully created!</p></div>');
			}
		}, 'json');
    });
*/

	/* -- Custom CSS -- */

	function initCustomCSS () {
		var ctrl = $('#customize-control-pls-custom-css_ctrl'); 

		// Hide the theme customizer control that actually connects to theme option...
		ctrl.hide();

		// Copy the initial custom css to the viewable "Edit Custom CSS" textarea...
		var css = ctrl.find('textarea').val();
		$('#custom_css').val(css);
	}
	// Call on initial customizer load...
	initCustomCSS();

	function updateCustomCSS (css) {
		var custom_css = $('#customize-control-pls-custom-css_ctrl textarea');

		// Handle case where fetched CSS equals what's currently in the input (i.e., won't trigger preview refresh)
		if (custom_css.val() == css) {
			// console.log('Same-sies!!!');
			customizer_global.previewLoaded();
			return;
		}

		custom_css.val(css);
		custom_css.trigger('keyup');
	}

	$('#color_select').on('change', function (event) {
		// Check for wp JS object (we need this to update styling) and for current theme support--exit if either are false...
		var supportedThemeList = ['columbus','ventura','tampa','highland','manchester','bluestone','slate','ontario','charlotte','toronto','parkcity'];
		if (!_wpCustomizeSettings || supportedThemeList.indexOf(_wpCustomizeSettings.theme.stylesheet) == -1 ) {
			var errMsg = $('#color_message.error');
			errMsg.html('<h3>Sorry, this feature is currently not available for this theme</h3>');
			errMsg.show();

			return;
		}

		// Just in case...
		$('#color_message.error').hide();

		// Check for "none"...
		if ($(this).val() == 'none') { return; }	

		// Let the user know there's work being done...
		setPreviewLoading();

		// Check for default
		if ($(this).val() == 'default') {
			updateCustomCSS('');
			return;
		}

		// Construct request to fetch styles...
		var data = {
    	  	action: 'load_custom_styles',
	        color: $(this).val()
	    };

	    // console.log(data);
	    // return;

	    $.post(ajaxurl, data, function (response) {
	    	// console.log(response);
	    	if (response && response.styles) {

				//pass pane opened event to mixpanel
				mixpanel.track("Customizer - Color Changed", {'theme' : $('#theme_choices').val(), 'color' : data.color });

	    		// Change the linked CSS textarea to trigger an update of the preview pane...
	    		updateCustomCSS(response.styles);

	    		// Change visible CSS textarea editor to reflect update...
				$('#custom_css').val(response.styles);
	    	}
	    },'json');
	});

	$('#toggle_css_edit').on('click', function (event) {
		event.preventDefault();
		// console.log('clicked!');

		var show_txt = '[+] Show'
		var hide_txt = '[\u2013] Hide';
		
		var jThis = $(this);
		var editDiv = $('#css_edit_container');

		if ( jThis.text() == show_txt ) {
			jThis.text(hide_txt);
			editDiv.show();
		}
		else {
			jThis.text(show_txt);
			editDiv.hide();
		}
	});

	$('#submit_custom_css').on('click', function (event) {
		var new_css = $('#custom_css').val();

		setPreviewLoading();
		updateCustomCSS(new_css);
	});

});	


