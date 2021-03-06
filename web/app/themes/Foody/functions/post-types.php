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
            'singular_name' => 'אביזר',
            'show_in_menu' => is_main_site() 
        ),
        'technique' => array(
            'id' => 'technique',
            'name' => 'טכניקות',
            'singular_name' => 'טכניקה',
            'show_in_menu' => is_main_site()
        ),
        'ingredient' => array(
            'id' => 'ingredient',
            'name' => 'מצרכים',
            'singular_name' => 'מצרך',
            'taxonomies' => array('category', 'post_tag'),
            'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
            'show_ui' => true,
            'show_in_menu' => is_main_site()
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
        'course' => array(
            'id' => 'course',
            'name' => 'קורסים',
            'singular_name' => 'קורס',
            'taxonomies' => array(),
            'show_in_menu' => is_main_site(),
            'supports' => array('title', 'editor', 'thumbnail', 'revisions'),
            'unsupported' => array('editor'),
            'show_ui' => true,
            'rewrite' => array(
                'slug' => 'courses',
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
        ),
        'feed_channel' => array(
            'id' => 'feed_channel',
            'name' => 'מתחמי פידים',
            'singular_name' => 'מתחם פידים',
            'taxonomies' => array(),
            'show_ui' => true,
            'rewrite' => array(
                'slug' => 'areas',
                'with_front' => true
            )
        ),
        'filter' => array(
            'id' => 'filter',
            'name' => 'פילטרים',
            'singular_name' => 'פילטר',
            'taxonomies' => array(),
            'supports' => array('title', 'revisions'),
            'unsupported' => array('editor'),
            'show_ui' => true,
            'rewrite' => array(
                'slug' => 'feed/filter',
                'with_front' => true
            )
        ),
        'comm_rule' => array(
            'id' => 'comm_rule',
            'name' => 'מצרכים מסחריים',
            'singular_name' => 'מצרך מסחרי',
            'taxonomies' => array(),
            'supports' => array('title', 'revisions'),
            'unsupported' => array('editor'),
            'show_ui' => true,
            'rewrite' => false,
            'query_var' => false,
            'publicly_queryable' => false,
            'public' => false
        ),
        'organizations' => array(
            'id' => 'organizations',
            'name' => 'ארגונים לקורסים',
            'singular_name' => 'ארגון לקורסים',
            'show_in_menu' => is_main_site()
        ),
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

        if (!isset($type['show_in_menu'])) {
            $type['show_in_menu'] = true;
        }

        $args['show_in_menu'] = $type['show_in_menu'];

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

        if (!empty($type['unsupported']) && is_array($type['unsupported'])) {
            foreach ($type['unsupported'] as $f) {
                remove_post_type_support(strtolower('foody_' . $type['id']), $f);
            }
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
        'foody_ingredient',
        'foody_accessory',
        'foody_technique',
        'foody_feed_channel',
        'foody_comm_rule',
        'post'
    );

    $default_template = 'page-templates/content-with-sidebar.php';

    if (in_array($pagenow, array(
            'post-new.php',
            'post.php'
        )) && in_array(get_post_type(), $custom_content_post_types)) {
        ?>
        <script async defer type="text/javascript">
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
                'show_ui' => true,
                'show_in_menu' => is_main_site()
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
                'show_ui' => true,
                'show_in_menu' => is_main_site()
            )
        )
    );
}

add_action('init', 'limitations_init');

function sponsors_init()
{

    $labels = array(
        'name' => __('חברות מסחריות', 'foody'),
        'singular_name' => __('חברה מסחרית', 'foody'),
        'search_items' => __('חפש חברות מסחריות', 'foody'),
        'all_items' => __('כל החברות המסחריות', 'foody'),
        'edit_item' => __('ערוך חברה מסחרית', 'foody'),
        'update_item' => __('עדכן חברה מסחרית', 'foody'),
        'add_new_item' => __('הוסף חברה מסחרית', 'foody'),
        'not_found' => __('לא נמצאו חברות מסחריות', 'foody'),
        'menu_name' => __('חברות מסחריות', 'foody')
    );

    // create a new taxonomy
    register_taxonomy(
        'sponsors',
        ['foody_recipe'],
        array(
            'labels' => $labels,
            'public' => false,
            'rewrite' => false,
            'show_ui' => true,
            'show_in_menu' => is_main_site(),
            'show_in_nav_menus' => is_main_site(),
            'hierarchical' => true,
            'capabilities' => array(
                'assign_terms' => 'edit_posts',
                'edit_terms' => 'publish_posts',
                'show_ui' => true,
                'show_in_menu' => is_main_site(),
                'show_in_nav_menus' => is_main_site(),
            )
        )
    );
}

add_action('init', 'sponsors_init');


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
 *
 * @return int|null|string number of posts written by the author
 */
