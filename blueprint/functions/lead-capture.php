<?php

PLS_Lead_Capture::init();
class PLS_Lead_Capture {

    public static function init () {
        // Add shortcode for template...
        add_shortcode('lead_capture_template', array(__CLASS__, 'lead_capture_shortcode'));
    }

	public static function get_contact_form () {
        // Get args passed to shortcode
        $form_args = func_get_args();
        
        extract($form_args[0], EXTR_SKIP);

        $title_text = pls_get_option('pd-lc-form-title');
        $description = pls_get_option('pd-lc-form-description');

        // Default
        ob_start();
        ?>
            <div class="lead-capture-wrapper" style="width: <?php echo $width; ?>px !important; height:<?php echo $height; ?>px !important;">
              
              <?php if (($title_visible == 1) && !empty($title_text) ): ?>
                <p class="lc-title"><?php echo $title_text; ?></p>
              <?php endif ?>
              
              <!-- Lead Capture Description Text -->
              <?php if ( $description_visible == true && !empty( $description ) ): ?>
                <div class="lc-description">
                  <?php echo $description; ?>
                </div>
              <?php endif; ?>
              
              <!-- Contact Form -->
              <div class="lc-contact-form">
                <?php if (class_exists('Placester_Contact_Widget')) {
                    // Lead Capture Form
                    $instance = array(
                        "title" => '',
                        "title_visible" => $title_visible != false ? true : false,
                        "success_message" => !empty($success_message) ? $success_message : "Thank you for the email, we'll get back to you shortly",
                        // Name
                        "name_value" => $name_placeholder != "Full Name" ? $name_placeholder : "Full Name",
                        "name_required" => $name_required != false ? $name_required : true,
                        "name_error" => $name_error != false ? $name_error : false,
                        // Email
                        "email_value" => $email_placeholder != "Email Address" ? $email_placeholder : "Email Address",
                        "email_required" => $email_required != false ? $email_required : true,
                        "email_error" => $email_error != false ? $email_error : false,
                        // Phone
                        "phone_value" => $phone_placeholder != "Phone Number" ? $phone_placeholder : "Phone Number",
                        "phone_required" => $phone_required != false ? $phone_required : true,
                        "phone_error" => $phone_error != false ? $phone_error : false,
                        // Subject
                        "subject_value" => $subject_placeholder != "Subject" ? $subject_placeholder : "Subject",
                        "subject_required" => $subject_required != false ? $subject_required : true,
                        "subject_error" => $subject_error != false ? $subject_error : false,
                        // Question
                        "question_label" => $question_placeholder != "Comments" ? $question_placeholder : "Comments",
                        "question_required" => $question_required != false ? $question_required : true,
                        "question_error" => $question_error != false ? $question_error : false,
                        // Form Options
                        "phone_number" => $phone_include != true ? false : true,
                        "cc_value" => $cc_value != false ? $cc_value : false,
                        "bcc_value" => $bcc_value != false ? $bcc_value : false,
                        "back_on_lc_cancel" => '',
                        "button" => $button_text != "Submit" ? $button_text : "Submit",
                        "number" => 9
                    );

                    $args = array("id" => 99); //giving high tab index numbers as to not collide with sidebar widgets
                    $sb = new Placester_Contact_Widget();
                    $sb->number = $instance['number'];
                    $sb->widget($args,$instance);
                } ?>
              </div>
              
            </div>
        <?php
        
        $form = ob_get_clean();

        return $form;
    }

    // Lead Capture
    public static function lead_capture_shortcode($args) {
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

        echo self::get_contact_form($args_with_overrides);
    }

} // end class