<?php
/*
 * Minimalist HTML framework. 
 * Created by scribu in scbFramework
 *
 * Examples:
 *
 * pls_h( 'p', 'Hello world!' ); 
 * <p>Hello world!</p>
 *
 * pls_h( 'a', array( 'href' => 'http://example.com' ), 'A link' );
 * <a href="http://example.com">A link</a>
 *
 * pls_h( 'img', array( 'src' => 'http://example.com/f.jpg' ) );
 * <img src="http://example.com/f.jpg" />
 *
 * pls_h( 'ul', pls_h( 'li', 'a' ), pls_h( 'li', 'b' ) );
 * <ul><li>a</li><li>b</li></ul>
 *
 * @since 0.0.1
 */
function pls_h( $tag ) {

    $args = func_get_args();

    $tag = array_shift( $args );
    if ( !empty($args) && is_array( $args[0] ) ) {
        $closing = $tag;
        $attributes = array_shift( $args );
        foreach ( $attributes as $key => $value ) {
            if ( false === $value )
                continue;

            if ( true === $value )
                $value = $key;

            $tag .= ' ' . $key . '="' . esc_attr( $value ) . '"';
        }
    } else {
        list( $closing ) = explode( ' ', $tag, 2 );
    }

    if ( in_array( $closing, array( 'area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta' ) ) ) {
        return "<{$tag} />";
    }

    $content = implode( '', $args );

    return "<{$tag}>{$content}</{$closing}>";
}

/**
 * Creates an anchor html tag with given parameters.
 * 
 * @param string $url The anchor href value.
 * @param string $title The anchor content.
 * @param array $extra_attr Optional. Array containing extra attributes. 
 *  Attributes must be provided in the array( "attr_name" => "attr_value" ) form.
 * @return string The anchor element.
 * @since 0.0.1
 */
function pls_h_a( $url, $title = false, $extra_attr = array(), $noesc = false ) {

    $title = empty( $title ) ? $url : $title;

    /** Call the escaping or non-escaping html function to generate the link. */
    return pls_h( 'a', array( 'href' => $url ) + $extra_attr, $title );
}

/**
 * Creates an img html tag with given parameters.
 * 
 * @param string $src The img src attribute value.
 * @param string $title Option. Defaults to src. The alt attribute vaue.
 * @param array $extra_attr Optional. Array containing extra attributes. 
 *  Attributes must be provided in the array( "attr_name" => "attr_value" ) form.
 * @return string The img element.
 * @since 0.0.1
 */
function pls_h_img( $src, $alt = false, $extra_attr = array() ) {

    $alt = empty( $alt ) ? $src : $alt;

    return pls_h( 'img', array( 'src' => $src, 'alt' => $alt ) + $extra_attr + array( 'title' => $alt ) );
}

/**
 * TODO
 * 
 * @param mixed $content 
 * @param array $extra_attr 
 * @access public
 * @return void
 */
function pls_h_p( $content, $extra_attr = array() ) {

    return pls_h( 'p', $extra_attr, $content );
}

function pls_h_span( $content, $extra_attr = array() ) {

    return pls_h( 'span', $extra_attr, $content );
}

/**
 * TODO
 * 
 * @param mixed $content 
 * @param array $extra_attr 
 * @access public
 * @return void
 */
function pls_h_div( $content, $extra_attr = array() ) {

    return pls_h( 'div', $extra_attr, $content );
}

/**
 * TODO
 * 
 * @param mixed $for 
 * @param array $extra_attr 
 * @access public
 * @return void
 */
function pls_h_label( $content, $for = '', $extra_attr = array() ) {

    return pls_h( 'label', array( 'for' => $for ) + $extra_attr, $content );
}

function pls_h_li( $content, $extra_attr = array() ) {

    return pls_h( 'li', $extra_attr, $content );
}

/**
 * TODO
 * 
 * @param mixed $for 
 * @param array $extra_attr 
 * @access public
 * @return void
 */
function pls_h_checkbox( $checked, $extra_attr = array() ) {

    $attributes = array(
        'class' => 'checkbox',
        'type' => 'checkbox',
    ) + $extra_attr;

    if ( $checked )
        $attributes['checked'] = true;

    return pls_h( 'input', $attributes );
}

/**
 * TODO
 * 
 * @param mixed $option_array 
 * @param mixed $selected_value 
 * @param mixed $clone_value 
 * @param array $extra_attr 
 * @access public
 * @return void
 */
function pls_h_options( $option_array, $selected_value = false, $clone_value = false, $extra_attr = array() ) {

    if ( ! is_array( $option_array ) )
        return;

    $return = '';

    foreach ( $option_array as $key => $value ) {

        if ( $key === 'pls_empty_value' ) 
            $option_value = "";
        elseif ( ! $clone_value )
            $option_value = $key;
        else 
            $option_value = $value;

        $option_label = $value;

        $attr = array();

        if ( ( $selected_value ) && ( (string) $selected_value == (string) $option_value ) )
            $attr['selected'] = true;

        $attr['value'] = $option_value;

        if ( isset( $extra_attr[$key] ) )
            $attr = $attr + $extra_attr[$key];

        if ( isset( $extra_attr['all'] ) )
            $attr = $attr + $extra_attr['all'];

        $return .= pls_h( 'option', $attr, $option_label );
    }
    return $return;
}
