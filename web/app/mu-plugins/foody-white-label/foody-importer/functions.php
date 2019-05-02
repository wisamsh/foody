<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 4/27/19
 * Time: 4:53 PM
 */

add_filter('foody_import_post_meta_value', 'foody_handle_post_meta_value', 10, 4);

/**
 * @param $post_id
 * @param $key
 * @param $value
 * @param $blog_id
 * @return mixed
 */
function foody_handle_post_meta_value($post_id, $key, $value, $blog_id)
{
    switch_to_blog(1);
    $acf_field = get_field_object($key, $post_id);
    restore_current_blog();
    $types_to_change = [
        'taxonomy',
        'post_object',
        'image',
        'image_crop'
    ];

    if (!empty($acf_field)) {

        $type = $acf_field['type'];

        if (in_array($type, $types_to_change) && !empty($value)) {

            $fn = "foody_change_{$type}_meta";

            $value = call_user_func($fn, $post_id, $key, $value, $acf_field, $blog_id);
        }

    }
    return $value;
}

/*
 * functions based on acf field types.
 * all functions have the naming convention "foody_change_{$field->type}_meta"
 * */


/**
 * @param $post_id
 * @param $key
 * @param $value
 * @param $field
 * @param $blog_id
 * @return int
 */
function foody_change_taxonomy_meta($post_id, $key, $value, $field, $blog_id)
{

    if (!empty($value)) {

        // get term from main site by id
        switch_to_blog(1);
        $term_in_main = get_term($value, $field['taxonomy']);
        restore_current_blog();

        // term found in main
        if ($term_in_main instanceof WP_Term) {

            // find term by name from main in new blog
            switch_to_blog($blog_id);
            $term_by_name = get_term_by('name', $term_in_main->name, $field['taxonomy']);
            restore_current_blog();
            if ($term_by_name instanceof WP_Term) {
                // set term to the id of the term found by name
                $value = $term_by_name->term_id;
            }
        }
    }

    return $value;
}

/**
 * @param $post_id
 * @param $key
 * @param $value
 * @param $field
 * @param $blog_id
 * @return int
 */
function foody_change_post_object_meta($post_id, $key, $value, $field, $blog_id)
{
    if (!empty($value)) {

        // get term from main site by id
        switch_to_blog(1);
        $post_in_main = get_post($value);
        restore_current_blog();

        // term found in main
        if ($post_in_main instanceof WP_Post) {

            // find term by name from main in new blog
            switch_to_blog($blog_id);
            $post_by_name = get_page_by_title($post_in_main->post_title);
            restore_current_blog();
            if ($post_by_name instanceof WP_Post) {
                // set term to the id of the term found by name
                $value = $post_by_name->ID;
            }
        }
    }

    return $value;
}

/**
 * @param $post_id
 * @param $key
 * @param $value
 * @param $field
 * @param $blog_id
 * @return int
 */
function foody_change_image_crop_meta($post_id, $key, $value, $field, $blog_id)
{
    /**
     * in acf-image-crop the value is saved like this:
     * {"original_image":"2865","cropped_image":2923}
    */
    $parsed = json_decode($value);
    $new_value = [];
    foreach ($parsed as $image_type => $attachment_id) {
        $new_value[$image_type] = foody_change_image_meta($post_id, $key, $attachment_id, $field, $blog_id);
    }
    return json_encode($new_value);
}

/**
 * @param $post_id
 * @param $key
 * @param $value
 * @param $field
 * @param $blog_id
 * @return int
 */
function foody_change_image_meta($post_id, $key, $value, $field, $blog_id)
{
    if (!empty($value)) {

        $value_to_change = $value;
        if (!is_numeric($value_to_change) && is_array($value_to_change)) {
            $value_to_change = $value_to_change['ID'];
        }

        switch_to_blog(1);
        $url = wp_get_attachment_url($value_to_change);
        switch_to_blog($blog_id);
        if (!empty($url)) {
            $attachment_id = Foody_WhiteLabelDuplicator::upload_image(null, $url);
            if (is_numeric($attachment_id)) {
                $value = (int)$attachment_id;
            } elseif (is_wp_error($attachment_id)) {
                Foody_WhiteLabelLogger::error(
                    $attachment_id->get_error_message(),
                    array_merge(['error' => $attachment_id], $attachment_id->error_data)
                );
            }
        } else {
            Foody_WhiteLabelLogger::warning("empty image value for post: $post_id");
        }
    }

    return $value;
}
