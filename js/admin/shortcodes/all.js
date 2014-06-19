/**
 * Used by the admin shortcode settings pages
 */

jQuery(document).ready(function($){

	/**
	 * Put hint text into a text input
	 */
	function wptitlehint(id) {
		id = id || 'title';

		var title = $('#' + id), titleprompt = $('#' + id + '-prompt-text');

		if ( title.val() == '' )
			titleprompt.removeClass('screen-reader-text');

		titleprompt.click(function(){
			$(this).addClass('screen-reader-text');
			title.focus();
		});

		title.blur(function(){
			if ( this.value == '' )
				titleprompt.removeClass('screen-reader-text');
		}).focus(function(){
			titleprompt.addClass('screen-reader-text');
		}).keydown(function(e){
			titleprompt.addClass('screen-reader-text');
			$(this).unbind(e);
		});
	}

	/**
	 * Return error message if the title is invalid
	 */
	function empty_field(field_id) {
		if (!field_id) {
			field_id = '#title';
		}
		var prompt = '';
        var $title = $(field_id);
        if ($title.val() == '') {
            prompt = $title.attr('title');
        }
        return prompt;
	}


	/**
	 * popup preview dialog
	 */ 
	$('.pl_review_link').click(function(e) {
		e.preventDefault();

		var iframe_content = $('#preview_meta_widget').html();
		//TODO get width/height
		var options_width = '100%';
		var options_height = '99%';

		$('#pl_review_popup').html( iframe_content );
		$('#pl_review_popup iframe').css('width', options_width);
		$('#pl_review_popup iframe').css('height', options_height);

		$('#pl_review_popup').dialog({
			width: 800,
			height: 600,
			title: $(this).attr('title'),
		});
	});

	
	
	
	////////////////////////////////////////
	// Shortcode editor
	////////////////////////////////////////
	
	/**
	 * Changing shortcode type - update display options
	 */
	function sc_shortcode_selected() {
		var shortcode = $('#pl_sc_shortcode_type').val();
		if( shortcode == 'undefined' ) {
			// clicking "Select" shouldn't reflect the choice
			$('#choose_template').hide();
			$('#widget_meta_wrapper').hide();
			return;
		}
		update_template_links();

		// display template blocks
		$('#pl_sc_edit .pl_template_block').each(function() {
			$(this).css('display', ($(this).hasClass(shortcode) ? 'block' : 'none'));
		});
		
		// hide meta blocks not related to the post type and reveal the ones to be used
		$('#pl_sc_edit .pl_widget_block').each(function() {
			$(this).css('display', ($(this).hasClass(shortcode) ? 'block' : 'none'));
		});
		
		$('#choose_template').show();
		$('#widget_meta_wrapper').show();
	}
	
	/**
	 * If user changes shortcode type or template, update the template edit link 
	 */
	function update_template_links() {
		var shortcode = $('#pl_sc_shortcode_type').val();
		var tpl_select = $('#'+shortcode+'_template_block select');
		if (tpl_select) {
			var selected = tpl_select.find(':selected');
			var selected_tpl = tpl_select.val();
			var selected_tpl_type = selected.parent().prop('label');
			
			if (selected_tpl_type=='Default') {
				$('#edit_sc_template_create').attr("href", pl_sc_template_url+'&shortcode='+shortcode+'&action=copy&default='+selected_tpl).show();
				$('#edit_sc_template_edit').hide();
			}
			else {
				$('#edit_sc_template_create').hide();
				$('#edit_sc_template_edit').attr("href", pl_sc_template_url+'&id='+selected_tpl).show();
			}
		}
	}

	/**
	 * Any time we change a field on the shortcode edit page call this to save changes, which updates the preview window
	 */
	function sc_update_preview() {

		$('.pl_review_link').hide();
		
		var shortcode = $('#pl_sc_shortcode_type').val();
		
		if (shortcode!='undefined') {
			$('#pl_sc_edit .preview_load_spinner').show();

			// set a limit on max widget size
			var $width = $('#pl_sc_edit input[name="'+shortcode+'[width]"]');
			if ($width.val() > 1024 ) {
				$width.val('1024');
			}
			var $height = $('#pl_sc_edit input[name="'+shortcode+'[height]"]');
			if ($height.val() > 1024 ) {
				$height.val('1024');
			}
			
			var data = $('#pl_sc_edit form .'+shortcode).find('input,select,textarea').serializeArray();
			data.push({name:'action', value:'pl_sc_changed'}, {name:'shortcode', value:shortcode}, {name:'id', value:$('#pl_sc_edit form input[name="ID"]').val()});
			var args = $.param(data);
			$.ajax({
				data: args,
				// beforeSend: doAutoSave ? autosave_loading : null,
				type: "POST",
				url: ajaxurl,
				success: function( response ) {
					if (response.sc_long_str) {
						var width = response.width ? response.width+'px' : '100%';
						var height = response.height ? response.height+'px' : '100%';
						$('#preview_meta_widget').html('<iframe src="'+ajaxurl+'?action=pl_sc_preview&post_type=pl_general_widget&sc_str='+response.sc_long_str+'" width="'+width+'" height="'+height+'"></iframe>');
						$('#preview_meta_widget iframe').load( function() {
							$('#pl_sc_edit .preview_load_spinner').hide();
							$('#pl_sc_edit .pl_review_link').show();
						});
						$('#sc_slug_box .iframe_link').hide();
						$('#sc_slug_box .shortcode_link').hide();
						$('#sc_slug_box .shortcode_long_link').show().find('.slug').html(response.sc_long_str);
					}
				}
			});
		}
	}

	// shortcode changing
	$('#pl_sc_shortcode_type').change(sc_shortcode_selected);
	// template changing
	$('#pl_sc_edit .snippet_list').change(function(){
		update_template_links();
	});
	// date pickers
	$('#pl_sc_edit .trigger_datepicker').each(function(){
		$(this).datepicker();
	});
	// setup view based on current shortcode type, etc
	wptitlehint();
	$('#pl_sc_shortcode_type').trigger('change');
	// setup preview window if loading prexisting shortcode
	if ($('#preview_meta_widget').html()) {
		$('#pl_sc_edit .pl_review_link').show();
	}
	// call the preview update for every changed input and select in the shortcode edit view
	$('#pl_sc_edit input, #pl_sc_edit select').change(function() {
		sc_update_preview();
		_changesMade = true;
	});
	$('#pl_sc_edit input[type="submit"]').click(function() {
		_changesMade = false;
	});


	try{
		//$('#title').focus();
		// force a title in shortcode edit page
		// TODO not working on safari
		$('#pl_sc_edit').find('input,select,button').not('#title').click(function(e){
			var prompt = empty_field('#title');
			if (prompt) {
				e.preventDefault();
				alert(prompt);
				$('#title').focus();
				return;
			}
		});
	}catch(e){}


	////////////////////////////////////////
	// Template editor
	////////////////////////////////////////
	
	/**
	 * When the shortcode type is changed update hints, etc
	 */
	function tpl_type_selected() {
		var shortcode = $('#pl_sc_tpl_shortcode').val();
		
		$('#pl_sc_tpl_shortcode_selected').html('['+shortcode+']');
		
		// update the shortcode hints
		$('#subshortcodes .shortcode_block').hide();
		$('#subshortcodes .shortcode_block.'+shortcode).show();
		
		// display template blocks
		$('#pl_sc_tpl_edit .pl_template_block').each(function() {
			if ($(this).hasClass(shortcode)) {
				$(this).show();
				// activate the codemirror object
				$('.CodeMirror').each(function(i, el){
				    el.CodeMirror.refresh();
				});
			}
			else {
				$(this).hide();
			}
		});
	}
	
	/**
	 * Push edits on the template edit page so we can update the preview
	 */
	function tpl_update_preview() {
		$('#pl_sc_tpl_edit .preview_load_spinner').show();
		
		var shortcode = $('#pl_sc_tpl_edit [name="shortcode"]').val();
		var data = $('#pl_sc_tpl_edit form .'+shortcode).find('input,select,textarea').serializeArray();
		data.push({name:'action', value:'pl_sc_template_changed'},{name:'shortcode', value:shortcode});
		var args = $.param(data);
		$.ajax({
			data: args,
			// beforeSend: doAutoSave ? autosave_loading : null,
			type: "POST",
			url: ajaxurl,
			success: function( response ) {
				if (response) {
					$('#preview_meta_widget').html('<iframe src="'+ajaxurl+'?action=pl_sc_template_preview&post_type=pl_general_widget&shortcode='+shortcode+'" width="250px" height="250px"></iframe>');
					$('#preview_meta_widget iframe').load( function() {
						$('#pl_sc_tpl_edit .preview_load_spinner').hide();
						$('#pl_sc_tpl_edit .pl_review_link').show();
					});
				}
			}
		});
	}
	
	// update view when shortcode changed
	$('#pl_sc_tpl_shortcode').change(tpl_type_selected);
	// call the custom autosave for every changed input and select in the template edit view
	$('#pl_sc_tpl_edit').find('input, select, textarea').change(function() {
		tpl_update_preview();
		_changesMade = true;
	});
	$('#pl_sc_tpl_edit input[type="submit"]').click(function() {
		_changesMade = false;
	});
	// Update preview when creating a new template
	$('.save_snippet').click(function() {
		$('#pl_sc_tpl_post_type').trigger('change');
	});
	// Preview popup link
	$('#popup_existing_template').click(function(e){
		e.preventDefault();
	});
	// Add CodeMirror support to edit boxes
	$('#pl_sc_tpl_edit .pl_template_block textarea').each(function() {
		var cm = CodeMirror.fromTextArea(document.getElementById($(this).attr('id')), {
		    mode: $(this).closest('section').hasClass('mime_css')?"text/css":"text/html",
		    lineNumbers: true,
		    lineWrapping: true,
		    extraKeys: {"Ctrl-Q": function(cm){ cm.foldCode(cm.getCursor()); }},
		    foldGutter: true,
		    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],

		});
		// copy cm changes back to hidden field so the preview will work
		cm.on('change', function(){
			if (_previewWait) {
				clearTimeout(_previewWait);
			}
			_previewWait = setTimeout(function(){
				cm.save();
				tpl_update_preview();
				_changesMade = true;
			}, 1000);
		});
	});
	// Make before/after appear only if there is something in them
	$('#pl_sc_tpl_edit').find('.before_widget, .after_widget').each(function(){
		if (!$(this).find('textarea').val()) {
			$(this).find('.CodeMirror').hide();
		}
		$(this).find('label').wrap('<a href="#" />').click(function(e) {
			e.preventDefault();
			$(this).closest('section').find('.CodeMirror').toggle().get(0).CodeMirror.refresh();
		});
	});

	// trigger an event to set up the preview pane on page load 
	$('#pl_sc_tpl_shortcode').trigger('change');
	
	// toggler for seeing which csc uses the template
	$('#pl_sc_tpl_csc_link').click(function(e){
		e.preventDefault();
		$('#pl_sc_tpl_csc_list').toggle();
	});
	
	
	////////////////////////////////////////
	// All forms - check for unsaved edits
	////////////////////////////////////////
	var _changesMade = false;
	var _previewWait = 0;
	
	$(window).bind('beforeunload', function() {
		if (_changesMade)
			return autosaveL10n.saveAlert;
	});
	
});
