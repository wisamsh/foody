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
$user = new Foody_User();
$show_accessibility = get_theme_mod('foody_show_accessibility') || get_theme_mod('show_white_label_accessibility');
// always show on main site
if (!is_multisite() || is_main_site()) {
    $show_accessibility = true;
}
?>
<!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <?php if (strpos(get_page_template(), 'foody-course-register.php')) { ?>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <?php } ?>
    <meta name="google-site-verification" content="0onpCxafciFIuZuFGZ0w-_blKWe82J34Br2rAq1Tid4"/>
    <meta name="theme-color" content="#ED3D48">
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php Foody_Header::google_tag_manager(); ?>
    <?php if (get_current_blog_id() == 2) { ?>
        <script data-ad-client="ca-pub-3607762765478350" async
                src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <?php } ?>
    <?php wp_head(); ?>

</head>
<?php if (strpos(get_page_template(), 'foody-course-register.php')) { ?>
    <script src="https://public.bankhapoalim.co.il/bitcom/sdk"></script>
<?php } ?>

<body <?php body_class(); ?> dir="rtl">
<?php //do_action('foody_after_body') ?>

<?php

if (!wp_is_mobile() && (isset($_SESSION['background_image']) && !empty($_SESSION['background_image']))) {
    ?>
    <img class="body-background" src="<?php echo $_SESSION['background_image']['url'] ?>"
         alt="<?php echo $_SESSION['background_image']['alt'] ?>">
    <?php
    unset($_SESSION['background_image']);
}
?>

<div id="fb-root"></div>
<?php Foody_Header::google_tag_manager_iframe(); ?>

<?php if (!empty($edit_link = get_edit_post_link())): ?>
    <div dir="rtl" style="text-align: right; max-width: 960px;margin: 0 auto;position: relative;">
        <a href="<?php echo $edit_link ?>">
            <?php echo __('ערוך') ?>
        </a>
    </div>
<?php endif; ?>
<div id="page" class="site">

    <header id="masthead" class="site-header no-print">
        <?php if (is_multisite() && !is_main_site()): ?>
            <?php $header->the_foody_collaboration(false); ?>
        <?php endif; ?>

        <div class="socials d-none d-lg-block">

            <section class="header-top-container  d-none d-lg-flex">
                <?php $header->the_socials_bar() ?>

                <div class="search-bar search-bar-container">
                    <?php get_search_form(); ?>
                </div>
                <?php if (is_multisite() && !is_main_site()): ?>
                    <?php $header->the_foody_collaboration(true); ?>
                <?php endif; ?>
                <?php


                if (!wp_is_mobile() && $show_accessibility):

                    ?>
                    <button type="button" class="btn btn-default navbar-btn  d-none d-lg-block accessibility"
                            data-acsb="trigger" aria-label="פתיחת תפריט נגישות">
                        <?php $header->accessibility(); ?>
                        <div id="accessibility-container"></div>
                    </button>
                <?php endif; ?>
            </section>

        </div>

        <nav class="navbar navbar-expand-lg navbar-light navbar-toggleable-lg <?php $header->the_logo_nav_mode() ?>"
             role="navigation">

            <div class="container-fluid foody-navbar-container">

                <div class="site-branding">
                    <div class="logo-container d-none d-lg-block <?php $header->the_logo_mode() ?> ">
                        <div class="logo-container-desktop">
                            <?php the_custom_logo(); ?>
                        </div>
                    </div>

                    <div class="logo-container-mobile <?php $header->the_logo_mode() ?> d-block d-lg-none">
                        <?php
                        if (is_main_site()) {
                            echo get_logo_with_size('60', '60');
                        } else {
                            the_custom_logo();
                        }
                        ?>
                    </div>

                </div><!-- .site-branding -->


                <!-- Brand and toggle get grouped for better mobile display -->
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#foody-navbar-collapse"
                        aria-controls="foody-navbar-collapse" aria-expanded="false"
                        aria-label="Toggle navigation">
                    <i class="navbar-toggler-icon icon-menu-mobile"></i>
                </button>

                <?php if ($show_accessibility): ?>
                    <button type="button" class="btn btn-default navbar-btn d-block d-lg-none accessibility"
                            data-acsb="trigger" aria-label="פתיחת תפריט נגישות">
                        <?php $header->accessibility(); ?>
                        <div id="accessibility-container"></div>
                    </button>
                <?php endif; ?>

                <?php
                $nav_args = array(
                    'theme_location' => 'primary',
                );

                wp_nav_menu($nav_args);
                ?>

                <?php Foody_Header::whatsapp(['d-block', 'd-lg-none']) ?>

                <button type="button" class="btn btn-default navbar-btn btn-search d-block d-lg-none"
                        aria-label="חיפוש">

                    <img src="<?php echo $GLOBALS['images_dir'] . 'icons/search-bar.png' ?>" alt="search-bar">

                </button>


                <?php if (is_user_logged_in() && foody_is_registration_open()): ?>
                <?php if (wp_is_mobile()) { ?>
                <div class="d-none profile-picture-container">
                    <?php } else { ?>
                    <div class="d-flex profile-picture-container">
                        <?php } ?>
                        <?php
                        $link = is_user_logged_in() ? get_permalink(get_page_by_path('פרופיל-אישי')) : '';
                        ?>
                        <a href="<?php echo $link ?>">
                            <?php echo $user->get_image() ?>
                        </a>
                        <div class="user-name-header title">
                            <?php echo __('שלום') . " " . $user->user->first_name; ?>
                        </div>
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


        <!-- #site-navigation -->
    </header><!-- #masthead -->

    <?php
    if(get_page_template_slug() === 'page-templates/homepage.php') {
        $brands_avenue_group = get_field('brands_avenue', 'foody_brands_avenue');
        if (isset($brands_avenue_group['brands']) && !empty($brands_avenue_group['brands'])) { ?>
            <div class="brands-container">
                <?php
                foody_get_template_part(
                    get_template_directory() . '/template-parts/common/foody-brands.php',
                    array(
                        'brands' => $brands_avenue_group['brands'],
                        'title' => $brands_avenue_group['title']
                    )
                );
                ?>
            </div>
            <?php
        }
    }
    ?>

    <div id="content" class="site-content">
        <?php
        if(get_page_template_slug() === 'page-templates/homepage.php') {
            if (isset($brands_avenue_group['brands']) && !empty($brands_avenue_group['brands'])) {
                foody_get_template_part(get_template_directory() . '/template-parts/common/foody-brands-mobile.php', [
                    'brands' => $brands_avenue_group['brands'],
                    'title' => $brands_avenue_group['title']
                ]);
            }
        }

        if (wp_is_mobile()) {
            Foody_Header::whatsapp(['d-lg-block', 'floating', 'whatsapp-mobile']);
        } else {
            Foody_Header::whatsapp(['d-none', 'd-lg-block', 'floating']);
        }
        ?>

