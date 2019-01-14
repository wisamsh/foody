<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/11/18
 * Time: 11:02 AM
 *
 *
 * This singleton is responsible for providing
 * handler functions related to querying the database using the @see WP_Query class.
 *
 */
class Foody_Query
{

    // TODO remove the 'get_args' call in every func


    private static $default_args;

    private static $instance;

    public static $page = 'page';

    /**
     * Foody_Query constructor.
     */
    private function __construct()
    {
        self::$default_args = [
            'posts_per_page' => get_option('posts_per_page'),
            'post_status' => 'publish'
        ];
        if (is_front_page()) {
            self::$page = 'page';
        }
    }

    public static function get_instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Foody_Query();
        }

        return self::$instance;
    }


    public function homepage()
    {
        $args = self::get_args([
            'post_type' => ['foody_recipe', 'foody_playlist', 'post']
        ]);
        return $args;
    }

    public function category($id)
    {
        $args = self::get_args([
            'post_type' => ['foody_recipe', 'foody_playlist', 'post'],
            'cat' => $id
        ]);

        return $args;
    }

    public function author($id, $post_type)
    {
        $id = intval($id);
        $args = self::get_args([
            'post_type' => $post_type,
            'author' => $id
        ]);

        return $args;
    }


    public function search()
    {
        $search_term = get_search_query();
        $search_term = html_entity_decode($search_term);
        $search_term = esc_sql($search_term);
        $search_term = urldecode($search_term);

        global $wpdb;
        $search_term = $wpdb->esc_like($search_term);
        if (empty($search_term)) {
            if (isset($_POST['filter']['search'])) {
                $search_term = $_POST['filter']['search'];

            } elseif (isset($_POST['data']['search'])) {
                $search_term = $_POST['data']['search'];

            }
        }

        $args = self::get_args([
            'post_type' => ['foody_recipe', 'foody_playlist', 'post'],
            's' => $search_term
        ]);


        return $args;
    }

    public function tag($id)
    {
        $args = self::get_args([
            'post_type' => ['foody_recipe', 'foody_playlist', 'post'],
            'tag_id' => $id
        ]);

        return $args;
    }

    public function purchase_buttons($post_id)
    {
        return self::get_args([
            'post__in' => [
                $post_id
            ]
        ]);
    }


    /**
     * @param $context
     * @param array $context_args
     * @param bool $ranged
     * @return mixed|WP_Error
     */
    public function get_query($context, $context_args = [], $ranged = false)
    {
        if (method_exists($this, $context)) {
            $fn = array($this, $context);
            if ($context == 'homepage') {
                $foody_args = call_user_func($fn);
            } else {
                if (!is_array($context_args)) {
                    $context_args = array($context_args);
                }
                $foody_args = call_user_func_array($fn, $context_args);
            }

            $page = get_query_var('paged');


            if (!$page) {
                if (isset($_REQUEST['page'])) {
                    $page = $_REQUEST['page'];
                } else {
                    $page = $this->get_param('page');
                    if (!$page) {
                        $page = 1;
                    }
                }
            }

            $foody_args['paged'] = $page;

            if ($ranged) {
                $foody_args['posts_per_page'] = $this->get_posts_per_page() * $page;
                $foody_args['paged'] = 0;
            }

        } else {
            $foody_args = new WP_Error("invalid context: $context");
        }

        return $foody_args;
    }


    public function has_more_posts(WP_Query $query)
    {
        $current_page = intval($query->get('paged', 0));

        $max = $query->max_num_pages;

        if ($max > $current_page) {
            if ($current_page == 0) {
                $options_per_page = $this->get_posts_per_page();
                $query_per_page = intval($query->get('posts_per_page'));
                $query->set('posts_per_page', $options_per_page);
                $query->get_posts();
                $max = $query->max_num_pages;
                $current_page = $query_per_page / $options_per_page;
            }
        }

        return $max > $current_page;
    }

    private function get_posts_per_page()
    {
        return intval(get_option('posts_per_page'));
    }

    private function get_args($args = [])
    {
        return array_merge(
            self::$default_args,
            $args
        );
    }

    private function get_param($variableName, $default = null)
    {

        // Was the variable actually part of the request
        if (array_key_exists($variableName, $_REQUEST))
            return $_REQUEST[$variableName];

        // Was the variable part of the url
        $urlParts = explode('/', preg_replace('/\?.+/', '', $_SERVER['REQUEST_URI']));
        $position = array_search($variableName, $urlParts);
        if ($position !== false && array_key_exists($position + 1, $urlParts))
            return $urlParts[$position + 1];

        return $default;
    }

    public static function get_search_url($search_term)
    {
        $post_types = ['foody_playlist', 'foody_recipe', 'post'];

        global $wpdb;

        $base = home_url();

        $base = add_query_arg('s', $wpdb->prepare($search_term, []), $base);

        foreach ($post_types as $post_type) {
            $base = add_query_arg('post_type', $post_type, $base);
        }

        return $base;
    }

}