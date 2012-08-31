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
	function __construct() {

		/* Set the widget textdomain. */
		$this->textdomain = pls_get_textdomain();

		/* Set up the widget options. */
		$widget_options = array(
			'classname' => 'pls-agent',
			'description' => esc_html__( 'A widget that displays information about the agent.', $this->textdomain )
		);

		/* Create the widget. */
        parent::__construct( "pls-agent", esc_attr__( 'Placester: Agent Widget', $this->textdomain ), $widget_options );
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
	function widget( $args, $instance ) {

        list($args, $instance) = self::process_defaults($args, $instance);

        /** Extract the arguments into separate variables. */
		extract( $args, EXTR_SKIP );

        /** Get the agent information from the plugin. */
        $agent = PLS_Plugin_API::get_user_details();
        
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
            unset( $agent_html['title'], $agent_html['width'], $agent_html['height'] );

            /** Add the photo. */
            if ( ! empty( $instance['photo'] ) && ! empty( $agent['logo'] ) )
                $agent_html['photo'] = pls_h_img( $agent['logo'], trim( $agent['user']['first_name'] . ' ' . $agent['user']['last_name'] ), array( 'class' => 'photo' ) + ( ! empty( $instance['width'] ) ? array( 'width' => $instance['width'] ) : array() ) + ( ! empty( $instance['height'] ) ? array( 'height' => $instance['height'] ) : array() ) );

            /** Add the name. */
            if ( ! empty( $instance['name'] ) && ( ! empty( $agent['user']['first_name'] ) || ! empty( $agent['user']['last_name'] ) ) )
                $agent_html['name'] = pls_h( 'h5', array( 'class' => 'fn' ), trim( $agent['user']['first_name'] . ' ' . $agent['user']['last_name'] ) );

            /** Add the email address. */
            if ( ! empty( $instance['email'] ) && ! empty( $agent['user']['email'] ) )
                $agent_html['email'] = pls_h( 'span', array( 'class' => 'email' ), pls_h_a( "mailto:{$agent['user']['email']}", $agent['user']['email'] ) );

            /** Add the phone number. */
            if ( ! empty( $instance['phone'] ) && ! empty( $agent['user']['phone'] ) )
                $agent_html['phone'] = pls_h( 'span', array( 'class' => 'phone' ), PLS_Format::phone($agent['user']['phone']) );

            /** Add the description. */
            if ( ! empty( $instance['description']) && !empty($agent['slogan'] ) ) {
                $agent_html['description'] = pls_h( 'p', array( 'class' => 'desc' ), $agent['slogan'] );
            } else {
                $agent_html['description'] = '';
            }



            /** Combine the agent information. */
            $widget_body = pls_get_if_not_empty( $agent_html['photo'] ) . 
                pls_get_if_not_empty( $agent_html['name'] ) . 
                pls_get_if_not_empty( $agent_html['description'] ) .
                pls_get_if_not_empty( $agent_html['email'] ) . 
                pls_get_if_not_empty( $agent_html['phone'] ); 

            /** Wrap the agent information in a section element. */
            $widget_body = apply_filters( 'pls_widget_agent_inner', $widget_body, $agent_html, $agent, $instance, $widget_id );

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
	function update( $new_instance, $old_instance ) {

        $instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
        $instance['name'] = ( isset( $new_instance['name'] ) ? 1 : 0 );
        $instance['email'] = ( isset( $new_instance['email'] ) ? 1 : 0 );
        $instance['photo'] = ( isset( $new_instance['photo'] ) ? 1 : 0 );
        $instance['width'] = absint( $new_instance['width'] );
        $instance['height'] = absint( $new_instance['height'] );
        $instance['phone'] = ( isset( $new_instance['phone'] ) ? 1 : 0 );
        $instance['description'] = ( isset( $new_instance['description'] ) ? 1 : 0 );
        // $instance['extra'] = $new_instance['extra'];

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
     *
	 * @since 0.0.1
	 */
	function form( $instance ) {

		/** Set up the default form values. */
		$defaults = array(
			'title' => esc_attr__( 'Agent', $this->textdomain ),
            'name' => true,
            'email' => true,
            'photo' => true,
            'width' => 100,
            'height' => '',
            'phone' => true,
            'description' => true,
            // 'extra' => '',
		);

		/** Merge the user-selected arguments with the defaults. */
		$instance = wp_parse_args( (array) $instance, $defaults );

        /** Print the backend widget form. */
        echo pls_h_div(
            /** Print the Title input */
            pls_h_p( 
                pls_h_label( 
                    __( 'Title', pls_get_textdomain() ) . ':' .
                    pls_h( 
                        'input',
                        array(
                            'type' => 'text',
                            'class' => 'widefat',
                            'id' => $this->get_field_id( 'title' ),
                            'name' => $this->get_field_name( 'title' ),
                            'value' => esc_attr( $instance['title'] )
                        ) 
                    ), 
                    $this->get_field_id( 'title' ) 
                ) 
            ) . 
            /** Print the Name checkbox */
            pls_h_p( 
                pls_h_label( 
                    pls_h_checkbox( 
                        checked( $instance['name'], true, false ), 
                        array(
                            'id' => $this->get_field_id( 'name' ),
                            'name' => $this->get_field_name( 'name' ),
                        ) 
                    ) . 
                    ' ' . __( 'Name', pls_get_textdomain() ), 
                    $this->get_field_id( 'name' ) 
                ) 
            ) . 
            /** Print the Description checkbox */
            pls_h_p( 
                pls_h_label( 
                    pls_h_checkbox( 
                        checked( $instance['description'], true, false ), 
                        array(
                            'id' => $this->get_field_id( 'description' ),
                            'name' => $this->get_field_name( 'description' ),
                        ) 
                    ) . 
                    ' ' . __( 'Description', pls_get_textdomain() ), 
                    $this->get_field_id( 'description' ) 
                ) 
            ) . 
            /** Print the Email checkbox */
            pls_h_p( 
                pls_h_label( 
                    pls_h_checkbox( 
                        checked( $instance['email'], true, false ), 
                        array(
                            'id' => $this->get_field_id( 'email' ),
                            'name' => $this->get_field_name( 'email' ),
                        ) 
                    ) . 
                    ' ' . __( 'Email', pls_get_textdomain() ), 
                    $this->get_field_id( 'email' ) 
                ) 
            ) . 
            /** Print the Phone Number checkbox */
            pls_h_p( 
                pls_h_label( 
                    pls_h_checkbox( 
                        checked( $instance['phone'], true, false ), 
                        array(
                            'id' => $this->get_field_id( 'phone' ),
                            'name' => $this->get_field_name( 'phone' ),
                        ) 
                    ) . 
                    ' ' . __( 'Phone Number', pls_get_textdomain() ), 
                    $this->get_field_id( 'phone' ) 
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
                    ' ' . __( 'Photo', pls_get_textdomain() ), 
                    $this->get_field_id( 'photo' ) 
                ) 
            ) . 
            /** Print the Width text input */
            pls_h_p( 
                pls_h_label( 
                    __( 'Photo width', pls_get_textdomain() ) . ': ' .
                    pls_h( 
                        'input',
                        array(
                            'type' => 'text',
                            'size' => 4,
                            'id' => $this->get_field_id( 'width' ),
                            'name' => $this->get_field_name( 'width' ),
                            'value' => esc_attr( $instance['width'] )
                        ) 
                    ),
                    $this->get_field_id( 'width' ) 
                ) 
            ) . 
            /** Print the Height text input */
            pls_h_p( 
                pls_h_label( 
                    __( 'Photo height', pls_get_textdomain() ) . ': ' .
                    pls_h( 
                        'input',
                        array(
                            'type' => 'text',
                            'size' => 4,
                            'id' => $this->get_field_id( 'height' ),
                            'name' => $this->get_field_name( 'height' ),
                            'value' => esc_attr( $instance['height'] )
                        ) 
                    ),
                    $this->get_field_id( 'height' ) 
                ) 
            ) 
            /** Print the Extra HTML textarea */
            // pls_h_p( 
                // pls_h_label( 
                    // __( 'Extra HTML', pls_get_textdomain() ) . ":" .
                    // pls_h( 
                        // 'textarea',
                        // array(
                            // 'class' => 'widefat',
                            // 'type' => 'text',
                            // 'rows' => 7,
                            // 'cols' => 20,
                            // 'id' => $this->get_field_id( 'extra' ),
                            // 'name' => $this->get_field_name( 'extra' ),
                            // 'value' => esc_textarea( $instance['extra'] ),
                        // ) 
                    // ), 
                    // $this->get_field_id( 'extra' ) 
                // ) 
            // ) 
        ); 
	}

    function process_defaults ($args, $instance) {

        /** Define the default argument array. */
        $arg_defaults = array(
            'title' => 'Have any questions?',
            'before_title' => '<h3>',
            'after_title' => '</h3>',
            'before_widget' => '<section id="pls-agent-3" class="widget pls-agent widget-pls-agent">',
            'after_widget' => '</section>',
            'widget_id' => '',
            'clearfix' => true
        );


        /** Merge the arguments with the defaults. */
        $args = wp_parse_args( $args, $arg_defaults );


        /** Define the default argument array. */
        $instance_defaults = array(
            'photo' => true,
            'name' => true,
            'description' => true,
            'email' => true,
            'phone' => true,
            'width' => 100,
            'height' => 75,
            'widget_id' => ''
        );

        /** Merge the arguments with the defaults. */
        $instance = wp_parse_args( $instance, $instance_defaults );


        return array($args, $instance);
    }
} // end of class
