<?php

/**
 * Body of "placester_listings_list()" function.
 * For div-based mode.
 * @file /core/listings_list_lone_divbased.php
 */

include( 'listings_list_divbased_parts.php' );



// Data source url



$fields = implode( ',', $webservice_fields );
$datasource_url = $base_url . '/properties_divbased.php?fields=' . $fields;

list(
    $default_row_renderer,
    $row_renderer,
    $default_pager_renderer,
    $pager_data,
    $pager_renderer,
    $pager_rows_per_page,
    $default_pager_render_in_dom_element,
    $pager_render_in_dom_element
) = placester_list_divbased_parse( $parameters );


// Loading
$loading_data = array();
if ( isset( $parameters['loading'] ) )
    $loading_data = $parameters['loading'];

$default_loading_render_in_dom_element = 
    ! isset( $loading_data['render_in_dom_element'] );
if ( $default_loading_render_in_dom_element )
    $loading_render_in_dom_element = 'placester_listings_loading';
else
    $loading_render_in_dom_element = $loading_data['render_in_dom_element'];



// Default sort
$sort_query = '';
if ( isset( $parameters['sort_by'] ) ) {
    $sort_type = ( isset( $parameters['sort_type'] ) ? $parameters['sort_type'] : 'asc' );
    $sort_query = '&sort_by=' . $parameters['sort_by'] . '&sort_type=' . $sort_type;
}

?>
<script>

var placesterListLone_initialized = false;
var placesterListLone_datasource_base_url = '<?php echo $datasource_url ?>';
var placesterListLone_datasource_url = placesterListLone_datasource_base_url;
var placesterListLone_filter_query = '';
var placesterListLone_sort_query = '<?php echo $sort_query ?>';
var placesterListLone_page_number = 1;
var placesterListLone_pager_rows_per_page = <?php echo $pager_rows_per_page ?>;


<?php
if ( $default_row_renderer )
    placester_print_default_row_renderer();
if ( $default_pager_renderer )
    placester_print_default_pager_renderer( $pager_data );
?>



/**
 * Called when user selected new filter conditions.
 * New filtered data are requested and list is refreshed.
 *
 * @param string filter_query
 */
function placesterListLone_setFilter(filter_query)
{
    placesterListLone_page_number = 1;
    placesterListLone_filter_query = filter_query;
    placesterListLone_refresh();
}



/**
 * Called when user selected new sorting conditions.
 * New filtered data are requested and list is refreshed.
 *
 * @param string field
 * @param string direction
 */
function placesterListLone_setSorting(field, direction)
{
    placesterListLone_sort_query = "&sort_by=" + field + "&sort_type=" + direction;
    placesterListLone_refresh();
}



/**
 * Requests data for list according to actual paging/filtering parameters.
 */
function placesterListLone_refresh()
{
    var page_offset = (placesterListLone_page_number - 1) * 
        placesterListLone_pager_rows_per_page;

    placesterListLone_datasource_url = placesterListLone_datasource_base_url + 
        placesterListLone_filter_query + placesterListLone_sort_query + 
        "&offset=" + page_offset + 
        "&limit=" + placesterListLone_pager_rows_per_page;

    if (placesterListLone_initialized)
        placesterListLone_create();
}



/**
 * Switches page of list
 *
 * @param int n
 */
function placesterListLone_setPage(n)
{
    placesterListLone_page_number = n;
    placesterListLone_refresh();
}



/**
 * Enables/disables "list loading" element
 *
 * @param bool visible
 */
function placesterListLone_setLoadingVisible(visible)
{
    if (visible)
        $('#<?php echo $loading_render_in_dom_element ?>').css({display: ''});
    else
        $('#<?php echo $loading_render_in_dom_element ?>').css({display: 'none'});
}



/**
 * Makes request for data and then renders list
 */
function placesterListLone_create()
{
    placesterListLone_setLoadingVisible(true);

    /**
     *      If the user has set filters, respect them.
     */
    if (typeof placesterListLone_filter != 'undefined') {
        placesterListLone_datasource_url += placesterListLone_filter;
    };
    

    $.ajax(
    {
        type: 'GET',
        url: placesterListLone_datasource_url,
        dataType: 'json',
        success: function(data) 
        {
            placesterListLone_setLoadingVisible(false);
            output = '';
            for (var n = 0; n < data.properties.length; n++)
            {
                row_object = data.properties[n];
                row_html = <?php echo $row_renderer ?>(row_object);

                output += '<div id="listing_' + n + '">' + row_html + '</div>';
            }

            if (output) {
                $('#placester_listings_list').html(output);
            } else {
                placesterListLone_empty($('#placester_listings_list'));                
            };


            pager_output = '';
            pages = Math.floor(
                (data.total + placesterListLone_pager_rows_per_page - 1) / 
                placesterListLone_pager_rows_per_page);
            var html = <?php echo $pager_renderer ?>(pages, placesterListLone_page_number,
                'placesterListLone_setPage');

                $('#<?php echo $pager_render_in_dom_element ?>').html(html);                    

        }
    });
}

function placesterListLone_empty (dom_object)
{
    if (typeof custom_empty_listings_loader == 'function') {
        custom_empty_listings_loader(dom_object);
    } else {
        dom_object.html('<p>Sorry, no listings match that search. Maybe try something a bit broader? Or just give us a call and we\'ll personally help you find the right place.</p>');
    };

}


/**
 * List initialization
 */
$(document).ready(function() 
{
    placesterListLone_initialized = true;
    placesterListLone_refresh();
});

</script>

<?php

if ( $default_loading_render_in_dom_element )
    placester_print_default_loading_element();
if ( $default_pager_render_in_dom_element )
    echo '<div id="placester_listings_pager"></div>';

echo placester_listings_list_divbased_html( 'placester_listings_list', 
    $webservice_fields );
