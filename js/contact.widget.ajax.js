jQuery(document).ready(function($) {
	jQuery('#placester_contact').submit(function() {
		jQuery('.widget_contact input[type="submit"]').hide();
		jQuery('.placester_loading').show();
		var str = jQuery(this).serialize();
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: 'action=placester_contact&'+str,
			success: function(msg) {
					if(msg === 'sent') {
						
						jQuery('#placester_msg').hide();
						jQuery('.placester_loading').slideUp();
						jQuery('.widget_contact input[type="submit"]').slideUp();
						jQuery('#placester_contact').slideUp();
						jQuery('#placester_success').fadeIn('slow');
					}
					else {
						jQuery('#placester_msg').html(msg);
						jQuery('.placester_loading').hide();
						jQuery('.widget_contact input[type="submit"]').show();
						jQuery('#placester_msg').fadeIn('slow');
					}
			}
		});
		return false;
	});
});