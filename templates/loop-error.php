<?php
/**
 * Loop Error Template
 *
 * Displays an error message when no posts are found.
 *
 * @package PlacesterBlueprint
 * @subpackage Template
 */
?>
	<article id="post-0" <?php post_class() ?>>

		<section class="entry-content">

			<p><?php _e( 'Apologies, but no entries were found.', pls_get_textdomain() ); ?></p>

		</section><!-- .entry-content -->

	</article>

