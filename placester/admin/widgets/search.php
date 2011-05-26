<?php

/**
 * Create Search Widget
 */
class Placester_Search_Widget extends WP_Widget {
    
    function Placester_Search_Widget() {
        $widget_ops = array('classname' => 'placester_search_widget', 'description' => __( 'Search Placester Listings') );
        parent::WP_Widget(false, 'Placester Search Widget', $widget_ops);
    }

    function widget($args, $instance) {
        extract($args);
        global $placester_taxonomies;
        $title = apply_filters('widget_title', empty($instance['title']) ? '&nbsp;' : $instance['title']);
        $selected = "selected='selected'";

        echo $before_widget;

        if ( $title ) echo $before_title . $title . $after_title;

        echo '<form action="' . get_bloginfo("url") . '" method="get" id="searchform" class="search_form">';
        echo '<div><label class="search_label" for="s">Keywords</label>';
        echo '<input name="s" type="text" id="s" class="input_search" value="' . $_GET["s"] . '" /></div>';
        foreach ($placester_taxonomies as $tax) {
            
            if ( $tax['tax'] == 'bedrooms' || $tax['tax'] == 'baths' || $tax['tax'] == 'rent' ) {
                echo '<div>' . $tax["name"];
                echo '<select name="' . $tax["tax"] . '">';
                sort($tax['terms'], SORT_NUMERIC);
                foreach ( $tax["terms"] as $term ) {
                    $selected = ($_GET[$tax['tax']] == $term) ? "selected='selected'" : '';
                    $term = ( $term == '2500+' ) ? '2500' : $term;
                    $term_name = ($tax["tax"] == 'bedrooms' || $tax["tax"] == 'baths' || $term == '2500') ? $term . '&#43;' : $term;
                    $term_name = ($term == 'all') ? ucfirst($term) : $term_name;
                    
                    echo '<option value="' . $term . '" ' . $selected . ' >'  . $term_name . '</option>';
                    
                }
                echo '</select>';
            } elseif ( $tax['tax'] == 'city' || $tax['tax'] == 'state' || $tax['tax'] == 'zip' ) {
                echo '<div>' . $tax["name"];
                echo '<select name="' . $tax['tax'] . '" >';
                placester_search_locations( $tax['tax'] );
                echo '</select>';
            } elseif ( $tax['tax'] == 'type' ) {
                echo '<div>Filter results for: ';
                foreach ($tax['terms'] as $term) {
                    $checked = ($_GET['type'] == $term) ? "checked='checked'" : '';
                    echo '<div>' . ucfirst($term) . '<input type="checkbox" name="type" value="' . $term . '" ' . $checked . ' ></div>'; 
                    
                }

            }
                echo '</div>';
        }
          
        echo '<input type="submit" value="Search" /></form>' . $after_widget;
    }

    function update($new_instance, $old_instance){
        $instance = $old_instance;
        $instance['title'] = strip_tags(stripslashes($new_instance['title']));

        return $instance;
    }

    function form($instance){
        //Defaults
        $instance = wp_parse_args( (array) $instance, array('title'=>'') );

        $title = htmlspecialchars($instance['title']);

        // Output the options
        echo '<p><label for="' . $this->get_field_name('title') . '">' . __('Title:') . '</label><input class="widefat" type="text" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" value="' . $title . '" /></p>';
    }

}
