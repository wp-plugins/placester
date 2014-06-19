<?php

class Placester_Contact_Widget extends WP_Widget {

  function Placester_Contact_Widget() {
    $widget_ops = array(
      'classname' => 'pls-contact-form Placester_Contact_Widget',
      'description' => 'Works only on the Property Details Page.'
    );
    $this->WP_Widget( 'Placester_Contact_Widget', 'Placester: Contact Form', $widget_ops );
  }

  //Front end contact form
  function form($instance){
    //Defaults
    $instance = wp_parse_args( (array) $instance, array('title'=>'', 'button' => 'Submit', 'departments' => '') );

    $title = htmlspecialchars($instance['title']);

    extract($instance, EXTR_SKIP);

    $show_property_checked = isset($instance['show_property']) && $instance['show_property'] == 1 ? 'checked' : '';
    $show_subject_checked = isset($instance['subject']) && $instance['subject'] == 1 ? 'checked' : '';
    $show_phone_checked = isset( $instance['phone_number'] ) && $instance['phone_number'] == 1 ? 'checked' : '';

    // Output the options
    echo '<p><label for="' . $this->get_field_name('title') . '"> Title: </label><input class="widefat" type="text" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" value="' . $title . '" /></p>';
    echo '<p><label for="' . $this->get_field_name('button') . '"> Submit button label: </label><input class="widefat" type="text" id="' . $this->get_field_id('button') . '" name="' . $this->get_field_name('button') . '" value="' . $button . '" /></p>';
    // echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id('modern') . '" name="' . $this->get_field_name('modern') . '"' . $modern_checked . ' style="margin-right: 5px;"/><label for="' . $this->get_field_id('modern') . '"> Use placeholders instead of labels</label></p>';
    echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id('show_property') . '" name="' . $this->get_field_name('show_property') . '"' . $show_property_checked . ' style="margin-right: 5px;"/><label for="' . $this->get_field_id('show_property') . '"> Display property address on the form when viewing a property page</label></p>';
    echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id('subject') . '" name="' . $this->get_field_name('subject') . '"' . $show_subject_checked . ' style="margin-right: 5px;"/><label for="' . $this->get_field_id('subject') . '">Show the subject field in the contact form</label></p>';
    echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id('phone_number') . '" name="' . $this->get_field_name('phone_number') . '"' . $show_phone_checked . ' style="margin-right: 5px;"/><label for="' . $this->get_field_id('phone_number') . '">Show phone number field in the contact form</label></p>';
    echo '<p><label for="' . $this->get_field_name('departments') . '">Departments (separated by commas): </label><input class="widefat" type="text" id="' . $this->get_field_id('departments') . '" name="' . $this->get_field_name('departments') . '" value="' . $departments . '" /></p>';

    ?>

<?php 
  }
  
  // Update settings
  function update($new_instance, $old_instance){
    $instance = $old_instance;
    $instance['title'] = strip_tags(stripslashes($new_instance['title']));
    $instance['button'] = strip_tags(stripslashes($new_instance['button']));
    $instance['show_property'] = isset($new_instance['show_property']) ? 1 : 0;
    $instance['subject'] = isset($new_instance['subject']) ? 1 : 0;
    $instance['phone_number'] = isset($new_instance['phone_number']) ? 1 : 0;
    $instance['departments'] = strip_tags(stripslashes($new_instance['departments']));
    return $instance;
  }

