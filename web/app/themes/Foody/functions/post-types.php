<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 5/16/18
 * Time: 6:11 PM
 */


function register_post_types()
{
    $post_types = array(
        'recipe' => array(
            'name' => 'Recipes',
            'singular_name' => 'Recipe'
        ),
        'accessory' => array(
            'name' => 'Accessories',
            'singular_name' => 'Accessory'
        ),
        'technique' => array(
            'name' => 'Techniques',
            'singular_name' => 'Technique'
        )
    );

    foreach ($post_types as $type) {
        register_post_type(strtolower('foody_' . $type['singular_name']),
            array(
                'labels' => array(
                    'name' => __($type['name']),
                    'singular_name' => __($type['singular_name']),
                ),
                'public' => true,
                'has_archive' => true,
            )
        );
    }
}

add_action('init', 'register_post_types');


