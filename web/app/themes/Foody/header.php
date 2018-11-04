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
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#ED3D48">
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php wp_head(); ?>

</head>

<body <?php body_class(); ?> dir="rtl">
<div id="fb-root"></div>
<div id="page" class="site">

    <header id="masthead" class="site-header">
        <div class="socials d-none d-lg-block">
            <?php $header->the_socials_bar() ?>
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

                <div class="search-bar search-bar-container d-none d-lg-block">
                    <?php get_search_form(); ?>
                </div>

                <!-- Brand and toggle get grouped for better mobile display -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#foody-navbar-collapse"
                        aria-controls="foody-navbar-collapse" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <i class="navbar-toggler-icon icon-menu-mobile"></i>
                </button>

                <button type="button" class="btn btn-default navbar-btn  d-block d-lg-none accessibility">
                    <img src="<?php echo $GLOBALS['images_dir'] . 'icons/accessibility-red.png' ?>" alt="">
                </button>

                <button type="button" class="btn btn-default navbar-btn btn-search d-block d-lg-none">

                    <img src="<?php echo $GLOBALS['images_dir'] . 'icons/search-bar.png' ?>" alt="">

                </button>

                <!--                <div class="channels-nav">-->
                <!--                    <button type="button" class="btn btn-secondary" data-container="body" data-toggle="popover"-->
                <!--                            data-placement="bottom" data-html="true" data-content='-->
                <?php //$channels->the_menu() ?><!--'>-->
                <!--		                --><?php //echo foody_get_menu_title( 'channels-menu' ) ?>
                <!--                    </button>-->
                <!--                </div>-->

                <?php

                function my_nav_wrap($channels)
                {
                    $wrap = '<ul id="%1$s" class="%2$s">';
                    $wrap .= '<li class="channels-nav">';
                    $wrap .= '<button type="button" class="btn nav-link btn-channels-menu" data-container=".btn-channels-menu" data-toggle="popover"
                             data-placement="bottom" data-html="true" data-trigger="hover" data-content=\'';
                    $wrap .= $channels->get_the_menu() . '\'>';
                    $wrap .= foody_get_menu_title("channels-menu");
                    $wrap .= '<i class="icon-arrowleft"></i></button>';
                    $wrap .= '</li>';
                    $wrap .= '%3$s';
                    $wrap .= '</ul>';

                    return urldecode($wrap);
                }

                function mobile_nav_wrap($channels)
                {
                    $wrap = '<ul id="%1$s" class="%2$s">';
                    $wrap .= '%3$s';
                    $wrap .= '<li class="channels-nav">';
//					$wrap .= '<button type="button" class="btn btn-secondary nav-link" data-toggle="modal" data-target="#modal">';
//					$wrap .= foody_get_menu_title( "channels-menu" );
//					$wrap .= '</button>';
//					$wrap .= '<button type="button" class="btn btn-secondary nav-link btn-channels-menu" data-container="body" data-toggle="popover"
//                             data-placement="bottom" data-html="true" data-content=\'';
//					$wrap .= $channels->get_the_menu() . '\'>';
//					$wrap .= foody_get_menu_title( "channels-menu" );
                    $wrap .= '<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#channels-menu" 
                             aria-controls="channels-menu" aria-expanded="false" aria-label="Toggle navigation">';
                    $wrap .= foody_get_menu_title("channels-menu");
                    $wrap .= '</button>';
                    $wrap .= '</li>';
                    $wrap .= '</ul>';

                    return urldecode($wrap);
                }

                $nav_args = array(
                    'theme_location' => 'primary',
                    'depth' => 2,
                    'container' => 'div',
                    'container_class' => 'collapse navbar-collapse',
                    'container_id' => 'foody-navbar-collapse',
                    'menu_class' => 'nav navbar-nav',
                    'fallback_cb' => 'WP_Bootstrap_Navwalker::fallback',
                    'before_menu' => '<div class="close-menu d-sm-none">תפריט</div>',
                    'walker' => new WP_Bootstrap_Navwalker(),
                );
                $items_wrap = my_nav_wrap($channels);
                if (wp_is_mobile()) {
//					echo $channels->get_the_menu();
                    //$nav_args['items_wrap'] = mobile_nav_wrap( $channels );
                    $items_wrap = mobile_nav_wrap($channels);
                }

                //				$nav_args['items_wrap'] = $items_wrap;
                wp_nav_menu($nav_args);

                ?>



                <?php if (is_user_logged_in()): ?>

                    <div class="d-none d-lg-block profile-picture-container">

                        <?php
                        $link = is_user_logged_in() ? get_permalink(get_page_by_path('פרופיל-אישי')) : '';
                        ?>
                        <a href="<?php echo $link ?>">
                            <!--                            <img class="avatar" src="-->
                            <?php //echo $user->get_image() ?><!--" alt="">-->
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


    <?php
    if (!is_user_logged_in()) {
        $login_popup_args = [
            'id' => 'login-modal',
            'body' => do_shortcode('[foody-login]'),
            'btn_approve_classes' => 'hide',
            'btn_cancel_classes' => 'hide',
            'title' => '',
            'hide_buttons' => true
        ];

        foody_get_template_part(get_template_directory() . '/template-parts/common/modal.php', $login_popup_args);
    }

    ?>
    <div id="content" class="site-content">

