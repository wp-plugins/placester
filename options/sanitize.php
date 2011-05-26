<?php

/* Text */
add_filter( 'of_sanitize_text', 'sanitize_text_field' );

/* Textarea */
add_filter( 'of_sanitize_textarea', 'of_sanitize_textarea' );
function of_sanitize_textarea($input) {
	global $allowedtags;
	$output = wp_kses( $input, $allowedtags );
	return $output;
}

/* Select */
add_filter( 'of_sanitize_select', 'of_sanitize_enum', 10, 2);

/* Radio */
add_filter( 'of_sanitize_radio', 'of_sanitize_enum', 10, 2);

/* Check that the key value sent is valid */
function of_sanitize_enum( $input, $option ) {
	$output = '';
	if ( array_key_exists( $input, $option['options'] ) ) {
		$output = $input;
	}
	return $output;
}

/* Checkbox */
add_filter( 'of_sanitize_checkbox', 'of_sanitize_checkbox' );
function of_sanitize_checkbox( $input ) {
	if ( $input ) {
		$output = "1";
	} else {
		$output = "0";
	}
	return $output;
}

/* Multicheck */
add_filter( 'of_sanitize_multicheck', 'of_sanitize_multicheck', 10, 2 );
function of_sanitize_multicheck( $input, $option ) {
	$output = '';
	if ( is_array( $input ) ) {
		foreach( $option['options'] as $key => $value ) {
			$output[$key] = "0";
		}
		foreach( $input as $key => $value ) {
			if ( array_key_exists( $key, $option['options'] ) && $value ) {
				$output[$key] = "1"; 
			}
		}
	}
	return $output;
}

/* Color Picker */
/**
 * Sanitize a color represented in hexidecimal notation.
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @param    string    The value that this function should return if it cannot be recognized as a color.
 * @return   string
 *
 */
add_filter( 'of_sanitize_color', 'of_sanitize_hex' );
function of_sanitize_hex( $hex, $default = '' ) {
    $valid_hex =  of_validate_hex( $hex );
	if ( $valid_hex ) {
        $hex = $valid_hex === 3 ? $hex . $hex : $hex;
		return '#' . $hex;
	}
	return $default;
}

/**
 * Checks if a given color string is in the correct hex format
 *
 * @param    string    Color in hexidecimal notation. "#" may or may not be prepended to the string.
 * @return   bool
 *
 */
function of_validate_hex( $hex ) {
	$hex = trim( $hex );
	// Strip recognized prefixes.
	if ( 0 === strpos( $hex, '#' ) ) {
		$hex = substr( $hex, 1 );
	}
    // If '#' is url encoded
	elseif ( 0 === strpos( $hex, '%23' ) ) {
		$hex = substr( $hex, 3 );
	}
	// Regex match.
    if ( preg_match( '/^[0-9a-fA-F]{3}$/', $hex ) ) { 
		return 3;
    }
	else if ( preg_match( '/^[0-9a-fA-F]{6}$/', $hex ) ) {
		return 6;
	}
	else {
		return false;
	}
}
