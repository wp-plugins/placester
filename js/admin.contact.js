
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

    // Remove ZIP for Belize
    detached_zip_row = false>;
    $zip_row = jQuery('#company_location_zip').closest('tr');
    if (jQuery('#company_location_country').val() == "BZ") {
        $zip_row = $zip_row.detach();
        detached_zip_row = true;
    }
    jQuery('#company_location_country').change(function(){
        if ($(this).val() == "BZ") {
            $zip_row = $zip_row.detach();
            detached_zip_row = true;
        } else if (detached_zip_row) {
            $(this).closest('tr').after($zip_row);
            detached_zip_row = false;
        }

    });
});