  // Admin widget
  function widget($args, $instance) {
    

    // Find where this widget's sidebar falls in the list of registered sidebars
    // Use this to get a number we can use for unique tabindexes
    if(isset($args['id'])) {
      // Widget is rendering as part of a sidebar
      $sidebar_pos = array_search($args['id'], array_keys(wp_get_sidebars_widgets())) + 1;
    }
    elseif(isset($instance['number'])) {
      // Widget is being passed an instance number
      // the theme is probably instantiating the widget itself
      $sidebar_pos = $instance['number'];
    }
    else {
      // Nothing else to go on, really. Make a counter and
      // increment it each time the widget it rendered on this
      // page hit
      static $instance_count = 1;
      $sidebar_pos = $instance_count++;
    }

      global $post;
        
        if (!empty($post) && isset($post->post_type) && $post->post_type == 'property') {
          $data = PLS_Plugin_API::get_listing_in_loop();
        } else {
          $data = array();
        }

        // Labels and Values
        $title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
        $success_message = apply_filters('success_message', empty($instance['success_message']) ? 'Thank you for the email, we\'ll get back to you shortly' : $instance['success_message']);
        $submit_value = apply_filters('button', empty($instance['button']) ? 'Send' : $instance['button']);

        $email_label = apply_filters('email_label', !isset($instance['email_label']) ? 'Email Address (required)' : $instance['email_label']);
        $email_value = apply_filters('email_value', !isset($instance['email_value']) ? 'Email Address' : $instance['email_value']);
        
        $phone_label = apply_filters('phone_label', !isset($instance['phone_label']) ? 'Phone Number (required)' : $instance['phone_label']);
        $phone_value = apply_filters('phone_value', !isset($instance['phone_value']) ? 'Phone Number' : $instance['phone_value']);

        $subject_label = apply_filters('subject_label', !isset($instance['subject_label']) ? 'Subject' : $instance['subject_label']);
        $subject_value = apply_filters('subject_value', !isset($instance['subject_value']) ? 'Subject' : $instance['subject_value']);

        $departments_label = apply_filters('departments_label', !isset($instance['departments_label']) ? 'Department' : $instance['departments_label']);
        $departments_value = apply_filters('departments_value', !isset($instance['departments_value']) ? 'Department' : $instance['departments_value']);
        
        $name_label = apply_filters('name_label', !isset($instance['name_label']) ? 'Name (required)' : $instance['name_label']);
        $name_value = apply_filters('name_value', !isset($instance['name_value']) ? 'Name' : $instance['name_value']);
        
        $question_label = apply_filters('question_label', !isset($instance['question_label']) ? 'Questions/Comments' : $instance['question_label']);
        $question_value = apply_filters('question_value', !isset($instance['question_value']) ? 'Any questions for us?' : $instance['question_value']);
        
        $custom_link = apply_filters('custom_link', !isset($instance['custom_link']) ? '' : $instance['custom_link']);
        $custom_link_target = apply_filters('custom_link_target', !isset($instance['custom_link_target']) ? '_blank' : $instance['custom_link_target']);

        $form_title = apply_filters('form_title', !isset($instance['form_title']) ? '' : $instance['form_title']);

        // Reguired Attribute
        $name_required = isset($instance['name_required']) && $instance['name_required'] == "false" ? false : true;
        $email_required = isset($instance['email_required']) && $instance['email_required'] == "false" ? true : true;
        $phone_required = isset($instance['phone_required']) && $instance['phone_required'] == "true" ? true : false;
        $subject_required = isset($instance['subject_required']) && $instance['subject_required'] == "true" ? true : false;
        $question_required = isset($instance['question_required']) && $instance['question_required'] == "false" ? false : true;
        
        // Error Messages
        $name_error = isset($instance['name_error']) && $instance['name_error'] != "" ? $instance['name_error'] : "Your name is required.";
        $email_error = isset($instance['email_error']) && $instance['email_error'] != "" ? $instance['email_error'] : "A valid email is required.";
        $phone_error = isset($instance['phone_error']) && $instance['phone_error'] != "" ? $instance['phone_error'] : "A valid phone is required.";
        $question_error = isset($instance['question_error']) && $instance['question_error'] != "" ? $instance['question_error'] : "Don't forget to leave a question or comment.";
        $subject_error = isset($instance['subject_error']) && $instance['subject_error'] != "" ? $instance['subject_error'] : "What subject would you like to speak about?";

        // Classes
        $container_class = apply_filters('container_class', empty($instance['container_class']) ? '' : $instance['container_class']);
        $inner_class = apply_filters('inner_class', empty($instance['inner_class']) ? '' : $instance['inner_class']);
        $inner_containers = apply_filters('inner_containers', empty($instance['inner_containers']) ? '' : $instance['inner_containers']);
        $textarea_container = apply_filters('textarea_container', !isset($instance['textarea_container']) ? $inner_containers : $instance['textarea_container']);
        $button_class = apply_filters('button_class', !isset($instance['button_class']) ? 'button-primary' : $instance['button_class']);
        
        // Send To Options
        $email_confirmation = apply_filters('email_confirmation', empty($instance['email_confirmation']) ? false : $instance['email_confirmation']);
        $send_to_email = apply_filters('send_to_email', !isset($instance['send_to_email']) ? '' : $instance['send_to_email']);
        $cc_value = apply_filters('cc_value', !isset($instance['cc_value']) ? '' : $instance['cc_value']);
        $bcc_value = apply_filters('bcc_value', !isset($instance['bcc_value']) ? '' : $instance['bcc_value']);

        // Lead Capture Cookie
        $lead_capture_cookie = apply_filters('lead_capture_cookie', !isset($instance['lead_capture_cookie']) ? '' : $instance['lead_capture_cookie']);
        
        // Form Options
        // Get lead capture's force-back theme option from admin
        $back_on_lc_cancel_option = pls_get_option('pd-lc-force-back');
        
        if (!empty($instance['back_on_lc_cancel'])) {
          // if option has been set in the contact form call
          $back_on_lc_cancel = apply_filters('back_on_lc_cancel', !isset($instance['back_on_lc_cancel']) ? '' : $instance['back_on_lc_cancel']);
        } elseif (isset($back_on_lc_cancel_option)) {
          // Elseif the theme option is set, let the theme option set the force-back for canceling the lead capture form
          $back_on_lc_cancel = $back_on_lc_cancel_option;
        } else {
          // else, don't force users back
          $back_on_lc_cancel = 0;
        }
        
        $show_property = ( isset($instance['show_property']) && !empty($instance['show_property']) ) ? 1 : 0;
        $template_url = get_template_directory_uri();

        /** Define the default argument array. */
        $defaults = array(
          'before_widget' => '<section class="side-ctnr placester_contact ' . $container_class . ' widget">',
          'after_widget' => '</section>',
          'title' => '',
          'before_title' => '<h3>',
          'after_title' => '</h3>',
        );

        /** Merge the arguments with the defaults. */
        $args = wp_parse_args( $args, $defaults );

        extract($args, EXTR_SKIP);
        ?>
        
          <?php pls_do_atomic( 'contact_form_before_widget' ); ?>
          
          <?php echo $before_widget; ?>

              <?php pls_do_atomic( 'contact_form_before_title' ); ?>
              
              <?php echo $before_title . $title . $after_title; ?>
              
              <?php pls_do_atomic( 'contact_form_after_title' ); ?>
              
              <section class="<?php echo $inner_class; ?> common-side-cont placester_contact_form clearfix">

                  <div class="success"><?php echo $success_message; ?></div>

                  <form name="widget_contact" action="" method="post">

                    <?php //this must be included to get additional user data; ?>
                    <input type="hidden" name="ip" value="<?php print $ip = ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']; ?>"/>
                    <input type="hidden" name="user_agent" value="<?php print $_SERVER['HTTP_USER_AGENT'] ?>"/>
                    <input type="hidden" name="url" value="<?php print 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"] ?>"/>
                    
