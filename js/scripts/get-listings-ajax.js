$(document).ready(function($) {
    var markers = [];
    var my_listings_datatable = $('#placester_listings_list').dataTable( {
        "bFilter": false,
        "bProcessing": true,
        "bServerSide": true,
        "sServerMethod": "POST",
        'sPaginationType': 'full_numbers',
        "sAjaxSource": info.ajaxurl, //wordpress url thing
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            aoData.push( { "name": "action", "value" : "pls_listings_ajax"} );
            aoData = my_listings_search_params(aoData);
            $.ajax({
                "dataType" : 'json',
                "type" : "POST",
                "url" : sSource,
                "data" : aoData,
                "success" : function(ajax_response) {
                    if (ajax_response && ajax_response['aaData']) {
                        custom_total_results(ajax_response);
                        for (var current_marker in markers) {
                          markers[current_marker].setMap(null);
                        }
                        if (typeof window['google'] != 'undefined') {
                          markers = [];
                          var bounds = new google.maps.LatLngBounds();
                          for (var listing in ajax_response['aaData']) {
                              var listing_json = ajax_response['aaData'][listing][1];
                              var marker = create_marker(listing_json, pls_google_map);
                              bounds.extend(marker.getPosition());
                              markers.push(marker);
                          }
                          pls_google_map.fitBounds(bounds);
                        }
                    };

                    //required to load the datatable
                   fnCallback(ajax_response)
                }
            });
        } 
    });

    function create_marker (listing_json, map) {

      //create marker
      var marker = new google.maps.Marker({
          position: new google.maps.LatLng(listing_json['location']['coords'][0], listing_json['location']['coords'][1]),
          map: map
      });

      if (listing_json['images'][0]['url']) {
        var image_url = listing_json['images'][0]['url'];
      };

      //create click content
      var content = '<div id="content">'+
                        '<div id="siteNotice">'+'</div>'+
                          '<h2 id="firstHeading" class="firstHeading">'+ listing_json['location']['full_address'] +'</h2>'+
                          '<div id="bodyContent">'+
                            '<img width="80px" height="80px" style="float: left" src="'+image_url+'" />' +
                            '<ul style="float: right; width: 130px">' +
                              '<li> Beds: '+ listing_json['cur_data']['beds'] +'</li>' +
                              '<li> Baths: '+ listing_json['cur_data']['baths'] +'</li>' +
                              '<li> Available: '+ listing_json['cur_data']['avail_on'] +'</li>' +
                              '<li> Price: '+ listing_json['cur_data']['price'] +'</li>' +
                            '</ul>' +
                          '</div>' +
                          '<div style="margin: 15px 70px; float: left; font-size: 16px; font-weight: bold;"><a href="'+listing_json['cur_data']['url']+'">View Details</a></div>' +
                          '<div class="clear"></div>' +
                        '</div>'+
                      '</div>';


      //create info window
      infowindow = new google.maps.InfoWindow({
          content: content

      });

      //set on click
      google.maps.event.addListener(marker, 'click', function() {
        infowindow.open(map,marker);
      });
      //
      marker.setMap(map);

      return marker;
    }

    //save as a reference.
    window.my_listings_datatable = my_listings_datatable;

    // prevents default on search button
    $('.pls_search_form_listings, #sort_by, #sort_dir').live('change submit', function(event) {
        event.preventDefault();
        my_listings_datatable.fnDraw();
    });

    // parses search form and adds parameters to aoData
    function my_listings_search_params (aoData) {
        $.each($('.pls_search_form_listings, .sort_wrapper').serializeArray(), function(i, field) {
            aoData.push({"name" : field.name, "value" : field.value});
        });
        aoData.push({"name": "context", "value" : $('#context').attr('class')});
        return aoData;
    }

    if (typeof custom_total_results == 'function') {
      function custom_total_results (ajax_response) {
        $('#pls_listings_search_results #pls_num_results').html(ajax_response.iTotalDisplayRecords);
      }  
    };
    

    //datepicker
    $("input#metadata-max_avail_on_picker, #metadata-min_avail_on_picker").datepicker({
            showOtherMonths: true,
            numberOfMonths: 2,
            selectOtherMonths: true
    });
});