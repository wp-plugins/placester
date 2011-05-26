<?php

$name = $_REQUEST['template_iframe'];
$name = preg_replace('/[^0-9A-Za-z_]/', '', $name);

if ( strlen( $name ) <= 0)
{
    ?>
    <div style="text-align: center; margin-top: 100px">
        Select a template from the list of templates at the right to 
        view the preview.
    </div>
    <?php
    return;
}

// check permission
if ( ! placester_is_template_active( $name ) ) {
    ?>
    <div style="text-align: center; margin-top: 100px">
        This template is not active.
    </div>
    <?php
    return;
}


// load template
list( $content, $thumbnail_url ) = placester_get_template_content( $name );

if ( ! isset( $_REQUEST['mode'] ) ) {
    echo $content;
}
else {
    ?>
    <input type="hidden" id="thumbnail_url" value="<?php echo htmlspecialchars($thumbnail_url); ?>" />
    <textarea id="textarea_content" 
        style="width: 100%; height: 380px"><?php echo htmlspecialchars($content); ?></textarea>
    <?php
}
