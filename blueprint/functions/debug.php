<?php
/**
 * Wrapper function for the PLS_Debug::dump() function.
 */
function pls_dump() {
	$args = func_get_args();
	PLS_Debug::dump($args);
}

/**
 * Wrapper function for the PLS_Debug::trace() function.
 */
function pls_trace($str='') {
	PLS_Debug::trace($str, 2);
}



PLS_Debug::init();
/**
 * A class that includes theme debugging functions
 *
 * @static
 */
class PLS_Debug {

	private static $debug_messages = array();
	private static $message_text = '';
	private static $show_debug = false;

	public static function init() {
		add_action('init', array(__CLASS__, 'setup'));
	}

	public static function setup() {
		if (function_exists('pls_get_option') && (self::$show_debug = pls_get_option('display-debug-messages'))) {
			add_action('wp_footer', array(__CLASS__, 'show_window'));
			//add_action('admin_footer', array(__CLASS__, 'show_window'));
		}
	}

	public function trace($str='', $depth=1) {
		if (!defined('WP_DEBUG') && !self::$show_debug) {
			return;
		}
		$bt = debug_backtrace();
		$depth = ($depth<1 || $depth>=count($bt) ? 1 : $depth);
		$fn = (empty($bt[$depth]['class']) ? '' : $bt[$depth]['class'].'::') . $bt[$depth]['function'] . '()' . (empty($bt[$depth-1]['line']) ? '' : ', Line:'.$bt[$depth-1]['line']);
		if (is_object($str) || is_array($str)) {
			$str = '<pre>'.print_r($str,true).'</pre>';
		}
		self::$debug_messages[] = 'TRACE '.time().': '.$fn.($str?"<br/>\n".$str:'');
		if (defined('WP_DEBUG')) {
			error_log(str_replace(array('<br/>','<pre>','</pre>'), '', end(self::$debug_messages)));
		}
	}

	public function backtrace($depth = 0) {
		if (!defined('WP_DEBUG') && !self::$show_debug) {
			return;
		}
		$bt = debug_backtrace();
		$str = '';
		$depth = ($depth<1 ? count($bt) : ++$depth);
		for ($i=1; $i<$depth; $i++) {
			$t = $bt[$i];
			$str .= ($i>1 ? "   " : '').(empty($t['class']) ? '' : $t['class'].':') . $t['function'] . '(';
			$param = '';
			foreach($t['args'] as $a) {
				if ($param) $param .= ', ';
				if (is_array($a)) {
					$param .= 'array('.count($a).')';
				}
				elseif (is_object($a)) {
					$param .= 'object';
				}
				else {
					$param .= "'".(strlen($a)>150 ? substr($a,0,150).'...' : $a)."'";
				}
			}
			$str .= "$param)".(empty($bt[$i-1]['line'])?'':", line:{$bt[$i-1]['line']}")."<br/>\n";
		}

		self::$debug_messages[] = time().': '.$str;
		if (defined('WP_DEBUG')) {
			error_log(str_replace(array('<br/>','<pre>','</pre>'), '', end(self::$debug_messages)));
		}
	}

	public static function show_window () {
		// optionally show debug messages.
		if (self::$show_debug) {
			self::assemble_messages();
			?>
<div
	style="position:fixed; bottom:0px; left:0px; width:100%; height:35%; background-color:#F8F8F8; overflow:auto; border-top:2px solid black; font-size:11px; color:black; z-index:9999;">
	<h4>Blueprint Debug Messages</h4>
	<?php echo self::$message_text; ?>
</div>
			<?php
		}
	}

	// Adds routing messages for easy debugging.
	// TODO: Move this to a global class so devs
	// turn it on easily and see what's going on.
	public static function add_msg ($new_message) {
		self::$debug_messages[] = $new_message;
	}

	public static function assemble_messages ($messages_array = false) {
		self::$message_text = "<ul>";

		foreach ( (array) self::$debug_messages as $message) {
			self::$message_text .= self::style_message($message);
		}

		self::$message_text .= "</ul>";
	}

	public static function style_message ($message, $indent = false) {
        $styled_message = "<li>";

        if ($indent) {
            $styled_message .= "<ul>";
        }

        if ( is_array($message) || is_object($message) ) {
            foreach ($message as $item) {
                $styled_message .= self::style_message($item, true);
            }
        }
        else {
            $styled_message .= $message;
        }

        if ($indent) {
            $styled_message .= "</ul>";
        }

        $styled_message .= "</li>";

        return $styled_message;
    }

    /**
     * Dumps a variable for debugging purposes
     *
     * @param mixed $data The variable that needs to be dumped.
     * @static
     */
    public static function dump() {
        $args = func_get_args();
        /**
         *  If the given variable is an array use print_r
        */
        foreach ( $args as $data ) {
            if( is_array( $data ) ) {
                print "<pre>-----------------------\n";
                print_r( $data );
                print "-----------------------</pre>\n";
            } elseif ( is_object( $data ) || is_bool( $data ) ) {
                print "<pre>==========================\n";
                var_dump( $data );
                print "===========================</pre>\n";
            } else {
                print "<pre>=========&gt; ";
                echo $data;
                print " &lt;=========</pre>";
                echo "\n";
            }
        }
    }
    //end class
}
