<?php
/**
 * Template Name: Blog Posts
 *
 * This is the template for the blog page
 *
 * @package PlacesterBlueprint
 * @subpackage Template
 */
?>

<?php PLS_Route::get_template_part( 'loop-meta'); // Loads the loop-meta.php template. ?>

<?php query_posts( 'post_type=post' ); // Get the blog posts ?>

<?php PLS_Route::get_template_part( 'loop-entries' ) ?>