                    <?php if (isset($lead_capture_cookie) && $lead_capture_cookie == true) { ?>
                      <input type="hidden" name="lead_capture_cookie" value="true">
                    <?php } ?>
                    
                    <input type="hidden" name="id" value="<?php if(isset($data['id'])) { echo $data['id']; } ?>">
                    <input type="hidden" name="fullAddress" value="<?php echo @self::_get_full_address($data);  ?>">
                    <input type="hidden" name="email_confirmation" value="<?php echo $email_confirmation;  ?>">
                    <input type="hidden" name="send_to_email" value="<?php echo $send_to_email;  ?>">
                    <input type="hidden" name="cc_value" value="<?php echo @$cc_value;  ?>">
                    <input type="hidden" name="bcc_value" value="<?php echo @$bcc_value;  ?>">
                    <input type="hidden" name="back_on_lc_cancel" value="<?php echo @$back_on_lc_cancel; ?>">
                    <input type="hidden" name="form_submitted" value="0">
                    <input type="hidden" name="custom_link" value="<?php echo @$custom_link; ?>">
                    <input type="hidden" name="custom_link_target" value="<?php echo @$custom_link_target; ?>">
                    <input type="hidden" name="form_title" value="<?php echo @$form_title; ?>">

                    <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                    <label class="required" for="name"><?php echo $name_label; ?></label>
                    <input class="required" id="name" placeholder="<?php echo $name_value ?>" type="text" name="name" tabindex="<?php echo $sidebar_pos; ?>1" <?php echo $name_required == true ? 'required="required"' : '' ?> <?php echo !empty($name_error) ? 'data-message="'.$name_error.'"' : ''; ?> />
                    <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>

