<?php
if ( !function_exists( 'placester_of_init' ) ) {
    define( 'OPTIONS_FRAMEWORK_DIRECTORY', plugins_url( '', __FILE__ ) . '/' );
    define( 'OPTIONS_FRAMEWORK_URL', dirname( __FILE__) );
    require_once ( OPTIONS_FRAMEWORK_URL . '/core.php');
}
