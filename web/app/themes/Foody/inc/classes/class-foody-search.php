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

    /**
     * Foody_Search constructor.
     */
    public function __construct()
    {

    }

    public function query($args)
    {
        $this->types = group_by($args['types'], 'type');
        /*
         * {
         *  search:'asfgag',
         *  'types':[{
         *      type:'category|ingredient|technique|accessory',
         *      exclude:false,
         *      id:8
         *  }]
         * }
         * */
    }

    function search_ingredient()
    {

        $meta_key = 'ingredients_ingredients_groups_$i_ingredients_$j_ingredient';

        // filter
        function my_posts_where($where)
        {

            $where = str_replace("meta_key = 'ingredients_ingredients_groups_\$_ingredients_\$_ingredient", "meta_key LIKE 'ingredients_ingredients_groups_%_ingredients_%_ingredient", $where);

            return $where;
        }

        function my_posts_where_sql($where, WP_Query $query)
        {
            if ($query->get('custom_sql')) {
                $where = str_replace("meta_key = 'ingredients_ingredients_groups_\$_ingredients_\$_ingredient", $query->get('custom_sql'), $where);
            }

            return $where;
        }

        $keys = [];

        for ($i = 0; $i < 3; $i++) {

            for ($j = 0; $j < 10; $j++) {
                $keys[] = str_replace('$j', $j, str_replace('$i', $i, $meta_key));
            }
        }

//        $keys = array_map(function ($key) {
//            return [
//                'relation' => 'OR',
//                [
//                    'key' => $key,
//                    'compare' => 'NOT EXISTS'
//                ],
//                [
//                    'key' => $key,
//                    'compare' => '!=',
//                    'value' => 2665
//                ],
//
//            ];
//        }, $keys);

        $keys = implode(',', $keys);
        add_filter('posts_where', 'my_posts_where_sql', 10, 2);
        $args = [
            'post_type' => 'foody_recipe',
            'custom_sql' => "meta_key in ({$keys}) AND meta_value NOT IN (2665)",
            'meta_query' => [
                'key' => 'ingredients_ingredients_groups_$_ingredients_$_ingredient'
            ]
        ];

//        $args['meta_query'] = array_merge($args['meta_query'], $keys);

        $query = new WP_Query($args);

        $posts = $query->get_posts();
        remove_filter('posts_where', 'my_posts_where_sql');
    }

}

class Foody_QueryBuilder
{

    private $meta_keys = [
        'ingredient' => 'ingredients_ingredients_groups_$_ingredients_$_ingredient',
        'technique' => 'techniques_techniques',
        'accessory' => 'accessories_accessories',
        'limitation' => 'limitations'
    ];

    private $meta_query_array = [];

    private $categories__in = [];
    private $categories__not_in = [];

    /**
     * Foody_QueryBuilder constructor.
     */
    public function __construct()
    {
    }


    public function meta_query($args, $type)
    {

        $meta_args = array_map(function ($arg) use ($type) {
            $meta_arg = [
                'key' => $this->meta_keys[$type],
                'value' => $arg['id'],
                'compare' => '='
            ];
            if (isset($arg['exclude']) && $arg['exclude']) {
                $meta_arg['compare'] = '!=';
            }

            return $meta_arg;
        }, $args);


        $this->meta_query_array = array_merge($this->meta_query_array, $meta_args);

        return $this;
    }

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


}