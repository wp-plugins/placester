<?php

class Placester_Contact_Widget extends WP_Widget {

  function Placester_Contact_Widget() {
    $widget_ops = array('classname' => 'Placester_Contact_Widget', 'description' => 'Works only on the Property Details Page.' );
    $this->WP_Widget( 'Placester_Contact_Widget', 'Placester: Contact Form', $widget_ops );
  }

  //Front end contact form
  function form($instance){
    //Defaults
    $instance = wp_parse_args( (array) $instance, array('title'=>'', 'modern' => 0) );

    $title = htmlspecialchars($instance['title']);

    extract($instance, EXTR_SKIP);

    $checked = $instance['modern'] == 1 ? 'checked' : '';

    // Output the options
    echo '<p><label for="' . $this->get_field_name('title') . '"> Title: </label><input class="widefat" type="text" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" value="' . $title . '" /></p>';

    echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id('modern') . '" name="' . $this->get_field_name('modern') . '"' . $checked . ' style="margin-right: 5px;"/><label for="' . $this->get_field_id('modern') . '"> Use placeholders instead of labels</label></p>';
    
    ?>
     <p style="font-size: 0.9em;">
        Warning: This widget is designed to be used to send queries about a certain listing and therefore only works on the Property Details Page.
    </p>     
    <?php 
  }
  
  // Update settings
  function update($new_instance, $old_instance){
    $instance = $old_instance;
    $instance['title'] = strip_tags(stripslashes($new_instance['title']));
    $instance['modern'] = isset($new_instance['modern']) ? 1 : 0;
    return $instance;
  }
  
  // Admin widget
  function widget($args, $instance) {
      global $post;
        
        if (!empty($post) && isset($post->post_type) && $post->post_type == 'property') {
          $data = unserialize($post->post_content);
        } else {
          $data = array();
        }
        extract($args);
        
				$title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
				$submit_value = apply_filters('button', empty($instance['button']) ? 'Send' : $instance['button']);
				$email_label = apply_filters('email_label', empty($instance['email_label']) ? 'Email Address (required)' : $instance['email_label']);
				$phone_label = apply_filters('phone_label', empty($instance['phone_label']) ? 'Phone Number (required)' : $instance['phone_label']);
				$fname_label = apply_filters('fname_label', empty($instance['fname_label']) ? 'First Name (required)' : $instance['fname_label']);
				$lname_label = apply_filters('lname_label', empty($instance['lname_label']) ? 'Last Name (required)' : $instance['lname_label']);
				$question_label = apply_filters('question_label', empty($instance['question_label']) ? 'Any questions for us?' : $instance['question_label']);
				$container_class = apply_filters('container_class', empty($instance['container_class']) ? '' : $instance['container_class']);
				$inner_class = apply_filters('inner_class', empty($instance['inner_class']) ? '' : $instance['inner_class']);
        $inner_containers = apply_filters('inner_containers', empty($instance['inner_containers']) ? '' : $instance['inner_containers']);
        
        $email_confirmation = apply_filters('email_confirmation', empty($instance['email_confirmation']) ? false : $instance['email_confirmation']);
        
        $modern = ( isset($instance['modern']) && !empty($instance['modern']) ) ? 1 : 0;
        $template_url = get_template_directory_uri();

    
        echo '<section class="side-ctnr placester_contact ' . $container_class . '">' . "\n";
        if ( $title ) {
          echo '<h3>' . $title . '</h3>';
        } 
          ?>
              <section class="<?php echo $inner_class; ?> common-side-cont clearfix">
                  <div class="msg">Thank you for the email, we\'ll get back to you shortly</div>
                  <form name="widget_contact" action="" method="post">
                  <?php
                  // For HTML5 enabled themes
                  if ( $modern == 0 ) { ?>
                    <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                      <label class="required" for="firstName"><?php echo $fname_label; ?></label><input class="required" id="firstName" value="First Name" type="text" name="firstName" tabindex="1" />
                    <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>

                    <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                    <label class="required" for="lastName"><?php echo $lname_label; ?></label><input class="required" id="lastName" value="Last Name" type="text" name="lastName" tabindex="2" />
                    <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>

                    <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                    <label class="required" for="email"><?php echo $email_label; ?></label><input class="required" id="email" value="Email Address" type="email" name="email" tabindex="3" />
                    <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>

                    <?php if(isset($instance['phone_number'])) { ?>
                      <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                      <label class="required" for="phone"><?php echo $phone_label; ?></label><input class="required" id="phone" value="Phone Number" type="text" name="phone" tabindex="4" />
                      <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>
                    <?php } ?>

                    <?php echo empty($instance['inner_containers']) ? '' : '<div class="' . $instance['inner_containers'] .'">'; ?>
                    <label for="question"><?php echo $question_label; ?></label><textarea rows="5" name="question" tabindex="5"></textarea>
                    <?php echo empty($instance['inner_containers']) ? '' : '</div>'; ?>

                    <input type="hidden" name="id" value="<?php echo @$data['id'];  ?>">
                    <input type="hidden" name="fullAddress" value="<?php echo @$data['location']['full_address'];  ?>">
                    <input type="hidden" name="email_confirmation" value="<?php echo $email_confirmation;  ?>">
                  <?php } else { ?>
                    <input class="required" placeholder="<?php echo $email_label; ?>" type="email" name="email" tabindex="1" />
                    <input class="required" placeholder="<?php echo $fname_label; ?>" type="text" name="firstName" tabindex="2" />
                    <input class="required" placeholder="<?php echo $lname_label; ?>" type="text" name="lastName" tabindex="3" />
                    <textarea rows="5" placeholder="<?php echo $question_label; ?>" name="question" tabindex="4"></textarea>
                    <input type="hidden" name="id" value="<?php echo @$data['id'];  ?>">
                    <input type="hidden" name="fullAddress" value="<?php echo @$data['location']['full_address'];  ?>">
                    <input type="hidden" name="email_confirmation" value="<?php echo $email_confirmation;  ?>">
                  <?php } ?>
                    <input type="submit" value="<?php echo $submit_value; ?>" tabindex="5" />
                  </form>
                <div class="placester_loading"></div>
              </section>  
              <div class="separator"></div>
            </section>
    <?php }
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

