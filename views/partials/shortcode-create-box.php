<?php
/**
 * Displays main shortcode edit meta box used in the shortcode edit view
 */

// get list of shortcodes w/ attrs
if (empty($pl_shortcodes_attr)) {
	$pl_shortcodes_attr = PL_Shortcode_CPT::get_shortcode_attrs();
}

$options_class = $filters_class = '';
?>
<div class="postbox">

	<h3>Create Shortcode</h3>

	<div class="inside">

		<!-- Type and Template -->
		<div>

			<!-- Type -->
			<section class="post_types_list_wrapper row-fluid">

				<div class="span2">
					<label class="section-label" for="pl_sc_shortcode_type">Type:</label>
				</div>

				<div class="span9">

					<select id="pl_sc_shortcode_type" name="shortcode" class="">

						<option id="pl_sc_shortcode_undefined" value="undefined">Select</option>

						<?php
						foreach( $pl_shortcodes_attr as $pl_shortcode => $sct_args ):
							$link_class = ($pl_shortcode == $values['shortcode']) ? 'selected-type' : '';
							$selected = ( !empty($link_class) ) ? 'selected="selected"' : '';
							?>
							<option id="pl_sc_shortcode_<?php echo $sct_args['shortcode']; ?>" class="<?php echo $link_class; ?>" value="<?php echo $pl_shortcode; ?>" <?php echo $selected; ?>>
								<?php echo $sct_args['title']; ?>
							</option>
							<?php
							// build our class list for the Options and Templates sections in this loop
							if (!empty($sct_args['options'])) {
								$options_class .= ' '.$pl_shortcode;
							}
							if (!empty($sct_args['filters'])) {
								$filters_class .= ' '.$pl_shortcode;
							}
						endforeach;
						?>
					</select>

				</div>

			</section><!-- /.post_types_list_wrapper -->

			<!-- Template / Layout -->
			<section id="choose_template" style="display:none;">
				<div class="span2">
					<label class="section-label" for="pl_template">Template:</label>
				</div>
				<div>
					<?php foreach( $pl_shortcodes_attr as $pl_shortcode => $sct_args ): ?>
						<?php if(!empty($sct_args['options']['context'])):?>
							<div class="pl_template_block <?php echo $pl_shortcode; ?>" id="<?php echo $sct_args['shortcode'];?>_template_block" style="display: none;">
								<?php
								$value = isset( $values[$pl_shortcode]['context'] ) ? $values[$pl_shortcode]['context'] : $sct_args['options']['context']['default'];
								PL_Router::load_builder_partial('shortcode-template-list.php', array(
											'shortcode' => $sct_args['shortcode'],
											'post_type' => $pl_shortcode,
											'select_name' => $pl_shortcode.'[context]',
											'class' => '',
											'value' => $value,
									)
								);
								?>
							</div>
						<?php endif;?>
					<?php endforeach;?>
					<div class="edit-sc-template-edit">
						<a id="edit_sc_template_create" href="" id="create-new-template-link">(create new)</a>
						<a id="edit_sc_template_edit" href="" id="create-new-template-link" style="display:none;">(edit)</a>
					</div>
				</div>
			</section><!-- /edit-sc-choose-template -->

		</div><!-- /#post_types_list -->


		<!-- Options / Filters -->
		<div id="widget_meta_wrapper"  class="sc-meta-section" style="display: none;">

			<div class="pl_widget_block <?php echo $options_class;?>">
				<div>
					<h3>Options:</h3>
				</div>
				<?php
				// build options for each shortcode type
				foreach( $pl_shortcodes_attr as $pl_shortcode => $sct_args ) {?>
					<div class="pl_widget_block <?php echo $pl_shortcode;?>">
					<?php
					foreach($sct_args['options'] as $field => $f_args) {
						if ($field == 'context') {
							// template field already handled
							continue;
						}
						elseif ($field == 'pl_featured_listing_meta') {
							// create button and placeholder for selected listings
							echo pls_generate_featured_listings_ui(array(
									'name' => 'Featured Meta',
									'desc' => '',
									'id' => $pl_shortcode.'-pl_featured_listing_meta',
									'type' => 'featured_listing'
								) ,$pl_featured_meta_value
								, $pl_shortcode);
							continue;
						}
						$value = isset( $values[$pl_shortcode][$field] ) ? $values[$pl_shortcode][$field] : $f_args['default'];
						$_POST[$pl_shortcode][$field] = $value;
						$f_args['css'] = (!empty($f_args['css']) ? $f_args['css'].' ' : '') . $pl_shortcode;
						PL_Form::item($field, $f_args, 'POST', $pl_shortcode, 'sc_edit_', true);
					}?>
					</div>
					<?php
				}
				?>
			</div>

			<div class="pl_widget_block <?php echo $filters_class;?>">
				<div>
					<h3>Filters:</h3>
				</div>
				<?php
				// fill POST array for the forms (required after new widget is created)
				foreach( $pl_shortcodes_attr as $pl_shortcode => $sct_args ) {
					if (!empty($sct_args['filters'])) {
						foreach($sct_args['filters'] as $f_key=>$f_args) {
							$value = isset( $values[$pl_shortcode][$f_key] ) ? $values[$pl_shortcode][$f_key] : '';
							if ($value) {
								$_POST[$pl_shortcode][$f_key] = $value;
							}
						}
						?>
						<div class="pl_widget_block <?php echo $pl_shortcode?>">
						<?php PL_Form::generate_form($sct_args['filters'],
								array('method' => "POST",
									'title' => true,
									'wrap_form' => false,
									'include_submit' => false,
									'id' => $pl_shortcode.'-filters',
									'parent' => $pl_shortcode,
									'echo_form' => true,
								),
								'sc_edit_');?>
						</div>
						<?php
					}
				}
				?>
			</div><!-- /.pl_widget_block -->

			<?php wp_nonce_field( 'pl_cpt_meta_box_nonce', 'meta_box_nonce' );?>

			<div class="clear"></div>

		</div> <!-- /#widget-meta-wrapper -->
	</div><!-- /.inside -->
</div><!-- /.postbox -->