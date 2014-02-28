/**
 *  Applies Chosen to forms.
 */
// $( document ).ready( function() {
//     $("select").chosen({allow_single_deselect: true});
// });


// For datatable
jQuery(document).ready(function($) {

    var my_leads_datatable = $('#placester_leads_list').dataTable( {
            "bFilter": false,
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod": "POST",
            'sPaginationType': 'full_numbers',
            'sDom': '<"dataTables_top"pi>lftpir',
            "sAjaxSource": ajaxurl, //wordpress url thing
            "aoColumns" : [
                { sWidth: '100px' },    
                { sWidth: '200px' },    
                { sWidth: '70px' },     
                { sWidth: '100px' },    
                { sWidth: '100px' },    
                { sWidth: '100px' }     
            ],
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "action", "value" : "datatable_my_leads"} );
                // aoData.push( { "name": "sSearch", "value" : $('input#address_search').val() })
                // aoData = my_listings_search_params(aoData);
            }
        });



    // hide/show action links in rows
    $('tr.odd, tr.even').live('mouseover', function(event) {
        $(this).find(".row_actions").show();
    });
    $('tr.odd, tr.even').live('mouseout', function(event) {
        $(this).find(".row_actions").hide();
    });


    // prevents default on search button
    $('#pls_admin_my_listings').live('change', function(event) {
        event.preventDefault();
        my_listings_datatable.fnDraw();
    });

});



