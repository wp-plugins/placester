/**
 * Form Validation used from jquerytools.org
 * 
 */

jQuery(document).ready(function($) {

    var widget = jQuery('.side-ctnr.placester_contact');

    var clear_form = function(form) {
        form.find('input[type="text"], input[type="email"], input[type="phone"], textarea').val('');
    };
    
    // Validating on mousedown to beat Chrome to validation
    $('.side-ctnr.placester_contact form input[type="submit"]').on('mousedown', function() {
      
        var this_form = $(this).parent('.side-ctnr.placester_contact form');
        
        // get fields that are required from form and execture validator()
        var inputs = $(this_form).find("input[required], textarea[required]").validator({
            messageClass: 'contact-form-validator-error', 
            offset: [10,0],
            message: "<div><span></span></div>",
            position: 'top center'
          });
        
        // check required field's validity
        inputs.data("validator").checkValidity();
        
    });

    // Submit
    $('.side-ctnr.placester_contact form').submit(function(event) {
      
      $this = jQuery(this);
      var str = jQuery(this).serialize();
      
      // Check for invalid fields. This is needed because autofill will allow the form to submit
      if ($('.invalid', this).length) {
        return false;
      };
      
      // Show loading
      widget.find('.pls-contact-form-loading').show();
      
      event.preventDefault ? event.preventDefault() : event.returnValue = false;
      
      // Set Cookie
      jQuery.cookies.set('lead_capture_visitor', 1); // no expiration set
      
      // If we get this far, send the contact form along!
      jQuery.ajax({
          type: 'POST',
          url: info.ajaxurl,
          data: 'action=placester_contact&' + str,
          success: function(msg) {
              if(msg === 'sent') { // Success!
                  // hide spinner
                  widget.find('.pls-contact-form-loading').fadeOut('fast');
                  
                  // Add success treatments to all contact forms on page, not just 'this' one
                  $('.side-ctnr.placester_contact form').addClass('form_submitted');
                  $('.side-ctnr.placester_contact form').find('input[type="submit"]').val('Sent!');
                  clear_form($('.side-ctnr.placester_contact form'));
                  // remove all form errors and slide up the form
                  $('.contact-form-validator-error').remove();
                  $('.side-ctnr.placester_contact form').slideUp();
                  
                  // Show success message
                  setTimeout(function() {
                    $(".placester_contact .success").show('fast');
                    // mark contact form as submitted so lead capture's force-back functionality doesn't fire
                    $(".placester_contact input[name='form_submitted']").val(1);
                  },500);
                  
                  // if is in dialog box (lead capture), close it
                  if ($('.side-ctnr.placester_contact form').parents('.ui-dialog').length > 0) {
                    setTimeout(function() {
                      $('#property-details-lead-capture').dialog("close");
                    },2000);
                  }
                  
              } else { // Unsuccessful!
                  // hide spinner
                  widget.find('.pls-contact-form-loading').hide();
              }
          }
      });
    });
    
});
