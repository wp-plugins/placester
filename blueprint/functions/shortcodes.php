<?php 

// Register Shortcodes
function pls_bp_shortcodes () {
  add_shortcode('lead_capture_template','lead_capture_shortcode');
}
add_action('init', 'pls_bp_shortcodes');

// Lead Capture
function lead_capture_shortcode($args) {
  $args_with_overrides = shortcode_atts(
    array(
      // Lead Capture Wrapper
      'width' => '',
      'height' => '',
      'title_visible' => true,
      // Contact Form
      'title_text' => '',
      'title' => '',
      'success_message' => '',
      'cc_value' => '',
      'bcc_value' => '',
      // Name
      'name_placeholder' => 'Full Name',
      'name_required' => true,
      'name_error' => 'Your name is required.',
      // Email
      'email_placeholder' => 'Email Address',
      'email_required' => true,
      'email_error' => 'A valid email is required.',
      // Phone
      'phone_include' => true,
      'phone_placeholder' => 'Phone Number',
      'phone_required' => "false",
      'phone_error' => 'Your phone number is required.',
      // Subject
      'subject_placeholder' => 'Subject',
      'subject_required' => "false",
      'subject_error' => 'Please add a subject.',
      // Question
      'question_placeholder' => 'Comments',
      'question_required' => true,
      'question_error' => "Don't forget to leave a question or comment.",
      'button_text' => 'Submit',
      // Description
      'description_visible' => true,
      // Form Options
      'back_on_lc_cancel' => ''
    ), 
    $args
  );

  echo PLS_Lead_Capture::get_contact_form($args_with_overrides);
}