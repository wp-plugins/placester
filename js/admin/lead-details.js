// For datatable
jQuery(document).ready(function($) {

    var my_leads_datatable = $('#placester_saved_search_list').dataTable( {
            "bFilter": false,
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod": "POST",
            'sPaginationType': 'full_numbers',
            'sDom': '<"dataTables_top"pi>lftpir',
            "sAjaxSource": ajaxurl, //wordpress url thing
            "aoColumns" : [
                { sWidth: '120px' },    
                { sWidth: '230px' },    
                { sWidth: '330px' },    
                { sWidth: '100px' },    
                { sWidth: '200px' }     
            ],
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "action", "value" : "get_saved_searches"} );
                aoData.push( { "name": "lead_id", "value" : $('input#lead_id').val()} );
            }
        });

    $('#delete_lead').live('click', function (event) {
        event.preventDefault();
        var delete_request = {}
        delete_request['action'] = 'delete_lead';
        delete_request['lead_id'] = $('input#lead_id').val();

        if ("DELETE" == prompt("Are you sure you want to DELETE " + $('span.name').text() + "?\n\nYou will never be able to recover this lead, their saved searches, or their favorites ever again. \n\n Type DELETE to remove this lead forever.")) {
            $.post(ajaxurl, delete_request, function(data, textStatus, xhr) {
                if (data.result && data.result == '1') {
                    alert('success');
                } else {
                    alert('failed');
                }
            }, 'json');    
        } else {
            alert('You either cancelled the delete or did not type DELETE correctly. This lead HAS NOT been deleted.');
        }
    });


    $('#pls_delete_search').live('click', function (event) {
        event.preventDefault();
        var delete_request = {}
        delete_request['action'] = 'delete_lead_search';
        delete_request['lead_id'] = $('input#lead_id').val();
        delete_request['search_id'] = $(this).attr('ref');
        console.log(delete_request);

        if ("DELETE" == prompt("Are you sure you want to DELETE this search?\n\nYou will never be able to recover this search and all email notifications will stop immediately. \n\n Type DELETE to remove this search forever.")) {
            $.post(ajaxurl, delete_request, function(data, textStatus, xhr) {
                if (data.result && data.result == '1') {
                    alert('success');
                } else {
                    alert('failed');
                }
            }, 'json');    
        } else {
            alert('You either cancelled the delete or did not type DELETE correctly. This search HAS NOT been deleted.');
        }
    });

    var my_favorites_datatable = $('#placester_favorite_listings_list').dataTable( {
            "bFilter": false,
            "bProcessing": true,
            "bServerSide": true,
            "sServerMethod": "POST",
            'sPaginationType': 'full_numbers',
            'sDom': '<"dataTables_top"pi>lftpir',
            "sAjaxSource": ajaxurl, //wordpress url thing
            "aoColumns" : [
                { sWidth: '60px' },    
                { sWidth: '200px' },    
                { sWidth: '60px' },    
                { sWidth: '60px' },    
                { sWidth: '100px' },  
                { sWidth: '60px' },   
                { sWidth: '100px' }     
            ],
            "fnServerParams": function ( aoData ) {
                aoData.push( { "name": "action", "value" : "get_favorites_datatable"} );
                aoData.push( { "name": "lead_id", "value" : $('input#lead_id').val()} );
            }
        });



    // hide/show action links in rows
    $('tr.odd, tr.even').live('mouseover', function(event) {
        $(this).find(".row_actions").show();
    });
    $('tr.odd, tr.even').live('mouseout', function(event) {
        $(this).find(".row_actions").hide();
    });


    // Editing a lead
    $('#cancel').click(function () {
        alert('need to do this');
    });

    $('#pls_search_form button#pls_search_form_submit_button').click(function (event) {
        event.preventDefault();
        var form_values = {};
        form_values['action'] = 'update_lead';
        $.each($('#pls_search_form :input').serializeArray(), function(i, field) {
            form_values[field.name] = field.value;
        });
        $.post(ajaxurl, form_values, function(data, textStatus, xhr) {
            if (data.result && data.result == '1') {
                alert('success');
            } else {
                alert('failed');
            }
        }, 'json');
    });

});


// New Search


