<?php
/**
 * Sets up the default framework sidebars if the theme supports them. Theme 
 * developers may choose to use or not these sidebars, create new sidebars, 
 * or unregister individual sidebars. A theme must register support for 
 * 'pls-sidebars' to use them.
 *
 * @package PlacesterBlueprint
 * @subpackage Functions
 */

/** Register widget areas. */
add_action( 'widgets_init', 'pls_register_sidebars' );

/**
 * Register the default framework sidebars. Theme developers may optionally 
 * choose to support these sidebars within their themes or add more custom 
 * sidebars to the mix.
 *
 * @since 0.0.1
 * @uses register_sidebar() Registers a sidebar with WordPress.
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function pls_register_sidebars() {

	/** Get theme-supported sidebars. */
    $sidebar_support = get_theme_support( 'pls-sidebars' );

	/** If there is no array of sidebars IDs, return. */
    if ( ! is_array( $sidebar_support[0] ) )
        return;

  /** Set up the default sidebar arguments. */
  if ( get_theme_support( 'pls-main-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'primary',
        'name' =>  'Default Sidebar',
        'description' => 'The main (primary) widget area, most often used as a sidebar on pages that do not have a custom sidebar.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-home-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'home',
        'name' =>  'Home Page',
        'description' => 'The home page widget area, most often used as a sidebar on pages that do not have a custom sidebar.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-listings-search-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'listings-search',
        'name' => 'Listings Search Sidebar',
        'description' => 'The main (primary) widget area, most often used as a sidebar.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-single-property-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'single-property',
        'name' => 'Single Property Sidebar',
        'description' => 'Widget area displayed on single property details page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  /** Set up other sidebar arguments to be available on request. */
  if ( get_theme_support( 'pls-rental-search-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'rental-search',
        'name' => 'Rental Search Sidebar',
        'description' => 'Widget area displayed on Rental Search page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-sales-search-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'sales-search',
        'name' => 'Sales Search Sidebar',
        'description' => 'Widget area displayed on Sales Search page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-contact-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'contact',
        'name' => 'Contact Page Sidebar',
        'description' => 'Widget area displayed on Contact page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-blog-index-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'blog-index',
        'name' => 'Blog Index Sidebar',
        'description' => 'Widget area displayed on Blog Index page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-single-post-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'single-post',
        'name' => 'Single Blog Post Sidebar',
        'description' => 'Widget area displayed on single blog post page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-neighborhoods-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'neighborhoods',
        'name' => 'Neighborhoods Page Sidebar',
        'description' => 'Widget area displayed on Neighborhoods Index page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-single-neighborhood-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'single-neighborhood',
        'name' => 'Single Neighborhood Sidebar',
        'description' => 'Widget area displayed on single neighborhood details page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-testimonials-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'testimonials',
        'name' => 'Testimonials Page Sidebar',
        'description' => 'Widget area displayed on Testimonials Index page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-agents-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'agents',
        'name' => 'Agents Page Sidebar',
        'description' => 'Widget area displayed on Agents Index page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

  if ( get_theme_support( 'pls-services-sidebar' ) ) {
      $sidebars[] = array(
        'id' => 'services',
        'name' => 'Services Page Sidebar',
        'description' => 'Widget area displayed on Services Index page.',
        'before_widget' => '<section id="%1$s" class="widget %2$s widget-%2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
      );
  }

   // pls_dump($sidebar_support);

    // loop through and create sidebars
    foreach ($sidebars as $sidebar) {
        if (in_array($sidebar['id'], $sidebar_support[0])) {
            register_sidebar( $sidebar );
        }
    }

}