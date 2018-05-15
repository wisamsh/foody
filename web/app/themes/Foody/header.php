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
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="http://gmpg.org/xfn/11">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">

    <header id="masthead" class="site-header">
        <div class="socials">

        </div>
        <div class="site-branding">
        </div><!-- .site-branding -->

        <nav id="site-navigation" class="main-navigation">
<!--            <div class="primary-navbar-container">-->
<!--		        --><?php //wp_nav_menu(
//			        ['menu' => 'Top Navigation',
//			         'depth' => 2,
//			         'container' => false,
//			         'menu_class' => 'nav navbar-nav ',
//				        //Process nav menu using our custom nav walker
//				     'walker' => new WP_Bootstrap_Navwalker(),
//				     'theme_location' => 'primary',
//				     'menu_id' => 'primary-menu']
//		        );
//		        ?>
<!--            </div>-->
        </nav><!-- #site-navigation -->
    </header><!-- #masthead -->

    <div id="content" class="site-content">
