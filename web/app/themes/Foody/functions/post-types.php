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
            'singular_name' => 'Recipe',
	        'taxonomies'=>array('category','post_tag'),
            'supports' => array( 'title', 'editor', 'thumbnail', 'revisions' ),
            'show_ui' => true,
        ),
        'accessory' => array(
            'name' => 'Accessories',
            'singular_name' => 'Accessory'
        ),
        'technique' => array(
            'name' => 'Techniques',
            'singular_name' => 'Technique'
        ),
        'ingredient' => array(
            'name' => 'Ingredients',
            'singular_name' => 'Ingredient'
        )
    );

    foreach ($post_types as $type) {

    	$args = array(
		    'labels' => array(
			    'name' => __($type['name']),
			    'singular_name' => __($type['singular_name']),
		    ),
		    'public' => true,
		    'has_archive' => true,
	    );

    	if(isset($type['taxonomies'])){
		    $args['taxonomies'] = $type['taxonomies'];
	    }
        register_post_type(strtolower('foody_' . $type['singular_name']),
	        $args
        );

    	$supported_features = array(
            'title',
            'editor',
            'author',
            'thumbnail',
            'excerpt',
            'trackbacks',
            'custom-fields',
            'comments',
            'revisions',
            'page-attributes',
            'post-formats'
        );

        foreach ($supported_features as $feature) {
            add_post_type_support(strtolower('foody_' . $type['singular_name']),$feature);
        }

    }
}

add_action('init', 'register_post_types');


