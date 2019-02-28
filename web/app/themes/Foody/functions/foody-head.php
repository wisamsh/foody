<?php
/**
 * This functions file include head
 * modifications and actions (most probably usage of the wp_head action hook).
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/11/18
 * Time: 11:15 PM
 */

add_action('wp_head', 'Foody_Header::facebook_init');


function get_page_type()
{

    $type = 'article';

    if (is_home() || is_front_page()) {
        $type = 'home';
    } elseif (is_category()) {
        $type = 'category';
    } elseif (is_search()) {
        $type = 'search';
    } elseif (is_author()) {
        $type = 'author';
    } elseif (is_single()) {
        $post_type = get_post_type();

        if (!empty($post_type) && $post_type != 'post') {
            $type = str_replace('foody_', '', $post_type);
        }
    } elseif (is_page_template('page-templates/categories.php')) {

        $type = 'categories';

    } elseif (is_page_template('page-templates/profile.php')) {

        $type = 'profile';

    } elseif (is_page('הנבחרת שלנו')) {

        $type = 'team';

    }

    return $type;
}


function foody_js_globals_main($vars)
{

    $vars['queryPage'] = Foody_Query::$page;
    $vars['objectID'] = get_queried_object_id();
    $vars['title'] = get_the_title();
    $vars['type'] = get_page_type();
    $vars['postsPerPage'] = get_option('posts_per_page');
    $vars['mixpanelToken'] = MIXPANEL_TOKEN;


    if (is_single()) {

        $vars['post'] = [
            'ID' => get_the_ID(),
            'type' => get_post_type(),
            'title' => get_the_title()
        ];
    }

    $queried_object = get_queried_object();
    if (is_category() || is_tag()) {
        $vars['title'] = $queried_object->name;
    } elseif (is_author()) {
        $vars['title'] = $queried_object->data->display_name;
    }


    return $vars;
}

add_filter('foody_js_globals', 'foody_js_globals_main');


function is_tablet($vars)
{
    $tablet_browser = foody_is_tablet();

    $vars['isTablet'] = $tablet_browser;
    return $vars;
}

add_filter('foody_js_globals', 'is_tablet');


function foody_set_og_image()
{
    if (is_author()) {

        $author = new Foody_Author();

        $author_image = $author->topic_image();
        $image = "<meta property=\"og:image\" content=\"$author_image\">";

        $image .= '<meta property="og:image:width" content="96">';
        $image .= '<meta property="og:image:height" content="96">';
        echo $image;
    }


}

add_action('wp_head', 'foody_set_og_image');


function foody_hide_mobile_filter($vars)
{
    $queried_object = get_queried_object();
    $show_filters = get_field('show_filters', $queried_object);
    if ($show_filters === false) {
        $vars['hideFilter'] = true;
    }
    return $vars;
}

add_filter('foody_js_globals', 'foody_hide_mobile_filter');


function foody_env_scripts()
{
    $scripts = [
        'http://foody.moveodevelop.com' => [
            "    (function(h,o,t,j,a,r){

        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};

        h._hjSettings={hjid:1114919,hjsv:6};

        a=o.getElementsByTagName('head')[0];

        r=o.createElement('script');r.async=1;

        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;

        a.appendChild(r);

    })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');"
        ],
        'https://foody.co.il' => [
        ]
    ];

    if (isset($scripts[home_url()])) {
        $env_scripts = $scripts[home_url()];

        foreach ($env_scripts as $script) {

            ?>
            <script>

                <?php echo $script ?>

            </script>
            <?php
        }
    }
}

add_action('wp_head', 'foody_env_scripts');

function foody_category_pagination()
{
    if (is_category()) {
        $page = 1;
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
        }

        global $wp_query;
        $max_pages = $wp_query->max_num_pages;

        $prev = $page - 1;
        $next = $page + 1;

        $link = get_term_link(get_queried_object_id());
        if ($prev > 0) {
            $href = $link . "?page=" . $prev;
            echo '<link id="pagination-prev" rel="prev" href="' . $href . '">';
        }

        if ($next <= $max_pages) {
            $href = $link . "?page=" . $next;
            echo '<link id="pagination-next" rel="next" href="' . $href . '">';
        }
    }
}

add_action('wp_head', 'foody_category_pagination');


function add_filter_query_arg($vars)
{
    $vars['filterQueryArg'] = Foody_Query::$filter_query_arg;

    return $vars;
}

add_filter('foody_js_globals', 'add_filter_query_arg');