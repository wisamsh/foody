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
            'id' => 'recipe',
            'name' => 'מתכונים',
            'singular_name' => 'מתכון',
            'taxonomies' => array('category', 'post_tag'),
            'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
            'show_ui' => true,
        ),
        'accessory' => array(
            'id' => 'accessory',
            'name' => 'אביזרים',
            'singular_name' => 'אביזר'
        ),
        'technique' => array(
            'id' => 'technique',
            'name' => 'טכניקות',
            'singular_name' => 'טכניקה'
        ),
        'ingredient' => array(
            'id' => 'ingredient',
            'name' => 'מצרכים',
            'singular_name' => 'מצרך',
            'taxonomies' => array('category', 'post_tag'),
            'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
            'show_ui' => true,
        ),
        'playlist' => array(
            'id' => 'playlist',
            'name' => 'פלייליסטים',
            'singular_name' => 'פלייליסט',
            'taxonomies' => array('category', 'post_tag'),
            'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
            'show_ui' => true,
            'rewrite' => array(
                'slug' => 'playlist',
                'with_front' => true
            )
        ),
        'channel' => array(
            'id' => 'channel',
            'name' => 'ערוצים',
            'singular_name' => 'ערוץ',
            'taxonomies' => array('category', 'post_tag'),
            'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
            'show_ui' => true,
            'rewrite' => array(
                'slug' => 'channel',
                'with_front' => true
            )
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

        if (isset($type['rewrite'])) {
            $args['rewrite'] = $type['rewrite'];
        }

        if (isset($type['taxonomies'])) {
            $args['taxonomies'] = $type['taxonomies'];
        }
        register_post_type(strtolower('foody_' . $type['id']),
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
            add_post_type_support(strtolower('foody_' . $type['id']), $feature);
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
        'foody_playlist',
        'post'
    );

    $default_template = 'page-templates/content-with-sidebar.php';

    if (in_array($pagenow, array('post-new.php', 'post.php')) && in_array(get_post_type(), $custom_content_post_types)) {
        ?>
        <script type="text/javascript">
            (function ($) {
                $(document).ready(function () {
                    $('#page_template').val('<?php echo $default_template?>');
                });
            })(jQuery);
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
            'label' => __('יחידות מידה'),
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


function pans_init()
{
    // create a new taxonomy
    register_taxonomy(
        'pans',
        'foody_accessory',
        array(
            'label' => __('תבניות'),
            'public' => true,
            'rewrite' => array('slug' => 'pan'),
            'capabilities' => array(
                'assign_terms' => 'edit_posts',
                'edit_terms' => 'publish_posts',
                'show_ui' => true
            )
        )
    );
}

add_action('init', 'pans_init');


function limitations_init()
{
    // create a new taxonomy
    register_taxonomy(
        'limitations',
        'foody_ingredient',
        array(
            'label' => __('מגבלות'),
            'public' => true,
            'rewrite' => array('slug' => 'limitation'),
            'capabilities' => array(
                'assign_terms' => 'edit_posts',
                'edit_terms' => 'publish_posts',
                'show_ui' => true
            )
        )
    );
}

add_action('init', 'limitations_init');


/**
 * Retrieves all custom foody
 * post types
 *
 * @return array
 */
function foody_get_post_types()
{
    $all_types = get_post_types('', 'names');
    $all_types = array_values($all_types);

    $all_types = array_filter($all_types, function ($type) {
        return preg_match('/foody_/', $type) || $type == 'post';
    });
    return $all_types;
}

/**
 *
 * Counts the number of posts
 * written by the author with consideration
 * of custom post types
 *
 * @param null $post_author author id
 * @param array $post_type post types to count
 * @param array $post_status
 * @return int|null|string number of posts written by the author
 */
function foody_count_posts_by_user($post_author = null, $post_type = array(), $post_status = array())
{
    global $wpdb;

    if (empty($post_author))
        return 0;

    $post_status = (array)$post_status;
    $post_type = (array)$post_type;

    $sql = $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = %d AND ", $post_author);

    //Post status
    if (!empty($post_status)) {
        $argtype = array_fill(0, count($post_status), '%s');
        $where = "(post_status=" . implode(" OR post_status=", $argtype) . ') AND ';
        $sql .= $wpdb->prepare($where, $post_status);
    }

    if (empty($post_type)) {
        $post_type = foody_get_post_types();
    }

    //Post type

    $argtype = array_fill(0, count($post_type), '%s');
    $where = "(post_type=" . implode(" OR post_type=", $argtype) . ') AND ';
    $sql .= $wpdb->prepare($where, $post_type);


    $sql .= '1=1';
    $count = $wpdb->get_var($sql);
    return $count;
}


add_filter('rewrite_rules_array', 'mmp_rewrite_rules');
function mmp_rewrite_rules($rules)
{
    $newRules = array();
    $newRules['playlist/(.+)/(.+)/?$'] = 'index.php?foody_playlist=$matches[0]&recipename=$matches[1]'; // my custom structure will always have the post name as the 5th uri segment

    return array_merge($newRules, $rules);
}

function filter_post_type_link($link, $post)
{
    if ($post->post_type == 'foody_playlist') {

        $recipe_name = get_query_var('recipe', null);
        if (is_null($recipe_name)) {
            $recipes = posts_to_array('recipes', $post->ID);
            if (is_array($recipes) && count($recipes) > 0) {
                $recipe_name = $recipes[0]->post_name;
                $link = add_query_arg('recipe', $recipe_name, $link);
            }
        }
    }
    return $link;
}

add_filter('post_type_link', 'filter_post_type_link', 10, 2);


/**
 * @param WP_Post $post
 */
function post_to_foody_post($post)
{

    $foody_post = null;

    switch ($post->post_type) {
        case 'foody_recipe':
            $foody_post = new Foody_Recipe($post);
            break;
        case 'foody_playlist':
            $foody_post = new Foody_Playlist($post);
            break;
        default:
            $foody_post = new Foody_Article($post);
            break;
    }

    return $foody_post;
}

function my_pre_get_posts(WP_Query $query)
{

    if (is_admin())
        return;

    if (is_search() && $query->is_main_query()) {
        $query->set('post_type', 'foody_recipe');
    }

}

add_action('pre_get_posts', 'my_pre_get_posts');