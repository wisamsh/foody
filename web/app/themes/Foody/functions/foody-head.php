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

    } elseif (is_page_template('page-templates/foody-campaign.php') || is_page_template('page-templates/foody-campaign-extended.php')) {

        $type = 'campaign';

    } elseif (is_page_template('page-templates/foody-course.php')) {

        $type = 'course';

    }

    return $type;
}


function foody_js_globals_main($vars)
{

    $vars['queryPage'] = apply_filters('foody_page_query_var', Foody_Query::$page);
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
        if ($vars['post']['type'] === "foody_recipe") {
            $feed_area_id = get_field('recipe_channel', $vars['post']['ID']);
            $recipe_referer = isset($_GET['referer']) ? $_GET['referer'] : $feed_area_id;
            $vars['post']['categories'] = wp_get_post_terms($vars['post']['ID'], 'category');
            $vars['post']['publisher'] = $recipe_referer  ? get_field('publisher_name', $recipe_referer) : '';
        }
        if ($vars['post']['type'] === "foody_course") {
            $vars['post']['hostName'] = get_field('host_name', $vars['post']['ID']);
            if(empty(['post']['hostName'])){
                $vars['post']['hostName'] = get_field('course_page_main_cover_section_host_name', $vars['post']['ID']);
            }
        }
    }

    $queried_object = get_queried_object();
    if (is_category() || is_tag()) {
        $vars['title'] = $queried_object->name;
    } elseif (is_author()) {
        $vars['title'] = $queried_object->data->display_name;
    }

    // Custom accessibility
    $show_custom_accessibility = get_theme_mod('show_white_label_accessibility');

    if ($show_custom_accessibility) {
        $vars['show_custom_accessibility'] = $show_custom_accessibility;
        $vars['custom_accessibility_class'] = get_theme_mod('white_label_accessibility_class');
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


function campaign_name($vars)
{
    if (get_page_type() == 'campaign') {
        if (is_user_logged_in()) {
            $extended_campaign_url = get_field('extended_campaign_url');
            if (!empty($extended_campaign_url)) {
                $vars['extended_campaign_url'] = $extended_campaign_url['url'];
            }
            $vars['seen_extended_approvals'] = Foody_User::user_has_meta('seen_extended_approvals');
        }
    }
    $registration_page = get_page_by_title('הרשמה');
    $vars['campaign_name'] = get_field('campaign_name', $registration_page);
    $vars['campaign_url'] = get_field('campaign_link', $registration_page);

    return $vars;
}

add_filter('foody_js_globals', 'campaign_name');

function channel_name($vars){
    global $post;
    $page = get_queried_object();
    if(isset($_GET) && isset($_GET['referer']) && $_GET['referer']){
        $vars['channel_name'] = get_the_title($_GET['referer']);
    }
    elseif (isset($post->ID) && isset($post->post_type) && ($post->post_type == 'foody_recipe' || $post->post_type === 'post') && get_field('recipe_channel', $post->ID)){
        $vars['channel_name'] = get_the_title(get_field('recipe_channel', $post->ID));
        $vars['referered_area'] = get_field('recipe_channel', $post->ID);
    }
    elseif(isset($page->taxonomy) && $page->taxonomy === 'category' && get_field('recipe_channel', $page)) {
        $vars['channel_name'] = get_the_title(get_field('recipe_channel', $page));
        $vars['referered_area'] = get_field('recipe_channel', $page);
    }
    return $vars;
}

add_filter('foody_js_globals', 'channel_name');

function channel_publisher_name($vars){
    global $post;
    $page = get_queried_object();

    if(isset($_GET) && isset($_GET['referer']) && $_GET['referer']){
        $vars['channel_publisher_name'] = get_field('publisher_name' ,$_GET['referer']);
    }
    elseif (isset($post->ID) && isset($post->post_type) && ($post->post_type == 'foody_recipe' || $post->post_type === 'post') && get_field('recipe_channel', $post->ID)){
        $recipe_referer = get_field('recipe_channel', $post->ID);
        $vars['channel_publisher_name'] = get_field('publisher_name' ,$recipe_referer);
    }
    elseif(isset($page->taxonomy) && $page->taxonomy === 'category' && get_field('recipe_channel', $page)) {
        $recipe_referer = get_field('recipe_channel', $page);
        $vars['channel_publisher_name'] = get_field('publisher_name' ,$recipe_referer);
    }
    if (get_post_type() == 'foody_feed_channel') {
        $vars['channel_publisher_name'] = get_field('publisher_name');
    }
    return $vars;
}

add_filter('foody_js_globals', 'channel_publisher_name');

function page_template_name($vars){
    $template_slug = get_page_template_slug();
    $template_name  = str_replace('page-templates/', '', $template_slug);
    $template_name  = str_replace('.php', '', $template_name);
    $vars['page_template_name'] = $template_name;

    return $vars;
}

add_filter('foody_js_globals', 'page_template_name');

function can_user_rate($vars){
    global $post;
    $rating_obj = new Foody_Rating();
    $can_user_rate = !$rating_obj->user_rated($post->ID, get_current_user_id());
    $vars['can_user_rate'] = $can_user_rate;

    return $vars;
}

add_filter('foody_js_globals', 'can_user_rate');

function foody_set_og_image()
{
    if (is_author()) {

        $author = new Foody_Author();

        $author_image = $author->topic_image(250);
        $image = "<meta property=\"og:image\"  itemprop=\"image\" content=\"" . $author_image . "\">";

        $image .= '<meta property="og:image:width" content="300">';
        $image .= '<meta property="og:image:height" content="200">';
        echo $image;
    } else if (get_post_type() == 'foody_feed_channel') {

        $cover_image = get_field('cover_image');
        if (!empty($cover_image) && isset($cover_image['url'])) {
            $image = "<meta property=\"og:image\" content=\"" . $cover_image['url'] . "\">";

            $image .= '<meta property="og:image:width" content="1024">';
            $image .= '<meta property="og:image:height" content="683">';
            echo $image;
        }
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
        ],
        'https://foody.co.il' => []
    ];

    if (isset($scripts[home_url()])) {
        $env_scripts = $scripts[home_url()];

        foreach ($env_scripts as $script) {

            ?>
            <script async defer>

                <?php echo $script ?>

            </script>
            <?php
        }
    }
}

