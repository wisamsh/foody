<?php
/**
 * Created by PhpStorm.
 * User: moveosoftware
 * Date: 6/30/19
 * Time: 5:05 PM
 */

namespace FoodyAPI;


class Foody_BotHandler
{
    public static $MAX_RECIPES = 100;


    /**
     * Foody_BotHandler constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $params array
     * @return array
     */
    public function query($params)
    {
        $posts = $this->getPosts($params);
        return $this->extractAttributesFromRecipes($posts, $params);
    }

    public function getResults($params)
    {

        $posts = $this->getPosts($params);

        $posts = array_map('get_permalink', $posts);

        return $posts;
    }

    private function getPosts($params)
    {
        $args = $this->parseQueryArgs($params);

        $posts = [];
        if (!empty($args)) {

            $args['suppress_filters'] = false;

            add_filter('posts_where', array($this, 'botPostsWhere'), 11, 2);

            $posts = get_posts($args);

            remove_filter('posts_where', array($this, 'botPostsWhere'), 11);

        }

        return $posts;
    }

    /**
     * @param $params array
     * @return array|null
     */
    private function parseQueryArgs($params)
    {
        $defaults = [
            'ingredients' => [],
            'tags' => [],
            'accessories' => [],
            'techniques' => [],
            'authors' => [],
            'level' => '',
            'time' => [],
        ];

        $params = wp_parse_args($params, $defaults);

        $args = null;

        if (!empty(array_filter($params))) {
            $args = [
                'post_type' => 'foody_recipe',
                'post_status' => 'publish',
                'posts_per_page' => self::$MAX_RECIPES,
                'meta_query' => [
                    'relation' => 'AND'
                ]
            ];

            if (!empty($params['ingredients'])) {
                $ingredients = $this->findPostsByTitle($params['ingredients'], 'foody_ingredient');
                if (count($ingredients) != count($params['ingredients'])) {
                    $ingredients[] = 'INVALID';
                }
                $args['has_wildcard_key'] = true;
                $args['meta_query'][] = [
                    'key' => 'ingredients_ingredients_groups_$_ingredients_$_ingredient',
                    'compare' => 'IN',
                    'value' => $ingredients
                ];
            }

            if (!empty($params['tags'])) {
                $tags = $this->getTermsByNames($params['tags'], 'post_tag');
                if (count($tags) != count($params['tags'])) {
                    $tags[] = 'INVALID';
                }
                $args['tag__in'] = $tags;
                $args['category__in'] = $tags;
            }

            if (!empty($params['accessories'])) {
                $accessories = $this->findPostsByTitle($params['accessories'], 'foody_accessory');
                if (count($accessories) != count($params['accessories'])) {
                    $accessories[] = 'INVALID';
                }
                foreach ($accessories as $accessory) {
                    $args['meta_query'][] = [
                        'key' => 'accessories_accessories',
                        'compare' => 'LIKE',
                        'value' => '"' . $accessory . '"'
                    ];
                }
            }

            if (!empty($params['techniques'])) {
                $techniques = $this->findPostsByTitle($params['techniques'], 'foody_technique');
                if (count($techniques) != count($params['techniques'])) {
                    $techniques[] = 'INVALID';
                }
                foreach ($techniques as $technique) {
                    $args['meta_query'][] = [
                        'key' => 'techniques_techniques',
                        'compare' => 'LIKE',
                        'value' => '"' . $technique . '"'
                    ];
                }
            }

            if (!empty($params['limitations'])) {
                $limitations = $this->getTermsByNames($params['limitations'], 'limitations');
                if (count($limitations) != count($params['limitations'])) {
                    $limitations[] = 'INVALID';
                }
                foreach ($limitations as $limitation) {
                    $args['meta_query'][] = [
                        'key' => 'limitations',
                        'compare' => 'LIKE',
                        'value' => '"' . $limitation . '"'
                    ];
                }
            }

            if (!empty($params['authors'])) {
                // find authors by names
                $authors = $this->findAuthorsByDisplayName($params['authors']);
                if (count($authors) != count($params['authors'])) {
                    $authors[] = 'INVALID';
                }
                $args['author__in'] = $authors;
            }

            if (!empty($params['level'])) {
                $level_settings = get_field_object('field_5b34fe4c9c7eb');
                $level = array_search($params['level'], $level_settings['choices']);
                $args['meta_query'][] = [
                    'key' => 'overview_difficulty_level',
                    'compare' => '=',
                    'value' => $level
                ];
            }

            if (!empty(($time = $params['time'])) && (!empty($time['min']) || !empty($time['max']))) {

                $time_meta_query = [

                ];

                if (!empty($time['min'])) {
                    $time_meta_query[] = [
                        'key' => 'overview_preparation_time_time',
                        'compare' => '>=',
                        'value' => intval($time['min']),
                        'type' => 'NUMERIC'
                    ];
                }

                if (!empty($time['max'])) {
                    $time_meta_query[] = [
                        'key' => 'overview_preparation_time_time',
                        'compare' => '<=',
                        'value' => intval($time['max']),
                        'type' => 'NUMERIC'
                    ];
                }

                if (!empty($time_meta_query)) {
                    $time_meta_query['relation'] = 'AND';
                    $args['meta_query'][] = $time_meta_query;
                }
            }

        }

        return $args;
    }