                    <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                    <label class="required" for="email"><?php echo $email_label; ?></label><input class="required" id="email" placeholder="<?php echo $email_value ?>" type="email" name="email" tabindex="<?php echo $sidebar_pos; ?>2" <?php echo $email_required == true ? 'required="required"' : '' ?> <?php echo !empty($email_error) ? 'data-message="'.$email_error.'"' : ''; ?> />
                    <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>

                    <?php if(!empty($instance['phone_number'])) { ?>
                      <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                      <label class="required" for="phone"><?php echo $phone_label; ?></label><input class="required" id="phone" placeholder="<?php echo $phone_value ?>" type="text" name="phone" tabindex="<?php echo $sidebar_pos; ?>3" <?php echo $phone_required == true ? 'required="required"' : '' ?> <?php echo !empty($phone_error) ? 'data-message="'.$phone_error.'"' : ''; ?> />
                      <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>
                    <?php } ?>

                    <?php if(!empty($instance['subject'])) { ?>
                      <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                      <label class="required" for="subject"><?php echo $subject_label; ?></label><input class="required" id="subject" placeholder="<?php echo $subject_value ?>" type="text" name="subject" tabindex="<?php echo $sidebar_pos; ?>4" <?php echo $subject_required == true ? 'required="required"' : '' ?> <?php echo !empty($subject_error) ? 'data-message="'.$subject_error.'"' : ''; ?> />
                      <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>
                    <?php } ?>

                    <?php if( !empty($instance['departments']) ) { ?>
                      <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                      <label class="required" for="department"><?php echo $departments_label; ?></label>
                      <?php $departments = explode( ',', $instance['departments']) ?>
                      <select id="department" placeholder="<?php echo $departments_value ?>" name="department" tabindex="<?php echo $sidebar_pos; ?>5">
                        <?php foreach ($departments as $department): ?>
                          <option value="<?php echo $department ?>"><?php echo $department ?></option>
                        <?php endforeach ?>
                      </select>
                      <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>
                    <?php } ?>

                    <?php if($show_property == 1) : ?>
                      <?php $full_address = @self::_get_full_address($data); if(!empty($full_address)) : ?>
                        <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                        <label>Property</label><span class="info"><?php echo str_replace("\n", " ", $full_address); ?></span>
                        <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>                      
                      <?php endif; ?>
                    <?php endif; ?>

