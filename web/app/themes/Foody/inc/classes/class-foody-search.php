<?php

/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 9/6/18
 * Time: 6:28 PM
 */
class Foody_Search
{

    const type_ingredient = 'ingredient';
    const type_category = 'category';
    const type_technique = 'technique';
    const type_limitation = 'limitation';
    const type_accessory = 'accessory';
    const type_tags = 'tag';
    const type_authors = 'author';

    private $types;

    private $query_builder;

    public $wildcards = [
        'ingredients_ingredients_groups_$_ingredients_$_ingredient' => 'ingredients_ingredients_groups_%_ingredients_%_ingredient'
    ];

    private $types_aliases = [
        'ingredient' => 'ingredients',
        'category' => 'categories',
        'technique' => 'techniques',
        'accessory' => 'accessories',
        'limitation' => 'limitations',
        'tag' => 'tags',
        'author' => 'authors'
    ];

    /**
     * Foody_Search constructor.
     */
    public function __construct()
    {
        $this->query_builder = new Foody_QueryBuilder();
    }

    public function query($args, $wp_args = [])
    {
        /*
         * args:
        * {
        *  search:'asfgag',
        *  posts ids to include in the filter
        *  context:[1,3,56],
        *  'types':[{
        *      type:'categories|ingredients|techniques|accessories|limitations|tags',
        *      exclude:false,
        *      id:8
        *  }]
        * }
        * */

        $query = $this->build_query($args, $wp_args);

        $this->before_query();

        $posts = $query->get_posts();

        $this->after_query();

        return $posts;
    }


    /**
     * Get wp query
     * @param array $args
     * @param array $wp_args
     * @return WP_Query
     */
    public function build_query($args, $wp_args = [], $sort = '')
    {
        $this->types = group_by($args['types'], 'type');

        foreach ($this->types as $type => $values) {
            $this->maybe_add_to_query($type);
        }


        if (isset($args['context'])) {
            if (is_array($args['context'])) {
                $args['context'] = array_map('intval', $args['context']);
                $this->query_builder->context($args['context']);
            }
        }

        if (!empty($sort)) {
            $this->query_builder->sort($sort);
        }

        $query = $this->query_builder
            ->build($wp_args);


        return $query;
    }

    public function before_query()
    {
        // add filter to manage meta_key wildcard placeholders
        if ($this->query_builder->has_wildcard_key) {
            add_filter('posts_where', array($this, 'replace_wildcards_keys'), 10, 1);
        }
    }


    public function after_query()
    {
        // remove filter to prevent query
        // corruption in other queries
        if ($this->query_builder->has_wildcard_key) {
            remove_filter('posts_where', array($this, 'replace_wildcards_keys'));
        }

    }

    private function maybe_add_to_query($type)
    {
        $values = $this->get_values_for_type($type);

        // only add to query if
        // has values
        if (!empty($values)) {

            if (isset($this->types_aliases[$type]) || in_array($type, array_values($this->types_aliases))) {

                // type alias corresponds
                // to builder method.
                // Example: type->ingredient, method->ingredients
                $fn = $this->types_aliases[$type];
                if (is_null($fn)) {
                    $fn = $type;
                }
                if (method_exists($this->query_builder, $fn)) {
                    call_user_func(array($this->query_builder, $fn), $values);
                } else {
                    if (WP_ENV == 'development') {
                        throw new Exception("unknown builder method: $fn");
                    }
                }
            }
        }
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

    private function get_values_for_type($type)
    {
        $values = [];

        // safety
        if (!is_null($this->types)) {

            if (isset($this->types[$type])) {
                $values = $this->types[$type];
            } else {
                // if we have an alias for
                // the current type call method
                // again with alias.
                if (isset($this->types_aliases[$type])) {
                    $type = $this->types_aliases[$type];
                    return $this->get_values_for_type($type);
                }
            }
        }

        return $values;
    }

}

class Foody_QueryBuilder
{

    public $has_wildcard_key = false;

    public $sort_keys = [
        'popular',
        'date',
        'title'
    ];

    public $order;
    public $order_by;
    public $meta_key;
    public $meta_type;

    private $meta_query_array = [];

    private $categories__in = [];
    private $categories__not_in = [];

    private $tags__in = [];
    private $tags__not_in = [];

    private $post__not_in = [];
    private $post__in = [];

    private $author__in;
    private $author__not_in;

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
            if (isset($category_arg['exclude']) && $category_arg['exclude'] != "false") {
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


            $values = array_map(function ($ingredient) {
                return $ingredient['id'];
            }, $ingredients_to_exclude);


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
                        'value' => $values
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
            $values = array_map(function ($ingredient) {
                return $ingredient['id'];
            }, $ingredients_to_include);
            $this->has_wildcard_key = true;
            $this->meta_query_array[] = [
                'key' => 'ingredients_ingredients_groups_$_ingredients_$_ingredient',
                'compare' => 'IN',
                'value' => $values
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

    public function authors($authors)
    {
        $parsed = $this->parse_args($authors);

        if (count($parsed['exclude']) > 0) {
            $this->author__not_in = array_map(function ($author) {
                return $author['id'];
            }, $parsed['exclude']);
        }

        if (count($parsed['include']) > 0) {
            $this->author__in = array_map(function ($author) {
                return $author['id'];
            }, $parsed['include']);
        }

    }

    /**
     * Includes only posts within
     * the current page
     * @param $ids
     * @return $this
     */
    public function context($ids)
    {
        $ids = array_unique($ids);
        $this->post__in = array_merge($this->post__in, $ids);
        return $this;
    }

    /**
     * Creates a WP_Query instance
     * built from the passed args
     *
     * @return WP_Query
     */
    public function build($wp_args = [])
    {

        $args = $this->get_args();

        $args = array_merge_recursive($args, $wp_args);

        $query = new WP_Query($args);

        return $query;
    }

    public function get_args()
    {
        $args = [
            'has_wildcard_key' => $this->has_wildcard_key,
            'post_type' => ['foody_recipe', 'foody_playlist'],
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

        if (!empty($this->author__not_in)) {
            $args['author__not_in'] = $this->author__not_in;
        }

        if (!empty($this->author__in)) {
            $args['author__in'] = $this->author__in;
        }


        if (!empty($this->s)) {
            $args['s'] = $this->s;
        }


        if (!empty($this->post__in)) {

            $this->post__in = array_filter($this->post__in, function ($post_id) {
                return !in_array($post_id, $this->post__not_in);
            });


            $args['post__in'] = $this->post__in;
        }

        if (!empty($this->order)) {
            $args['order'] = strtoupper($this->order);
            $args['orderby'] = $this->order_by;
            if (!empty($this->meta_key)) {
                $args['meta_key'] = $this->meta_key;

                if (!empty($this->meta_type)) {
                    $args['meta_type'] = $this->meta_type;

                }
            }
        }

        return $args;
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
            if (isset($arg['exclude']) && $arg['exclude'] == "true") {
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

    public function sort($sort)
    {
        $args = explode('_', $sort);

        $key = $args[0];
        $order = $args[1];

        $this->order = $order;

        if (in_array($key, $this->sort_keys)) {
            switch ($key) {
                case 'popular':
                    $this->order_by = 'meta_value_num';
                    $this->meta_key = 'post_views_count';
                    $this->meta_type = 'NUMERIC';
                    break;
                case 'title':
                    $this->order_by = 'title';
                    break;
            }
        }
    }

}