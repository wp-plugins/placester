<?php
global $shortcode_subpages;

$location = !empty($location) ? $location : '';
?>
<div class="pl-sc-wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Shortcode Templates'); ?>
	<script type="text/javascript">
	jQuery(function(){
		location.href = '<?php echo $location?>';
	});
	</script>
	<div class="preview_load_spinner">
		<img src="<?php echo PL_PARENT_URL . 'images/preview_load_spin.gif'; ?>" alt="Admin page loading..." />
	</div>
</div>
<?php
