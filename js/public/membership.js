jQuery(document).ready(function($) {

    // Beat Chrome's HTML5 tooltips for form validation
    $('form.pl_lead_register_form input[type="submit"]').on('mousedown', function() {
        validate_register_form(this);
    });

    $('form#pl_login_form input[type="submit"]').on('mousedown', function() {
        validate_login_form();
    });
    
    // Catch "Enter" keystroke and block it from submitting, except on Submit button
    $('.pl_lead_register_form').bind('keypress', function(e) {
        var code = e.keyCode || e.which;
        if (code  == 13) {
            validate_register_form(this);
        }
    });

    $('#pl_login_form').bind('keypress', function(e) {
        var code = e.keyCode || e.which;
        if (code  == 13) {
            validate_login_form();
        }
    });
    
    $('.pl_lead_register_form').bind('submit', function (event) {     
        // Prevent default form submission logic
        event.preventDefault();
        var form = $(this);
        
        if ($('.invalid', this).length) {
          return false;
        };
        
        nonce = $(this).find('#register_nonce_field').val();
        username = $(this).find('#reg_user_email').val();
        email = $(this).find('#reg_user_email').val();
        password = $(this).find('#reg_user_password').val();
        confirm = $(this).find('#reg_user_confirm').val();

        data = {
            action: "pl_register_site_user",
            username: username,
            email: email,
            nonce: nonce,
            password: password,
            confirm: confirm
        };

        register_user(data);
    });
    
    // Initialize validator and add the custom form submission logic
    $('form#pl_login_form').bind('submit', function (event) {
        // Prevent default form submission logic
        event.preventDefault();
        var form = $(this);

        if ($('.invalid', this).length) {
            return false;
        }

        username = $(form).find('#user_login').val();
        password = $(form).find('#user_pass').val();
        remember = $(form).find('#rememberme').val();

        data = {
            action: "pl_login_site_user",
            username: username,
            password: password,
            remember: remember
        };

        login_user(data);
    });
    
    if (typeof $.fancybox == "function") {
        // Register Form Fancybox
        $('.pl_register_lead_link').fancybox({
            "hideOnContentClick": false,
            "scrolling": true,
            onClosed: function () {
                $('.register-form-validator-error').remove();
            }
        });

        // Login Form Fancybox
        $('.pl_login_link').fancybox({
            "hideOnContentClick": false,
            "scrolling": true,
            onClosed: function () {
                $('.login-form-validator-error').remove();
            }
        });

        $(document).ajaxStop(function() {
            favorites_link_signup();
        });
    }

    favorites_link_signup();

    function favorites_link_signup () {
        if (typeof $.fancybox == 'function') {
            $('.pl_register_lead_favorites_link').fancybox({
                "hideOnContentClick": false,
                "scrolling": true
            }); 
        }
    }
    
    function register_user (data) {
        // Need to validate here too, just in case someone press enter in the form instead of pressing submit
        validate_register_form();

        $.post(info.ajaxurl, data, function (response) {
            if (response && response.success) {
                // Remove error messages
                $('.register-form-validator-error').remove();

                // Remove form
                $("#pl_lead_register_form_inner_wrapper").slideUp();

                // Show success message
                setTimeout(function () { $("#pl_lead_register_form .success").show('fast'); }, 500);

                // send window to redirect link
                setTimeout(function () { window.location.href = window.location.href; }, 1500);

                $('#pl_lead_register_form .success').fadeIn('fast');
                setTimeout(function () { window.location.href = window.location.href; }, 700);
            }
            else {
                // Error Handling
                var errors = (response && response.errors) ? response.errors : {};

                // jQuery Tools Validator error handling
                $('form#pl_lead_register_form').validator();

                // Take possible errors and create new object with correct ones to pass to validator
                error_keys = new Array("user_email", "user_password", "user_confirm");
                error_obj = new Object();

                for (key in errors) {
                    if (error_keys.indexOf(key) != -1) {
                        error_obj[key] = errors[key];
                    }
                }

                $('form#pl_lead_register_form input').data("validator").invalidate(error_obj);
            }
        }, 'json');
    }
    
    function login_user (data) {
        // Need to validate here too, just in case someone press enter in the form instead of pressing submit
        validate_login_form();

        $.post(info.ajaxurl, data, function (response) {
            // If request successfull empty the form...
            if (response && response.success) {
                // Remove error messages...
                $('.login-form-validator-error').remove();

                // Hide form...
                $("#pl_login_form_inner_wrapper").slideUp();

                // Show success message
                // setTimeout(function() { $('#pl_login_form .success').show('fast'); }, 500);
             
                // Send window to redirect link...
                window.location.href = window.location.href;
            } 
            else {
                // Error Handling
                var errors = (response && response.errors) ? response.errors : {};

                // jQuery Tools Validator error handling
                $('form#pl_login_form').validator();

                // Take possible errors and create new object with correct ones to pass to validator
                error_keys = new Array("user_login", "user_pass");
                error_obj = new Object();

                for (key in errors) {
                    if (error_keys.indexOf(key) != -1) {
                        error_obj[key] = errors[key];
                    }
                }

                $('form#pl_login_form input').data("validator").invalidate(error_obj);
            }
        }, 'json');
    }

    function validate_register_form () {
        var this_form;

        if (arguments.length > 0) {
            this_form = $(arguments[0]);
            this_form = this_form.closest('form');
        } 
        else {
            var this_form = $('form#pl_lead_register_form'); 
        }
        // get fields that are required from form and execture validator()
        var inputs = $(this_form).find("input[required]").validator({
            messageClass: "register-form-validator-error", 
            offset: [10,0],
            message: "<div><span></span></div>",
            position: "top center"
        });

        // check required field's validity
        inputs.data("validator").checkValidity();
    }

    function validate_login_form () {
        var this_form = $('form#pl_login_form');

        // get fields that are required from form and execture validator()
        var inputs = $(this_form).find('input[required]').validator({
            messageClass: "login-form-validator-error", 
            offset: [10,0],
            message: "<div><span></span></div>",
            position: "top center"
        });

        // check required field's validity
        inputs.data("validator").checkValidity();
    }

    /*
     * Property/Listing "favorites" functionality...
     */

    // Don't ajaxify the add to favorites link for guests
    $('#pl_add_favorite:not(.guest)').live('click', function (event) {
        event.preventDefault();

        var spinner = $(this).parent().find(".pl_spinner");
        spinner.show();

        property_id = $(this).attr('href');

        data = {
            action: 'add_favorite_property',
            property_id: property_id.substr(1)
        };

        var that = this;
        $.post(info.ajaxurl, data, function (response) {
            spinner.hide();

            // This property will only be set if WP determines user is of admin status...
            if ( response.is_admin) {
                alert('Sorry, admins currently aren\'t able to maintain a list of "favorite" listings');
            }

            if ( response.id ) {
                $(that).hide();
                if ($(that).attr('id') == 'pl_add_favorite') {
                    $(that).parent().find('#pl_remove_favorite').show();
                } 
                else {
                    $(that).parent().find('#pl_add_favorite').show();
                };
            }
        }, 'json');
    });

    $('#pl_remove_favorite').live('click',function (event) {
        event.preventDefault();
        var that = this;
        $spinner = $(this).parent().find(".pl_spinner");
        $spinner.show();

        property_id = $(this).attr('href');
        data = {
            action: 'remove_favorite_property',
            property_id: property_id.substr(1)
        };

        $.post(info.ajaxurl, data, function (response) {
            $spinner.hide();
            // If request successfull
            if ( response != 'errors' ) {
                $('#pl_add_favorite').show();
                $('#pl_remove_favorite').hide();
            }
        }, 'json');
    }); 

/* TODO: Get FB login working...
    
    //
    // Facebook Login
    //

    // Additional JS functions here
    window.fbAsyncInit = function() {
        fb_init();

        // check FB login status
        FB.getLoginStatus(function(response) {
    
        // Is user logged into FB?
        if (response.status === 'connected') {
            // var accessToken = response.authResponse.accessToken;
            console.log(response);
            var user_id = response.authResponse.userID;


            // get user info
            var u_info = '';

            FB.api('/me', function(user) {
                console.log(user);
                u_info = user;
            });
    
            // console.log(u_info);

            // verified_response = parse_signed_request(signed_request);
            // if (verified_response) {
            //   connect_wp_fb(user_id);
            // } else {
            //   console.log('sorry, something went wrong');
            // }

            // log in user if user_id exists in our user list via ajax

            // else prompt them to register
            } 
            else if (response.status === 'not_authorized') {
                // not_authorized
                console.log("not authorized");
                // login();
            } 
            else {
                // not_logged_in
                console.log("not logged in");
                // add login button
                // login_to_fb();
            }
        });
    };

    function fb_init () {
        FB.init({
            appId: "263914027073402", // App ID
            channelUrl: "<?php echo get_template_directory_uri(); ?>/fb_channel.html", // Channel File
            status: true, // check login status
            cookie: true, // enable cookies to allow the server to access the session
            xfbml: true  // parse XFBML
        });
    }

    function connect_wp_fb (user_id) {
        data = {
            action: 'connect_wp_fb',
            user_id: user_id//,
            // user_nickname: user_nickname
        };

        $.ajax({
            url: info.ajaxurl,
            data: data, 
            async: false,
            type: "POST",
            success: function (response) { 
                // console.log(response); 
            }
        });
    }

    function parse_signed_request (signed_request) {
        data = {
            action: 'parse_signed_request',
            signed_request: signed_request
        };

        success = false;

        $.ajax({
            url: info.ajaxurl,
            data: data, 
            async: false,
            type: "POST",
            success: function (response) {
                success = true;
            }
        });

        return success;
    }
*/

});
