<?php

class PLS_Widget_Mortgage_Calculator extends WP_Widget {

  function PLS_Widget_Mortgage_Calculator() {
    $widget_ops = array( 'classname' => 'example', 'description' => '' );

    /* Widget control settings. */
    $control_ops = array( 'width' => 200, 'height' => 290 );

    /* Create the widget. */
    $this->WP_Widget( 'PLS_Widget_Mortgage_Calculator', 'Placester: Mortgage Calculator', $widget_ops, $control_ops );
  }

  function widget( $args, $instance ) {
    // Widget output
    extract($args);
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
    $home_price = empty($instance['home_price']) ? ' ' : apply_filters('home_price', $instance['home_price']);
    ?>

    <script type="text/javascript">
      jQuery( document ).ready( function($) {
      
        $("#mCalc").live("click", function(){ 
          var data = {};
          var M,P,n,r,dp;
          
          $.each($(this).closest('form').serializeArray(), function(i, field) {
               data[field.name] = field.value;
          });
          
          M = parseInt(data.price) ? parseInt(data.price) : function () { alert('You must enter a number for the price (rather then a word)'); return false; }()
          n = parseInt( data.term ) * 12;
          r = parseFloat( data.rate ) / 1200;
          dp = 1 - parseFloat( data.down ) / 100;
          M = M * dp;
          
          P = ( M*( r*Math.pow(1+r,n) ) ) / ( Math.pow( 1+r,n )-1 ); 
          
          if(!isNaN(P)) {
            $("#pls_Payment input, #pls_payment_price span").val(P.toFixed(2));
          } else {
            alert('There was an error, please check your values.');
          }
          return false;
        });
        
      });
    </script>

    <section id="pls_mortgage_calc" class="pls_mortgage_calc_wrapper widget">
  
      <div class="widget-inner pls_mortgage_calc_widget_inner">
  
        <h3 class="widget-title"><?php echo $title; ?></h3>
    
        <form method="post" action="" id="pls_mortgage_calc_form">

          <div class="calc_input_wrapper">
            <label>Price of Home ($)</label>
            <input type="text" name="price" id="pls_Price" class="mortgageField" tabindex=15 value="<?php echo $home_price; ?>" />
          </div>
          <div class="calc_input_wrapper">
            <label>Down Payment (%)</label>
            <input type="text" name="down" id="pls_Down" class="mortgageField" tabindex=16 value="20" />
          </div>
          <div class="calc_input_wrapper">
            <label>Interest Rate (%)</label>
            <input type="text" name="rate" id="pls_Rate" class="mortgageField" tabindex=17 value="5" />
          </div>
          <div class="calc_input_wrapper">
            <label>Loan Term (years)</label>
            <input type="text" name="term" id="pls_Term" class="mortgageField" tabindex=18 value="30" />
          </div>

          <input type="submit" id="mCalc" onclick="return false" tabindex=19 class="button-primary" value="Calculate">
      
          <div id="pls_Payment">
            <span>$</span><input type="text" name="payment" id="calc-submit-total" />
          </div>
    
        </form>

      </div>

    </section>

    <?php
  }

  function update( $new_instance, $old_instance ) {
    // Save widget options
    $instance = $old_instance;
    $instance['title'] = strip_tags($new_instance['title']);
    $instance['home_price'] = strip_tags($new_instance['home_price']);
    
    return $instance;
  }

  function form( $instance ) {
    // Output admin widget options form
    $instance = wp_parse_args( (array) $instance, array( 'title' => 'Mortgage Calculator', 'home_price' => '250000' ) );
    $title = strip_tags($instance['title']);
    $home_price = strip_tags($instance['home_price']);
    ?>
      <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php echo 'Title' ?>: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></label></p>
      <p><label for="<?php echo $this->get_field_id('home_price'); ?>"><?php echo "Calculator's starting home price" ?>: <input class="widefat" id="<?php echo $this->get_field_id('home_price'); ?>" name="<?php echo $this->get_field_name('home_price'); ?>" type="text" value="<?php echo esc_attr($home_price); ?>" /></label></p>
    <?php
  }
}