<?php

class PLS_Widget_Services extends WP_Widget {

	/**
	 * Widget Constuctor
	 */
	function PLS_Widget_Services() {
		$widget_ops = array( 'classname' => 'pls_services_widget', 'description' => 'Featured Services Widget' );

		/* Widget control settings. */
		$control_ops = array( 'width' => 200, 'height' => 350 );

		/* Create the widget. */
		$this->WP_Widget( 'PLS_Widget_Services', 'Placester: Services Widget', $widget_ops, $control_ops );
	}

  /**
   * Widget Output
   *
   * @param $args (array)
   * @param $instance (array) Widget values.
   */
  function widget( $args, $instance ) {
    // widget output
    extract($args);

    $title = empty( $instance['title'] ) ? ' ' : apply_filters( 'widget_title', $instance['title'] );
    $number_of_posts = $instance['number_of_posts'];

    if ( !post_type_exists('service')) {
     return false;
    }

    // Testimonials start
    $args = array( 'numberposts' => $number_of_posts, 'post_type' => 'service' );
    $services = get_posts( $args );

    $widget_body = '<section class="featured-services-widget">';

    foreach( $services as $service ) : setup_postdata($service);

         $post_item = array(
           'url' => $service->guid,
           'content' => $service->post_content,
           'title' => $service->post_title,
           'image' => '',
           'id' => $service->ID
         );

         if (has_post_thumbnail( $service->ID ) ) {
           $post_image = wp_get_attachment_image_src( get_post_thumbnail_id( $service->ID ) );
           $post_item['image'] = '<img src="'.$post_image[0].'" style="width:100%;" />';
         }

         ob_start();
         ?>
         <article class="featured-service" itemscope itemtype="http://schema.org/Offer">
           <div class="service-image">
             <?php echo $post_item['image']; ?>
           </div>
           <h4 itemprop="name"><?php echo $post_item['title']; ?></h4>
           <p><?php echo PLS_Format::shorten_text($post_item['content'], 130); ?></p>
           <p><a href="<?php echo $post_item['url']; ?>" itemprop="url">Read More</a></p>
         </article>
         <?php 

         $single_testimonial = ob_get_clean();

         /** Wrap the post in an article element and filter its contents. */
         $single_testimonial = apply_filters( 'pls_widget_services_post_inner', $single_testimonial, $post_item, $instance, $widget_id );

         /** Append the filtered post to the post list. */
         $widget_body .= apply_filters( 'pls_widget_services_post_outer', $single_testimonial, $post_item, $instance, $widget_id );


       endforeach;

     $widget_body .= '</section>';

     // Display Widget
     echo $before_widget;
       if( $instance['title'] ) {
         echo $before_title . $title . $after_title;
       }
       echo $widget_body;
     echo $after_widget;
   }

	/**
	 * Update Widget
	 *
	 * @param $new_instance (array) New widget values.
	 * @param $old_instance (array) Old widget values.
	 *
	 * @return (array) New values.
	 */
	function update( $new_instance, $old_instance ) {
		// Save widget options
		$instance = $old_instance;

		//Strip tags from title to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		$instance['number_of_posts'] = $new_instance['number_of_posts'];

		return $instance;
	}

	/**
	 * Widget Options Form
	 *
	 * @param $instance (array) Widget values.
	 */
	function form( $instance ) {

		// Defaults
		$instance = wp_parse_args( (array) $instance, array( 
											'title' => '',
											'number_of_posts' => '1'
		) );

		// Values
		$title = esc_attr( $instance['title'] );
		$number_of_posts = $instance['number_of_posts'];
		?>

		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">Title:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('number_of_posts'); ?>">Number of Services to display:</label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'number_of_posts' ); ?>" name="<?php echo $this->get_field_name( 'number_of_posts' ); ?>" type="text" value="<?php echo $number_of_posts; ?>" />
		</p>

		<?php
	}
}

add_action( 'widgets_init', create_function( '', 'return register_widget("PLS_Widget_Services");' ) );