function foody_count_posts_by_user($post_author = null, $post_type = array(), $post_status = array())
{
    global $wpdb;

    if (empty($post_author)) {
        return 0;
    }

    $post_type = (array)$post_type;

    $sql = $wpdb->prepare("SELECT COUNT(*) FROM $wpdb->posts WHERE post_author = %d AND ", $post_author);

    $where = "(post_status='publish') AND ";
    $sql .= $where;

    //Post status
//    if (!empty($post_status)) {
//        $argtype = array_fill(0, count($post_status), '%s');
//        $where = "(post_status=" . implode(" OR post_status=", $argtype) . ') AND ';
//        $sql .= $wpdb->prepare($where, $post_status);
//    }

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
                if (is_numeric($recipes[0])) {
                    $recipes[0] = get_post($recipes[0]);
                }
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

    if (is_admin()) {
        return;
    }

    if (is_search() && $query->is_main_query()) {
        $query->set('post_type', ['foody_recipe', 'post', 'foody_feed_channel']);
    }

}

add_action('pre_get_posts', 'my_pre_get_posts');


/**
 * Hooks into foody_js_globals to
 * add relevant javascript variables
 * based on the post type.
 *
 * @param $single_template
 *
 * @return mixed
 */
function custom_post_type_js_vars($single_template)
{
    global $post;

    $foody_post = Foody_Post::create($post);

    $js_vars = $foody_post->js_vars();

    add_filter('foody_js_globals', function ($vars) use ($js_vars) {
        return array_merge($js_vars, $vars);
    });

    unset($foody_post);

    return $single_template;
}

add_filter('single_template', 'custom_post_type_js_vars');


function foody_ingredient_custom_template($template)
{
    if (in_array(get_post_type(), ['foody_ingredient', 'foody_accessory', 'foody_technique'])) {
        $template = get_template_directory() . '/page-templates/content-with-sidebar.php';
    }

    return $template;
}

add_filter('single_template', 'foody_ingredient_custom_template');

add_filter('foody_page_query_var', 'foody_set_custom_page_var');

function foody_set_custom_page_var($page)
{
    if (get_post_type() == 'foody_filter') {
        $page = 'foody-page';
    }

    return $page;
}


function foody_posts_page_script()
{
    global $post;
    $referer_google = false;
    $referer_facebook = false;
    $referer_taboola = false;
    $referer_outbrain = false;

    $post_type = get_post_type();
    if ($post_type == 'post' ||
        $post_type == 'foody_recipe' ||
        $post_type == 'foody_feed_channel' ||
        ($post_type == 'page' && is_page_template('page-templates/centered-content.php')) ||
        ($post_type == 'foody_course' && is_page_template('page-templates/foody-course-efrat.php')) ||
        is_page_template('page-templates/items.php') ||
        is_category()) {

        /** from feed area */
        if ($post_type == 'foody_recipe' || $post_type == 'post') {
            $feed_area_id = isset($post->ID) ? get_field('recipe_channel', $post->ID) : get_field('recipe_channel');
            $feed_area_id = is_category() ? get_field('recipe_channel', get_queried_object()) : $feed_area_id;
            if (is_array($_GET) && (isset($_GET['referer']) || $feed_area_id)) {
                $referer = isset($_GET['referer']) ? $_GET['referer'] : $feed_area_id;

                $enable_google = get_field('google_set_for_recipes', $referer);
                $enable_facebook = get_field('facebook_set_for_recipes', $referer);
                $enable_taboola = get_field('taboola_set_for_recipes', $referer);
                $enable_outbrain = get_field('outbrain_set_for_recipes', $referer);

                if ($enable_facebook) {
                    $referer_facebook = $referer;
                }
                if ($enable_google) {
                    $referer_google = $referer;
                }
                if ($enable_taboola) {
                    $referer_taboola = $referer;
                }
                if ($enable_outbrain) {
                    $referer_outbrain = $referer;
                }
            }
        }
        if (is_category()) {
            $referer_outbrain = $referer_facebook = $referer_google = $referer_taboola = get_queried_object();
        }

        /* facebook pixel */
        $pixel_code = get_field('pixel_code', $referer_facebook);
        if (!empty($pixel_code)) {
            $pixel_code = html_entity_decode($pixel_code);
            if (strpos($pixel_code, '<script>') == false || strpos($pixel_code, '</script>') == false) {
                $pixel_code = add_script_tags(handle_bad_apostrophe($pixel_code));
            }
            $pixel_code = remove_unnecessary_tags($pixel_code);
            echo $pixel_code;
        }

        /* taboola pixel */
        $pixel_code_taboola = get_field('pixel_code_taboola', $referer_taboola);
        if (!empty($pixel_code_taboola)) {
            $pixel_code_taboola = html_entity_decode($pixel_code_taboola);
            if (strpos($pixel_code_taboola, '<script>') == false || strpos($pixel_code_taboola, '</script>') == false) {
                $pixel_code_taboola = add_script_tags(handle_bad_apostrophe($pixel_code_taboola));
            }
            $pixel_code_taboola = remove_unnecessary_tags($pixel_code_taboola);
            echo $pixel_code_taboola;
        }

        /* outbrain pixel */
        $pixel_code_outbrain = get_field('pixel_code_outbrain', $referer_outbrain);
        if (!empty($pixel_code_outbrain)) {
            $pixel_code_outbrain = html_entity_decode($pixel_code_outbrain);
            if (strpos($pixel_code_outbrain, '<script') == false || strpos($pixel_code_outbrain, '</script>') == false) {
                $pixel_code_outbrain = add_script_tags(handle_bad_apostrophe($pixel_code_outbrain));
            }
            $pixel_code_outbrain = remove_unnecessary_tags($pixel_code_outbrain);
            echo $pixel_code_outbrain;
        }


        /* google pixel */
        $pixel_code_google = get_field('pixel_code_google', $referer_google);
        if (!empty($pixel_code_google)) {
            $pixel_code_google = html_entity_decode($pixel_code_google);
            $pixel_code_google = remove_unnecessary_tags($pixel_code_google);
            $pixel_code_google = add_script_tags_google($pixel_code_google);
            echo $pixel_code_google;
        }

    }
}

