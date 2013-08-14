<?php

class PL_Taxonomies {
  
  public function create ( $taxonomies ) {
  	
  	foreach ($taxonomies as $taxonomy) {
  		
  		if ( !taxonomy_exists( $taxonomy['taxonomy_name'] ) ) {
  			return false;
  		}

      // create terms in taxonomy
  		foreach ($taxonomy['terms'] as $term) {
        wp_insert_term( $term['term_name'], $taxonomy['taxonomy_name'], $term['args'] );
  		}
		
  	}

  }

}