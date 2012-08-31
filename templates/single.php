<?php
/**
 * Single template
 *
 * This template is used when a single post or page is viewed.
 *
 * @package PlacesterBlueprint
 * @subpackage Template
 */

?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

    <?php pls_do_atomic( 'before_entry' ); ?>
    
    <article <?php post_class() ?> id="post-<?php the_ID(); ?>" class="grid_8 alpha">

        <?php pls_do_atomic( 'open_entry' ); ?>

        <header>
            <?php PLS_Route::get_template_part( 'loop', 'meta' ) ?>
        </header>

        <?php pls_do_atomic( 'before_entry_content' ); ?>

        <?php the_content( __( 'Read the rest of this entry &raquo;', pls_get_textdomain() ) ); ?>

        <div class="entry-meta">
            <?php wp_link_pages( array( 'before' => '<p><strong>' . __( 'Pages', pls_get_textdomain() ) . ':</strong> ', 'after' => '</p>', 'next_or_number' => 'number' ) ); ?>
            <?php the_tags( '<p class="blog-tags blog-meta"><span>' . __( 'Tags', pls_get_textdomain() ) . ':</span> ', ', ', '</p>'); ?>
        </div>

        <?php pls_do_atomic( 'after_entry_content' ); ?>

        <footer>

            <p class="blog-meta"> 
            <?php printf( __( 'This entry was posted by %1$s, on <time datetime="%2$s">%3$s</time> at <time>%4$s</time> and is filed under %5$s.', pls_get_textdomain() ), get_the_author(), get_the_time( 'Y-m-d' ), get_the_time( 'l, F jS, Y' ), get_the_time(), get_the_category_list( ', ' ) ); ?> 

                <?php if ( comments_open() ) {
                    _e( 'You can <a href="#respond">skip to the end</a> and leave a response.', pls_get_textdomain() );

                } else {
                    _e( 'Comments are currently closed.', pls_get_textdomain() );

                } edit_post_link( __( 'Edit this entry', pls_get_textdomain() ), ' ', '.' ); ?>
            </p>

        </footer>

        <nav class="grid_8 alpha">
            <div class="prev"><?php previous_post_link( '&laquo; %link' ) ?></div>
            <div class="next"><?php next_post_link( '%link &raquo;' ) ?></div>
        </nav>

        <?php comments_template( '/comments.php', true ); ?>

        <?php pls_do_atomic( 'close_entry' ); ?>
        
    </article>

    <?php pls_do_atomic( 'after_entry' ); ?>
    
<?php endwhile; else: ?>
    
    <?php get_template_part( 'loop', 'error' ); ?>
    
<?php endif; ?>
