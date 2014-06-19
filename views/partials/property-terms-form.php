<?php
/**
 * Edit a property location (neighborhood, etc) page. Based on WP Edit tag form.
 */

// don't load directly
if (!defined('ABSPATH') )
	die('-1');

if (empty($tag_ID) ) { ?>
	<div id="message" class="updated"><p><strong><?php _e('You did not select an item for editing.' ); ?></strong></p></div>
<?php
	return;
}

do_action($taxonomy . '_pre_edit_form', $tag, $taxonomy); ?>

<div class="wrap">
	<?php echo PL_Helper_Header::pl_settings_subpages(); ?>

	<h2>Edit <?php echo $tax->labels->singular_name; ?> Page</h2>
	<div id="ajax-response"></div>
	<form name="edittag" id="edittag" method="post" action="" class="validate">
	<input type="hidden" name="action" value="editedtag" />
	<input type="hidden" name="tag_ID" value="<?php echo esc_attr($tag->term_id) ?>" />
	<input type="hidden" name="taxonomy" value="<?php echo esc_attr($taxonomy) ?>" />
	<?php wp_original_referer_field(true, 'previous'); wp_nonce_field('update-tag_' . $tag_ID); ?>
		<table class="form-table">
			<tr class="form-field form-required">
				<th scope="row" valign="top"><label for="name"><?php _ex('Name', 'Taxonomy Name'); ?></label></th>
				<td><input name="name" id="name" type="text" disabled="disabled" value="<?php if (isset($tag->name ) ) echo esc_attr($tag->name); ?>" />
				<p class="description"><?php _e('The name is how it appears on your site.'); ?></p></td>
			</tr>
	<?php if (!global_terms_enabled() ) { ?>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="slug"><?php _ex('Slug', 'Taxonomy Slug'); ?></label></th>
				<td><input name="slug" id="slug" type="text" disabled="disabled" value="<?php if (isset($tag->slug ) ) echo esc_attr(apply_filters('editable_slug', $tag->slug)); ?>" size="40" />
				<p class="description"><?php _e('The &#8220;slug&#8221; is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.'); ?></p></td>
			</tr>
	<?php } ?>
	<?php if (is_taxonomy_hierarchical($taxonomy) ) : ?>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="parent"><?php _ex('Parent', 'Taxonomy Parent'); ?></label></th>
				<td>
					<?php wp_dropdown_categories(array('hide_empty' => 0, 'hide_if_empty' => false, 'name' => 'parent', 'orderby' => 'name', 'taxonomy' => $taxonomy, 'selected' => $tag->parent, 'exclude_tree' => $tag->term_id, 'hierarchical' => true, 'show_option_none' => __('None'))); ?>
				</td>
			</tr>
	<?php endif; // is_taxonomy_hierarchical() ?>
			<tr class="form-field">
				<th scope="row" valign="top"><label for="description"><?php _ex('Description', 'Taxonomy Description'); ?></label></th>
				<?php /*
				<td><?php wp_editor($tag->description, 'description') ?><br />
				*/ ?>
				<td><textarea name="description" id="description" rows="5" cols="50" class="large-text"><?php echo $tag->description; // textarea_escaped ?></textarea><br />
				<span class="description"><?php _e('The description is not prominent by default, however some themes may show it.'); ?></span></td>
			</tr>
			<?php do_action($taxonomy . '_edit_form_fields', $tag, $taxonomy); ?>
		</table>
	<?php
	do_action($taxonomy . '_edit_form', $tag, $taxonomy);

	submit_button(__('Update'));
	?>
	</form>
</div>
