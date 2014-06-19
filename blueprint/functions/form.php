<?php

// Extract functionality from contact and feedburner widgets to a separate method
// Reuse in contact/lead form
// Download form add
// Sign in/up from BP
// Separate independent validation methods
// email send separately
// actions in form UIs
// decomposable (to some extend) UI for forms
// default facade functions for build + submit

class PLS_Form {
	
	/**
	 * 
	 * @var string form type
	 * Form types: contact, lead_capture, newsletter,
	 * sign in, sign up
	 */ 
	private $form_type;
	
	public function __construct( $form_type = 'contact' ) {
		$this->form_type = $form_type; 
	}
	
	public function build_form( $args = array() ) {
		switch( $this->form_type ) {
			case 'contact':
				return $this->build_contact_form( $args );
			case 'lead_capture':
				return $this->build_lead_capture_form( $args );
			case 'newsletter':
				return $this->build_newsletter_form( $args );
			case 'sign_in':
				return $this->build_login_form( $args );
			case 'sign_up':
				return $this->build_signup_form( $args );
		}		
	}
	
	public function submit_form( $types = array(), $args = array() ) {
		if( in_array( 'email_admin', $types ) ) {
			self::email_admin( $args );
		}
		if( in_array( 'email_verification', $types ) ) {
			self::email_verification( $args );
		}
		if( in_array( 'download_form', $types ) ) {
			self::download_form( $args );
		}
		if( in_array( 'add_lead', $types ) ) {
			self::add_lead( $args );
		}
		if( in_array( 'drop_cookie', $types ) ) {
			self::drop_cookie( $args );
		}
	}
	
	// Email admin
	private function email_admin( $args ) {
		
	} 
	
	// Email verification
	private function email_verification( $args ) {
		
	}
	
	// Download form
	private function download_form( $args ) {
		
	}
	
	// Add lead capture
	private function add_lead( $args ) {
		
	}
	
	// Drop cookie for lead capture
	private function drop_cookie( $args ) {
		$lead_capture_cookie = apply_filters('lead_capture_cookie', !isset($instance['lead_capture_cookie']) ? '' : $instance['lead_capture_cookie']);
	}
	
	private function build_lead_capture_form( $args ) {
		$defaults = array(
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
				);
		
		$args = wp_parse_args( $args, $defaults );
		
		return PLS_Lead_Capture::get_contact_form($args);
	}
	
	private function build_contact_form( $args ) {
		$defaults = array(
			'form_id' => 'main-form',
			'inner_containers' => 'inner-container',
			'title' => '',
			'title_visible' => false,
			'success_message' => 'Thank you for the email, we\'ll get back to you shortly',
			'name_value' => 'Full Name',
			'email_value' => 'Email Address',
			'question_label' => 'Comments',
			'form_widget_id' => 19
		);
		$args = wp_parse_args($args, $defaults);
		extract( $args );

		$instance = array(
				"id" => $form_id,
				"inner_containers" => $inner_containers,
				"title" => $title,
				"title_visible" => $title_visible,
				"success_message" => $success_message,
				"name_value" => $name_value,
				"email_value" => $email_value,
				"question_label" => $question_label,
		);

		$sb = new Placester_Contact_Widget();
		
		ob_start();
		$sb->widget( $args,$instance );
		return ob_get_clean();
		
	}
	
	private function build_newsletter_form( $args ) {
		$defaults = array(
				'form_id' => 'main-form',
				'inner_containers' => 'inner-container',
				'title' => '',
				'title_visible' => false,
				'success_message' => 'Thank you for the email, we\'ll get back to you shortly',
				'name_value' => 'Full Name',
				'email_value' => 'Email Address',
				'question_label' => 'Comments',
				'form_widget_id' => 19,
				'title_widget' => 'New Listings Alert',
				'email_placeholder' => 'Enter Email Address'
		);
		$args = wp_parse_args($args, $defaults);
		
		$instance = array( 
			'title' => $args['title_widget'], 
			'email_placeholder' => $args['email_placeholder']
		);
		
		$newsletter_widget = new PLS_Widget_Feedburner_Widget();
		
		ob_start();
		$newsletter_widget->widget( $args, $instance );
		return ob_get_clean();
	}
	
