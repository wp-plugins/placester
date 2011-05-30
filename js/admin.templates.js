
var placester_template_edit_mode = false;

jQuery(document).ready(function()
{
    if (jQuery('#current_template_name').val().length <= 0)
       placester_set_active_template(false);


    jQuery('.template_item').click(function()
    {
        placester_template_edit_mode = false;
        jQuery('#edit_template').val('Edit Template');

        jQuery('#current_template_name').val(jQuery(this).attr('system_name'));
        placester_set_active_template(jQuery(this).attr('active') == '1');

        placester_load_iframe();
    });

    jQuery('#edit_template').click(function()
    {
        if (!placester_template_edit_mode)
        {
            placester_template_edit_mode = true;
            placester_load_iframe();
            jQuery('#edit_template').css({'display': 'none'});

            if (jQuery('#current_template_name').val().substr(0, 5) == 'user_')
                jQuery('#save_template_user_panel').css({'display': ''});
            else
                jQuery('#save_template_panel').css({'display': ''});
        }
    });

    jQuery('#save_template').click(function()
    {
        v = jQuery('#preview_iframe').contents().
            find('#textarea_content').val();
        jQuery('#save_thumbnail_url').val(jQuery('#preview_iframe').contents().
            find('#thumbnail_url').val());
        jQuery('#save_template_content').val(v);
    });
    jQuery('#save_template_as').click(function()
    {
        v = jQuery('#preview_iframe').contents().
            find('#textarea_content').val();
        jQuery('#save_thumbnail_url').val(jQuery('#preview_iframe').contents().
            find('#thumbnail_url').val());
        jQuery('#save_template_content').val(v);
    });
});



function placester_load_iframe()
{
    var url = 'admin.php?page=placester_templates&template_iframe=' +
        jQuery('#current_template_name').val();
    if (placester_template_edit_mode)
        url += "&mode=edit";

    jQuery('#preview_iframe').attr('src', url);
}


function placester_set_active_template(is_active)
{
    jQuery('#edit_template').css({'display': ''});
    jQuery('#save_template_panel').css({'display': 'none'});
    jQuery('#save_template_user_panel').css({'display': 'none'});

    var v = is_active ? '' : 'disabled';
    jQuery('#edit_template').attr('disabled', v);
}