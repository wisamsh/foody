<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 7/12/18
 * Time: 11:25 AM
 */


/**
 * creates an array of WP_Post from an acf
 * post object field.
 * If $class is not null and is instanceof
 * @link Foody_Post an array of that type will be returned.
 *
 * @param $selector string field name. must be a field of type post (id or object)
 * @param null|int $post post id
 * @param null|string $class class name to create
 * @return WP_Post[]|Foody_Recipe[] array of posts objects
 */
function posts_to_array($selector, $post = null, $class = null)
{


    $posts = array();

    $posts_field = get_field($selector, $post);

    if (array_not_empty($posts_field)) {
        foreach ($posts_field as $item) {
            if (is_int($item)) {
                $item = get_post($item);
            }

            if ($class != null && class_exists($class)) {

                $foody_post = new $class($item);
                if ($foody_post instanceof Foody_Post) {
                    $posts[] = $foody_post;
                } else {
                    $posts[] = $item;
                }
            } else {
                $posts[] = $item;
            }


        }
    }

    return $posts;

}

/*
   Debug preview with custom fields
*/

add_filter('_wp_post_revision_fields', 'add_field_debug_preview');
function add_field_debug_preview($fields){
    $fields["debug_preview"] = "debug_preview";
    return $fields;
}

add_action( 'edit_form_after_title', 'add_input_debug_preview' );
function add_input_debug_preview() {
    echo '<input type="hidden" name="debug_preview" value="debug_preview">';
}

