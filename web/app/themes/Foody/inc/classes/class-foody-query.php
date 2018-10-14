<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 10/11/18
 * Time: 11:02 AM
 */
class Foody_Query
{

    // TODO maybe remove the 'get_args' call in every func


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
            'post_type' => ['foody_recipe', 'foody_playlist']
        ]);
        return $args;
    }

    public function category($id)
    {
        $args = self::get_args([
            'post_type' => ['foody_recipe', 'foody_playlist', 'post'],
            'post_status' => 'publish', // TODO add to default args
            'cat' => $id
        ]);

        return $args;
    }

    public function author($id, $post_type)
    {
        $args = self::get_args([
            'post_type' => $post_type,
            'author' => $id
        ]);

        return $args;
    }


    public function search()
    {
        $args = self::get_args([
            'post_type' => ['foody_recipe', 'foody_playlist'],
            's' => $this->get_query('s')
        ]);


        return $args;
    }


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

    private function get_args($args)
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

}