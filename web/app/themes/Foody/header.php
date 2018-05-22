<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Foody
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php wp_head(); ?>
    <script>
        imagesUri = '<?php echo $GLOBALS['images_dir'] ?>';
    </script>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">

    <header id="masthead" class="site-header">
        <div class="socials d-none d-sm-block">

        </div>
        <div class="site-branding">
            <div class="logo-container d-none d-sm-block">
                <div class="logo-container-desktop">
                    <?php the_custom_logo() ?>
                    <div class="d-none d-sm-inline-block cellcom-tv">
                        <img class="cellcom-tv-img" src="<?php echo $GLOBALS['images_dir'] . 'foody-in-cellcom.png' ?>"
                             alt="Cellcom TV">
                    </div>
                </div>


            </div>

            <div class="logo-container-mobile d-block d-sm-none">
                <?php echo get_logo_with_size('60', '60'); ?>
            </div>

        </div><!-- .site-branding -->

        <nav class="navbar navbar-expand-md navbar-light navbar-toggleable-md" role="navigation">



            <div class="container-fluid foody-navbar-container">

                <div class="search-bar d-none d-sm-block">
                    <input type="text" class="search">
                </div>

                <!-- Brand and toggle get grouped for better mobile display -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#foody-navbar-collapse"
                        aria-controls="foody-navbar-collapse" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <button type="button" class="btn btn-default navbar-btn  d-block d-sm-none">

                    <img src="<?php echo $GLOBALS['images_dir'] . 'icons/accessibility-red.png' ?>" alt="">

                </button>
                <button type="button" class="btn btn-default navbar-btn d-block d-sm-none">

                </button>
                <button type="button" class="btn btn-default navbar-btn d-block d-sm-none">

                </button>
                <button type="button" class="btn btn-default navbar-btn d-block d-sm-none">

                </button>
                <button type="button" class="btn btn-default navbar-btn d-block d-sm-none">

                </button>
                <button type="button" class="btn btn-default navbar-btn d-block d-sm-none">

                    <img src="<?php echo $GLOBALS['images_dir'] . 'icons/search-bar.png' ?>" alt="">

                </button>

                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'depth' => 2,
                    'container' => 'div',
                    'container_class' => 'collapse navbar-collapse',
                    'container_id' => 'foody-navbar-collapse',
                    'menu_class' => 'nav navbar-nav',
                    'fallback_cb' => 'WP_Bootstrap_Navwalker::fallback',
                    'walker' => new WP_Bootstrap_Navwalker(),
                ));
                ?>
                <div class="d-none d-sm-block">
                    <img class="avatar" src="<?php echo $GLOBALS['images_dir'] . 'avatar.png' ?>" alt="">
                </div>

            </div>


        </nav>


        <!-- #site-navigation -->
    </header><!-- #masthead -->

    <div id="content" class="site-content">
