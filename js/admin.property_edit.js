
var images_popup_object = null;

/*
 * Opens popup with property images
 */
function images_popup()
{
    if (images_popup_object != null)
        images_popup_object.dialog('close');

    images_popup_object = 
        jQuery('<div style="width: 800px; margin: 0; padding: 0">' + 
            '<iframe src="admin.php?page=placester_properties&id=<?php echo $property_id ?>&popup=images" height="545" width="100%"></iframe>' +
            '</div>')
            .dialog(
                {
                    title: 'Edit Images', 
                    height: 580,
                    width: 800,
                    zIndex: 2
                });
}



/*
 * Lightbox linkage with image. popup iframe code calls that function
 * to display lightbox with image content. Iframe cannt do that since lightbox must be 
 * fullframe
 */
function show_image(url)
{ 
    jQuery("#lightbox_link")
        .attr('href', url)
        .trigger('click');
}
