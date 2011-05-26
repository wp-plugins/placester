<?php 
class Placester_Contact_Widget extends WP_Widget {

  function Placester_Contact_Widget() {
    $widget_ops = array('classname' => 'widget_contact', 'description' => __( 'Add a simple AJAX contact form to all listings') );
    parent::WP_Widget(false, 'Placester Contact Form', $widget_ops);
  }

  //Front end contact form
  function widget($args, $instance) {

    global $post;
    if($post->post_type == 'property') {
        $data = placester_property_get($post->post_name);
    extract($args);
    $title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
    $modern = $instance['modern'] ? 1 : 0;

    $template_url = get_bloginfo('template_url');

    echo $before_widget;

    // Create the title, if there is one
    if ( $title ) echo $before_title . $title . $after_title;

    // Create form
    echo '<form id="placester_contact" name="widget_contact" action="" method="post">';

    // For HTML5 enabled themes
    if ( $modern == 0 ) {
      echo '<label class="required" for="email">Email Address (required)</label><input class="required" type="email" name="email"/>';
      echo '<label class="required" for="firstName">First Name (required)</label><input class="required" type="text" name="firstName"/>';
      echo '<label class="required" for="lastName">Last Name (required)</label><input class="required" type="text" name="lastName"/>';
      echo '<label for="question">Any questions for us?</label><textarea rows="5" name="question"></textarea>';
      echo '<input type="hidden" name="placesterEmail" value="' . $data->contact->email . '">';
      echo '<input type="hidden" name="id" value="' . $data->id . '">';
      echo '<input type="hidden" name="fullAddress" value="' . $data->location->full_address . '">';
    } else {
      echo '<input class="required" placeholder="Email Address (required)" type="email" name="email"/>';
      echo '<input class="required" placeholder="First Name (required)" type="text" name="firstName"/>';
      echo '<input class="required" placeholder="Last Name (required)" type="text" name="lastName"/>';
      echo '<textarea rows="5" placeholder="Any questions for us?" name="question"></textarea>';
      echo '<input type="hidden" name="placesterEmail" value="' . $data->contact->email . '">';
      echo '<input type="hidden" name="id" value="' . $data->id . '">';
      echo '<input type="hidden" name="fullAddress" value="' . $data->location->full_address . '">';
    }

    // Submit button, success and error messages for ajax callback
    echo '<input type="submit" value="Send it" /></form><div class="placester_loading"></div><div id="placester_success">Thanks for the email, we\'ll get back to you shortly</div><div id="placester_msg"></div></div>';

    echo $after_widget;
        }
  }

  // Update settings
  function update($new_instance, $old_instance){
    $instance = $old_instance;
    $instance['title'] = strip_tags(stripslashes($new_instance['title']));
    $instance['modern'] = isset($new_instance['modern']) ? 1 : 0;

    return $instance;
  }

  // Admin widget
  function form($instance){
    //Defaults
    $instance = wp_parse_args( (array) $instance, array('title'=>'', 'modern' => 0) );

    $title = htmlspecialchars($instance['title']);

    extract($instance, EXTR_SKIP);

    $checked = $instance['modern'] == 1 ? 'checked' : '';

    // Output the options
    echo '<p><label for="' . $this->get_field_name('title') . '">' . __('Title:') . '</label><input class="widefat" type="text" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" value="' . $title . '" /></p>';

    echo '<p><input class="checkbox" type="checkbox" id="' . $this->get_field_id('modern') . '" name="' . $this->get_field_name('modern') . '"' . $checked . '/><label for="' . $this->get_field_name('modern') . '">' . __('Use placeholders instead of labels') . '</label></p>'; 
  }
}

add_action('init', 'placester_contact_widget');

// Style
function placester_contact_widget() {

  $myStyleUrl = WP_PLUGIN_URL . '/placester/css/contact.widget.ajax.css';
  wp_enqueue_style( 'contactwidgetcss', $myStyleUrl );
  $myScriptUrl = WP_PLUGIN_URL . '/placester/js/contact.widget.ajax.js';
  wp_enqueue_script( 'contactwidgetjs', $myScriptUrl, array('jquery') );
}

// Ajax function
function ajax_placester_contact() {
    if( !empty($_POST) ) {

      $error = "";

      // Check to make sure sure that a valid email address is submitted
      if( trim($_POST['email']) == '' )  {
        $error .= "An email address is required<br/>";
      } else if ( !eregi("^[A-Z0-9._%-]+@[A-Z0-9._%-]+\.[A-Z]{2,4}$", trim($_POST['email'])) ) {
        $error .= "A valid email address is required<br/>";
      } else {
        $email = trim($_POST['email']);
      }

      // Check to make sure that the first name field is not empty
      if( trim($_POST['firstName']) == '' ) {
        $error .= "Your first name is required<br/>";
      } else {
        $firstName = trim($_POST['firstName']);
      }

      // Check to make sure that the last name field is not empty
      if( trim($_POST['lastName']) == '' ) {
        $error .= "Your last name is required<br/>";
      } else {
        $lastName = trim($_POST['lastName']);
      }

      // Check the question field
      if( trim($_POST['question']) == '' ) {
        $question = "They had no questions at this time";
      } else {
        $question = $_POST['question'];
      }

      // Check the hidden fields
      if( trim($_POST['placesterEmail']) == '' ) {
        $error .= "What do you think you're doing?";
      } else {
        $placesterEmail = $_POST['placesterEmail'];
      }

      if( trim($_POST['id']) == '' ) {
        $error .= "What do you think you're doing?";
      } else {
        $id = $_POST['id'];
      }

      if( trim($_POST['fullAddress']) == '' ) {
        $error .= "What do you think you're doing?";
      } else {
        $fullAddress = $_POST['fullAddress'];
      }


    if( empty($error) ) {

      $message  = "Someone wants to get in touch with you. \n\n" .
                  "Listing number: " . $id . "\n" .
                  "Listing address: " . $fullAddress . "\n\n" .
                  "Name: " . $firstName . " " . $lastName . "\n" .
                  "Email: " . $email . "\n" .
                  "Question: " . $question . "\n\n";
      // Mail it
      $placester_Mail = wp_mail($placesterEmail, 'Contact on listing', $message);
      echo "sent";

    } else {
      echo $error;
    }
    die();
  }
}