add_action('wp_head', 'foody_env_scripts');

function foody_page_content_pagination()
{
    if (is_category() || is_home() || is_front_page()) {
        $page = get_query_var('page');
        if (isset($_GET['page'])) {
            $page = $_GET['page'];
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        if (empty($page)) {
            $page = 1;
        }
        $args = [
            'post_type' => ['foody_recipe', 'foody_playlist', 'post'],
            'post_status' => 'publish',
            'fields' => 'ids'
        ];

        $posts_per_page = get_option('posts_per_page');
        $link = home_url();

        if (is_category()) {
            $args['cat'] = get_queried_object_id();
            $link = get_term_link(get_queried_object_id());
        }

        $q = new WP_Query($args);

        $posts_count = $q->found_posts;
        if (is_numeric($posts_count)) {
            $posts_count = intval($posts_count);
        } else {
            $posts_count = 0;
        }

        $max_pages = $posts_count / $posts_per_page;

        $prev = $page - 1;
        $next = $page + 1;
        $q_or_path = '/page/';
        if (is_category()) {
            $q_or_path = '?page=';
        }
        if ($prev > 0) {
            $href = $link . $q_or_path . $prev;
            echo '<link id="pagination-prev" rel="prev" href="' . $href . '">';
        }

        if ($next <= $max_pages) {
            $href = $link . $q_or_path . $next;
            echo '<link id="pagination-next" rel="next" href="' . $href . '">';
        }
    }
}

add_action('wp_head', 'foody_page_content_pagination');


function add_filter_query_arg($vars)
{
    $vars['filterQueryArg'] = Foody_Query::$filter_query_arg;

    return $vars;
}

add_filter('foody_js_globals', 'add_user_data_globals');

function add_user_data_globals($vars)
{
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $social = get_user_meta($user_id, 'wsl_current_provider', true);
        if (!empty($social)) {
            $vars['user'] = [
                'social_type' => $social
            ];
        }
    }

    return $vars;
}

