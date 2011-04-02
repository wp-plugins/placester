<?php

/**
 * Body of "register_filter_form()" function
 * This file is processed only when function is really called
 */

?>
<script>
var placesterFilter_fields = 
    [
        'available_on',
        'bathrooms',
        'bedrooms',
        'half_baths',
        'listing_types',
        'location[city]',
        'location[state]',
        'location[zip]',
        'max_bathrooms',
        'max_bedrooms',
        'max_half_baths',
        'max_price',
        'min_bathrooms',
        'min_bedrooms',
        'min_half_baths',
        'min_price',
        'property_type',
        'purchase_types',
        'zoning_types',
        'is_new',
        'is_featured'
    ];



/*
 * Utility to get current HTTP GET parameters
 */
$.extend(
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
 * Initialization of filter form
 */
$('#<?php echo $form_dom_id ?>').ready(function()
{
    var http = $.getUrlVars();

    var filter_query = '';
    for (var n = 0; n < placesterFilter_fields.length; n++)
    {
        var field = placesterFilter_fields[n];
        var v = http[field];
        if (typeof(v) == 'string' && v.length > 0)
        {
            $('#<?php echo $form_dom_id ?>').children('input[name=' + field + ']').val(v);
            filter_query += '&' + field + '=' + escape(v);
        }
    }

    if (filter_query.length > 0)
        placesterFilter_refreshDependent(filter_query);
});



/*
 * Handler when filter form is submitted. 
 * Actually lists are asked to refresh with actual filter data.
 */
$('#<?php echo $form_dom_id ?>').submit(function(event)
{
    var filter_query = '';
    for (var n = 0; n < placesterFilter_fields.length; n++)
    {
        var field = placesterFilter_fields[n];
        var v = null;
        if ($(this).children('input[name="' + field + '"]').is('[type="checkbox"]'))
        {
            if ($(this).children('input[name="' + field + '"]').attr('checked'))
                v = 'true';
        }
        else
        {
            v = $(this).children('input[name="' + field + '"]').val();
            if (typeof(v) != 'string')
                v = $(this).children('select[name="' + field + '"]').val();
        }
        if (typeof(v) == 'string' && v.length > 0)
            filter_query += '&' + field + '=' + escape(v);
    }
   
    event.preventDefault();
    placesterFilter_refreshDependent(filter_query);
});



/*
 * Asks map/list to refresh with actual filter data.
 *
 * @param string filter_query
 */
function placesterFilter_refreshDependent(filter_query)
{
    if (typeof(placesterMap_setFilter) != 'undefined')
        placesterMap_setFilter(filter_query);
    if (typeof(placesterListLone_setFilter) != 'undefined')
        placesterListLone_setFilter(filter_query);
}

</script>