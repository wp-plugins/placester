<?php
/**
 * The Agents widget gives users the ability to add agent information.
 *
 * @package PlacesterBlueprint
 * @subpackage Classes
 */

/**
 * Agent Widget Class
 *
 * @since 0.0.1
 */
class PLS_Widget_Agent extends WP_Widget {

	/**
	 * Textdomain for the widget.
	 * @since 0.0.1
	 */
	var $textdomain;

	/**
	 * Set up the widget's unique name, ID, class, description, and other options.
	 * @since 0.0.1
	 */
	public function __construct() {

		/* Set the widget textdomain. */
		$this->textdomain = pls_get_textdomain();

		/* Set up the widget options. */
		$widget_options = array(
			'classname' => 'pls-agent',
			'description' => 'A widget that displays information about the agent.'
		);

		/* Create the widget. */
        parent::__construct( "pls-agent", 'Placester: Agent Widget', $widget_options );
	}


	/**
	 * Outputs and filters the widget.
     * 
     * The widget connects to the plugin using the framework plugin api class. 
     * If the class returns false, this means that either the plugin is 
     * missing, either the it has no API key set.
     *
	 * @since 0.0.1
	 */
	public function widget( $args, $instance ) {

        list($args, $instance) = self::process_defaults($args, $instance);

        /** Extract the arguments into separate variables. */
        extract( $args, EXTR_SKIP );

        /** Get the agent information from the plugin. */
        $agent = PLS_Plugin_API::get_user_details();

        $agent_array = array();

        // pls_dump($agent);
        /** If the plugin is active, and has an API key set... */
        if ( $agent ) {

            /* Output the theme's $before_widget wrapper. */
            echo $before_widget;

            /* If a title was input by the user, display it. */
            $widget_title = '';
            if ( !empty( $instance['title'] ) )
                $widget_title = $before_title . apply_filters( 'widget_title',  $instance['title'], $instance, $this->id_base ) . $after_title;

            /** This array will hold the html for the agent info sections and will be passed to the filters. */
            $agent_html = $instance;

            unset( $agent_html['title'] );

            // Add Name
            if ( !empty($instance['name_custom']) ) {
              // if admin set custom name
              $agent_html['name'] = self::checkForCustom($instance, 'name');
              $agent_array['name'] = $instance['name_custom'];
            } else {
              // otherwise use admin's name
              if ( ! empty( $instance['name'] ) && ( ! empty( $agent['user']['first_name'] ) || ! empty( $agent['user']['last_name'] ) ) ) {
                  $agent_html['name'] = pls_h_p(trim( $agent['user']['first_name'] . ' ' . $agent['user']['last_name'] ), array( 'class' => 'fn h5', 'itemprop' => 'name'  ) );
                  $agent_array['name'] = $agent['user']['first_name'] . ' ' . $agent['user']['last_name'];
              } else {
                  $agent_html['name'] = '';
                  $agent_array['name'] = '';
              }
            }
            
            // Add Email
            if ( !empty($instance['email_custom']) ) {
              // if admin set custom email
              $agent_html['email'] = self::checkForCustom($instance, 'email');
              $agent_array['email'] = $instance['email_custom'];
            } else {
              // otherwise use admin's email
              if ( ! empty( $instance['email'] ) && ! empty( $agent['user']['email'] ) ) {
                  $agent_html['email'] = pls_h_p(pls_h_a( "mailto:{$agent['user']['email']}", $agent['user']['email'] ), array( 'class' => 'email', 'itemprop' => 'email' ) );
                  $agent_array['email'] = $agent['user']['email'];
              } else {
                  $agent_html['email'] = '';
                  $agent_array['email'] = '';
              }
            }
            
            // Add Phone
            if ( !empty($instance['phone_custom']) ) {
              // if admin set custom phone
              $agent_html['phone'] = self::checkForCustom($instance, 'phone');
              $agent_array['phone'] = $instance['phone_custom'];
            } else {
              // otherwise use admin's phone
              if ( ! empty( $instance['phone'] ) && ! empty( $agent['user']['phone'] ) ) {
                  $agent_html['phone'] = pls_h_p(PLS_Format::phone($agent['user']['phone']), array( 'class' => 'phone', 'itemprop' => 'phone' ) );
                  $agent_array['phone'] = PLS_Format::phone( $agent['user']['phone'], array( 'class' => 'phone', 'itemprop' => 'phone' ) );
              } else {
                  $agent_html['phone'] = '';
                  $agent_array['phone'] = '';
              }
            }
            
            // Add Description
            if ( !empty($instance['description_custom']) ) {
              // if admin set custom description
              $agent_html['description'] = self::checkForCustom($instance, 'description');
              $agent_array['description'] = $instance['description_custom'];
            } else {
              // otherwise use admin's descriptions
              if ( ! empty( $instance['description']) && pls_get_option('pls-user-description') ) {
                  $agent_bio = pls_get_option('pls-user-description');
                  $agent_html['description'] = pls_h_p($agent_bio, array( 'class' => 'desc p4', 'itemprop' => 'description' ) );
                  $agent_array['description'] = $agent_bio;
              } else {
                  $agent_html['description'] = '';
                  $agent_array['description'] = '';
              }
            }


            // Add Photo
            if ( !empty($instance['image_uri']) ) {
              // if admin set custom photo
              self::checkForCustom($instance, 'photo');
              $agent_array['photo'] = $instance['image_uri'];
            } else {
              // otherwise use admin's photo
              $user_image_option = pls_get_option('pls-user-image');
              if ( ! empty( $instance['photo'] ) && ! empty( $user_image_option ) ) {
                  $agent_html['photo'] = pls_h_img( @pls_get_option('pls-user-image'), trim( $agent['user']['first_name'] . ' ' . $agent['user']['last_name'] ), array( 'class' => 'photo', 'itemprop' => 'image' ) + array() + array() );
                  $agent_array['photo'] = $user_image_option;
              } else {
                if (isset($agent['user']['headshot'])) {
                  $agent_html['photo'] = pls_h_img( $agent['user']['headshot'], trim( $agent['user']['first_name'] . ' ' . $agent['user']['last_name'] ), array( 'class' => 'photo', 'itemprop' => 'image' ) + array() + array() );
                  $agent_array['photo'] = $agent['user']['headshot'];
                } else {
                  $agent_array['photo'] = '';
                }
              }
            }

            // Form the HTML elements
            // photo
            $agent_html['photo'] = '<img class="pls-agent-phone" src="'.esc_url($instance['image_uri']).'" />';
            // texts
            $agent_info = array('name', 'email', 'phone', 'description');
            foreach ($agent_info as $value) {
              $agent_html[$value] = pls_h_p(
                $agent_html[$value],
                array(
                  'class' => 'pls-agent-'.$value
                )
              );
            }
            
            
            /** Combine the agent information. */
            $widget_body = $agent_html['photo'] . $agent_html['name'] . $agent_html['email'] . $agent_html['phone'] . $agent_html['description']; 

            /** Wrap the agent information in a section element. */
            $widget_body = apply_filters( 'pls_widget_agent_inner', $widget_body, $agent_html, $agent_array, $instance, $agent, $widget_id );

            /** Apply a filter on the whole widget */
            echo apply_filters( 'pls_widget_agent', $widget_title . $widget_body, $widget_title, $before_title, $after_title, $widget_body, $agent_html, $agent,  $instance, $widget_id );

            /* Close the theme's widget wrapper. */
            if ($args['clearfix']) {
                echo '<div class="clearfix"></div>';
            }
            
            echo $after_widget;

        } elseif ( current_user_can( 'administrator' ) ) {

            /** Display an error message if the user is admin. */
            // echo pls_get_no_plugin_placeholder( $widget_id );
        }
	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
     *
	 * @since 0.0.1
	 */
	public function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['name'] = ( isset( $new_instance['name'] ) ? 1 : 0 );
        $instance['name_custom'] = strip_tags( $new_instance['name_custom'] );
        $instance['email'] = ( isset( $new_instance['email'] ) ? 1 : 0 );
        $instance['email_custom'] = strip_tags( $new_instance['email_custom'] );
        $instance['phone'] = ( isset( $new_instance['phone'] ) ? 1 : 0 );
        $instance['phone_custom'] = strip_tags( $new_instance['phone_custom'] );
        $instance['description'] = ( isset( $new_instance['description'] ) ? 1 : 0 );
        $instance['description_custom'] = strip_tags( $new_instance['description_custom'] );
        $instance['photo'] = ( isset( $new_instance['photo'] ) ? 1 : 0 );
        $instance['image_uri'] = strip_tags( $new_instance['image_uri'] );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
     *
	 * @since 0.0.1
	 */
	public function form( $instance ) {

		/** Set up the default form values. */
		$defaults = array(
            'title' => 'Agent',
            'name' => true,
            'name_custom' => '',
            'email' => true,
            'email_custom' => '',
            'phone' => true,
            'phone_custom' => '',
            'description' => false,
            'description_custom' => '',
            'photo' => true,
            'image_uri' => ''
		);

		/** Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

        /** Print the backend widget form. */
        echo pls_h_div(
            /** Print the Title input */
            pls_h_p( 
                pls_h_label( 
                    'Title' . ':' .
                    pls_h( 
                        'input',
                        array(
                            'type' => 'text',
                            'class' => 'widefat',
                            'id' => $this->get_field_id( 'title' ),
                            'name' => $this->get_field_name( 'title' ),
                            'value' => esc_attr( $instance['title'] ),
                            'style' => 'font-weight:normal'
                        ) 
                    ), 
                    $this->get_field_id( 'title' ),
                    array('style' => 'font-weight:bold')
                ) 
            ) . 
            // Name checkbox and override input
            pls_h_p( 
                pls_h_label( 
                    pls_h_checkbox( 
                        checked( $instance['name'], true, false ), 
                        array(
                            'id' => $this->get_field_id( 'name' ),
                            'name' => $this->get_field_name( 'name' ),
                        ) 
                    ) . 
                    ' ' . 'Include Name', 
                    $this->get_field_id( 'name' ) 
                ) 
            ) .
            pls_h_p(
                pls_h_label(
                    'Override Name:' .
                    pls_h( 
                        'input',
                        array(
                            'type' => 'text',
                            'class' => 'widefat',
                            'id' => $this->get_field_id( 'name_custom' ),
                            'name' => $this->get_field_name( 'name_custom' ),
                            'value' => esc_attr( $instance['name_custom'] ),
                            'style' => 'font-weight:normal'
                        )
                    ),
                    $this->get_field_id( 'name_custom' ),
                    array('style' => 'font-weight:bold')
                )
            ) . 
            // Email checkbox and override input
            pls_h_p( 
                pls_h_label( 
                    pls_h_checkbox( 
                        checked( $instance['email'], true, false ), 
                        array(
                            'id' => $this->get_field_id( 'email' ),
                            'name' => $this->get_field_name( 'email' ),
                        ) 
                    ) . 
                    ' ' . 'Include Email', 
                    $this->get_field_id( 'email' ) 
                ) 
            ) .
            pls_h_p(
                pls_h_label(
                    'Override Email:' .
                    pls_h( 
                        'input',
                        array(
                            'type' => 'text',
                            'class' => 'widefat',
                            'id' => $this->get_field_id( 'email_custom' ),
                            'name' => $this->get_field_name( 'email_custom' ),
                            'value' => esc_attr( $instance['email_custom'] ),
                            'style' => 'font-weight:normal'
                        )
                    ),
                    $this->get_field_id( 'email_custom' ),
                    array('style' => 'font-weight:bold')
                )
            ) . 
            // Phone checkbox and override input
            pls_h_p( 
                pls_h_label( 
                    pls_h_checkbox( 
                        checked( $instance['phone'], true, false ), 
                        array(
                            'id' => $this->get_field_id( 'phone' ),
                            'name' => $this->get_field_name( 'phone' ),
                        ) 
                    ) . 
                    ' ' . 'Include Phone', 
                    $this->get_field_id( 'phone' ) 
                ) 
            ) .
            pls_h_p(
                pls_h_label(
                    'Override Phone:' .
                    pls_h( 
                        'input',
                        array(
                            'type' => 'text',
                            'class' => 'widefat',
                            'id' => $this->get_field_id( 'phone_custom' ),
                            'name' => $this->get_field_name( 'phone_custom' ),
                            'value' => esc_attr( $instance['phone_custom'] ),
                            'style' => 'font-weight:normal'
                        )
                    ),
                    $this->get_field_id( 'phone_custom' ),
                    array('style' => 'font-weight:bold')
                )
            ) .
            // Description checkbox and override input
            pls_h_p( 
                pls_h_label( 
                    pls_h_checkbox( 
                        checked( $instance['description'], true, false ), 
                        array(
                            'id' => $this->get_field_id( 'description' ),
                            'name' => $this->get_field_name( 'description' ),
                        ) 
                    ) . 
                    ' ' . 'Include Description', 
                    $this->get_field_id( 'description' ) 
                ) 
            ) .
            pls_h_p(
                pls_h_label(
                    'Override Description:' .
                    pls_h( 
                        'input',
                        array(
                            'type' => 'text',
                            'class' => 'widefat',
                            'id' => $this->get_field_id( 'description_custom' ),
                            'name' => $this->get_field_name( 'description_custom' ),
                            'value' => esc_attr( $instance['description_custom'] ),
                            'style' => 'font-weight:normal;height:'
                        )
                    ),
                    $this->get_field_id( 'description_custom' ),
                    array('style' => 'font-weight:bold')
                )
            ) .


            /** Print the Photo checkbox */
            pls_h_p( 
                pls_h_label( 
                    pls_h_checkbox( 
                        checked( $instance['photo'], true, false ), 
                        array(
                            'id' => $this->get_field_id( 'photo' ),
                            'name' => $this->get_field_name( 'photo' ),
                        ) 
                    ) . 
                    ' ' . 'Photo', 
                    $this->get_field_id( 'photo' ) 
                ) 
            ) .
            pls_h_p(
              pls_h_label(
                'Image'
                // array(
                //   'for' => $this->get_field_id('image_uri'),
                //   'value' => esc_attr( $instance['image_uri'] ),
                //   'id' => "image_tag-". esc_attr( $this->get_field_id('image_uri') ),
                // ) 
              ) .
              pls_h(
                'input',
                array(
                  'name' => $this->get_field_name('image_uri'),
                  'type' => 'text',
                  'id' => "image-". esc_attr( $this->get_field_id('image_uri') ),
                  'value' => $instance['image_uri'],
                  'class' => '.agent-widget-img-text-input'
                )
              ) .
              pls_h(
                'img',
                array(
                  'id' => "image_tag-" . esc_attr( $this->get_field_id("image_uri") ),
                  'src' => $instance['image_uri'],
                  'class' => '.agent-widget-img-tag'
                )
              ) .
              pls_h(
                'input',
                array(
                  'type' => 'button',
                  'id' => 'select-img-' . $this->get_field_id("image_uri"),
                  'value' => 'Select Image',
                  'class' => 'agent-widget-img-button'
                )
              )
            )

        );
        
	}


    private static function process_defaults ($args, $instance) {

        /** Define the default argument array. */
        $arg_defaults = array(
            'title' => 'Have any questions?',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
            'before_widget' => '<section id="pls-agent-3" class="widget pls-agent widget-pls-agent" itemscope="" itemtype="http://schema.org/RealEstateAgent">',
            'after_widget' => '</section>',
            'widget_id' => '',
            'clearfix' => true
        );


        /** Merge the arguments with the defaults. */
        $args = wp_parse_args( $args, $arg_defaults );

        /** Define the default argument array. */
        $instance_defaults = array(
            'name' => true,
            'name_custom' => false,
            'email' => true,
            'email_custom' => false,
            'phone' => true,
            'phone_custom' => false,
            'description' => true,
            'description_custom' => false,
            'photo' => false,
            'image_uri' => false,
            'widget_id' => ''
        );

        /** Merge the arguments with the defaults. */
        $instance = wp_parse_args( $instance, $instance_defaults );

        return array($args, $instance);
    }

    public function checkForCustom ($instance, $attribute) {
        if ( !empty( $instance[$attribute.'_custom'] ) ) {
            $agent_html[$attribute] = $instance[$attribute.'_custom'];
        } else {
            $agent_html[$attribute] = '';
        }
        return $agent_html[$attribute];
    }
    
    
} // end of class

// queue up the necessary js
function upload_js_enqueue() {
  wp_enqueue_style('thickbox');
  wp_enqueue_script('media-upload');
}

add_action('admin_enqueue_scripts', 'upload_js_enqueue');

function agent_image_js () {
  ob_start();
  ?>
    <script>
      var image_field;
      var input_field;
      jQuery(function($){
        $(document).on('click', 'input.agent-widget-img-button', function(evt){
          var image_id = $(this).attr('id');

          var partial_image_id = image_id.substr(image_id.indexOf('widget-pls-agent'));
          input_field = $(this).siblings('#image-' + partial_image_id);
          image_field = $(this).siblings('#image_tag-' + partial_image_id);
          tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
          return false;
        });
        window.send_to_editor = function(html) {
          imgurl = $('img', html).attr('src');
          image_field.attr('src',imgurl);
          input_field.val(imgurl);
          tb_remove();
        }
      });
    </script>
  <?php
  echo ob_get_clean();
}
add_action('admin_footer-widgets.php', 'agent_image_js', false, true);