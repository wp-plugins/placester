<?php
global $shortcode_subpages, $page_now, $plugin_page;

$action = 'edit';
$ID = 'pl_listing_template__1';
$notice = $message = '';
$nonce_action = 'edit-sc-template_' . $ID;
$template = PL_Listing_Customizer::get_template($ID);

if ($action == 'edit' && !empty($_POST['submit'])) {
	if (empty($_POST['title'])) {
		$notice = 'Please provide a title for the template.';
	}
	else {
		$id = PL_Listing_Customizer::save_template($ID, $_POST);
		if ($id) {
			$message = 'Settings saved.';
		}
	}
	// unescape form fields
	foreach($_POST as $key=>&$val) {
		if (!is_array($val)) {
			$val = stripcslashes($val);
		}
	}
	$template = array_merge($template, $_POST);
}

$title = 'Custom Template';
$form_link = '';
$delete_link = $page_now.'?page='.$plugin_page.'&action=delete&id='.$ID;
$form_action = 'edit';
$used_by = PL_Listing_Customizer::template_used_by($ID);
$tpl_args = PL_Listing_Customizer::get_args();


?>
<div class="wrap pl-sc-wrap">
	<?php echo PL_Helper_Header::pl_subpages('placester_shortcodes', $shortcode_subpages, 'Listing Details Customizer'); ?>

	<div id="pl_sc_tpl_edit">
		<?php if ( $notice ) : ?>
		<div id="notice" class="error"><p><?php echo $notice ?></p></div>
		<?php endif; ?>
		<?php if ( $message ) : ?>
		<div id="message" class="updated"><p><?php echo $message; ?></p></div>
		<?php endif; ?>

		<p>
		Use this form to build a template that can be used to customize the page that displays an individual property listing.
		</p>

		<div id="notice" class="hide-if-js error"><p>JavaScript is required to use the template editor. Please enable JavaScript on your browser and reload this page.</p></div>

		<form name="post" action="<?php echo $form_link?>" method="post" id="post"<?php do_action('post_edit_form_tag'); ?> class="hide-if-no-js">
			<?php wp_nonce_field($nonce_action); ?>
			<input type="hidden" id="hiddenaction" name="action" value="<?php echo esc_attr( $form_action ) ?>" />
			<input type="hidden" id="originalaction" name="originalaction" value="<?php echo esc_attr( $form_action ) ?>" />
			<input type="hidden" id="id" name="id" value="<?php echo esc_attr($ID) ?>" />
			<input type="hidden" id="title" name="title" value="<?php echo $title ?>" />
			
			<div id="poststuff">
				<div id="post-body" class="metabox-holder">
					<div id="post-body-content">

						<div id="pl-sc-tpl-meta-box" class="pl-sc-meta-box">
							<div class="postbox ">
								<h3>Listing Page Template</h3>
							
								<div class="inside">
							
									<!-- Template Contents -->
									<section class="row-fluid lc-meta-section">
							
										<!-- Template HTML/CSS -->
										<div class="span8">
											<div class="pl_template_block">
												<?php
												foreach($tpl_args['template'] as $field => $f_args) {
													if ($action!='edit') {
														$_POST[$field] = !empty($f_args['default']) ? $f_args['default'] : '';
													}
													else {
														$_POST[$field] = !empty( $template[$field] ) ? $template[$field] : '';
													}
													$f_args['css'] = (!empty($f_args['css'])?$f_args['css'].' ':'').$field;
													PL_Form::item($field, $f_args, 'POST', '', 'pl-lc-tpl-edit', true);
												}?>
											</div>
										</div>
											
										<!-- Search Sub-Shortcodes -->
										<div id="subshortcodes" class="span2">
											<h3>Template Tags</h3>
											<?php $subcodes = '';?>
											<?php foreach($tpl_args['subcodes'] as $subcode=>$atts): ?>
												<?php $subcodes .= '<h4 class="subcode"><a href="#">[' . $subcode . ']</a></h4>';?>
												<?php if (!empty($atts['help'])):?>
													<?php $subcodes .= '<div class="description subcode-help">'. $atts['help'] .'</div>';?>
												<?php endif;?>
											<?php endforeach;?>
											<p>Use the following tags to customize the Page Body of your template. When the template is rendered in a web page, the tag will be replaced with the corresponding attribute of the property listing:<br /><?php echo $subcodes?></p>
										</div>
							
									</section><!-- /Template Contents -->
							
									<?php wp_nonce_field( 'pl_sc_meta_box_nonce', 'meta_box_nonce' );?>
							
									<div class="clear"></div>
							
								</div><!-- /.inside -->
							
							</div><!-- /.postbox -->
						</div><!-- /pl-lc-tpl-meta-box -->
						
					</div>

				</div><!-- /post-body -->
			</div>
			
			<p class="submit"><input type="submit" value="Save Changes" class="button-primary" id="submit" name="submit"></p>
			
		</form>

		<div id="ajax-response"></div>
	</div>
</div>