    private function extractAttributesFromRecipes(array $posts, $params)
    {
        $foody_posts = array_map('Foody_Post::create', $posts);
        $foody_recipes = array_filter($foody_posts, function ($post) {
            return $post instanceof \Foody_Recipe;
        });

        $attributes = [
            'tags' => [],
            'ingredients' => [],
            'accessories' => [],
            'techniques' => [],
            'limitations' => [],
        ];

        /** @var \Foody_Recipe $foody_recipe */
        foreach ($foody_recipes as $foody_recipe) {

            // ingredients
            $ingredients_titles = [];
            foreach ($foody_recipe->ingredients_groups as $ingredients_group) {
                $group_ingredients_titles = array_map(function ($ingredient) {
                    /** @var \Foody_Ingredient $ingredient */
                    return $ingredient->getTitle();
                }, $ingredients_group['ingredients']);

                $ingredients_titles = array_merge($ingredients_titles, $group_ingredients_titles);
            }

            $ingredients_titles = array_diff($ingredients_titles, $params['ingredients']);
            $attributes['ingredients'] = $ingredients_titles;


            // tags and categories
            $tags = wp_get_post_tags($foody_recipe->id, ['fields' => 'names']);
            $categories = wp_get_post_categories($foody_recipe->id, ['fields' => 'names']);
            $tags = array_merge($tags, $categories);
            if (!empty($tags)) {
                $attributes['tags'] = array_merge($attributes['tags'], array_diff($tags, $params['tags']));
            }


            $repeaters = [
                'accessories' => [
                    'selector' => 'accessories_accessories',
                    'mapper' => 'get_the_title'
                ],
                'techniques' => [
                    'selector' => 'techniques_techniques',
                    'mapper' => 'get_the_title'],
                'limitations' => [
                    'selector' => 'limitations',
                    'mapper' => function ($limitation) {
                        return get_term_field('name', $limitation);
                    }],
            ];

            foreach ($repeaters as $name => $args) {
                $accessories = get_field($args['selector'], $foody_recipe->id);
                $accessories = array_map($args['mapper'], $accessories);
                $attributes[$name] = array_merge($attributes[$name], array_diff($accessories, $params[$name]));
            }
        }

        foreach ($attributes as &$attribute) {
            $attribute = array_unique($attribute);
            $attribute = array_values($attribute);
        }

        return $attributes;
    }

    public function findPostsByTitle($titles, string $post_type)
    {
        global $wpdb;
        $titles_in = implode(', ', array_fill(0, count($titles), '%s'));
        $query = "SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' and post_title in ($titles_in) and post_type = '$post_type'";
        $query = $wpdb->prepare($query, $titles);
        $results = $wpdb->get_results($query);
        $results = array_map(function ($post) {
            return $post->ID;
        }, $results);
        return $results;
    }

    private function getTermsByNames($names, string $taxonomy)
    {
        $args = [
            'name' => $names,
            'taxonomy' => $taxonomy,
            'fields' => 'ids'
        ];

        $query = new \WP_Term_Query($args);

        return $query->get_terms();
    }


    /**
     * @param $where string
     * @param $query \WP_Query
     * @return mixed
     */
    public function botPostsWhere($where, $query)
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

    private function findAuthorsByDisplayName($authors)
    {
        global $wpdb;
        $names = implode(', ', array_fill(0, count($authors), '%s'));
        $query = "SELECT ID FROM {$wpdb->users} WHERE display_name in ($names)";
        $query = $wpdb->prepare($query, $authors);

        $results = $wpdb->get_results($query);
        $results = array_map(function ($post) {
            return $post->ID;
        }, $results);

        return $results;
    }


}