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
$user_param = is_user_logged_in() ? $user->user->ID : 'false';
$show_accessibility = get_theme_mod('foody_show_accessibility') || get_theme_mod('show_white_label_accessibility');
$google_site_verification_id = get_option( 'foody_google_site_verification_id', false );
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
    <?php if($google_site_verification_id){ ?>
        <meta name="google-site-verification" content="<?php echo $google_site_verification_id; ?>"/>
    <?php } ?>
    <meta name="theme-color" content="#E6392B">
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php Foody_Header::google_tag_manager(); ?>
    <?php if (get_current_blog_id() == 2) { ?>
        <script data-ad-client="ca-pub-3607762765478350" async
                src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <?php } ?>
    <script>
        var walkMeUser = <?php echo $user_param; ?>;
        if (!walkMeUser){
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            }

            var walkMeUnknownUser = getCookie ('_ga')
        }
        
    </script>
    <?php
    // show walkMe feature
    if(get_current_blog_id() == 1 && get_option( 'foody_show_walkme', false )){
        $walkme_script = get_option( 'foody_google_walkme_script', '' );
        if(!empty($walkme_script)){
           echo $walkme_script;
        }
    }
    ?>
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
    <img class="body-background no-print" src="<?php echo $_SESSION['background_image']['url'] ?>"
         alt="<?php echo $_SESSION['background_image']['alt'] ?>">
    <?php
    unset($_SESSION['background_image']);
}
?>

<div id="fb-root"></div>
<?php Foody_Header::google_tag_manager_iframe(); ?>

<?php if (!empty($edit_link = get_edit_post_link()) && !wp_is_mobile()): ?>
    <div dir="rtl" style="text-align: right; max-width: 960px;margin: 0 auto;position: relative;">
        <a href="<?php echo $edit_link ?>">
            <?php echo __('????????') ?>
        </a>
    </div>