                    <?php echo empty($instance['textarea_container']) ? '' : '<div class="' . $instance['textarea_container'] .'">'; ?>
                    <label for="question"><?php echo $question_label; ?></label>
                    <textarea rows="5" id="question" name="question" placeholder="<?php echo $question_value; ?>" tabindex="<?php echo $sidebar_pos; ?>6" <?php echo $question_required == true ? 'required="required"' : '' ?> <?php echo !empty($question_error) ? 'data-message="'.$question_error.'"' : ''; ?>></textarea>
                    <?php echo empty($instance['textarea_container']) ? '' : '</div>'; ?>
                    

                  <input type="submit" value="<?php echo $submit_value; ?>" class="<?php echo $button_class; ?>" tabindex="<?php echo $sidebar_pos; ?>7" />
                  
                  <div class="pls-contact-form-loading" style='display:none;'>
                    <div id="medium-spinner"><div class="bar1"></div><div class="bar2"></div><div class="bar3"></div><div class="bar4"></div><div class="bar5"></div><div class="bar6"></div><div class="bar7"></div><div class="bar8"></div></div>
                  </div>
                  
                </form>
                
              </section>
              <div class="separator"></div>

            <?php echo $after_widget; ?>

            <?php pls_do_atomic( 'contact_form_after_widget' ); ?>
            
    <?php }

  /**
   * Compensate for different address fields in the API response
   * @param array $data
   * @return string
   */
  function _get_full_address($data) {
    if(isset($data['location']['full_address'])) {
      return $data['location']['full_address'];
    }
    elseif(isset($data['location']['address'])) {
      // TODO: Localize address formatting
      $address  = $data['location']['address'] . " \n";
      $address .= $data['location']['locality'] . ", " . $data['location']['region'] . " " . $data['location']['postal'] . " \n";
      $address .= $data['location']['country'];

      return $address;
    }
    else {
      return '';
    }
  }

  // }
} // End Class

// add_action('init', 'placester_contact_widget');
// // Style
// function placester_contact_widget() {
//     $myStyleUrl = WP_PLUGIN_URL . '/placester/css/contact.widget.ajax.css';
//     wp_enqueue_style( 'contactwidgetcss', $myStyleUrl );
//     $myScriptUrl = WP_PLUGIN_URL . '/placester/js/contact.widget.ajax.js';
//     wp_enqueue_script( 'contactwidgetjs', $myScriptUrl, array('jquery') );

//     // Get current page protocol
//     $protocol = isset( $_SERVER['HTTPS'] ) ? 'https://' : 'http://';

//     $params = array(
//         'ajaxurl' => admin_url( 'admin-ajax.php', $protocol ),
//     );
//     wp_localize_script( 'contactwidgetjs', 'contactwidgetjs', $params );
// }

