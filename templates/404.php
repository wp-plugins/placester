<?php
/**
 * 404 Template
 *
 * The 404 template is used when a reader visits an invalid URL on your site. By default, the template will 
 * display a generic message.
 *
 * @package PlacesterBlueprint
 * @subpackage Template
 * @link http://codex.wordpress.org/Creating_an_Error_404_Page
 */
?>
<section class="left-content">

	<section id="post-0" <?php post_class() ?>>
        <h2 class="error-404-title entry-title"><?php 'Not Found'; ?></h2>
		<section class="entry-content">
 
            <p>
                <?php printf( 'You tried going to %1$s, and it doesn\'t exist. All is not lost! You can search for what you\'re looking for.', '<code>' . home_url( esc_url( $_SERVER['REQUEST_URI'] ) ) . '</code>' ); ?>
            </p>

            <?php get_search_form(); // Loads the searchform.php template. ?>

		</section><!-- .entry-content -->

	</section>

</section>
