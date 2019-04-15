<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/14/19
 * Time: 5:37 PM
 */

function my_acf_load_post_object_value($value, $post_id, $field)
{
    return $value;
}


add_filter('acf/load_value/type=post_object', 'my_acf_load_post_object_value', 10, 3);


function my_acf_load_taxonomy_value($value, $post_id, $field)
{
    if (!empty($value)){
        $tax = $field['taxonomy'];

        $global_taxonomies = [
            'units',
            'pans',
            'limitations',
        ];

        if (in_array($tax, $global_taxonomies)) {

        }
    }

    return $value;
}


add_filter('acf/load_value/type=taxonomy', 'my_acf_load_taxonomy_value', 10, 3);