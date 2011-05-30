<?php

//
// save template
//
$current_template_name = '';

if ( isset( $_REQUEST['save_template_as'] ) ) {
    $template_name = $_REQUEST['save_template_name'];
    $v = stripslashes( $_REQUEST['save_template_content'] );

    $post = array(
         'post_type' => 'placester_template',
         'post_title' => $template_name,
         'post_status' => 'publish',
         'post_author' => 1,
         'post_content' => $v
      );

    $post_id = wp_insert_post( $post );
    if ( $post_id > 0 ) {
        delete_post_meta( $post_id, 'thumbnail_url' );
        add_post_meta( $post_id, 'thumbnail_url', $_REQUEST['save_thumbnail_url'] );
    }

    placester_success_message( 'Template saved for that listing' );
    $current_template_name = 'user_' . $post_id;
}


if ( isset( $_REQUEST['save_template'] ) ) {
    $template_name = $_REQUEST['save_template_name'];
    $v = stripslashes( $_REQUEST['save_template_content'] );

    $post_id = substr( $_REQUEST['current_template_name'], 5 );
    $post = array(
         'ID' => $post_id,
         'post_type' => 'placester_template',
         'post_content' => $v
      );

    wp_update_post( $post );

    placester_success_message( 'Template saved for that listing' );
    $current_template_name = 'user_' . $post_id;
}


if ( isset( $_REQUEST['delete_template'] ) ) {
    $post_id = substr( $_REQUEST['current_template_name'], 5 );
    wp_delete_post( $post_id );

    placester_success_message( 'Template was deleted' );
    $current_template_name = '';
}



//
// load templates list
//

$templates = placester_get_templates();

//
// define active template
//

$base_iframe_url = 'admin.php?page=placester_templates';

$current_name = '';
$current_iframe_src = $base_iframe_url . '&template_iframe=';

if ( strlen( $current_template_name ) > 0 ) {
    $current_name = $current_template_name;
    $current_iframe_src = $base_iframe_url .
        '&template_iframe=' . $current_template_name;
}


?>
<script>
var placester_theme_url = '<?php bloginfo( 'template_directory' ) ?>';
</script>

<div class="wrap">
    <?php placester_admin_header( 'placester_templates' ) ?>

    <form method="post" action="admin.php?page=placester_templates">
        <input type="hidden" id="current_template_name" name="current_template_name" 
            value="<?php echo htmlspecialchars( $current_name ) ?>" />
        <div class="template_menu">
            <h3>Templates</h3>
            <?php
            foreach ( $templates as $i ) {
                ?>
                <div>
                    <img src="<?php echo $i['thumbnail_url'] ?>" class="template_item" 
                        system_name="<?php echo $i['system_name'] ?>" 
                        title="<?php echo $i['name'] ?>" 
                        active="<?php echo $i['active'] ?>" />
                </div>
                <?php
            }
            ?>
        </div>

        <div class="template_preview">
            <h3>Preview</h3>
            <iframe src="<?php echo $current_iframe_src ?>" id="preview_iframe"></iframe>
        </div>

        <p class="submit">
            <input type="hidden" id="save_template_content" 
                name="save_template_content" />
            <input type="hidden" id="save_thumbnail_url" 
                name="save_thumbnail_url" />
            <input type="button" id="edit_template" class="button" 
                value="Edit Template" />

            <div id="save_template_panel" style="display: none">
                <label for="save_template_name">Name:</label>
                <input type="text" name="save_template_name" id="save_template_name" />
                <input type="submit" id="save_template_as" class="button-primary" 
                    name="save_template_as" value="Save Template As" />
            </div>
            <div id="save_template_user_panel" style="display: none">
                <input type="submit" id="save_template" class="button-primary" 
                    name="save_template" value="Save Template" />
                <input type="submit" id="delete_template" class="button-primary" 
                    name="delete_template" value="Delete Template" />
            </div>
        </p>
        <p>
            Available shortcodes:<br />
            <div style="float: left; margin-right: 20px">
                [available_on]<br />
                [bathrooms]<br />
                [bedrooms]<br />
                [contact.email]<br />
                [contact.phone]<br />
                [description]<br />
                [half_baths]<br />
                [id]<br />
            </div>
            <div style="float: left; margin-right: 20px">
                [location.address]<br />
                [location.city]<br />
                [location.coords.latitude]<br />
                [location.coords.longitude]<br />
                [location.state]<br />
                [location.unit]<br />
                [location.zip]<br />
            </div>
            <div style="float: left">
                [price]<br />
                [url]
            </div>
            <div class="clear"></div>
        </p>
    </form>
</div>