add_filter('foody_js_globals', 'add_filter_query_arg');

function foody_style_placeholder()
{
    ?>
    <style>
        body {
            -webkit-transition: opacity .15s;
            -moz-transition: opacity .15s;
            -ms-transition: opacity .15s;
            -o-transition: opacity .15s;
            transition: opacity .15s;
            opacity: 0;
        }
    </style>
    <?php
}


add_action('wp_head', 'foody_style_placeholder');


function add_bg_class($classes)
{

    $bg_image = foody_get_background_image();

    $has_background = !empty($bg_image);

    $bg_class = $has_background ? 'has-background' : '';

    $classes[] = $bg_class;

    return $classes;
}

add_filter('body_class', 'add_bg_class');

add_action('wp_head', 'foody_replace_to_webp');

function foody_replace_to_webp()
{
    ?>
    <script>
        window.onload = function () {
            var images;
            images = document.getElementsByTagName('img');
            for (var i = 0, len = images.length; i < len; i++) {
                if (images[i].hasAttribute('data-src')) {
                    images[i].src = images[i].getAttribute('data-src');
                }
            }
        };
    </script>
    <?php
}

function foody_background_image_referer()
{
    ?>
    <script>
        function createRefererLinks(background_referer) {
            if (background_referer) {
                let content = document.getElementsByClassName('content');
                let links = content[0].getElementsByTagName("a");
                for (let i = 0; i < links.length; i++) {
                    let linkURL = new URL(links[i].href);
                    let disableReferrer = typeof $(links[i]).attr('data-disable_referrer') != 'undefined' && parseInt($(links[i]).attr('data-disable_referrer')) === 1;
                    if (!disableReferrer && linkURL && !linkURL.hash && linkURL.origin == window.location.origin && linkURL.href != window.location.origin + '/' && linkURL.href != window.location.href && !linkURL.search.includes('referer')) {
                        if (links[i].href.includes('?')) {
                            links[i].href += '&referer=' + background_referer;
                        } else {
                            links[i].href += '?referer=' + background_referer;
                        }
                    }
                }
            }
        }
    </script>
    <?php

    if (!is_search() && get_post_type() == 'foody_feed_channel') {
        ?>
        <script>
            jQuery(document).ready(($) => {
                setTimeout(() => {
                    let background_referer = '' + <?php echo get_queried_object_id(); ?>;
                    createRefererLinks(background_referer);
                })
            });

        </script>
        <?php
    }
    elseif ((isset($_GET['referer']) && !empty($_GET['referer'])) && (is_category() || is_tag() || get_post_type() == 'foody_filter')) {
        ?>
        <script>
            jQuery(document).ready(($) => {
                setTimeout(() => {
                    let background_referer = '' + <?php echo $_GET['referer']; ?>;
                    createRefererLinks(background_referer);
                });
            });
        </script>
        <?php
    }
    elseif(get_post_type() == 'foody_recipe' || get_post_type() === "post"){
        $channel_connection = get_field('recipe_channel');

        if($channel_connection) {
            ?>
            <script>
                jQuery(document).ready(($) => {
                    setTimeout(() => {
                        let background_referer = '' + <?php echo $channel_connection; ?>;
                        // createRefererLinks(background_referer);
                    })
                });

            </script>
            <?php
        }
    }
    elseif(is_category()){
        $channel_connection = get_field('recipe_channel', get_queried_object());

        if($channel_connection) {
            ?>
            <script>
                jQuery(document).ready(($) => {
                    setTimeout(() => {
                        let background_referer = '' + <?php echo $channel_connection; ?>;
                        createRefererLinks(background_referer);
                    })
                });

            </script>
            <?php
        }
    }
}

add_action('wp_head', 'foody_background_image_referer');