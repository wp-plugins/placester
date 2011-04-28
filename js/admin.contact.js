
/*
 * Handles image uploading
 */
jQuery(document).ready(function()
{
    jQuery('.file_upload').click(function()
    {
        var id = jQuery(this).attr('id');
        id = id.substr(0, id.length - 7);
        jQuery('#' + id + '_file').upload(
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
