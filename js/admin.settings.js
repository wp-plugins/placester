
/**
 * Links upload textboxes with jquery upload script
 */
jQuery(document).ready(function()
{
    jQuery('input[type=file]').change(function()
    {
        var id = jQuery(this).attr('id');
        id = id.substr(0, id.length - 5);
        jQuery(this).upload(
            'admin.php?page=placester_settings&ajax_action=upload', 
            function(res)
            {
                jQuery('#' + id + '_thumbnail').html(
                    '<img src="' + res.thumbnail + '" />');
                jQuery('#' + id).val(res.id);
            },
            'json');
    });
});



/**
 * tinymce stuff
 */
jQuery(document).ready(function($) {

    var id = 'placester_map_info_template';

    
    $('#'+id+'_toggleVisual').click(
        function() {
            tinyMCE.execCommand('mceAddControl', false, id);
        }
    );

    $('#'+id+'_toggleHTML').click(
        function() {
            tinyMCE.execCommand('mceRemoveControl', false, id);
        }
    );

    var id2 = 'placester_list_details_template';

    $('#'+id2+'_toggleVisual').click(
        function() {
            tinyMCE.execCommand('mceAddControl', false, id2);
        }
    );

    $('#'+id2+'_toggleHTML').click(
        function() {
            tinyMCE.execCommand('mceRemoveControl', false, id2);
        }
    );

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
