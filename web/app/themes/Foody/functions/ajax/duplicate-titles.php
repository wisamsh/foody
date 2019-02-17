<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 8/6/18
 * Time: 5:10 PM
 */

$relevant_post_types = [
    'foody_ingredient',
    'foody_accessory'
];

function duplicate_titles_enqueue_scripts($hook)
{
    if (!in_array($hook, array('post.php', 'post-new.php'))) {
        return;
    }

    $screen = get_current_screen();

    $post_type = $screen->post_type;


    global $relevant_post_types;

    if (!in_array($post_type, $relevant_post_types)) {
        return;
    }
    wp_enqueue_script('duptitles', wp_enqueue_script('duptitles', get_template_directory_uri() . '/resources/js/admin/duptitles.js', array('jquery')), array('jquery'));

//    wp_enqueue_script('duptitles', get_template_directory() . '/resources/js/admin/duptitles.js', array('jquery'));
}

add_action('admin_enqueue_scripts', 'duplicate_titles_enqueue_scripts', 2000);
add_action('wp_ajax_title_check', 'duplicate_title_check_callback');

function duplicate_title_check_callback()
{
    function title_check()
    {
        global $relevant_post_types;

        $post_types = implode(',', array_map(function ($type) {
            return "'" . $type . "'";
        }, $relevant_post_types));

        $title = $_POST['post_title'];
        $post_id = $_POST['post_id'];
        global $wpdb;
        $sim_titles = "SELECT post_title FROM $wpdb->posts WHERE post_status = 'publish' AND post_type IN ($post_types) 
						AND post_title = '{$title}' AND ID != {$post_id} ";
        $sim_results = $wpdb->get_results($sim_titles);
        if ($sim_results) {
            return "<div style='color:red'>כותרת הפוסט כבר קיימת במערכת</div>";
        } else {
            return '<div style="color:green">This title is unique</div>';
        }
    }

    echo title_check();
    die();
}

function disable_autosave()
{
    wp_deregister_script('autosave');
}

add_action('wp_print_scripts', 'disable_autosave');



add_action( 'wp_insert_post', 'duplicate_title_check_callback', 10, 3 );