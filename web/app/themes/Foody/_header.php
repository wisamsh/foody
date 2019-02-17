<?php

?>
<!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#ED3D48">
    <link rel="profile" href="http://gmpg.org/xfn/11">

    <?php wp_head(); ?>

</head>

<body <?php body_class(); ?> dir="rtl">


<div id="page" class="site">

    <header id="masthead" class="site-header no-print">


        <?php


        $nav_args = array(
            'theme_location' => 'primary',
        );

        wp_nav_menu($nav_args);
        ?>

        <!-- #site-navigation -->
    </header><!-- #masthead -->

    <div id="content" class="site-content">

