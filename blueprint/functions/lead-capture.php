<?php

PLS_Lead_Capture::init();
class PLS_Lead_Capture {

    public static function init () {
        add_action('wp_ajax_pls_update_client_profile', array(__CLASS__, 'update'));
    }

    public static function update () {
        $person_details = $_POST;
        $result = PLS_Plugin_API::update_person_details($person_details);

        echo json_encode($result);
        die();
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

} // end class