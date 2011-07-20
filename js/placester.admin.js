/* 
 * General Placester scripts that
 * apply to the whole wordpress backend
 * 
 */
jQuery(document).ready(function($) {
    jQuery('#hide-theme-alert')
        .click(function(e){
            e.preventDefault();
            $warning = jQuery(this).closest('.updated');
            data = {
                action: 'update_theme_alert',
            };
            jQuery.get(ajaxurl, data, function(response) {
                if (response) {
                    $warning.fadeOut('200', function() {
                        jQuery(this).remove();
                    });
                }
            });
        });
});
