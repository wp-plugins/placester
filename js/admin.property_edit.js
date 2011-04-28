
var images_popup_object = null;

/*
 * Utility to get current HTTP GET parameters
 */
jQuery.extend(
{
    getUrlVars: function()
    {
        var vars = [];
        var p = window.location.href.indexOf('?');
        if (p >= 0)
        {
            var hashes = window.location.href.slice(p + 1).split('&');
            for(var i = 0; i < hashes.length; i++)
            {
              var hash = hashes[i].split('=');
              vars.push(unescape(hash[0]));
              vars[unescape(hash[0])] = unescape(hash[1]);
            }
        }
        return vars;
    }
});



/*
 * Opens popup with property images
 */
function images_popup()
{
    if (images_popup_object != null)
        images_popup_object.dialog('close');

    var http = jQuery.getUrlVars();
    var id = http['id'];
    images_popup_object = 
        jQuery('<div style="width: 800px; margin: 0; padding: 0">' + 
            '<iframe src="admin.php?page=placester_properties&id=' + id + '&popup=images" height="545" width="100%"></iframe>' +
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
