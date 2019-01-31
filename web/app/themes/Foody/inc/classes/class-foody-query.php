<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/11/18
 * Time: 11:02 AM
 *
 *
 * This singleton is responsible for providing
 * handler methods related to querying the database using the @see WP_Query class.
 * Each method adds the relevant query for its context.
 * For example, the method @see Foody_Query::category() ads a 'cat' query to the final
 * query that will finally be executed.
 */
class Foody_Query
{

    private static $default_args;

    private static $instance;

    public static $page = 'page';

    public static $query_prefix = 'fd_';

    public static $query_params = [
        'in' => 'ingredients',
        'ca' => 'categories',
        'te' => 'techniques',
        'li' => 'limitations',
        'ac' => 'accessories',
        'ta' => 'tags',
        'au' => 'authors'
    ];

    /**
     * Foody_Query constructor.
     */
    private function __construct()
    {
        self::$default_args = [
            'posts_per_page' => get_option('posts_per_page'),
            'post_status' => 'publish',
            'post_type' => ['foody_recipe', 'foody_playlist', 'post']
        ];
        if (is_front_page()) {
            self::$page = 'page';
        }
    }

    public static function get_query_params()
    {
        $ret_val = [];
        foreach (self::$query_params as $query_param => $type) {
            $ret_val[self::$query_prefix . $query_param] = $type;
        }
        return $ret_val;
    }

    private function parse_query_args($context, $context_args)
    {
        $query_args = array_keys(self::$query_params);
        $foody_search = new Foody_Search($context, $context_args);

        $args = [
            'types' => [],
            'after_foody_query' => true
        ];

        foreach ($query_args as $query_arg) {
            $value = $this->foody_get_query_arg($query_arg);
            if (!empty($value)) {
                $args['types'] = array_merge($this->parse_query_values($query_arg), $args['types']);
            }
        }

        return $foody_search->build_query($args, [], '', true);
    }

    private function foody_get_query_arg($key)
    {
        $value = '';

        if (isset($_GET[self::$query_prefix . $key])) {
            $value = $_GET[self::$query_prefix . $key];
        }

        return $value;
    }

    private function parse_query_values($query_arg)
    {
        $values = $this->array_query_string_to_array($query_arg);
        $type = self::$query_params[$query_arg];

        $filter_items = array_map(function ($value) use ($type) {

            $exclude = strpos($value, '-');
            $filter_value = abs(intval($value));
            return [
                'type' => $type,
                'value' => $filter_value,
                'exclude' => $exclude
            ];
        }, $values);

        return $filter_items;
    }

    /**
     * @param $key string key in $_GET
     * @param string $delimiter used to separate multiple values, defaults to ','
     * @return array
     */
    private function array_query_string_to_array($key, $delimiter = ',')
    {
        $arr = [];
        $value = $this->foody_get_query_arg($key);
        if (!empty($value)) {
            $arr = array_map(function ($val) {
                return trim($val);
            }, explode($delimiter, $value));
            if (is_array($arr) && !empty($arr)) {
                $arr = array_filter($arr, function ($value) {
                    return !empty($value) && is_numeric($value);
                });
            }
        }

        return $arr;
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
        $args = self::get_args();
        return $args;
    }

    public function category($id)
    {
        $args = self::get_args([
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

    public function profile($content_type)
    {
        $user = new Foody_User();

        if (!$user->user->ID) {
            return [];
        }

        $args = [];

        if ($content_type == 'favorites') {
            $posts = $user->favorites;
            if (!empty($posts)) {
                $posts = array_map('intval', array_values($posts));
                $args['posts_per_page'] = count($posts);
            }
        } elseif ($content_type == 'channels') {
            $posts = $user->get_followed_content();
            if (!empty($posts)) {
                $posts = array_map(function ($post) {
                    $retval = $post;
                    if ($post instanceof WP_Post || $post instanceof stdClass) {
                        $retval = $post->ID;
                    }
                    return $retval;
                }, $posts);
            }
        }

        if (!isset($posts)) {
            return [];
        }

        $args = array_merge([
            'post__in' => $posts,
//            'post_type' => ['foody_recipe', 'foody_playlist'],
            'orderby' => 'date',
            'order' => 'DESC'
        ], $args);

        return self::get_args($args);
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

        $filter_args = $this->parse_query_args($context, $context_args);
        $foody_args = array_merge($filter_args, $foody_args);
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