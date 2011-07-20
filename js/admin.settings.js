
/**
 * Links upload textboxes with jquery upload script
 */
jQuery(document).ready(function()
{
    jQuery('.file_upload').click(function()
    {
        var id = jQuery(this).attr('id');
        id = id.substr(0, id.length - 7);
        jQuery('#' + id + '_file').upload(
            'admin.php?page=placester_settings&ajax_action=upload', 
            function(res)
            {
                jQuery('#' + id + '_thumbnail').html(
                    '<img src="' + res.thumbnail + '" />');
                jQuery('#' + id).val(res.id);
            },
            'json');
    });
   
   jQuery("#refresh_user_data")
        .click(function(e) {
            var answer = confirm("This will reset all you information. Do you want to continue?")
            if (!answer) {
                e.preventDefault();
            }
        });

});



/**
 * tinymce stuff
 */
jQuery(document).ready(function($) {

    var id = 'placester_map_info_template';
    jQuery('#'+id+'_toggleVisual').click(function() {
            tinyMCE.execCommand('mceAddControl', false, id);
    });
    jQuery('#'+id+'_toggleHTML').click(function() {
            tinyMCE.execCommand('mceRemoveControl', false, id);
    });

    var id2 = 'placester_list_details_template';
    jQuery('#'+id2+'_toggleVisual').click(function() {
            tinyMCE.execCommand('mceAddControl', false, id2);
    }); 
    jQuery('#'+id2+'_toggleHTML').click(function() {
            tinyMCE.execCommand('mceRemoveControl', false, id2);
    });

    var id3 = 'placester_snippet_layout';
    jQuery('#'+id3+'_toggleVisual').click(function() {
            tinyMCE.execCommand('mceAddControl', false, id3);
    }); 
    jQuery('#'+id3+'_toggleHTML').click(function() {
            tinyMCE.execCommand('mceRemoveControl', false, id3);
    });

    var id4 = 'placester_listing_layout';
    jQuery('#'+id4+'_toggleVisual').click(function() {
            tinyMCE.execCommand('mceAddControl', false, id4);
    }); 
    jQuery('#'+id4+'_toggleHTML').click(function() {
            tinyMCE.execCommand('mceRemoveControl', false, id4);
    });

});



/**
 * Reaction to entering post slug 
 */
jQuery(document).ready(function($) {

   if ($('#placester_url_slug').val().length <= 0) {
       $('#url_target').html('SOMETHING');
   } else {
       $('#url_target').html($('#placester_url_slug').val());
   };
   
    $('#placester_url_slug').keyup( function () {
       $('#url_target').html($('#placester_url_slug').val()); 
    });

});
