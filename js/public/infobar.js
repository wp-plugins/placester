jQuery(document).ready(function($) {

	$('#toggle_demo').live('click', function(event) {
		event.preventDefault();
		// console.log("in here...");
	  	$.post(info.ajaxurl, {action: 'demo_data_off'}, function(response) {
            // Now that demo data has been turned off, reload the page to reflect the change...
            location.reload(true);
        },'json');
	});

	$('#infobar .msg .close').live('click', function() {
		$('#infobar').css('display', 'none');
	});

});