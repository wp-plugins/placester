<?php
/**
 * Loop Entries Templates
 *
 * Loops over a list entries and displays them. It is include on the archive and blog pages.
 *
 * @package PlacesterBlueprint
 * @subpackage Template
 */
?>
<?php if (have_posts()) : ?>

    <?php while (have_posts()) : the_post(); ?>

        <?php pls_do_atomic( 'before_entry' ); ?>

        <article <?php post_class() ?> id="post-<?php the_ID(); ?>">

            <?php pls_do_atomic( 'open_entry' ); ?>

            <header>

                <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf( 'Permalink to %1$s', the_title_attribute( 'echo=false' ) ) ?>"><?php the_title(); ?></a></h2>
                <time datetime="<?php the_time('Y-m-d')?>"><?php the_time('F jS, Y') ?></time>
                <span class="author"><?php printf( 'by %1$s', get_the_author()) ?></span>

            </header>

            <?php pls_do_atomic( 'before_entry_content' ); ?>

            <div class="entry-summary">
                <?php the_excerpt(); ?>
            </div><!-- .entry-summary -->

            <div class="entry-meta">
                <a class="more-link" href="<?php the_permalink() ?>"> <?php _e( 'Continue reading <span class="meta-nav">&rarr;</span>', pls_get_textdomain() ) ?></a>
            </div><!-- .entry-meta -->

            <?php pls_do_atomic( 'after_entry_content' ); ?>

            <footer>
                <?php the_tags( __( 'Tags', pls_get_textdomain() ) . ': ', ', ', '<br />'); ?> 
                Posted in <?php the_category( ', ' ) ?>
                | <?php edit_post_link( __( 'Edit', pls_get_textdomain() ), '', ' | ' ); ?>
                <?php comments_popup_link( __( 'No Comments', pls_get_textdomain() ) . '&#187;', __( '1 Comment', pls_get_textdomain() ) . '&#187;', __( '% Comments', pls_get_textdomain() ) . '&#187;' ); ?>
            </footer>

            <?php pls_do_atomic( 'close_entry' ); ?>

        </article>

        <?php pls_do_atomic( 'after_entry' ); ?>

    <?php endwhile; ?>

    <nav class="posts">
        <div class="prev"><?php next_posts_link( __( '&laquo; Older Entries', pls_get_textdomain() ) ) ?></div>
        <div class="next"><?php previous_posts_link( __( 'Newer Entries &raquo;', pls_get_textdomain() ) ) ?></div>
    </nav>
    
<?php else : ?>
    
    <?php get_template_part( 'loop-error' ); ?>
    
<?php endif; ?>
