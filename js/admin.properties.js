
var placesterListLone_datatable = null;
var placesterListLone_filter = '';

var placesterListLone_datasource_url = '';



/*
 * Returns HTML of new/featured icon
 *
 * @param bool value
 * @param string icon_filename
 * @return string
 */
function icon_html(value, icon_filename)
{
    if (value)
        return '<img src="' + placesterListLone_base_url + 
            '/images/' + icon_filename + '" />';
    else
        return '';
}



/*
 * Returns HTML of new/featured button
 *
 * @param string property_id
 * @param bool value
 * @param string field
 * @param string name
 * @param string mirror_column
 * @param string mirror_column_image_filename
 * @return string
 */
function flag_html(property_id, value, field, name, mirror_column, 
    mirror_column_image_filename)
{
    return '<a href="#" onclick=\'flag_click(this, "' + property_id + '", "' + 
        field + '", "' + name + '", ' + mirror_column + ', "' + 
        mirror_column_image_filename + '")\'>' + 
        flag_html_inner(value, name) + '</a>';
}



/*
 * Returns inner text of new/featured button
 *
 * @param bool value
 * @param string name
 * @return string
 */
function flag_html_inner(value, name)
{
    if (value)
        return name;
    else
        return 'Not ' + name;
}



/*
 * Handler for button click. Changes associated flag via ajax.
 *
 * @param object cell
 * @param string property_id
 * @param string field
 * @param string name
 * @param string mirror_column
 * @param string mirror_column_image_filename
 */
function flag_click(cell, property_id, field, name, mirror_column,
    mirror_column_image_filename)
{
    jQuery.ajax(
        { 
           url: placesterListLone_base_url + 
                '/admin/properties_ajax.php?id=' + property_id + 
                '&field=' + field, 
           context: cell, 
           dataType: 'json',
           success: 
               function (data)
               {
                   cell.innerHTML = flag_html_inner(data.new_value, name);
                   cell.parentNode.parentNode.children[mirror_column].innerHTML = 
                       icon_html(data.new_value, mirror_column_image_filename);
               },
           error: function() { alert('error'); }
        });
}



/*
 * Returns value of DOM object in a format name=value
 *
 * @param string id
 * @param string parameter_name
 * @return string
 */
function add_field(id, parameter_name)
{
    var v = jQuery('#' + id).val();
    var s = '';
    if (typeof(v) == 'string' && v.length > 0)
        s = '&' + parameter_name + '=' + escape(v);

    return s;
}



/**
 * Creates or recreates datatable object
 */
function placesterListLone_create()
{
    if (placesterListLone_datatable != null)
        placesterListLone_datatable.fnClearTable(false);

    placesterListLone_datatable = jQuery('#placester_listings_list').dataTable(
        {
            'bFilter': false,
            'bPaginate': true,
            'iDisplayLength': 20,
            'bLengthChange': false,
            'bSort': true,
            'bInfo': true,
            'bAutoWidth': false,
            'sPaginationType': 'full_numbers',
            'bProcessing': true,
            'bServerSide': true,
            'bDestroy': true,
            'sAjaxSource': placesterListLone_datasource_url + placesterListLone_filter,
            'aoColumns': 
                [
                    {
                        'fnRender': 
                            function(row_data) 
                            { return icon_html(row_data.aData[8], 'property_new.gif') },
                        'bSortable': false
                    },
                    {
                        'fnRender': 
                            function(row_data) 
                            { return icon_html(row_data.aData[9], 'property_featured.png') }, 
                        'bSortable': false
                    },
                    {'fnRender': function(row_data) { return row_data.aData[2] }},
                    {'fnRender': function(row_data) { return row_data.aData[3] }},
                    {'fnRender': function(row_data) { return row_data.aData[4] }},
                    {'fnRender': function(row_data) { return row_data.aData[5] }},
                    {'fnRender': function(row_data) { return row_data.aData[6] }},
                    {
                        'fnRender': 
                            function(row_data) 
                            { 
                                var s = '<a href="' + row_data.aData[7] + 
                                    '" target="_blank">Details</a>';
                                return s;
                            }
                    },
                    {
                        'fnRender': 
                            function(row_data) 
                            { 
                                return flag_html(row_data.aData[10], row_data.aData[8], 
                                    'is_new', 'New', 0, 'property_new.gif');
                            }
                    },
                    {
                        'fnRender': 
                            function(row_data) 
                            { 
                                return flag_html(row_data.aData[10], row_data.aData[9], 
                                    'is_featured', 'Featured', 1, 
                                    'property_featured.png');
                            }
                    },
                    {
                        'fnRender': 
                            function(row_data)
                            { 
                                var s = '<a href="admin.php?page=placester_properties&id=' + 
                                    row_data.aData[10] + '">Edit</a>';
                                return s;
                            }
                    }
                ]
        });
}



/**
 * Initializes list
 */
jQuery(document).ready(function() 
{
    placesterListLone_datasource_url = placesterListLone_base_url + 
        '/properties_datatable.php?' +
        'no_admin_filter=yes&' +
        'fields=empty,empty,location.address,bathrooms,bedrooms,price,location.city,url,is_new,is_featured,id';

    placesterListLone_create();

    jQuery('#filter_button').click(function()
    {
        placesterListLone_filter = add_field('location_city', 'location[city]') +
            add_field('location_state', 'location[state]') +
            add_field('location_zip', 'location[zip]') +
            add_field('min_bathrooms', 'min_bathrooms') +
            add_field('min_bedrooms', 'min_bedrooms');
        placesterListLone_create();
    });

});
