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
  <?php placester_admin_header('placester_properties') ?>

  <h3>Edit Listing</h3>
      <?php if ($provider = placester_provider_check()): ?>
          <div style="margin: 50px 50px 0 50px; padding: 10px; border: 2px solid #E6DB55; background: lightYellow; margin-bottom: 10px">
              <p style="margin-bottom: 0px"><?php echo 'Your account is being synced with ' . $provider["name"] . ' and so you can\'t create new listings inside this website. However, any property you create in <a href="'.$provider["url"].'">'.$provider["name"].'</a> will appear here automatically.'; ?></p>
          </div>
      <?php else: ?>
      <form method="post" action="admin.php?page=placester_properties&id=<?php echo $property_id ?>">
        <?php
        if (strlen($error_message) > 0)
          placester_error_message($error_message);

        placester_postbox_container_header();
        property_form($p, $v);
        placester_postbox_container_footer();
        ?>

        <div id="images"></div>
        <input type="button" name="images" class="button" value="Edit Images" onclick="images_popup()" />

        <p class="submit">
          <input type="submit" name="edit_finish" class="button-primary" 
            value="Modify Listing" />
        </p>
      </form>
    <?php endif ?>
    
  <a id="lightbox_link" class="lightbox"</a>
</div>