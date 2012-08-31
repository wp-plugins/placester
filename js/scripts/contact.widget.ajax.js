jQuery(document).ready(function($) {

  $(".side-ctnr.placester_contact form input#firstName").focusout( function() {
    if(this.value == '') {
      this.value = 'First Name';
    }
  });
  $('.side-ctnr.placester_contact form input[name="firstName"]').focus( function() {
    if(this.value == 'First Name') {
      this.value = '';
    }
  });

  $(".side-ctnr.placester_contact form input#lastName").focusout( function() {
    if(this.value == '') {
      this.value = 'Last Name';
    }
  });
  $('.side-ctnr.placester_contact form input[name="lastName"]').focus( function() {
    if(this.value == 'Last Name') {
      this.value = '';
    }
  });

  $(".side-ctnr.placester_contact form input#email").focusout( function() {

    if(this.value == '') {
      this.value = 'Email Address';
    }
  });
  $('.side-ctnr.placester_contact form input[type="email"]').focus( function() {
    if(this.value == 'Email Address') {
      this.value = '';
    }
  });

  $(".side-ctnr.placester_contact form input#email").focusout( function() {

    if(this.value == '') {
      this.value = 'Email Address';
    }
  });
  $('.side-ctnr.placester_contact form input#phone').focus( function() {
    if(this.value == 'Phone Number') {
      this.value = '';
    }
  });

   var widget = jQuery('.side-ctnr.placester_contact');
	jQuery('.side-ctnr.placester_contact form').submit(function(e) {
        $this = jQuery(this);
        e.preventDefault();

        widget.find('.placester_loading').show();

		var str = jQuery(this).serialize();
        
        var clear_form = function(form) {
            form.find('input[type="text"], input[type="email"], textarea').val('');
        }
        
		jQuery.ajax({
			type: 'POST',
            url: info.ajaxurl,
			data: 'action=placester_contact&' + str,
			success: function(msg) {
					if(msg === 'sent') {
						// jQuery(this).append(success_msg);
                        widget.find('.placester_loading').fadeOut('fast');
						widget.find('.msg')
                            .html('Thank you for the email. We\'ll get back to you shortly.')
                            .removeClass('error')
                            .addClass('success')
                            .fadeIn('slow');
                            // .delay(2000)
                            // .fadeOut('slow')
                            // .removeClass('success');
                        clear_form($this);
					}
					else {
                        widget.find('.placester_loading').hide();
						widget.find('.msg')
                            .html(msg)
                            .removeClass('success')
                            .addClass('error')
                            .fadeIn('slow');
					}
			}
		});

	});
});
