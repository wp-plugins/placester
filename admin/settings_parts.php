<?php

/**
 * Admin interface: Settings tab
 * Utilities
 */

/**
 * Cuts array with possible values
 *
 * @param array $array
 * @param array $possible_values
 */
function cut_if_fullset(&$array, $possible_values)
{
    foreach ($possible_values as $key => $value)
    {
        if (!in_array($key, $array))
            return;
    }

    $array = array();
}



/**
 * Prints checkbox in html <table> row
 *
 * @param string $label
 * @param string $option_name
 */
function row_checkbox($label, $option_name)
{
    $v = get_option($option_name);
    $checked = (strlen($v) > 0 ? " checked" : "");

    ?>
    <tr valign="top">
      <th><?php echo $label ?></th>
      <td>
        <input type="checkbox" name="<?php echo $option_name ?>" value="1" <?php echo $checked ?>/>
        <label for="<?php echo $option_name ?>"></label>
      </td>
    </tr>
    <?php
}



/**
 * Prints list of checkboxes in html <table> row
 *
 * @param string $label
 * @param array $values
 * @param string $option_name
 */
function row_checkboxes($label, $values, $option_name)
{
    $checked_items = get_option($option_name);
    if (!is_array($checked_items))
        $checked_items = array();

    ?>
    <div style='float: left; padding-right: 20px'>
      <h4><?php echo $label ?></h4>
      <table>
        <?php
        foreach ($values as $item_name => $item_label)
        {
            $name = $option_name . '_' . $item_name;
            $checked = 
                (in_array($item_name, $checked_items) 
                     || count($checked_items) <= 0 
                 ? ' checked' : '');

            ?>
            <tr valign="top">
              <th></th>
              <td>
                <input type="checkbox" name="<?php echo $name ?>" value="1" <?php echo $checked ?>/>
                <label for="<?php echo $name ?>"><?php echo $item_label ?></label>
              </td>
            </tr>
            <?php
        }
      ?>
      </table>
    </div>
    <?php
}



/**
 * Prints image upload box in html <table> row
 *
 * @param string $label
 * @param string $option_name
 * @param string $tip
 * @param string $tip2
 */
function row_image($label, $option_name, $tip = '', $tip2 = '')
{
    $img = '';
    $id = get_option($option_name);
    if (strlen($id) > 0)
    {
        $thumbnail = wp_get_attachment_image_src($id, 'thumbnail');
        $img = '<img src="' . $thumbnail[0] . '" />';
    }

    ?>
    <tr valign="top">
      <th scope="row"><label><?php echo $label ?></label></th>
      <td>
        <input type="file" name="file" id="<?php echo $option_name ?>_file" />
        <input type="hidden" name="<?php echo $option_name ?>" 
          id="<?php echo $option_name ?>" value="<?php echo $id ?>" />
          <div style="clear: both"></div>
          <div style="">
            <?php echo $tip2; ?>            
          </div>
      </td>
      <td>
        <?php echo $tip; ?>
      </td>
    </tr>
    <tr valign="top">
      <th></th>
      <td><div id="<?php echo $option_name ?>_thumbnail"><?php echo $img ?></div></td>
    </tr>
    <?php
}



/**
 * Prints textarea in html <table> row
 *
 * @param string $label
 * @param string $option_name
 */
function row_textarea($label, $option_name)
{
    ?>

      <th scope="row" style="width: 100px"><label for="<?php echo $option_name ?>"><?php echo $label ?></label></th>
      <td style="width: 400px">
            <p align="right">
                <a id="<?php echo $option_name . "_toggleVisual"; ?>" class="button toggleVisual">Visual</a>
                <a id="<?php echo $option_name . "_toggleHTML"; ?>" class="button toggleHTML">HTML</a>
            </p>
        <textarea name="<?php echo $option_name ?>" rows="5" 
          class="heading form-input-tip" 
          style="width:100%"><?php echo get_option($option_name) ?></textarea>
      </td>

    <?php
}



/**
 * Prints textbox in html <table> row
 *
 * @param string $label
 * @param string $option_name
 */
function row_textbox($label, $option_name)
{
    ?>

      <th scope="row"><label for="<?php echo $option_name ?>"><?php echo $label ?></label></th>
      <td>
        <input type="text" name="<?php echo $option_name ?>"
          value="<?php echo get_option($option_name) ?>" 
          id="<?php echo $option_name ?>"
          class="heading form-input-tip" 
          style="width:100%" />
      </td>

    <?php
}
