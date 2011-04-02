<?php

/**
 * Admin interface: Edit listing form
 * Form for editing property
 */

$images_url = plugins_url('/images', dirname(__FILE__));

?>
<script>

jQuery(document).ready(function()
{
    jQuery('a.lightbox').lightBox(
    {
        imageLoading: '<?php echo $images_url ?>/lightbox-ico-loading.gif',
        imageBtnPrev: '<?php echo $images_url ?>/lightbox-btn-prev.gif',
        imageBtnNext: '<?php echo $images_url ?>/lightbox-btn-next.gif',
        imageBtnClose: '<?php echo $images_url ?>/lightbox-btn-close.gif',
        imageBlank: '<?php echo $images_url ?>/lightbox-blank.gif',
    });
});

</script>

<div class="wrap">
  <?php admin_header('placester_properties') ?>

  <h3>Edit Listing</h3>
  <form method="post" action="admin.php?page=placester_properties&id=<?php echo $property_id ?>">
    <?php
    if (strlen($error_message) > 0)
      placester_error_message($error_message);

    property_form($p, $v);
    ?>

    <div id="images"></div>
    <input type="button" name="images" value="Edit Images" onclick="images_popup()" />

    <p class="submit">
      <input type="submit" name="edit_finish" class="button-primary" 
        value="Modify Listing" />
    </p>
  </form>

  <a id="lightbox_link" class="lightbox"</a>
</div>