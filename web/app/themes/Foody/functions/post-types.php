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
            'taxonomies' => array('category', 'post_tag'),
            'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
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
        ),
        'playlist' => array(
            'name' => 'Playlists',
            'singular_name' => 'Playlist'
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
            'capability_type' => 'post'
        );

        if (isset($type['taxonomies'])) {
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
            add_post_type_support(strtolower('foody_' . $type['singular_name']), $feature);
        }

    }
}

add_action('init', 'register_post_types');


/**
 *
 */
function foody_remove_page_template()
{
    global $pagenow;

    $custom_content_post_types = array(
        'foody_recipe',
        'foody_article',
        'foody_playlist'
    );

    $default_template = 'page-templates/content-with-sidebar.php';

    if (in_array($pagenow, array('post-new.php', 'post.php')) && in_array(get_post_type(), $custom_content_post_types)) {
        ?>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    $('#page_template').val('<?php echo $default_template?>');
                })


            })(jQuery)
        </script>
        <?php
    }
}

add_action('admin_footer', 'foody_remove_page_template', 10);


function units_init()
{
    // create a new taxonomy
    register_taxonomy(
        'units',
        'foody_ingredient',
        array(
            'label' => __('Units'),
            'public' => true,
            'rewrite' => array('slug' => 'unit'),
            'capabilities' => array(
                'assign_terms' => 'edit_posts',
                'edit_terms' => 'publish_posts',
                'show_ui' => true
            )
        )
    );
}

add_action('init', 'units_init');