<?php

/**
 * Admin interface: Dashboard tab
 * Entry point
 */

?>
<div style="width: 950px" class="wrap"> 
    
    
  <?php admin_header('placester_dashboard') ?>
	<?php create_postbox_container_top('width:620px'); ?>
		<?php create_postbox('lead-sources', 'Lead Traffic', "<div id='line_chart_wrapper'><div style='height: 255px' id='linechart'></div></div>") ?>
		<?php create_postbox('leads', 'Leads', "<div id='leads_container'> </div>") ?>
	<?php create_postbox_container_bottom(); ?>
	
	<?php create_postbox_container_top('width:320px'); ?>
		<?php create_postbox('lead-sources', 'Lead Sources', "<div style='margin-left: 50px; height: 215px' id='piechart'></div>") ?>
		<?php create_postbox('recent-news', 'Placester News', 'this is a test') ?>
		<?php create_postbox('recent-news', 'About Us', 'this is a test') ?>
	<?php create_postbox_container_bottom(); ?>

<div class="clear"></div>
</div>
<?php if (get_option('placester_company_id') && get_option('placester_api_key')): ?>
    <script>
        jQuery(document).ready(function($) {
           line_widget('linechart', "<?php echo sha1("line" . get_option('placester_company_id') . get_option('placester_api_key')); ?>", "<?php echo get_option('placester_company_id') ?>"); 
           pie_widget('piechart', "<?php echo sha1("pie" . get_option('placester_company_id') . get_option('placester_api_key')); ?>", "<?php echo get_option('placester_company_id') ?>"); 
           leads_widget('leads_container', "<?php echo sha1("leads" . get_option('placester_company_id') . get_option('placester_api_key')); ?>", "<?php echo get_option('placester_company_id') ?>"); 
        });
    </script>    
<?php else: ?>
    <script>

    jQuery(document).ready(function($) {

        show_widget_error('linechart', '<?php  echo plugins_url(); ?>/placester/images/line-null.png', 'No traffic data yet.  Enter an api key in the <a href="/wp-admin/admin.php?page=placester_settings">settings</a> tab or an email address in the <a href="/wp-admin/admin.php?page=placester_contact">personal</a> tab', 'width: 300px; margin-left: -150px');
        show_widget_error('leads_container', '<?php  echo plugins_url(); ?>/placester/images/leads-null.png', 'No leads yet.  Enter an api key in the <a href="/wp-admin/admin.php?page=placester_settings">settings</a> tab or an email address in the <a href="/wp-admin/admin.php?page=placester_contact">personal</a> tab', 'width: 300px; margin-left: -150px');
        show_widget_error('piechart', '<?php  echo plugins_url(); ?>/placester/images/pie-null.png', 'No traffic data yet.  Enter an api key in the <a href="/wp-admin/admin.php?page=placester_settings">settings</a> tab or an email address in the <a href="/wp-admin/admin.php?page=placester_contact">personal</a> tab', 'width: 200px; margin: 100px 0 0 -100px');

    });

    </script>
<?php endif ?>
