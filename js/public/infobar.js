jQuery(document).ready(function($) {

	$('#toggle_demo').live('click', function(event) {
		event.preventDefault();
		// console.log("in here...");
	  	$.post(info.ajaxurl, {action: 'demo_data_off'}, function(response) {
            // Now that demo data has been turned off, reload the page to reflect the change...
            //
          	// Note that "parent" is used, in order to allow refreshing from inside iframes--this
          	// still works when in the main window, as its parent property is self-referential.
            window.parent.location.reload(true);
        },'json');
	});

	$('#infobar .msg .close').live('click', function() {
		$('#infobar, #infobar-buffer').css('display', 'none');
	});

});