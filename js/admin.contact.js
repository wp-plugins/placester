
/*
 * Handles image uploading
 */
jQuery(document).ready(function()
{
    jQuery('input[type=file]').change(function()
    {
        var id = jQuery(this).attr('id');
        id = id.substr(0, id.length - 5);
        jQuery(this).upload(
            'admin.php?page=placester_contact&ajax_action=upload', 
            function(res)
            {
                jQuery('#' + id + '_thumbnail').html(
                    '<img src="' + res.thumbnail + '" />');
                jQuery('#' + id).val(res.id);
            },
            'json');
    });
});
