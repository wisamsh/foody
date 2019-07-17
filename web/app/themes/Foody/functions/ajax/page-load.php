<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/15/19
 * Time: 4:00 PM
 */


add_action('wp_ajax_load_foody_social', 'load_foody_social');
add_action('wp_ajax_nopriv_load_foody_social', 'load_foody_social');

function load_foody_social()
{

    ob_start();

    dynamic_sidebar('foody-social');
    ?>
    <script>
        if (typeof sbi_init === "function") {
            sbi_init();
        }

        if (window.FB && window.FB && window.FB.XFBML && typeof window.FB.XFBML.parse === 'function') {
            window.FB.XFBML.parse();
        }
    </script>
    <?php


    $sidebar = ob_get_contents();
    ob_end_clean();
//    $sidebar = str_replace("'", '"', $sidebar);
    $sidebar = str_replace("&quot;", '"', $sidebar);
    $sidebar = preg_replace('/\n+/', '', $sidebar);

    echo $sidebar;
    wp_die();
}

add_action('wp_ajax_load_homepage_feed', 'load_homepage_feed');
add_action('wp_ajax_nopriv_load_homepage_feed', 'load_homepage_feed');

function load_homepage_feed()
{
    $homepage = new Foody_HomePage();

    $homepage->feed();

    wp_die();
}

