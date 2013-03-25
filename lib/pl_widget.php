<?php

class PL_Widget extends WP_Widget {
	
	/**
	 * Array of fields for the widget 
	 * 
	 * A field has the following blocks:
	 * - type (field type)
	 * - ID (some field unique ID)
	 * - label (something to be set on the field box before that)
	 * - value - for all fields
	 * 
	 * Available types right now (see field types)
	 * 
	 * @var array $fields
	 */
	private $fields = array();
	
	private static $field_types = array(
			'text',
			'checkbox',
			'dropdown'
		);
	
	public function __construct( $args = array(), $fields = array() ) {
		$this->fields = $this->validate_fields( $fields );
		$args = wp_parse_args( $args, array( 'base_id' => 'pl_widget', 'name' => 'PL Widget' ) ); 
		
        $this->WP_Widget(
            $args['base_id'],
            $args['name'],
            array( 'classname' => '', 'description' => __( 'Placester Widget', 'pls' ) ),
            array( ) // you can pass width/height as parameters with values here
        );
		
	}
	
	public function form( $instance ) {
		foreach( $instance as $key => $value ) {
			$field = $fields[$key];
		?>	<p>
			<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $field['label']; ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( $key ); ?>" name="<?php echo $key; ?>" type="text" value="<?php echo esc_attr( $field['value'] ); ?>" />
			</p>
		<?php }
	}
	
	public function update( $new_instance, $old_instance ) {
		$count = count( $old_instance );
		foreach( $old_instance as $key => $value ) {
			$new_instance[$key] = esc_attr( $old_instance[$key] );
		}
		
		return $new_instance;
	}
	
	public function widget( $args, $instance ) {
		// outputs the content of the widget
	}

	/**
	 * Validate fields passed as an array 
	 * 
	 * @param array $fields
	 * @return the valid array if field types are correct, or empty array otherwise
	 */
	public function validate_fields( $fields ) {
		if( ! is_array( $fields ) ) { 
			return array();
		}
		
		foreach( $fields as $field ) {
			if( ! ( in_array( $field['value'], self::$field_types ) ) ) {
				return array();
			}
		}
		
		return apply_filters( $this->name . '_fields_validate', $fields );
	}
	
	private function get_field_by_key( $key ) {
		
	}
}