jQuery(document).ready(function($) {

    function update_global_form_status() {
        var active_filter = $('#selected_global_filter').val();
        // property_type filters have their . switched out to - because of jQuery issues finding "."
        active_filter = active_filter.replace(".","-");
        // console.log(active_filter);
        $('#global_filter_form').find('.currently_active_filter').removeClass('currently_active_filter').hide();
        $('#global_filter_form').find('section.' + active_filter).show().addClass('currently_active_filter');
    }

    function are_global_filters_active () {
        if ($('#active_filters #active_filter_item').length > 0) {
            $('#global_filter_wrapper').addClass('filters_active');
        } 
        else {
            $('#global_filter_wrapper').removeClass('filters_active');
            $('#global_filter_active').html('');
            $('.global_filters.tagchecklist p.label').remove();
        }
    }

    function get_current_form_values () {
        var current_form_values = {};

        $.each($('#active_filters').serializeArray(), function(i, field) {
            if (current_form_values[field.name] && current_form_values[field.name] instanceof Array) {
                current_form_values[field.name].push(field.value);
            } 
            else if (current_form_values[field.name]) {
                // Key exists with a singular/non-array value, so create an array to hold multiple values...
                var old_val = current_form_values[field.name];
                current_form_values[field.name] = new Array(old_val, field.value);
            }
            else {
                current_form_values[field.name] = field.value;  
            }
        });

        return current_form_values;
    }
    form_vals = get_current_form_values;

    // Called this function automatically when the script is first loaded...
    update_global_form_status();
    
    $('#selected_global_filter').on('change', function () {
        update_global_form_status();
    });

    $('#add-single-filter').on('click', function () {
        $('#global_filter_message').html('');
        $('#global_filter_message').removeClass('red');
        $('#global_filter_message').removeClass('green');

        var key = $('.currently_active_filter select, .currently_active_filter input').attr('name');
        var value = $('.currently_active_filter select, .currently_active_filter input').val();
        var current_form_values = get_current_form_values();
        // console.log(current_form_values);
        // console.log(key, value);
        if (current_form_values[key] == value || (current_form_values[key] instanceof Array && $.inArray(value, current_form_values[key]) >= 0)) {
            $('#global_filter_message').html('That filter is already active. Select another one.');
            $('#global_filter_message').addClass('red');
        } 
        else if (value != 'false') {
            if (current_form_values[key] && current_form_values[key] instanceof Array) {
                current_form_values[key].push(value);
            }
            else if (current_form_values[key]) {
                // Key exists with a singular/non-array value, so create an array to hold multiple values...
                var old_val = current_form_values[key];
                current_form_values[key] = new Array(old_val, value);
            }
            else {
                current_form_values[key] = value;
            }
            

            var new_row = '<li>';
            new_row += '<span class="col1">' + key.replace('_', ' ') + '</span>';
            new_row += '<span class="col2">' + value.replace('_', ' ') + '</span>';
            new_row += '<span class="col3"><a class="button-secondary" id="remove-single-filter">Remove Filter</a></span>';
            new_row += '</li>';

            $('.pls_active_filters ul').append(new_row);

            // $('form#active_filters').append('<span id="active_filter_item"><a href="#" class="remove_filter"></a><span class="global_dark_label">'+key.replace('_', ' ')+'</span>: '+value.replace('_', ' ')+'<input type="hidden" name="'+key+'" value="'+value+'"></span>');
    
            // current_form_values['action'] = 'user_save_global_filters';
            // console.log(current_form_values);
            // $.post(ajaxurl, current_form_values, function(data, textStatus, xhr) {
            //     // console.log(data);
            //     if (data && data.result) {
            //         $('#global_filter_message').removeClass('red');
            //         $('#global_filter_message').html(data.message);
            //         $('#global_filter_message').addClass('green');

            //         // Add newly added filter to "Active Filters" UI...
            //         $('form#active_filters').append('<span id="active_filter_item"><a href="#" class="remove_filter"></a><span class="global_dark_label">'+key.replace('_', ' ')+'</span>: '+value.replace('_', ' ')+'<input type="hidden" name="'+key+'" value="'+value+'"></span>');

            //         are_global_filters_active();
            //     } 
            //     else {
            //         $('#global_filter_message').removeClass('green');
            //         $('#global_filter_message').html(data.message);
            //         $('#global_filter_message').addClass('red');                    
            //     };
            // }, 'json'); 
        } 
        else {
            $('#global_filter_message').html('Select a value for your filter.').addClass('red');
            $('#global_filter_message').addClass('red');
        }

        setTimeout(function () {
            $('#global_filter_message').html('');
        }, 1500);
    });


    $('#active_filters').on('click', '.remove_filter', function (event) {
        event.preventDefault(); 
        $(this).closest('#active_filters span#active_filter_item').remove();
        
        var current_form_values = get_current_form_values();

        current_form_values['action'] = 'user_save_global_filters';
        $.post(ajaxurl, current_form_values, function(data, textStatus, xhr) {
            // console.log(data);
            if (data && data.result) {
                $('#global_filter_message').removeClass('red');
                $('#global_filter_message').html(data.message);
                $('#global_filter_message').addClass('green');
                are_global_filters_active();
            } 
            else {
                $('#global_filter_message').removeClass('green');
                $('#global_filter_message').html(data.message);
                $('#global_filter_message').addClass('red');                    
            }
            setTimeout(function () {
                $('#global_filter_message').html('');
            }, 1500);
        }, 'json');
    });

    $('#remove_global_filters').on('click', function () {
        $('#global_filter_message_remove').removeClass('red');
        $('#global_filter_message_remove').addClass('green');
        $('#global_filter_message_remove').html('Working....');
        $.post(ajaxurl, {action: 'user_remove_all_global_filters'}, function(data, textStatus, xhr) {
            // console.log(data);
            if (data && data.result) {
                $('#global_filter_message_remove').html(data.message);
                $('#global_filter_message_remove').addClass('green');
                
                // Refresh page to update view...
                window.location.href = window.location.href;
            }
            else {
                $('#global_filter_message_remove').removeClass('green');
                $('#global_filter_message_remove').html(data.message);
                $('#global_filter_message_remove').addClass('red'); 
            }
        }, 'json');

        setTimeout(function () {
            $('#global_filter_message_remove').html('');
        }, 1500);
    });

});











