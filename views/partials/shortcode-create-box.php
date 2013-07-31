<?php
/**
 * Displays main shortcode edit meta box used in the shortcode edit view for shortcodes related to property listings
 */

// get list of shortcodes w/ attrs
if (empty($pl_shortcodes_attr)) {
	$pl_shortcodes_attr = PL_Shortcode_CPT::get_shortcode_attrs();
}

$options_class = $filters_class = '';

// build list of sort options which will be the same for any sc that uses a sort list 
$sort_list = array();
foreach( $pl_shortcodes_attr as $pl_shortcode => $sct_args ) {
	if (!empty($sct_args['filters'])) {
		foreach($sct_args['filters'] as $f_key=>$f_args) {
			$skip = false;
			switch($f_key) {
				case 'metadata':
					$key = 'cur_data';
					break;
				case 'custom':
					$key = 'uncur_data';
					break;
				case 'rets':
					$skip = true;
					break;
				default:
					$key = $f_key;
			}
			if (!$skip) {
				if (!empty($f_args['type'])) {
					if ($f_args['type']=='bundle') {
						if (isset($f_args['bound']) && is_array($f_args['bound'])) {
							$params = ( isset($f_args['bound']['params']) ? $f_args['bound']['params'] : array() ) ;
							// If "params" is a single element, encapsulate in an array...
							if ( isset($params) && !is_array($params) ) {
								$params = array($params);
							}
							$bundle_list = call_user_func_array(array($f_args['bound']['class'], $f_args['bound']['method']), $params);
							foreach($bundle_list as $f_bargs) {
								$sort_list[$key.'.'.$f_bargs['key']] = $f_bargs['name'];
							}
						}
					}
					elseif ($f_args['type']!='checkbox') {
						$sort_list[$key] = $f_args['label'];
					}
				}
				if (empty($f_args['type'])) {
					// group of options
					foreach($f_args as $f_bkey=>$f_bargs) {
						if (!empty($f_bargs['type'])) {
							if ($f_bargs['type']!='checkbox') {
								if (strpos($f_bkey, 'min_')===false && strpos($f_bkey, 'max_')===false) {
									$sort_list[$key.'.'.$f_bkey] = $f_bargs['label'];
								}
							}
						}
					}
				}
			}
		}
		break;
	}
}

?>
<div class="postbox">

	<h3>Create Shortcode</h3>

	<div class="inside">

		<!-- Type and Template -->
		<div>

			<!-- Type -->
			<div class="post_types_list_wrapper row-fluid">

				<div class="span2">
					<label class="section-label" for="pl_sc_shortcode_type">Type:</label>
				</div>

				<div class="span8">

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

			</div><!-- /.post_types_list_wrapper -->

			<!-- Template / Layout -->
			<div id="choose_template" class="row-fluid" style="display:none;">
				<div class="span2">
					<label class="section-label" for="pl_template">Template:</label>
				</div>
				<div class="span8">
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
						<a id="edit_sc_template_edit" href="" class="create-new-template-link" style="display:none;">Edit</a>
						<a id="edit_sc_template_duplicate" href="" class="duplicate-new-template-link">Duplicate</a>
						<a id="edit_sc_template_copy" href="" class="create-new-template-link">Copy</a>
						<a id="edit_sc_template_create" href="" class="create-new-template-link">Create</a>
					</div>
				</div>
			</div><!-- /edit-sc-choose-template -->

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
					foreach($sct_args['options'] as $field => &$f_args) {
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
						elseif ($field == 'sort_by_options') {
							// save the full list of sort by names so we can use on the front end
							$f_args['options'] += $sort_list;
							asort($f_args['options']);
							update_option('pl_'.$pl_shortcode.'_formval_'.$field, $f_args['options']);
						}
						elseif ($field == 'sort_by') {
							// should have the same options as sort_by_options
							$f_args['options'] = $sct_args['options']['sort_by_options']['options'];
						}				
						$value = isset( $values[$pl_shortcode][$field] ) ? $values[$pl_shortcode][$field] : $f_args['default'];
						$_POST[$pl_shortcode][$field] = $value;
						$f_args['css'] = (!empty($f_args['css']) ? $f_args['css'].' ' : '') . $pl_shortcode;
						PL_Form::item($field, $f_args, 'POST', $pl_shortcode, 'sc_edit_', true);
					}
					?>
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
						<?php
							PL_Form::generate_form($sct_args['filters'],
								array('method' => "POST",
									'title' => true,
									'wrap_form' => false,
									'include_submit' => false,
									'id' => $pl_shortcode.'-filters',
									'parent' => $pl_shortcode,
									'echo_form' => true,
								),
								'sc_edit_');
						?>
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