add_action('wp_head', 'foody_posts_page_script');


/**
 * Use in posts_where filter to search for recipes with ingredients
 * @param $where
 * @param WP_Query $query
 *
 * @return mixed
 */
function ingredient_posts_where($where, WP_Query $query)
{
    if ($query->get('has_wildcard_key')) {
        $where = str_replace(
            "meta_key = 'ingredients_ingredients_groups_\$_ingredients_\$_ingredient",
            "meta_key LIKE 'ingredients_ingredients_groups_%_ingredients_%_ingredient",
            $where
        );
    }

    return $where;
}

function add_script_tags($code)
{
    $has_img_tag = false;
    $change_code = $code;
    $split_string = '';
    if (strpos($change_code, '<img') != false) {
        $split_string = explode('<img', $change_code);
        if (count($split_string) > 1) {
            $has_img_tag = true;
        }
    }
    if (strpos($change_code, '<script>') == false && strpos($change_code, '<script') == false) {
        if ($has_img_tag) {
            $change_code = '<script>' . $split_string[0];
        } else {
            $change_code = '<script>' . $change_code;
        }
    }
    if (strpos($change_code, '</script>') == false) {
        if ($has_img_tag) {
            $change_code .= '</script>' . '<noscript>' . '<img' . $split_string[1] . '</noscript>';
        } else {
            $change_code = $change_code . '</script>';
        }
    }

    return $change_code;
}

function remove_unnecessary_tags($pixel_code)
{
    $pixel_code = str_replace('<p>', '', $pixel_code);
    $pixel_code = str_replace('</p>', '', $pixel_code);
    $pixel_code = str_replace('<br />', '', $pixel_code);
    $pixel_code = str_replace('<br/>', '', $pixel_code);
    $pixel_code = str_replace('<br>', '', $pixel_code);
    $pixel_code = str_replace('<p style="direction: ltr;">', '', $pixel_code);
    $pixel_code = str_replace('<!– Facebook Pixel Code –>', '', $pixel_code);
    $pixel_code = str_replace('<!-- Facebook Pixel Code -->', '', $pixel_code);

    return $pixel_code;
}

function add_script_tags_google($pixel_code)
{
    $delimiter = '<!--';
    $opposite_delimiter = '-->';
    $res_array = [];
    $changed = false;
    $counter = 0;
    $last_pushed= 0;

    $pixel_code = str_replace(['‘','’'], "'", $pixel_code);
    $splited_code = preg_split("~(" . $delimiter . ")~", $pixel_code, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($splited_code as $code) {
        if ($code != $delimiter && !empty($code)) {
            if (strpos($code, '<script>') == false && strpos($code, '<script') == false) {
                $splited_sub_code = preg_split("~(". $opposite_delimiter . ")~", $code, -1, PREG_SPLIT_DELIM_CAPTURE);
                if ($splited_sub_code != false) {
                    if(isset($splited_sub_code[1]) && $splited_sub_code[1] == $opposite_delimiter){
                        $splited_sub_code[1] = '-->'. "\r\n" .'<script>';
                    }
                    array_push($res_array,implode("", $splited_sub_code));
                    $last_pushed = count($res_array);
                    $counter++;
                }

                $changed = true;
            }
            if (strpos($code, '</script>') == false) {
                if($last_pushed != $counter) {
                    array_push($res_array, $code."\r\n".'</script>'."\r\n");
                    $counter++;
                }
                else{
                    $res_array[$last_pushed -1] = $res_array[$last_pushed -1]."\r\n".'</script>'."\r\n";
                }
                $changed = true;
            }
        } elseif (!empty($code)) {
            $counter++;
            array_push($res_array, $code);
        }
    }
    if ($changed) {
        $pixel_code = '';
        foreach ($res_array as $code_part) {
            $pixel_code .= $code_part;
        }
    }

    return $pixel_code;
}

function insertAtPosition($string, $insert, $position)
{
    return implode($insert, str_split($string, $position));
}

function handle_bad_apostrophe($pixel_code)
{
    $pixel_code_result = str_replace("‘", "'", $pixel_code);
    $pixel_code_result = str_replace("’", "'", $pixel_code_result);
    $pixel_code_result = str_replace("′", "'", $pixel_code_result);


    return $pixel_code_result;
}