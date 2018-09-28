<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/6/18
 * Time: 6:28 PM
 */
class Foody_Search
{

    private $types;

    private $query_builder;

    public $wildcards = [
        'ingredients_ingredients_groups_$_ingredients_$_ingredient' => 'ingredients_ingredients_groups_%_ingredients_%_ingredient'
    ];

    /**
     * Foody_Search constructor.
     */
    public function __construct()
    {
        $this->query_builder = new Foody_QueryBuilder();
    }

    public function query($args)
    {
        $this->types = group_by($args['types'], 'type');
        /*
         * {
         *  search:'asfgag',
         *  'types':[{
         *      type:'categories|ingredients|techniques|accessories|limitations',
         *      exclude:false,
         *      id:8
         *  }]
         * }
         * */

        if (isset($this->types['ingredient'])) {
            $ingredients = $this->types['ingredient'];
            $this->query_builder
                ->ingredients($ingredients);
        }

        if (isset($this->types['category'])) {
            $categories = $this->types['category'];
            $this->query_builder
                ->categories($categories);
        }

        if (isset($this->types['technique'])) {
            $techniques = $this->types['technique'];
            $this->query_builder
                ->techniques($techniques);
        }

        if (isset($this->types['limitation'])) {
            $limitation = $this->types['limitation'];
            $this->query_builder
                ->limitations($limitation);
        }


        if (isset($this->types['tag'])) {
            $tags = $this->types['tag'];
            $this->query_builder
                ->tags($tags);
        }


        $query = $this->query_builder
            ->build();

        // add filter to manage meta_key wildcard placeholders
        if ($this->query_builder->has_wildcard_key) {
            add_filter('posts_where', array($this, 'replace_wildcards_keys'), 10, 1);
        }




        $posts = $query->get_posts();

        // remove filter to prevent query
        // corruption in other queries
        if ($this->query_builder->has_wildcard_key) {
            remove_filter('posts_where', array($this, 'replace_wildcards_keys'));
        }

        echo count($posts);

    }

    /**
     * Filter callback for 'posts_where'.
     * Replaces wildcard placeholders with
     * actual wildcards.
     *
     * @param string $where
     * @return string
     */
    function replace_wildcards_keys($where)
    {
        foreach ($this->wildcards as $search => $replace) {
            $where = str_replace(
                "meta_key = '" . $search . "'",
                "meta_key LIKE '" . $replace . "'",
                $where
            );
        }

        return $where;
    }

}

class Foody_QueryBuilder
{

    public $has_wildcard_key = false;

    private $meta_query_array = [];

    private $categories__in = [];
    private $categories__not_in = [];

    private $tags__in = [];
    private $tags__not_in = [];

    private $post__not_in = [];

    private $s;

    /**
     * Foody_QueryBuilder constructor.
     */
    public function __construct()
    {
    }


    /**
     * @param array $categories_args
     * @return $this
     */
    public function categories($categories_args)
    {
        foreach ($categories_args as $category_arg) {
            if (isset($category_arg['exclude']) && $category_arg['exclude']) {
                $this->categories__not_in[] = $category_arg['id'];
            } else {
                $this->categories__in[] = $category_arg['id'];
            }
        }

        return $this;
    }


    /**
     * @param array $ingredients
     * @return $this
     */
    public function ingredients($ingredients)
    {

        $parsed = $this->parse_args($ingredients);

        $ingredients_to_exclude = $parsed['exclude'];
        $ingredients_to_include = $parsed['include'];


        if (!empty($ingredients_to_exclude)) {
            function my_posts_where($where, WP_Query $query)
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

            add_filter('posts_where', 'my_posts_where', 10, 2);

            $args = [
                'has_wildcard_key' => true,
                'post_type' => 'foody_recipe',
                'meta_query' => [
                    [
                        'key' => 'ingredients_ingredients_groups_$_ingredients_$_ingredient',
                        'compare' => 'IN',
                        'value' => $ingredients_to_exclude
                    ]

                ],
                'fields' => 'ids'
            ];


            $query = new WP_Query($args);

            $posts = $query->get_posts();

            $this->post__not_in = array_merge($this->post__not_in, $posts);


            remove_filter('posts_where', 'my_posts_where');
        }


        if (!empty($ingredients_to_include)) {
            $this->has_wildcard_key = true;
            $this->meta_query_array[] = [
                'key' => 'ingredients_ingredients_groups_$_ingredients_$_ingredient',
                'compare' => 'IN',
                'value' => $ingredients_to_include
            ];
        }


        return $this;
    }

