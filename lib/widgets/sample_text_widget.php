<?php

class SampleTextWidget extends PL_Widget {
	
	public function __construct() {
		parent::__construct(array(), array());
	}
	
	public function widget( $args, $instance ) {
		// echo "Yataaaaaaaaaaaaaaaaaaa!";
	}
}

register_widget( 'SampleTextWidget' );