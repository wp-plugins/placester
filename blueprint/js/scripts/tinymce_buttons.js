(function($, undefined) {
    tinymce.create('tinymce.plugins.pls_placester', {
        init : function(ed, url) {
        	var js_index = url.indexOf('/js');
        	var image_path = url.substring(0, js_index);
            ed.addButton('pls_placester', {
                title : 'pls_placester.placester',
                image : image_path +'/i/icons/tinymce-shortcode-ico.png',
                onclick : function() {
                    /* idPattern = /(?:(?:[^v]+)+v.)?([^&=]{11})(?=&|$)/;
                    var vidId = prompt("placester Video", "Enter the id or url for your video");
                    var m = idPattern.exec(vidId);
                    // if (m != null && m != 'undefined')
                    ed.execCommand('mceInsertContent', false, '[placester id="'+vidId+'"]');
                    //    ed.execCommand('mceInsertContent', false, '[placester id="'+m[1]+'"]'); */
                }
            });
        },
        createControl : function(n, cm) {
        	if(n == 'pls_placester') {
	        	var c = cm.createMenuButton('pls_shortcodes', {
					title : 'Placester shortcodes',
				});
	
				c.onRenderMenu.add(function(c, m) {
					m.add({title : 'Shortcodes', 'class' : 'mceMenuItemTitle'}).setDisabled(1);
					
					// declare dialogs for mutliple open/hide
					var featured_dialog = jQuery('#dialog-featured-listings').dialog({ autoOpen: false });
					var static_dialog = jQuery('#dialog-static-listings').dialog({ autoOpen: false });
					
					// add buttons
					var menu_featured_listings = m.add({title:"Featured Listings", onclick: function() {
						featured_dialog.dialog('open');
					}});
					
					var menu_static_listings = m.add({title:"Static Listings", onclick: function() {
						static_dialog.dialog('open');
					}});
					
					// Handle shortcode buttons form actions 
					jQuery('#dialog-featured-form').submit(function() {
						var listings_select = jQuery('#featured-listings-select').val();
						
						// add shortcode only where select is found and there is a value in dropdown
						if(typeof listings_select !== "undefined" && listings_select !== null ) {
							c.editor.execCommand('mceInsertContent', false, pls_featured_listings_button(listings_select) );
						}
					
						featured_dialog.dialog('close');
						
						return false;
					});
					
					jQuery('#dialog-static-form').submit(function() {
						var listings_select = jQuery('#static-listings-select').val();
						
						// add shortcode only where select is found and there is a value in dropdown
						if(typeof listings_select !== "undefined" && listings_select !== null ) {
							c.editor.execCommand('mceInsertContent', false, pls_static_listings_button(listings_select) );
						}
						static_dialog.dialog('close');
						
						return false;
					});
					
					jQuery('.wp-dialog .ui-dialog-titlebar-close').on('click', function( closediv ) {
						jQuery(this).parent().parent().dialog('close');
					});
								 
				});
				
				return c;
        	}
        	
        	return null;
        },
        getInfo : function() {
            return {
                longname : "Placester Shortcode",
                author : 'Mario Peshev',
                authorurl : '',
                infourl : '',
                version : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('pls_placester', tinymce.plugins.pls_placester);
    
})();

// Sample shortcodes below

function pls_featured_listings_button( id ) {
	return '[featured_listings id="' + id + '"]'
}

function pls_static_listings_button( id ) {
	return '[static_listings id="' + id + '"]'
}
