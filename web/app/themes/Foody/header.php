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
$header = new Foody_Header();
$channels = new Foody_Channels_Menu();
$user = new Foody_User();

?>
<!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#ED3D48">
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php Foody_Header::google_tag_manager(); ?>
    <?php wp_head(); ?>

</head>

<body <?php body_class(); ?> dir="rtl">
<?php //do_action('foody_after_body') ?>

<?php

$background_image = foody_get_background_image();

if (!empty($background_image)) {
    ?>
    <img class="body-background" src="<?php echo $background_image['url'] ?>"
         alt="<?php echo $background_image['alt'] ?>">
    <?php
}
?>

<div id="fb-root"></div>
<?php Foody_Header::google_tag_manager_iframe(); ?>
<div id="page" class="site">

    <header id="masthead" class="site-header no-print">
        <div class="run d-block d-lg-none">
            <?php echo __('בהרצה') ?>
        </div>

        <div class="socials d-none d-lg-block">

            <section class="header-top-container  d-none d-lg-flex">
                <?php $header->the_socials_bar() ?>

                <div class="search-bar search-bar-container">
                    <?php get_search_form(); ?>
                </div>

                <span class="run-desktop run">
                    <?php echo __('בהרצה') ?>
                </span>

                <?php if (!wp_is_mobile()): ?>
                    <button type="button" class="btn btn-default navbar-btn  d-none d-lg-block accessibility">
                        <?php $header->accessibility(); ?>
                        <div id="accessibility-container"></div>
                    </button>
                <?php endif; ?>
            </section>

        </div>

        <nav class="navbar navbar-expand-lg navbar-light navbar-toggleable-lg" role="navigation">

            <div class="container-fluid foody-navbar-container">

                <div class="site-branding">
                    <div class="logo-container d-none d-lg-block">
                        <div class="logo-container-desktop">
                            <?php the_custom_logo() ?>
                        </div>
                    </div>

                    <div class="logo-container-mobile d-block d-lg-none">
                        <?php echo get_logo_with_size('60', '60'); ?>
                    </div>

                </div><!-- .site-branding -->


                <!-- Brand and toggle get grouped for better mobile display -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#foody-navbar-collapse"
                        aria-controls="foody-navbar-collapse" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <i class="navbar-toggler-icon icon-menu-mobile"></i>
                </button>


                <button type="button" class="btn btn-default navbar-btn  d-block d-lg-none accessibility">
                    <img src="<?php echo $GLOBALS['images_dir'] . 'icons/accessibility-red.png' ?>"
                         alt="<?php echo __('נגישות') ?>">
                    <div id="accessibility-container"></div>
                </button>

                <?php
                $nav_args = array(
                    'theme_location' => 'primary',
                );

                wp_nav_menu($nav_args);
                ?>

                <?php Foody_Social::whatsapp(['d-block', 'd-lg-none']) ?>

                <button type="button" class="btn btn-default navbar-btn btn-search d-block d-lg-none">

                    <img src="<?php echo $GLOBALS['images_dir'] . 'icons/search-bar.png' ?>" alt="">

                </button>


                <?php if (is_user_logged_in()): ?>

                    <div class="d-none d-lg-block profile-picture-container">

                        <?php
                        $link = is_user_logged_in() ? get_permalink(get_page_by_path('פרופיל-אישי')) : '';
                        ?>
                        <a href="<?php echo $link ?>">
                            <?php echo $user->get_image() ?>
                        </a>
                    </div>

                <?php endif; ?>

            </div>
        </nav>

        <div class="search-overlay d-lg-none">

            <div class="input-container">
                <!--suppress HtmlFormInputWithoutLabel -->
                <input type="search" class="search-autocomplete">
                <span class="close">&times;</span>
            </div>
            <div class="overlay-white">

            </div>

        </div>

        <?php
        if (wp_is_mobile()) {
            echo $channels->get_the_menu();
        }
        ?>


        <!-- #site-navigation -->
    </header><!-- #masthead -->

    <div id="content" class="site-content">
        <?php Foody_Social::whatsapp(['d-none', 'd-lg-block', 'floating']) ?>