      // Check to make sure that the first name field is not empty
      if( trim($_POST['firstName']) == '' || trim($_POST['firstName']) == 'First Name') {
        $error .= "Your first name is required<br/>";
      } else {
        $message .= "First Name: " . trim($_POST['firstName']) . " \n";
      }

      // Check to make sure that the last name field is not empty
      if( trim($_POST['lastName']) == '' || trim($_POST['lastName']) == 'Last Name' ) {
        $error .= "Your last name is required<br/>";
      } else {
        $message .= "Last Name: " . trim($_POST['lastName']) . " \n";
      }

      // Check to make sure sure that a valid email address is submitted
      if( trim($_POST['email']) == '' || trim($_POST['email']) == 'Email Address' )  {
        $error .= "An email address is required<br/>";
      } else if ( !eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email'])) ) {
        $error .= "A valid email address is required<br/>";
      } else {
        $message .= "Email Address: " . trim($_POST['email']) . " \n";
      }

      // Check to make sure that the last name field is not empty
      if (isset($_POST['phone'])) {
        if( trim($_POST['phone']) == '' || trim($_POST['phone']) == 'Phone Number' ) {
          $error .= "Your phone number is required<br/>";
        } else {
          $message .= "Phone Number: " . trim($_POST['phone']) . " \n";
        }
      }
      // Check the question field
      if( trim($_POST['question']) == '' ) {
        $question = "They had no questions at this time \n\n ";
      } else {
        $message .= "Questions: " . trim($_POST['question']) . " \n";
      }

      if( empty($_POST['id']) ) {
        $message .= "Listing ID: No specific listing \n";
      } else {
        $message .= "Listing ID: " . trim($_POST['id']) . " \n";
      }

      if( trim($_POST['fullAddress']) == '' ) {
        $message .= "Listing Address: No specific listing \n";
      } else {
        $message .= "Listing Address: " . $_POST['fullAddress'] . " \n";
      }

    if( empty($error) ) {

      $api_whoami = PLS_Plugin_API::get_user_details();

      if (trim($_POST['email_confirmation']) == true) {
        wp_mail($api_whoami['email'], 'Email confirmation was sent to ' . $_POST['email'] . ' from ' . home_url(), $message);
      } elseif ($api_whoami['email']) {
        $placester_Mail = wp_mail($api_whoami['email'], 'Prospective client from ' . home_url(), $message);
      }
      
      $name = $_POST['firstName'] . ' ' . $_POST['lastName'];
      PLS_Membership::create_person(array('metadata' => array('name' => $name, 'email' => $_POST['email'] ) )) ;


      if (trim($_POST['email_confirmation']) == true) {

        ob_start();
          include(get_template_directory() . '/custom/contact-form-email.php');
          $message_to_submitter = ob_get_contents();
        ob_end_clean();
              
        wp_mail( $_POST['email'], 'Form Submitted', $message_to_submitter );
      }
    
      echo "sent";
    } else {
      echo $error;
    }
    die;
  }
}