    /**
     * @param array $techniques
     * @return $this
     */
    public function techniques($techniques)
    {
        if (empty($techniques)) {
            return $this;
        }
        $this->repeater_search($techniques, 'techniques_techniques');

        return $this;
    }

    /**
     * @param array $accessories
     * @return $this
     */
    public function accessories($accessories)
    {
        if (empty($accessories)) {
            return $this;
        }
        $this->repeater_search($accessories, 'accessories_accessories');

        return $this;
    }

    /**
     * @param array $tags
     * @return $this
     */
    public function tags($tags)
    {
        if (empty($tags)) {
            return $this;
        }
        $parsed = $this->parse_args($tags);

        if (!empty($parsed['exclude'])) {
            $this->tags__not_in = array_map(function ($arg) {
                return $arg['id'];
            }, $parsed['exclude']);
        }

        if (!empty($parsed['include'])) {
            $this->tags__in = array_map(function ($arg) {
                return $arg['id'];
            }, $parsed['include']);
        }

        return $this;
    }

    /**
     * @param array $limitations
     * @return $this
     */
    public function limitations($limitations)
    {
        if (empty($limitations)) {
            return $this;
        }

        $this->repeater_search($limitations, 'limitations');

        return $this;
    }

    public function s($search)
    {
        if (empty($search)) {
            return $this;
        }

        $this->s = $search;

        return $this;
    }

    /**
     * Creates a WP_Query instance
     * built from the passed args
     *
     * @return WP_Query
     */
    public function build()
    {

        $args = [
            'has_wildcard_key' => $this->has_wildcard_key,
            'post_type' => 'foody_recipe',
            'meta_query' => $this->meta_query_array,
            'post__not_in' => $this->post__not_in
        ];

        if (!empty($this->categories__in)) {
            $args['category__and'] = $this->categories__in;
        }


        if (!empty($this->categories__not_in)) {
            $args['category__not_in'] = $this->categories__not_in;
        }


        if (!empty($this->tags__in)) {
            $args['tags__in'] = $this->tags__in;
        }


        if (!empty($this->tags__not_in)) {
            $args['tags__not_in'] = $this->tags__not_in;
        }

        if (!empty($this->s)) {
            $args['s'] = $this->s;
        }

        $query = new WP_Query($args);

        return $query;
    }


    // ====== Helper Functions ====== //

    private function repeater_search($args, $key)
    {
        $parsed = $this->parse_args($args);

        if (!empty($parsed['exclude'])) {

            $meta_query = [
                'relation' => 'AND',
            ];

            foreach ($parsed['exclude'] as $item) {
                $meta_query[] = [
                    'key' => $key,
                    'compare' => 'NOT LIKE',
                    'value' => '"' . $item['id'] . '"'
                ];
            }
        }

        if (!empty($parsed['include'])) {

            $meta_query = [
                'relation' => 'AND',
            ];

            foreach ($parsed['include'] as $item) {
                $meta_query[] = [
                    'key' => $key,
                    'compare' => 'LIKE',
                    'value' => '"' . $item['id'] . '"'
                ];
            }
        }

        if (isset($meta_query)) {
            $this->meta_query_array[] = $meta_query;
        }
    }

    private function parse_args($args)
    {
        $exclude = [];
        $include = [];


        foreach ($args as $arg) {
            if (isset($arg['exclude']) && $arg['exclude']) {
                $exclude[] = $arg;
            } else {
                $include[] = $arg;
            }
        }

        return [
            'exclude' => $exclude,
            'include' => $include
        ];

    }

}