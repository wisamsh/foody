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
<?php // for facebook metatags
require (__DIR__ . '/w_helpers/facebook_meta_tags.php');

//require (__DIR__ . '/w_helpers/taboola_in_head.php');
?>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
<?php require( __DIR__ . '/cache-tags.php' );?>
<?php if (strpos(get_page_template(), 'foody-course-register.php')) { ?>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<?php } ?>
<?php if($google_site_verification_id){ ?>
<meta name="google-site-verification" content="<?php echo $google_site_verification_id; ?>"/>
<?php } ?>
<meta name="theme-color" content="#ffffff">
<link rel="profile" href="http://gmpg.org/xfn/11">

<?php Foody_Header::google_tag_manager(); ?>
<?php if (get_current_blog_id() == 2) { ?>
<script data-ad-client="ca-pub-3607762765478350" async
src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<script type="text/javascript">
window._taboola = window._taboola || [];
_taboola.push({article:'auto'});
!function (e, f, u, i) {
if (!document.getElementById(i)){
e.async = 1;
e.src = u;
                    e.id = i;
f.parentNode.insertBefore(e, f);
                }
}(document.createElement('script'),
document.getElementsByTagName('script')[0],
'//cdn.taboola.com/libtrc/karingoren/loader.js',
'tb_loader_script');
if(window.performance && typeof window.performance.mark == 'function')
{window.performance.mark('tbl_ic');}

</script>

<?php } ?>
   
   
   
<?php wp_head();
   
?>
</head>

<?php if (strpos(get_page_template(), 'foody-course-register.php')) { ?>
<script src="https://public.bankhapoalim.co.il/bitcom/sdk"></script>
<?php } ?>

<body <?php body_class(); ?>
<?php if(get_post_type() == 'questions'){echo 'itemscope itemtype="https://schema.org/QAPage"';}?> dir="rtl">
<?php

if (get_post_type() != 'poll' && !wp_is_mobile() && (isset($_SESSION['background_image']) && !empty($_SESSION['background_image']))) {
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
<?php echo __('ערוך') ?>
</a>
<?php
//if there is google photo:
if(get_field('google_photo_link', get_the_ID()))
            {
$googlephoto = get_field('google_photo_link', get_the_ID());
echo(' |
<a href="'.$googlephoto.'" target="_blank">
לתמונת המתכון בגוגל פוטו
</a>
');
            }
?>
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

<section class="header-top-container d-none d-lg-flex">
<?php $header->the_socials_bar() ?>

<?php
//Wisam : for search bar only on foody : 
include_once('w_helpers/whitelabels-search.php');?>

<?php if (is_multisite() && !is_main_site()): ?>
<?php $header->the_foody_collaboration(true); ?>
<?php endif; ?>
<?php


if (!wp_is_mobile() && $show_accessibility):

?>
<button type="button" class="btn btn-default navbar-btn d-none d-lg-block accessibility"
data-acsb="trigger" aria-label="פתיחת תפריט נגישות">
<?php $header->accessibility(); ?>
<div id="accessibility-container"></div>
</button>
<?php endif; ?>
</section>

</div>







<nav class="navbar navbar-expand-lg navbar-light navbar-toggleable-lg <?php $header->the_logo_nav_mode() ?> mobile_footer_nav"
role="navigation">

<div class="container-fluid foody-navbar-container mobile_footer_container">

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

<img class="mobile_footer_search_icon" src="<?php echo $GLOBALS['images_dir'] . 'icons/search-bar.png' ?>" alt="search-bar">

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
<?php 
       
       //Wisam Manual scripts : 
       require_once(get_template_directory() . "/w_helpers/foody-manual-scripts.php"); 
       
       ?>