<?php endif; ?>
<div id="page" class="site">
    <?php $post_type = is_single() && isset($post) && isset($post->post_type) ? $post->post_type : ''; ?>
    <header id="masthead" class="site-header no-print <?php if ($post_type == 'foody_recipe' && wp_is_mobile()) {
        echo 'hidden-recipe-header';
    } ?>">
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
                            data-acsb="trigger" aria-label="?????????? ?????????? ????????????">
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
                            data-acsb="trigger" aria-label="?????????? ?????????? ????????????">
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
                        aria-label="??????????">

                    <img src="<?php echo $GLOBALS['images_dir'] . 'icons/search-bar.png' ?>" alt="search-bar">

                </button>


                <?php if (is_user_logged_in() && foody_is_registration_open()): ?>
                <?php if (wp_is_mobile()) { ?>
                <div class="d-none profile-picture-container">
                    <?php } else { ?>
                    <div class="d-flex profile-picture-container">
                        <?php } ?>
                        <?php
                        $link = is_user_logged_in() ? get_permalink(get_page_by_path('????????????-????????')) : '';
                        ?>
                        <a href="<?php echo $link ?>">
                            <?php echo $user->get_image() ?>
                        </a>
                        <div class="user-name-header title">
                            <?php echo __('????????') . " " . $user->user->first_name; ?>
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
    <?php if (is_single() && $post_type == 'foody_recipe' && wp_is_mobile()) { ?>
        <div class="search-overlay floating-mobile-header d-lg-none">

            <div class="input-container">
                <input type="search" class="search-autocomplete">
                <span class="close">&times;</span>
            </div>
            <div class="overlay-white">

            </div>

        </div>
        <div class="related-content-overlay floating-mobile-header">
            <div class="black-overlay">
            </div>
            <?php
            if(is_single()) {
                /** @var Foody_Recipe $recipe */
                $recipe = Foody_PageContentFactory::get_instance()->get_page();
                $id = method_exists($recipe, 'get_id') ? $recipe->get_id() : false;
                $similar_content = get_field('similar_content_group', $id);

                //if (!empty($similar_content) && !empty($similar_content['active_similar_content']) && $similar_content['active_similar_content'][0] == __('??????')) { ?>
                    <div class="related-recipes-container">
                        <div class="close-btn">&#10005;</div>
                        <?php $recipe->get_similar_content($similar_content); ?>
                    </div>
                <?php //}
            } ?>
        </div>
        <div class="sticky_bottom_header no-print">
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
                                data-acsb="trigger" aria-label="?????????? ?????????? ????????????">
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
                        <div class="logo-container-mobile <?php $header->the_logo_mode() ?> d-block d-lg-none">
                            <?php

                            if ( get_current_blog_id() === 5 ) {
                                $logo = get_theme_mod( 'custom_logo' );
                                $image = wp_get_attachment_image_src( $logo , 'full' );
                                $image_url = $image[0];
                                ?>
                                <button class="navbar-toggler custom-logo-link" type="button" data-toggle="collapse"
                                        data-target="#foody-navbar-collapse"
                                        aria-controls="foody-navbar-collapse" aria-expanded="false"
                                        aria-label="Toggle navigation">
                                    <!--                                <img class="foody-logo-text" src="-->
                                    <?php //echo $GLOBALS['images_dir'];?><!--/foody_logo-with-white.svg">-->
                                    <div class="foody-logo-text-custom-amit" style="background-size: 124px 57px;background-image: url( <?php echo  $image_url  ?>)"></div>
                                    <div class="foody-logo-hamburger hidden"></div>
                                    <div class="foody-logo-close hidden"></div>
                                </button>
                            <?php }

                            if (is_main_site()) { ?>
                                <button class="navbar-toggler custom-logo-link" type="button" data-toggle="collapse"
                                        data-target="#foody-navbar-collapse"
                                        aria-controls="foody-navbar-collapse" aria-expanded="false"
                                        aria-label="Toggle navigation">
                                    <!--                                <img class="foody-logo-text" src="-->
                                    <?php //echo $GLOBALS['images_dir'];?><!--/foody_logo-with-white.svg">-->
                                    <div class="foody-logo-text"></div>
                                    <div class="foody-logo-hamburger hidden"></div>
                                    <div class="foody-logo-close hidden"></div>
                                </button>
                            <?php }

                            if (get_current_blog_id() === 2) {
                                $logo = get_theme_mod( 'custom_logo' );
                                $image = wp_get_attachment_image_src( $logo , 'full' );
                                $image_url = $image[0];
                                ?>

                                <button class="navbar-toggler custom-logo-link" type="button" data-toggle="collapse"
                                        data-target="#foody-navbar-collapse"
                                        aria-controls="foody-navbar-collapse" aria-expanded="false"
                                        aria-label="Toggle navigation">
                                    <!--                                <img class="foody-logo-text" src="-->
                                    <?php //echo $GLOBALS['images_dir'];?><!--/foody_logo-with-white.svg">-->
                                    <div class="foody-logo-text-custom" style="background-image: url( <?php echo  $image_url  ?>)"></div>
                                    <div class="foody-logo-hamburger hidden"></div>
                                    <div class="foody-logo-close hidden"></div>
                                </button>
                            <?php   } ?>
                        </div>


                    </div>

                    <?php if ($show_accessibility): ?>
                        <button type="button" class="btn btn-default navbar-btn d-block d-lg-none accessibility"
                                data-acsb="trigger" aria-label="?????????? ?????????? ????????????">
                            <?php $header->accessibility(); ?>
                            <div id="accessibility-container"></div>
                        </button>
                    <?php endif; ?>

                    <?php
                        $similar_content = get_field('similar_content_group', $recipe->get_id());
                        $has_similar_content = !empty($similar_content) && !empty($similar_content['active_similar_content']) && $similar_content['active_similar_content'][0] == __('??????');
                        $class_has_similar_content = $has_similar_content ? '' : 'empty-related-content';
                    ?>
                    <div class="related-content-btn-container">
                        <span class="related-content-btn <?php echo $class_has_similar_content; ?>">?????????????? ????????????</span>
                    </div>

                    <div class="social-btn-container">
                        <i class="icon-share"></i>
                        <div class="social-buttons-container hidden">
                            <?php foody_get_template_part(
                                get_template_directory() . '/template-parts/content-social-actions-mobile-menu.php'
                            );
                            ?>
                        </div>
                    </div>
                    <div class="navbar-container hidden">
                        <div class="navbar-overlay hidden">
                        </div>
                        <?php
                            $navbar_purchase_class = '';
                            $num_of_purchase_buttons = $recipe->has_purchase_buttons();
                            if($num_of_purchase_buttons > 0){
                                $navbar_purchase_class = $num_of_purchase_buttons < 2 ? 'one-purchase-button' : 'two-purchase-button';
                            }
                        ?>
                        <div class="navbar-header <?php echo $navbar_purchase_class; ?> hidden ">
                            <!--                        <img src="-->
                            <?php //echo $GLOBALS['images_dir'] . 'top-mobile-menu.png' ?><!--" class="top-mobile-menu">-->
                            <div class="signup-purchase-container">
                                <?php if ( is_main_site() ) { ?>
                                    <a class="homepage-link" href="<?php echo get_home_url(); ?>">
                                        <div class="up-arrows">??</div>
                                        <?php echo __('?????????? ???????? ???? ')?>
                                        <span class="foody-name">FOODY</span>
                                    </a>
                                    <?php if (!is_user_logged_in()) { ?>
                                        <a class="signup-login-link"
                                           href="<?php echo get_permalink(get_page_by_path('??????????????')); ?>"><span class="singup-text">?????????? ??-</span><span class="foody-name">FOODY</span>
                                            <div class="up-arrows">??</div></a>
                                    <?php } else {

                                        echo "<div class='hello-user' >" . __('????????') . " " . $user->user->first_name . "</div>";
                                    }
                                    
                                    ?>

                              <?php } else { ?>
                                    <a class="homepage-link" href="<?php echo get_home_url(); ?>">
                                        <div class="up-arrows">??</div>
                                        <?php echo __('?????????? ???????? ???? ')?>
                                        <span class="foody-name"><?php echo get_bloginfo('name'); ?></span>
                                    </a>
                               <?php }
                                if (is_single() && method_exists($recipe, 'the_purchase_buttons')) {
                                    $recipe->the_purchase_buttons();
                                }
                                ?>

                            </div>
                        </div>
                        <?php
                        $nav_args = array(
                            'theme_location' => 'primary',
                        );

                        wp_nav_menu($nav_args);
                        ?>
                    </div>
                    <!--                    --><?php //Foody_Header::whatsapp(['d-block', 'd-lg-none']) ?>
                    <!---->
                    <button type="button" class="btn btn-default navbar-btn btn-search d-block d-lg-none"
                            aria-label="??????????">

                        <img src="
                    <?php echo $GLOBALS['images_dir'] . 'icons/search-bar.png' ?>" alt="search-bar">

                    </button>
                    <button type="button" class="btn btn-default navbar-btn btn-search-close hidden d-block d-lg-none"
                            aria-label="??????????">

                        <img src="
                    <?php echo $GLOBALS['images_dir'] . 'icons/search-bar.png' ?>" alt="search-bar">

                    </button>
                </div>
            </nav>
        </div>
    <?php } ?>
    <?php if (is_single() && $post_type == 'foody_recipe') {
        // add header for print
        Foody_Header::getPrintHeader();
    } ?>
    <?php
    if(get_page_template_slug() === 'page-templates/homepage.php') {
        $brands_avenue_group = get_field('brands_avenue', 'foody_brands_avenue');
        if (isset($brands_avenue_group['brands']) && !empty($brands_avenue_group['brands'])) { ?>

            <?php
        }
    }
    ?>

    <div id="content" class="site-content">
        <?php
        

        if (wp_is_mobile()) {
            Foody_Header::whatsapp(['d-lg-block', 'floating', 'whatsapp-mobile']);
        } else {
            Foody_Header::whatsapp(['d-none', 'd-lg-block', 'floating']);
        }
        ?>