// Ajax function
add_action( 'wp_ajax_placester_contact', 'ajax_placester_contact' );
add_action( 'wp_ajax_nopriv_placester_contact', 'ajax_placester_contact' );
function ajax_placester_contact() {
    
    if( !empty($_POST) ) {
      $error = "";
      $message = "A prospective client wants to get in touch with you. \n\n";

      // Check to make sure that the name field is not empty
      if( trim($_POST['name']) == '' || trim($_POST['name']) == 'Name' ) {
        $error .= "Your name is required<br/>";
      } else {
        $message .= "Name: " . trim($_POST['name']) . " \n";
      }

      // Check to make sure sure that a valid email address is submitted
      if( trim($_POST['email']) == '' || trim($_POST['email']) == 'Email Address' )  {
        $error .= "An email address is required<br/>";
      } else if ( !eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email'])) ) {
        $error .= "A valid email address is required<br/>";
      } else {
        $message .= "Email Address: " . trim($_POST['email']) . " \n";
      }

      // Check to make sure that the phone number field is not empty
      if (isset($_POST['phone'])) {
        if( trim($_POST['phone']) == '' || trim($_POST['phone']) == 'Phone Number' ) {
          $error .= "Your phone number is required<br/>";
        } else {
          $message .= "Phone Number: " . trim($_POST['phone']) . " \n";
        }
      }

      // Check the subject field
      if (isset($_POST['subject'])) {
        if( trim($_POST['subject']) == '' ) {
          // $message .= "They did not include a subject \n\n ";
          $subject = '';
        } else {
          $message .= "Subject: " . trim($_POST['subject']) . " \n";
          $subject = ': ' . trim($_POST['subject']);
        }
      }

      // Check the departments field
      if (isset($_POST['department'])) {
        if( trim($_POST['department']) == '' ) {
          // $message .= "They didn't select a department \n\n ";
        } else {
          $message .= "Requested Departments: " . trim($_POST['department']) . " \n";
        }
      }

      // Check the question field
      if( trim($_POST['question']) == '' ) {
        $message .= "They left no comment nor question. \n\n ";
      } else {
        $message .= "Questions: " . trim($_POST['question']) . " \n";
      }

      if( empty($_POST['id']) ) {
        // $message .= "Listing ID: No specific listing \n";
      } else {
        $message .= "Listing ID: " . trim($_POST['id']) . " \n";
      }

      if( trim($_POST['fullAddress']) == '' ) {
        // $message .= "Listing Address: No specific listing \n";
      } else {
        $message .= "Listing Address: " . $_POST['fullAddress'] . " \n";
      }

      $message .= "\n";
      $message .= "This message was sent from the contact form at: \n" . $_SERVER['HTTP_REFERER'] . " \n";

    if( empty($error) ) {

      $api_whoami = PLS_Plugin_API::get_user_details();
      $user_email = @pls_get_option('pls-user-email');

      // Check what email to send the form to...
      if ( !empty( $user_email ) ) {
        $email = $user_email;
      } elseif (!empty($api_whoami['user']['email'])) {
        $email = $api_whoami['user']['email'];
      } else {
        $email = $api_whoami['email'];
      }
      if (trim($_POST['send_to_email']) == true) {
        $email = $_POST['send_to_email'];
      }

      $headers = array();
      if ( !empty($_POST['cc_value']) ) {
        $headers[] = 'Cc: '.$_POST['cc_value'];
      }
      if ( !empty($_POST['bcc_value']) ) {
        $headers[] = 'Bcc: '.$_POST['bcc_value'];
      }

      // Append form title
      if (!empty($_POST['form_title'])) {
        $message .= "This message was sent from the contact form named: \n" . $_POST['form_title'];
      }
      // Append form's custom link
      if (!empty($_POST['custom_link'])) {
        $message .= "The visitor was sent to: \n" . $_POST['custom_link'];
      }

      if (trim($_POST['email_confirmation']) == true) {
        wp_mail($email, 'Email confirmation was sent to ' . $_POST['email'] . ' from ' . home_url(), $message, $headers);
      } elseif ($email) {
        $placester_Mail = wp_mail($email, 'Prospective client from ' . home_url(), $message, PLS_Plugin_API::merge_bcc_forwarding_addresses_for_sending($headers) );
      }
      
      $name = $_POST['name'];
      PLS_Membership::create_person(array('metadata' => array('name' => $name, 'email' => $_POST['email'] ) )) ;

      // Send a email confirmation
      if (trim($_POST['email_confirmation']) == true) {

        ob_start();
          include(get_template_directory() . '/custom/contact-form-email.php');
          $message_to_submitter = ob_get_clean();

        wp_mail( $_POST['email'], 'Form Submitted' . $subject, $message_to_submitter );
      }

      // As long as there are no errors we'll allow custom links to override
      // the normal form submission. 
      if (!empty($_POST['custom_link'])) {
        return false;
      }

      echo "sent";
    } else {
      echo $error;
    }
    die;
  }
}