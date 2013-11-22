<?php
/**
 * Header Template
 *
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js ie6 oldie" xmlns:fb="http://ogp.me/ns/fb#" <?php language_attributes(); ?> <?php echo PLS_Micro_Data::itemtype('html'); ?>><![endif]-->
<!--[if IE 7]><html class="no-js ie7 oldie" xmlns:fb="http://ogp.me/ns/fb#" <?php language_attributes(); ?> <?php echo PLS_Micro_Data::itemtype('html'); ?>><![endif]-->
<!--[if IE 8]><html class="no-js ie8 oldie" xmlns:fb="http://ogp.me/ns/fb#" <?php language_attributes(); ?> <?php echo PLS_Micro_Data::itemtype('html'); ?>><![endif]-->
<!--[if gt IE 8]><!--><html class="no-js" xmlns:fb="http://ogp.me/ns/fb#" <?php language_attributes(); ?> <?php echo PLS_Micro_Data::itemtype('html'); ?>><!--<![endif]-->
<head>

  <meta charset="<?php bloginfo( 'charset' ); ?>">

  <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
  Remove this if you use the .htaccess -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?php wp_title(''); ?></title>

  <?php if ( pls_get_option('pls-site-favicon') ) { ?>
    <link href="<?php echo pls_get_option('pls-site-favicon'); ?>" rel="shortcut icon" type="image/x-icon" />
  <?php } ?>

  <?php if ( (pls_get_option('pls-css-options')) && (pls_get_option('pls-custom-css')) ) { ?>
    <style id="pls-custom-css" type="text/css"><?php echo pls_get_option('pls-custom-css'); ?></style>
  <?php } ?>

  <!--[if lt IE 9]>
  <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
  <![endif]-->

  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
  <link rel="stylesheet" href="<?php echo get_stylesheet_uri(); ?>" type="text/css" media="all" />
  <?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

	<?php pls_do_atomic( 'open_body' ); ?>
    <div class="container_12 clearfix">

    	<?php pls_do_atomic( 'before_header' ); ?>
        <header id="branding" role="banner" class="grid_12" <?php echo PLS_Micro_Data::itemtype('organization'); ?>>

            <?php pls_do_atomic( 'open_header' ); ?>
            <div class="wrapper">
                <hgroup>

									<?php if (pls_get_option('pls-site-logo')): ?>
										<div id="logo">
                      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo pls_get_option('pls-site-title'); ?>" rel="home" <?php echo PLS_Micro_Data::itemprop('organization', 'url'); ?>>
											<img src="<?php echo pls_get_option('pls-site-logo') ?>" alt="<?php bloginfo( 'name' ); ?>" <?php echo PLS_Micro_Data::itemprop('organization', 'image'); ?> class="option-pls-site-logo">
											</a>
										</div>
									<?php endif; ?>

									<?php if (pls_get_option('pls-site-title')): ?>
										<h1 id="site-title" <?php echo PLS_Micro_Data::itemprop('organization', 'name'); ?>>
                      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo pls_get_option('pls-site-title'); ?>" rel="home" <?php echo PLS_Micro_Data::itemprop('organization', 'url'); ?> class="option-pls-site-title"><?php echo pls_get_option('pls-site-title'); ?></a>
                    </h1>

										<?php if (pls_get_option('pls-site-subtitle')): ?>
											<h2 id="site-description" <?php echo PLS_Micro_Data::itemprop('organization', 'description'); ?> class="option-pls-site-subtitle"><?php echo pls_get_option('pls-site-subtitle'); ?></h2>
										<?php endif ?>
									<?php endif; ?>

									<?php if (!pls_get_option('pls-site-logo') && !pls_get_option('pls-site-title')): ?>
										<h1 id="site-title" <?php echo PLS_Micro_Data::itemprop('organization', 'name'); ?>>
                      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home" <?php echo PLS_Micro_Data::itemprop('organization', 'url'); ?> class="option-pls-site-title"><?php bloginfo( 'name' ); ?></a>
                    </h1>
										<h2 id="site-description" <?php echo PLS_Micro_Data::itemprop('organization', 'description'); ?> class="option-pls-site-subtitle"><?php bloginfo( 'description' ); ?></h2>
									<?php endif; ?>

                </hgroup>

                <?php pls_do_atomic( 'header' ); ?>

                <div class="header-membership"><?php echo PLS_Plugin_API::placester_lead_control_panel(array('separator' => '|')); ?></div>

            </div>

            <?php pls_do_atomic( 'before_nav'); ?>

            <?php PLS_Route::get_template_part( 'menu', 'primary' ); // Loads the menu-primary.php template. ?>

            <?php pls_do_atomic( 'close_header' ); ?>
        </header>
    <?php pls_do_atomic( 'after_header' ); ?>