	private function build_login_form( $args ) {
		$url = '';
		if (is_home()) {
			$url = home_url();
		} else {
			$url = get_permalink();
		}
		ob_start();
		?>
                <form name="pl_login_form" id="pl_login_form" action="<?php echo home_url(); ?>/wp-login.php" method="post" class="pl_login_reg_form">

                    <?php pls_do_atomic( 'login_form_before_title' ); ?>

                    <div id="pl_login_form_inner_wrapper">
                      <h2>Login</h2>
                      <!-- redirect-uri="<?php //echo $_SERVER["HTTP_REFERER"]; ?>" -->
                      <!-- <fb:registration fields="name,location,email" width="260"></fb:registration> -->
                      
                      <?php pls_do_atomic( 'login_form_before_email' ); ?>
                      
                      <p class="login-username">
                        <label for="user_login">Email</label>
                        <input type="text" name="user_login" id="user_login" class="input" required="required" value="" tabindex="20" data-message="A valid email is needed" placeholder="Email" />
                      </p>
                      
                      <?php pls_do_atomic( 'login_form_before_password' ); ?>
                      
                      <p class="login-password">
                        <label for="user_pass">Password</label>
                        <input type="password" name="user_pass" id="user_pass" class="input" required="required" value="" tabindex="21" data-message="A password is needed" placeholder="Password" />
                      </p>
                      
                      <?php pls_do_atomic( 'login_form_before_remember' ); ?>
                      
                      <p class="login-remember">
                        <label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="22" /> Remember Me</label>
                      </p>
                      
                      <?php pls_do_atomic( 'login_form_before_submit' ); ?>
                      
                      <p class="login-submit">
                        <input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Log In" tabindex="23" />
                        <input type="hidden" name="redirect_to" value="<?php echo $url; ?>" />
                      </p>

                      <?php pls_do_atomic( 'before_login_title' ); ?>

                    </div>

                </form>
              <?php
            $login_form = ob_get_clean();

            return $login_form;
	}
	
	private function build_signup_form( $args ) {
		if ( ! is_user_logged_in() ) {
			ob_start();
			?>
		        <div style="display:none;">
		          <form method="post" action="#<?php echo $role; ?>" id="pl_lead_register_form" name="pl_lead_register_form" class="pl_login_reg_form" autocomplete="off">
		
		              <div style="display:none" class="success">You have been successfully signed up. This page will refresh momentarily.</div>
		
		              <div id="pl_lead_register_form_inner_wrapper">
		
		                <?php pls_do_atomic( 'register_form_before_title' ); ?>
		                
		                <h2>Sign Up</h2>
		
		                <?php pls_do_atomic( 'register_form_before_email' ); ?>
		                
		                <p class="reg_form_email">
		                  <label for="user_email">Email</label>
		                  <input type="text" tabindex="25" size="20" required="required" class="input" id="reg_user_email" name="user_email" data-message="A valid email is needed." placeholder="Email">
		                </p>
		                
		                <?php pls_do_atomic( 'register_form_before_password' ); ?>
		                
		                <p class="reg_form_pass">
		                  <label for="user_password">Password</label>
		                  <input type="password" tabindex="26" size="20" required="required" class="input" id="reg_user_password" name="user_password" data-message="Please enter a password." placeholder="Password">
		                </p>
		                
		                <?php pls_do_atomic( 'register_form_before_confirm_password' ); ?>
		                
		                <p class="reg_form_confirm_pass">
		                  <label for="user_confirm">Confirm Password</label>
		                  <input type="password" tabindex="27" size="20" required="required" class="input" id="reg_user_confirm" name="user_confirm" data-message="Please confirm your password." placeholder="Confirm Password">
		                </p>
		                
		                <?php pls_do_atomic( 'register_form_before_submit' ); ?>
		                
		                <p class="reg_form_submit">
		                  <input type="submit" tabindex="28" class="submit button" value="Register" id="pl_register" name="pl_register">
		                </p>
		                <?php echo wp_nonce_field( 'placester_true_registration', 'register_nonce_field' ); ?>
		                <input type="hidden" tabindex="29" id="register_form_submit_button" name="_wp_http_referer" value="/listings/">
		                
		                <?php pls_do_atomic( 'register_form_after_submit' ); ?>
		
		              </div>
		
		          </form>
		        </div>
	        <?php
	        $result = ob_get_clean();
	    } else {
	        ob_start();
	        ?>
	            <div style="display:none">
	                <div class="pl_error error" id="pl_lead_register_form">
	                 You cannot register a user if you are logged in. You shouldn't even see a "Register" link.
	                </div>
	            </div>
	        <?php
	        $result = ob_get_clean();
	    }
		
	    return $result; 
	}
}